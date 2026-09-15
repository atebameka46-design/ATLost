@extends('layouts.dashboard', ['title' => 'Signaler un document', 'heading' => 'Signaler un document'])
@section('dashboard-content')
<div class="dashboard-page-content section-page"><section class="dashboard-card content-card">
<p class="dashboard-eyebrow">Espace citoyen</p><h1 class="dashboard-title">Signaler un document trouvé</h1>
<form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="internal-search-form">@csrf
<label>Nom sur le document<input name="owner_name" value="{{ old('owner_name') }}" required></label>
<label>Type<select name="document_type" required><option value="">Sélectionnez</option><option value="CNI">Carte Nationale</option><option value="Permis">Permis de conduire</option><option value="Passeport">Passeport</option></select></label>
<label>Lieu de découverte<input name="location" value="{{ old('location') }}" required></label><label>Téléphone<input name="phone" value="{{ old('phone') }}" required></label>
<label>Description<textarea name="description">{{ old('description') }}</textarea></label><button class="dashboard-green-button" type="submit">Enregistrer le signalement</button>
<label>Photo du document<input type="file" name="photo" accept="image/*" required></label>
</form></section></div>
@endsection
