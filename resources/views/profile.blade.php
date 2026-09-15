@extends('layouts.dashboard', ['title' => __('messages.profile').' — ATLost', 'heading' => __('messages.profile')])

@section('dashboard-content')
@php($user = auth()->user())
<div class="dashboard-page-content profile-page">
    <div class="profile-hero"><div class="profile-avatar profile-avatar-large">@if ($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="Photo de profil de {{ $user->name }}">@else{{ strtoupper(substr($user->name, 0, 1)) }}@endif</div><div class="profile-hero-copy"><p class="dashboard-eyebrow">{{ __('messages.profile_title') }}</p><h1>{{ $user->name }}</h1><p>{{ $user->isAdmin() ? __('messages.administrator') : __('messages.citizen') }} · {{ __('messages.member_since') }} {{ $user->created_at?->format('M Y') ?? __('messages.recently') }}</p></div><span class="profile-role">{{ $user->isAdmin() ? __('messages.administrator') : __('messages.citizen') }}</span></div>
    @if (session('status')) <div class="profile-status"><i class="fa-solid fa-check"></i> {{ session('status') }}</div> @endif
    <div class="profile-stat-row"><div><strong>0</strong><span>{{ $user->isAdmin() ? __('messages.reports_processed') : __('messages.reports_created') }}</span></div><div><strong>{{ $user->isAdmin() ? '98%' : '0' }}</strong><span>{{ $user->isAdmin() ? __('messages.returns_48h') : __('messages.documents_found') }}</span></div><div><strong>{{ $user->created_at?->diffInDays(now()) ?? 0 }}</strong><span>{{ __('messages.days_on_atlost') }}</span></div></div>
    <div class="profile-content-grid">
        <section class="dashboard-card profile-form-card"><div class="profile-section-heading"><div><p class="dashboard-eyebrow">{{ __('messages.personal_info') }}</p><h2 class="dashboard-title">{{ __('messages.contact_details') }}</h2></div><span class="profile-section-icon"><i class="fa-solid fa-user"></i></span></div><button type="button" class="dashboard-green-button modal-trigger" data-target="profile-update-modal">{{ __('messages.update_profile') }}</button></section>
        <section class="dashboard-card profile-security-card"><div class="profile-section-heading"><div><p class="dashboard-eyebrow">{{ __('messages.security') }}</p><h2 class="dashboard-title">{{ __('messages.protect_account') }}</h2></div><span class="profile-section-icon"><i class="fa-solid fa-shield-halved"></i></span></div><div class="profile-security-line"><span><i class="fa-solid fa-check"></i></span><div><strong>{{ __('messages.account_secure') }}</strong><small>{{ __('messages.email_linked') }}</small></div></div><button type="button" class="dashboard-green-button modal-trigger" data-target="profile-password-modal">{{ __('messages.change_password') }}</button></section>
        @if ($user->isAdmin())<section class="dashboard-card profile-role-card"><p class="dashboard-eyebrow">{{ __('messages.admin_space') }}</p><h2 class="dashboard-title">{{ __('messages.platform_admin') }}</h2><p>{{ __('messages.admin_description') }}</p><a href="{{ route('admin.dashboard') }}" class="profile-text-link">{{ __('messages.back_to_dashboard') }} →</a></section>@else<section class="dashboard-card profile-role-card"><p class="dashboard-eyebrow">{{ __('messages.citizen_space') }}</p><h2 class="dashboard-title">{{ __('messages.activity') }}</h2><p>{{ __('messages.citizen_description') }}</p><a href="{{ route('citizen.dashboard') }}" class="profile-text-link">{{ __('messages.view_activity') }} →</a></section>@endif
        <section class="dashboard-card profile-preferences-card"><p class="dashboard-eyebrow">{{ __('messages.preferences') }}</p><h2 class="dashboard-title">{{ __('messages.help') }}</h2><p>{{ __('messages.help_message') }}</p><a href="{{ url('/#faq') }}" class="profile-text-link">{{ __('messages.faq') }} →</a></section>
        <section class="dashboard-card profile-preferences-card" id="settings"><p class="dashboard-eyebrow">{{ __('messages.preferences') }}</p><h2 class="dashboard-title">{{ __('messages.interface_preferences') }}</h2><div class="profile-setting-row"><div><strong>{{ __('messages.theme') }}</strong><small>{{ __('messages.theme_description') }}</small></div><select id="theme-switcher" aria-label="{{ __('messages.theme') }}"><option value="light">{{ __('messages.light') }}</option><option value="dim">{{ __('messages.soft') }}</option><option value="dark">{{ __('messages.dark') }}</option></select></div><div class="profile-setting-row"><div><strong>{{ __('messages.notifications_setting') }}</strong><small>{{ __('messages.notifications_desc') }}</small></div><span class="profile-toggle">{{ __('messages.enabled') }}</span></div></section>
        <section class="dashboard-card profile-preferences-card profile-account-actions"><p class="dashboard-eyebrow">{{ __('messages.account') }}</p><h2 class="dashboard-title">{{ __('messages.quick_actions') }}</h2><a href="{{ route('password.request') }}" class="profile-text-link">{{ __('messages.reset_password') }} →</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="profile-logout-button"><i class="fa-solid fa-right-from-bracket"></i> &nbsp; {{ __('messages.logout') }}</button></form></section>
    </div>
</div>

<div class="dashboard-modal" id="profile-update-modal" aria-hidden="true">
    <div class="dashboard-modal-panel">
        <div class="dashboard-modal-header"><h2>{{ __('messages.update_profile') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')
            <label>{{ __('messages.profile_photo') }}<input type="file" name="avatar" accept="image/*"></label>
            <label>{{ __('messages.full_name') }}<input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>
            @error('name')<small class="profile-error">{{ $message }}</small>@enderror
            <label>{{ __('messages.email_address') }}<input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
            @error('email')<small class="profile-error">{{ $message }}</small>@enderror
            <button type="submit" class="dashboard-green-button">{{ __('messages.save_changes') }}</button>
        </form>
    </div>
</div>

<div class="dashboard-modal" id="profile-password-modal" aria-hidden="true">
    <div class="dashboard-modal-panel">
        <div class="dashboard-modal-header"><h2>{{ __('messages.change_password') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
        <form method="POST" action="{{ route('profile.password') }}" class="profile-form">
            @csrf
            @method('PUT')
            <label>{{ __('messages.current_password') }}<input type="password" name="current_password" required></label>
            <label>{{ __('messages.new_password') }}<input type="password" name="password" required></label>
            <label>{{ __('messages.confirm_password') }}<input type="password" name="password_confirmation" required></label>
            <button class="dashboard-green-button">{{ __('messages.change_password') }}</button>
        </form>
    </div>
</div>

<style>
    .dashboard-modal { position: fixed; inset: 0; background: rgba(10, 15, 30, 0.58); display: none; align-items: center; justify-content: center; padding: 1.5rem; z-index: 1000; }
    .dashboard-modal.is-open { display: flex; }
    .dashboard-modal-panel { width: min(100%, 620px); background: #fff; border-radius: 18px; box-shadow: 0 24px 70px rgba(10, 15, 30, 0.22); border: 1px solid rgba(120, 124, 146, 0.18); padding: 1.25rem 1.25rem 1.4rem; }
    .dashboard-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .dashboard-modal-header h2 { margin: 0; }
    .dashboard-modal-close { border: 0; background: transparent; font-size: 1.5rem; cursor: pointer; color: #4a5168; }
</style>

<script>
    document.querySelectorAll('.modal-trigger').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.target;
            const modal = document.getElementById(targetId);
            if (modal) {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            }
        });
    });

    document.querySelectorAll('.dashboard-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    });

    document.querySelectorAll('.dashboard-modal-close').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = trigger.closest('.dashboard-modal');
            if (modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    });
</script>
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
