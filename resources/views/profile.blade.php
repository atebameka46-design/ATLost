@extends('layouts.dashboard', ['title' => 'Mon profil — ATLost', 'heading' => 'Mon profil'])

@section('dashboard-content')
@php($user = auth()->user())
<div class="dashboard-page-content profile-page">
    <div class="profile-hero"><div class="profile-avatar profile-avatar-large">{{ strtoupper(substr($user->name, 0, 1)) }}</div><div class="profile-hero-copy"><p class="dashboard-eyebrow">Compte ATLost</p><h1>{{ $user->name }}</h1><p>{{ $user->isAdmin() ? 'Administrateur de la plateforme' : 'Membre de la communauté ATLost' }} · Membre depuis {{ $user->created_at?->format('M Y') ?? 'récemment' }}</p></div><span class="profile-role">{{ $user->isAdmin() ? 'Administrateur' : 'Citoyen' }}</span></div>
    @if (session('status')) <div class="profile-status"><i class="fa-solid fa-check"></i> {{ session('status') }}</div> @endif
    <div class="profile-stat-row"><div><strong>0</strong><span>{{ $user->isAdmin() ? 'Signalements traités' : 'Signalements créés' }}</span></div><div><strong>{{ $user->isAdmin() ? '98%' : '0' }}</strong><span>{{ $user->isAdmin() ? 'Restitutions sous 48h' : 'Documents retrouvés' }}</span></div><div><strong>{{ $user->created_at?->diffInDays(now()) ?? 0 }}</strong><span>Jours sur ATLost</span></div></div>
    <div class="profile-content-grid">
        <section class="dashboard-card profile-form-card"><div class="profile-section-heading"><div><p class="dashboard-eyebrow">Informations personnelles</p><h2 class="dashboard-title">Vos coordonnées</h2></div><span class="profile-section-icon"><i class="fa-solid fa-user"></i></span></div><form method="POST" action="{{ route('profile.update') }}" class="profile-form">@csrf @method('PUT')<label>Nom complet<input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>@error('name')<small class="profile-error">{{ $message }}</small>@enderror<label>Adresse e-mail<input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>@error('email')<small class="profile-error">{{ $message }}</small>@enderror<button type="submit" class="dashboard-green-button">Enregistrer les modifications</button></form></section>
        <section class="dashboard-card profile-security-card"><div class="profile-section-heading"><div><p class="dashboard-eyebrow">Sécurité</p><h2 class="dashboard-title">Protéger votre compte</h2></div><span class="profile-section-icon"><i class="fa-solid fa-shield-halved"></i></span></div><div class="profile-security-line"><span><i class="fa-solid fa-check"></i></span><div><strong>Compte sécurisé</strong><small>Votre adresse e-mail est associée à votre compte.</small></div></div><a class="profile-action-link" href="{{ route('password.request') }}">Modifier mon mot de passe <b>›</b></a></section>
        @if ($user->isAdmin())<section class="dashboard-card profile-role-card"><p class="dashboard-eyebrow">Espace administrateur</p><h2 class="dashboard-title">Centre de pilotage</h2><p>Accédez à la modération, au suivi des restitutions et aux indicateurs de la plateforme.</p><a href="{{ route('admin.dashboard') }}" class="profile-text-link">Retourner au dashboard →</a></section>@else<section class="dashboard-card profile-role-card"><p class="dashboard-eyebrow">Votre espace citoyen</p><h2 class="dashboard-title">Votre activité</h2><p>Retrouvez vos signalements, recherches et documents qui vous concernent.</p><a href="{{ route('citizen.dashboard') }}" class="profile-text-link">Voir mon activité →</a></section>@endif
        <section class="dashboard-card profile-preferences-card"><p class="dashboard-eyebrow">Préférences</p><h2 class="dashboard-title">Besoin d’aide ?</h2><p>Notre équipe est disponible pour vous accompagner dans vos démarches sur ATLost.</p><a href="{{ url('/#faq') }}" class="profile-text-link">Consulter la FAQ →</a></section>
        <section class="dashboard-card profile-preferences-card" id="settings"><p class="dashboard-eyebrow">Personnalisation</p><h2 class="dashboard-title">Préférences de l’interface</h2><div class="profile-setting-row"><div><strong>Thème</strong><small>Choisissez l’apparence du dashboard</small></div><select id="theme-switcher" aria-label="Choisir le thème"><option value="light">Clair</option><option value="dim">Doux</option><option value="dark">Sombre</option></select></div><div class="profile-setting-row"><div><strong>Notifications</strong><small>Recevoir les alertes importantes</small></div><span class="profile-toggle">Activées</span></div></section>
        <section class="dashboard-card profile-preferences-card profile-account-actions"><p class="dashboard-eyebrow">Compte</p><h2 class="dashboard-title">Actions rapides</h2><a href="{{ route('password.request') }}" class="profile-text-link">Réinitialiser le mot de passe →</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="profile-logout-button"><i class="fa-solid fa-right-from-bracket"></i> &nbsp; Se déconnecter</button></form></section>
    </div>
</div>
<script>
    const themeSwitcher = document.getElementById('theme-switcher');
    const savedTheme = localStorage.getItem('atlost-theme') || 'light';
    document.body.classList.add(`theme-${savedTheme}`);
    if (themeSwitcher) {
        themeSwitcher.value = savedTheme;
        themeSwitcher.addEventListener('change', (event) => {
            document.body.classList.remove('theme-light', 'theme-dim', 'theme-dark');
            document.body.classList.add(`theme-${event.target.value}`);
            localStorage.setItem('atlost-theme', event.target.value);
        });
    }
</script>
@endsection
