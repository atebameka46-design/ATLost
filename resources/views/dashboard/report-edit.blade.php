@extends('layouts.dashboard', ['title' => 'Modifier le signalement', 'heading' => 'Modifier le signalement'])
@section('dashboard-content')
<div class="dashboard-page-content section-page">
    <section class="dashboard-card content-card">
        <p class="dashboard-eyebrow">Signalement #{{ $documentReport->id }}</p><h1 class="dashboard-title">Modifier le document</h1>
        <form method="POST" action="{{ route('reports.update-details', $documentReport) }}" enctype="multipart/form-data" class="internal-search-form">
            @csrf @method('PUT')
            <label>Nom sur le document<input name="owner_name" value="{{ old('owner_name', $documentReport->owner_name) }}" required></label>
            <label>Type<select name="document_type" required><option value="CNI" @selected($documentReport->document_type === 'CNI')>Carte Nationale</option><option value="Permis" @selected($documentReport->document_type === 'Permis')>Permis</option><option value="Passeport" @selected($documentReport->document_type === 'Passeport')>Passeport</option></select></label>
            <label>Lieu<input name="location" value="{{ old('location', $documentReport->location) }}" required></label>
            <label>Téléphone<input name="phone" value="{{ old('phone', $documentReport->phone) }}" required></label>
            <label>Description<textarea name="description">{{ old('description', $documentReport->description) }}</textarea></label>
            <label>Nouvelle photo (facultative)<input type="file" name="photo" accept="image/*"></label>
            <button class="dashboard-green-button" type="submit">Enregistrer</button>
        </form>
    </section>
</div>
@endsection
