<?php

namespace App\Http\Controllers;

use App\Models\DocumentAlert;
use App\Models\DocumentReport;
use App\Models\DocumentSearch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentSearchController extends Controller
{
    private function documentTypeRules(): array
    {
        return [
            'document_type' => ['nullable', 'in:CNI,Permis,Passeport,Other'],
            'document_type_other' => ['required_if:document_type,Other', 'nullable', 'string', 'max:40'],
        ];
    }

    private function resolveDocumentType(array $data): ?string
    {
        return ($data['document_type'] ?? null) === 'Other'
            ? trim((string) $data['document_type_other'])
            : ($data['document_type'] ?? null);
    }

    public function marketplace(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $documents = DocumentReport::query()
            ->whereIn('status', ['pending', 'approved'])
            ->whereDoesntHave('claims', fn ($query) => $query->where('status', 'completed'))
            ->when($query, fn ($builder) => $builder->where(function ($builder) use ($query) {
                $like = '%'.mb_strtolower($query).'%';
                $builder->whereRaw('LOWER(owner_name) LIKE ?', [$like])->orWhereRaw('LOWER(location) LIKE ?', [$like])->orWhereRaw('LOWER(document_type) LIKE ?', [$like]);
            }))
            ->latest()->paginate(12)->withQueryString();

        return view('marketplace', compact('documents', 'query'));
    }

    public function index(): View
    {
        return view('dashboard.section', [
            'section' => 'search',
            'searches' => auth()->user()->documentSearches()->latest()->get(),
            'results' => DocumentReport::query()
                ->whereIn('status', ['pending', 'approved'])
                ->whereDoesntHave('claims', fn ($query) => $query->where('status', 'completed'))
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): View
    {
        $data = $request->validate(['query' => ['required', 'string', 'max:120'], ...$this->documentTypeRules(), 'status' => ['nullable', 'in:approved,resolved'], 'location' => ['nullable', 'string', 'max:160']]);
        $data['document_type'] = $this->resolveDocumentType($data);
        unset($data['document_type_other']);
        $search = $request->user()->documentSearches()->create($data);
        $needle = mb_strtolower(trim($data['query']));
        $results = DocumentReport::query()
            ->where(function ($query) use ($needle) {
                $like = "%{$needle}%";
                $query->whereRaw('LOWER(owner_name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(location) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(document_type) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(description, \'\')) LIKE ?', [$like]);
            })
            ->when($data['document_type'] ?? null, fn ($query, $type) => $query->where('document_type', $type))
            ->whereIn('status', ['pending', 'approved'])
            ->whereDoesntHave('claims', fn ($query) => $query->where('status', 'completed'))
            ->when($data['location'] ?? null, fn ($query, $location) => $query->whereRaw('LOWER(location) LIKE ?', ['%'.mb_strtolower($location).'%']))
            ->latest()->get();

        return view('dashboard.section', ['section' => 'search', 'searches' => $request->user()->documentSearches()->latest()->get(), 'results' => $results, 'lastSearch' => $search]);
    }

    public function destroy(DocumentSearch $documentSearch): RedirectResponse
    {
        abort_unless($documentSearch->user_id === auth()->id(), 403);
        $documentSearch->delete();

        return back()->with('status', 'La recherche a été supprimée.');
    }

    public function alert(Request $request): RedirectResponse
    {
        $data = $request->validate(['query' => ['required', 'string', 'max:120'], ...$this->documentTypeRules()]);
        $data['document_type'] = $this->resolveDocumentType($data);
        unset($data['document_type_other']);
        DocumentAlert::firstOrCreate(['user_id' => $request->user()->id, 'query' => $data['query'], 'document_type' => $data['document_type'] ?? null]);

        return back()->with('status', 'Vous serez prévenu dès qu’un document correspondant sera disponible.');
    }

    public function update(Request $request, DocumentSearch $documentSearch): RedirectResponse
    {
        abort_unless($documentSearch->user_id === auth()->id(), 403);

        $data = $request->validate([
            'query' => ['required', 'string', 'max:120'],
            ...$this->documentTypeRules(),
            'status' => ['nullable', 'in:approved,resolved'],
            'location' => ['nullable', 'string', 'max:160'],
        ]);
        $data['document_type'] = $this->resolveDocumentType($data);
        unset($data['document_type_other']);

        $documentSearch->update($data);

        return back()->with('status', 'La recherche a été mise à jour.');
    }
}
