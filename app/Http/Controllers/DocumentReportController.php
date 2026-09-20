<?php

namespace App\Http\Controllers;

use App\Models\DocumentAlert;
use App\Models\DocumentClaim;
use App\Models\DocumentReport;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocumentReportController extends Controller
{
    private function rules(): array
    {
        return ['document_type' => ['required', 'in:CNI,Permis,Passeport,Other'], 'document_type_other' => ['required_if:document_type,Other', 'nullable', 'string', 'max:40'], 'owner_name' => ['required', 'string', 'max:120'], 'location' => ['required', 'string', 'max:160'], 'phone' => ['required', 'string', 'max:30'], 'reward_amount' => ['nullable', 'numeric', 'min:0', 'max:100000000'], 'description' => ['nullable', 'string', 'max:1000'], 'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']];
    }

    private function resolveDocumentType(array $data): string
    {
        return $data['document_type'] === 'Other' ? trim((string) $data['document_type_other']) : $data['document_type'];
    }

    public function create(): View
    {
        return view('dashboard.report-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data['document_type'] = $this->resolveDocumentType($data);
        unset($data['document_type_other']);
        $data['photo_path'] = $request->file('photo')->store('document-reports', 'public');
        $report = DocumentReport::create([...$data, 'user_id' => $request->user()?->id, 'status' => 'approved']);
        if ($report->user_id) {
            UserNotification::create(['user_id' => $report->user_id, 'title' => 'Signalement enregistré', 'message' => "Votre signalement #{$report->id} est maintenant visible dans la base.", 'action_url' => route('reports.show', $report)]);
        }
        DocumentAlert::where(function ($q) use ($report) {
            $q->whereRaw("LOWER(?) LIKE '%' || LOWER(query) || '%'", [$report->owner_name]);
        })
            ->when($report->document_type, fn ($q) => $q->where(function ($q) use ($report) {
                $q->whereNull('document_type')->orWhere('document_type', $report->document_type);
            }))
            ->get()->each(fn ($alert) => UserNotification::create(['user_id' => $alert->user_id, 'title' => 'Document disponible', 'message' => 'Un document correspondant à votre alerte vient d’être signalé.', 'action_url' => route('reports.show', $report)]));

        return $request->user() ? redirect()->route('citizen.documents')->with('status', "Signalement #{$report->id} enregistré.") : back()->with('status', 'Votre signalement a été enregistré.');
    }

    public function index(): View
    {
        return view('dashboard.section', ['section' => 'documents', 'reports' => auth()->user()->documentReports()->latest()->get()]);
    }

    public function show(DocumentReport $documentReport): View
    {
        return view('dashboard.report-show', compact('documentReport'));
    }

    public function claim(Request $request, DocumentReport $documentReport): RedirectResponse
    {
        abort_if($documentReport->status === 'resolved', 409, 'Ce document a déjà été restitué.');
        $claim = DocumentClaim::firstOrNew(['document_report_id' => $documentReport->id, 'claimant_id' => $request->user()->id]);
        $wasNewRequest = ! $claim->exists || $claim->status === 'cancelled';
        if ($wasNewRequest) {
            $claim->fill(['status' => 'pending', 'payment_status' => 'unpaid', 'appointment_at' => null])->save();
        }
        if ($wasNewRequest && $documentReport->user_id && $documentReport->user_id !== $request->user()->id) {
            UserNotification::create(['user_id' => $documentReport->user_id, 'title' => 'Nouvelle demande de récupération', 'message' => "{$request->user()->name} souhaite récupérer votre document signalé #{$documentReport->id}.", 'action_url' => route('reports.show', $documentReport)]);
        }

        return back()->with('status', 'Votre demande a été envoyée au propriétaire du signalement.');
    }

    public function acceptClaim(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($documentClaim->report->user_id === $request->user()->id, 403);
        $documentClaim->update(['status' => 'accepted']);
        UserNotification::create(['user_id' => $documentClaim->claimant_id, 'title' => 'Demande acceptée', 'message' => 'Le signaleur a accepté votre demande. Vous pouvez convenir d’un rendez-vous.', 'action_url' => route('reports.show', $documentClaim->report)]);

        return back()->with('status', 'Demande acceptée. Convenez maintenant d’un rendez-vous et du paiement.');
    }

    public function appointment(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($documentClaim->report->user_id === $request->user()->id || $documentClaim->claimant_id === $request->user()->id, 403);
        $data = $request->validate(['appointment_at' => ['required', 'date', 'after:now']]);
        $documentClaim->update(['appointment_at' => $data['appointment_at']]);
        $recipientId = $request->user()->id === $documentClaim->claimant_id ? $documentClaim->report->user_id : $documentClaim->claimant_id;
        UserNotification::create(['user_id' => $recipientId, 'title' => 'Rendez-vous proposé', 'message' => 'Un rendez-vous a été proposé pour la remise du document.', 'action_url' => route('reports.show', $documentClaim->report)]);

        return back()->with('status', 'Rendez-vous enregistré.');
    }

    public function pay(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($documentClaim->claimant_id === $request->user()->id, 403);
        abort_unless($documentClaim->status === 'accepted', 422, 'La demande doit être acceptée avant le paiement.');
        abort_if($documentClaim->payment_status === 'verified', 409, 'Ce paiement est déjà validé.');

        $data = $request->validate([
            'payment_method' => ['required', 'in:mobile_money,bank_transfer'],
            'payment_reference' => ['required', 'string', 'max:120'],
            'payment_proof' => ['nullable', 'required_if:payment_method,bank_transfer', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);
        $amount = (float) $documentClaim->report->reward_amount;
        $fee = round($amount * ((float) config('services.atlost.service_fee_percentage')) / 100, 2);
        $proofPath = $request->hasFile('payment_proof') ? $request->file('payment_proof')->store('payment-proofs', 'public') : null;

        $documentClaim->update([
            'payment_status' => 'submitted',
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'],
            'payment_proof_path' => $proofPath,
            'paid_amount' => $amount,
            'service_fee_amount' => $fee,
            'payment_submitted_at' => now(),
        ]);
        UserNotification::create(['user_id' => $documentClaim->report->user_id, 'title' => 'Paiement à vérifier', 'message' => 'Un paiement a été déclaré pour la récupération de votre document.', 'action_url' => route('admin.payments')]);

        return back()->with('status', 'Paiement déclaré. Il sera vérifié par ATLost avant la restitution.');
    }

    public function adminPayments(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('dashboard.payments', ['claims' => DocumentClaim::with(['report', 'claimant'])->whereIn('payment_status', ['submitted', 'verified', 'rejected'])->latest('payment_submitted_at')->get()]);
    }

    public function verifyPayment(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_unless($documentClaim->payment_status === 'submitted', 422);

        $documentClaim->update(['payment_status' => 'verified', 'payment_verified_at' => now(), 'payment_verified_by' => $request->user()->id]);
        UserNotification::create(['user_id' => $documentClaim->claimant_id, 'title' => 'Paiement validé', 'message' => 'Votre paiement a été validé. La restitution peut maintenant être confirmée.', 'action_url' => route('reports.show', $documentClaim->report)]);

        return back()->with('status', 'Paiement validé.');
    }

    public function rejectPayment(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_unless($documentClaim->payment_status === 'submitted', 422);

        $documentClaim->update(['payment_status' => 'rejected']);
        UserNotification::create(['user_id' => $documentClaim->claimant_id, 'title' => 'Paiement à corriger', 'message' => 'Votre paiement n’a pas pu être validé. Vérifiez la référence et le justificatif.', 'action_url' => route('reports.show', $documentClaim->report)]);

        return back()->with('status', 'Paiement rejeté.');
    }

    public function completeClaim(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($documentClaim->report->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        abort_unless($documentClaim->status === 'accepted' && $documentClaim->payment_status === 'verified', 422, 'Le paiement doit être validé avant la confirmation.');
        abort_unless($documentClaim->appointment_at, 422, 'Un rendez-vous doit être défini avant la confirmation.');

        DB::transaction(function () use ($documentClaim, $request): void {
            $netPayout = $documentClaim->netPayoutAmount();
            $documentClaim->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => $request->user()->id,
                'payout_status' => 'available',
                'payout_amount' => $netPayout,
            ]);
            $documentClaim->report->update(['status' => 'resolved']);
            $documentClaim->report->user()->increment('wallet_balance', $netPayout);
            UserNotification::create([
                'user_id' => $documentClaim->report->user_id,
                'title' => 'Gain disponible',
                'message' => "La restitution a été confirmée. Votre gain net de {$netPayout} FCFA est disponible au retrait.",
                'action_url' => route('reports.show', $documentClaim->report),
            ]);
            UserNotification::create(['user_id' => $documentClaim->claimant_id, 'title' => 'Document restitué', 'message' => 'La restitution de votre document a été confirmée.', 'action_url' => route('reports.show', $documentClaim->report)]);
        });

        return back()->with('status', 'La restitution a été confirmée. Le gain net a été ajouté au solde du signaleur.');
    }

    public function withdrawPayout(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($documentClaim->report->user_id === $request->user()->id, 403);
        abort_unless($documentClaim->status === 'completed', 422, 'Le document doit être restitué avant le retrait.');
        abort_unless($documentClaim->payout_status === 'available', 422, 'Le gain n’est pas disponible pour retrait.');

        $payoutAmount = (float) $documentClaim->payout_amount;
        abort_if($payoutAmount <= 0, 422, 'Aucun gain n’est disponible pour retrait.');

        $documentClaim->report->user()->decrement('wallet_balance', $payoutAmount);
        $documentClaim->update(['payout_status' => 'withdrawn', 'paid_out_at' => now()]);
        UserNotification::create([
            'user_id' => $documentClaim->report->user_id,
            'title' => 'Retrait effectué',
            'message' => "Vous avez retiré {$payoutAmount} FCFA de vos gains ATLost.",
            'action_url' => route('reports.show', $documentClaim->report),
        ]);

        return back()->with('status', "Retrait de {$payoutAmount} FCFA effectué.");
    }

    public function cancelClaim(Request $request, DocumentClaim $documentClaim): RedirectResponse
    {
        abort_unless($documentClaim->claimant_id === $request->user()->id, 403);
        abort_if($documentClaim->status === 'completed', 409, 'Cette demande est déjà terminée.');
        $documentClaim->update(['status' => 'cancelled']);
        UserNotification::create(['user_id' => $documentClaim->report->user_id, 'title' => 'Demande annulée', 'message' => "La demande du document #{$documentClaim->report->id} a été annulée.", 'action_url' => route('reports.show', $documentClaim->report)]);

        return back()->with('status', 'Votre demande de récupération a été annulée.');
    }

    public function edit(DocumentReport $documentReport): View
    {
        abort_unless($documentReport->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('dashboard.report-edit', compact('documentReport'));
    }

    public function updateDetails(Request $request, DocumentReport $documentReport): RedirectResponse
    {
        abort_unless($documentReport->user_id === auth()->id() || auth()->user()->isAdmin(), 403);
        $rules = $this->rules();
        $rules['photo'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
        $data = $request->validate($rules);
        $data['document_type'] = $this->resolveDocumentType($data);
        unset($data['document_type_other']);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('document-reports', 'public');
        }
        unset($data['photo']);
        $documentReport->update($data);

        return redirect()->route('citizen.documents')->with('status', "Signalement #{$documentReport->id} modifié.");
    }

    public function update(Request $request, DocumentReport $documentReport): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $documentReport->update($request->validate(['status' => ['required', 'in:pending,approved,resolved,rejected']]));

        return back()->with('status', 'Le statut du signalement a été mis à jour.');
    }

    public function destroy(DocumentReport $documentReport): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin() || $documentReport->user_id === auth()->id(), 403);
        $documentReport->delete();

        return back()->with('status', 'Le signalement a été supprimé.');
    }
}
