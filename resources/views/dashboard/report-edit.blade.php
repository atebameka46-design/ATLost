@extends('layouts.dashboard', ['title' => 'Modifier le signalement', 'heading' => 'Modifier le signalement'])
@section('dashboard-content')
<div class="dashboard-page-content section-page">
    <section class="dashboard-card content-card">
        <p class="dashboard-eyebrow">Signalement #{{ $documentReport->id }}</p><h1 class="dashboard-title">Modifier le document</h1>
        <form method="POST" action="{{ route('reports.update-details', $documentReport) }}" enctype="multipart/form-data" class="internal-search-form">
            @csrf @method('PUT')
            <label>Nom sur le document<input name="owner_name" value="{{ old('owner_name', $documentReport->owner_name) }}" required></label>
            @php($isOtherDocumentType = ! in_array($documentReport->document_type, ['CNI', 'Permis', 'Passeport'], true))
            <label>Type<select name="document_type" required onchange="toggleOtherDocumentType(this, 'report-edit-other-type')"><option value="CNI" @selected($documentReport->document_type === 'CNI')>Carte Nationale</option><option value="Permis" @selected($documentReport->document_type === 'Permis')>Permis</option><option value="Passeport" @selected($documentReport->document_type === 'Passeport')>Passeport</option><option value="Other" @selected($isOtherDocumentType)>Autre document</option></select></label>
            <label id="report-edit-other-type" class="{{ $isOtherDocumentType ? '' : 'is-hidden' }}">Précisez le type de document<input name="document_type_other" value="{{ old('document_type_other', $isOtherDocumentType ? $documentReport->document_type : '') }}" maxlength="40" @required($isOtherDocumentType)></label>
            <label>Lieu<input name="location" value="{{ old('location', $documentReport->location) }}" required></label>
            <label>Téléphone<input name="phone" value="{{ old('phone', $documentReport->phone) }}" required></label>
            <label>Montant demandé pour la restitution (FCFA)<input name="reward_amount" type="number" min="0" step="0.01" value="{{ old('reward_amount', $documentReport->reward_amount) }}"></label>
            <label>Description<textarea name="description">{{ old('description', $documentReport->description) }}</textarea></label>
            <label>Nouvelle photo (facultative)<input type="file" name="photo" accept="image/*"></label>
            <button class="dashboard-green-button" type="submit">Enregistrer</button>
        </form>
    </section>
</div>
@endsection
