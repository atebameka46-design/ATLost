<?php

namespace App\Http\Controllers;

use App\Models\DocumentAlert;
use App\Models\DocumentClaim;
use App\Models\DocumentReport;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentReportController extends Controller
{
    private function rules(): array
    {
        return ['document_type' => ['required', 'in:CNI,Permis,Passeport'], 'owner_name' => ['required', 'string', 'max:120'], 'location' => ['required', 'string', 'max:160'], 'phone' => ['required', 'string', 'max:30'], 'description' => ['nullable', 'string', 'max:1000'], 'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']];
    }

    public function create(): View
    {
        return view('dashboard.report-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
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
        $documentClaim->update(['payment_status' => 'paid']);
        UserNotification::create(['user_id' => $documentClaim->report->user_id, 'title' => 'Paiement reçu', 'message' => 'Le paiement lié à la récupération de votre document a été enregistré.', 'action_url' => route('reports.show', $documentClaim->report)]);

        return back()->with('status', 'Paiement enregistré. Le processus est terminé après le rendez-vous.');
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
