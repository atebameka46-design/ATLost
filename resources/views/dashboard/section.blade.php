@extends('layouts.dashboard', ['title' => ucfirst($section) . ' — ATLost', 'heading' => ucfirst($section)])

@php
    $sections = [
        'documents' => ['eyebrow' => 'Espace citoyen', 'title' => 'Mes documents', 'description' => 'Retrouvez vos signalements et suivez chaque étape de restitution.', 'action' => '＋ Signaler un document'],
        'activity' => ['eyebrow' => 'Espace citoyen', 'title' => 'Mon activité', 'description' => 'Un aperçu de vos recherches, signalements et notifications récentes.', 'action' => '⌕ Nouvelle recherche'],
        'search' => ['eyebrow' => 'Recherche citoyenne', 'title' => 'Rechercher un document', 'description' => 'Recherchez un document retrouvé directement depuis votre espace sécurisé.', 'action' => 'Lancer la recherche'],
        'reports' => ['eyebrow' => 'Modération', 'title' => 'Signalements', 'description' => 'Centralisez les documents déclarés et traitez les demandes en attente.', 'action' => 'Filtrer les signalements'],
        'analytics' => ['eyebrow' => 'Pilotage', 'title' => 'Analytics', 'description' => 'Analysez les performances de la plateforme et les délais de restitution.', 'action' => 'Exporter le rapport'],
        'team' => ['eyebrow' => 'Administration', 'title' => 'Équipe', 'description' => 'Gérez les membres qui participent à la modération et aux restitutions.', 'action' => '＋ Inviter un membre'],
    ][$section];
@endphp

@section('dashboard-content')
<div class="dashboard-page-content section-page">
    <div class="section-page-hero"><div><p class="dashboard-eyebrow">{{ $sections['eyebrow'] }}</p><h1>{{ $sections['title'] }}</h1><p>{{ $sections['description'] }}</p></div><a href="#" class="dashboard-green-button">{{ $sections['action'] }}</a></div>
    @if ($section === 'search')
        <section class="dashboard-card search-interface"><div class="search-interface-heading"><div><p class="dashboard-eyebrow">Base nationale</p><h2 class="dashboard-title">Trouvez un document retrouvé</h2></div><span class="profile-section-icon"><i class="fa-solid fa-magnifying-glass"></i></span></div><form class="internal-search-form"><label>Nom ou référence<input type="search" placeholder="Ex. nom inscrit sur le document"></label><label>Type de document<select><option>Tous les documents</option><option>Carte nationale d’identité</option><option>Permis de conduire</option><option>Passeport</option></select></label><button class="dashboard-green-button" type="submit">Rechercher</button></form><div class="search-result-empty"><span><i class="fa-solid fa-magnifying-glass"></i></span><strong>Prêt à lancer une recherche</strong><p>Les résultats sécurisés apparaîtront ici.</p></div></section>
    @elseif ($section === 'documents')
        <section class="dashboard-card content-card"><div class="content-card-heading"><div><p class="dashboard-eyebrow">Suivi personnel</p><h2 class="dashboard-title">Vos documents</h2></div><button class="dashboard-outline-button">Filtrer</button></div><div class="content-list"><div class="content-list-empty"><span><i class="fa-solid fa-folder-open"></i></span><div><strong>Votre liste est vide</strong><small>Les documents signalés ou retrouvés apparaîtront ici.</small></div><a href="{{ url('/#rechercher') }}" class="profile-text-link">Créer un signalement →</a></div></div></section>
    @elseif ($section === 'activity')
        <section class="dashboard-card content-card"><div class="content-card-heading"><div><p class="dashboard-eyebrow">Historique</p><h2 class="dashboard-title">Activité récente</h2></div><span class="content-period">Aujourd’hui</span></div><div class="activity-timeline"><div><i class="fa-solid fa-check"></i><p><strong>Aucune action récente</strong><small>Votre historique apparaîtra dès votre première recherche.</small></p></div><div><i class="fa-solid fa-clock-rotate-left"></i><p><strong>Restez informé</strong><small>Les mises à jour de vos documents seront affichées ici.</small></p></div></div></section>
    @elseif ($section === 'reports')
        <section class="dashboard-card content-card"><div class="content-card-heading"><div><p class="dashboard-eyebrow">File de modération</p><h2 class="dashboard-title">Signalements à traiter</h2></div><div class="content-filter-pills"><span class="is-selected">Tous</span><span>En attente</span><span>Traités</span></div></div><div class="moderation-empty"><span><i class="fa-solid fa-check"></i></span><strong>Aucun signalement en attente</strong><p>La file de modération est à jour. Les nouveaux signalements seront listés ici.</p></div></section>
    @elseif ($section === 'analytics')
        <section class="analytics-content"><div class="dashboard-card analytics-chart"><div class="content-card-heading"><div><p class="dashboard-eyebrow">Performance globale</p><h2 class="dashboard-title">Évolution des restitutions</h2></div><span class="content-period">30 derniers jours</span></div><div class="fake-chart"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div><div class="chart-labels"><span>S1</span><span>S2</span><span>S3</span><span>S4</span></div></div><div class="dashboard-card analytics-insight"><p class="dashboard-eyebrow">À retenir</p><h2 class="dashboard-title">98% sous 48h</h2><p>La plateforme maintient un excellent taux de restitution.</p><a href="#" class="profile-text-link">Télécharger le rapport →</a></div></section>
    @elseif ($section === 'team')
        <section class="dashboard-card content-card"><div class="content-card-heading"><div><p class="dashboard-eyebrow">Collaborateurs</p><h2 class="dashboard-title">Membres de l’équipe</h2></div><button class="dashboard-green-button">＋ Inviter</button></div><div class="team-empty"><div class="team-avatar">+</div><strong>Commencez à collaborer</strong><p>Ajoutez des membres pour répartir la modération et accélérer les restitutions.</p><a href="#" class="profile-text-link">Gérer les invitations →</a></div></section>
    @endif
</div>
@endsection
