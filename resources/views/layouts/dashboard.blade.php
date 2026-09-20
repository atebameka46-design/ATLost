<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — ATLost</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dash-body">
    <div class="dash-shell">
        <aside class="dash-sidebar">
            <a href="{{ route('dashboard') }}" class="dash-brand">
                <img src="{{ asset('images/logo.png') }}" alt="ATLost">
                <span>ATLost</span>
            </a>

            <p class="dash-label">{{ __('messages.menu') }}</p>
            <nav class="dash-nav" aria-label="Navigation principale">
                <a class="{{ request()->routeIs('dashboard', 'citizen.dashboard', 'admin.dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i>{{ __('messages.dashboard') }}</a>
                @auth
                    <a class="{{ request()->routeIs('messages') ? 'active' : '' }}" href="{{ route('messages') }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i>{{ __('messages.messages') }}</a>
                    @if (auth()->user()->isAdmin())
                        <a class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}"><i class="fa-solid fa-flag" aria-hidden="true"></i>{{ __('messages.reports') }} <b>12+</b></a>
                        <a class="{{ request()->routeIs('admin.payments') ? 'active' : '' }}" href="{{ route('admin.payments') }}"><i class="fa-solid fa-money-check-dollar" aria-hidden="true"></i>Paiements</a>
                        <a class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}"><i class="fa-solid fa-chart-column" aria-hidden="true"></i>Analytics</a>
                        <a class="{{ request()->routeIs('admin.team') ? 'active' : '' }}" href="{{ route('admin.team') }}"><i class="fa-solid fa-users" aria-hidden="true"></i>{{ __('messages.team') ?? 'Team' }}</a>
                    @else
                        <a class="{{ request()->routeIs('citizen.documents') ? 'active' : '' }}" href="{{ route('citizen.documents') }}"><i class="fa-solid fa-folder-open" aria-hidden="true"></i>{{ __('messages.documents') }}</a>
                        <a class="{{ request()->routeIs('citizen.search') ? 'active' : '' }}" href="{{ route('citizen.search') }}"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>{{ __('messages.search') }}</a>
                        <a class="{{ request()->routeIs('citizen.activity') ? 'active' : '' }}" href="{{ route('citizen.activity') }}"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>{{ __('messages.activity') }}</a>
                    @endif
                @endauth
            </nav>

            <p class="dash-label general">{{ __('messages.general') }}</p>
            <nav class="dash-nav" aria-label="Navigation secondaire">
                @auth
                    <a class="{{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}"><i class="fa-solid fa-user" aria-hidden="true"></i>{{ __('messages.profile') }}</a>
                @else
                    <a href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>Connexion</a>
                @endauth
            </nav>

            <div class="dash-download">
                <small><i class="fa-solid fa-bullhorn" aria-hidden="true"></i> PUBLICITÉ</small>
                <strong>Besoin d’aide pour retrouver votre document ?</strong>
                <em>L’assistant ATLost vous guide à chaque étape.</em>
                <a href="{{ route('assistant') }}"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i> Ouvrir l’assistant</a>
            </div>
        </aside>

        <main class="dash-main">
            <header class="dash-top">
                <button type="button" class="dash-back-button" aria-label="Retour" onclick="goBackToPreviousPage()">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    <span>Retour</span>
                </button>
                <div class="dash-search" role="search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span>{{ __('messages.search_task', ['default' => 'Search task']) }}</span>
                    <kbd>⌘ F</kbd>
                </div>
                <div class="dash-tools">
                    <a href="{{ route('assistant') }}" class="dash-tool-link" aria-label="{{ __('messages.assistant') }}"><i class="fa-solid fa-wand-magic-sparkles"></i></a>
                    <button type="button" aria-label="{{ __('messages.messages', ['default' => 'Messages']) }}"><i class="fa-solid fa-envelope"></i></button>
                    @auth
                        <a href="{{ route('notifications') }}" class="dash-tool-link" aria-label="{{ __('messages.notifications') }}"><i class="fa-solid fa-bell"></i>@if(auth()->user()->notifications()->whereNull('read_at')->count())<b class="notification-count">{{ auth()->user()->notifications()->whereNull('read_at')->count() }}</b>@endif</a>
                        <details class="language-switcher"><summary aria-label="{{ __('messages.language') }}"><i class="fa-solid fa-globe"></i></summary><div class="language-menu"><a href="{{ route('language.switch', 'fr') }}">{{ __('messages.french') }}</a><a href="{{ route('language.switch', 'en') }}">{{ __('messages.english') }}</a></div></details>
                        <div class="dash-user">
                            @if (auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="Photo de profil de {{ auth()->user()->name }}">
                            @else
                                <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                            <div>
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>{{ auth()->user()->email }}</small>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="dash-user login-cta">Se connecter</a>
                    @endauth
                </div>
            </header>

            <div class="dash-content">
                @yield('dashboard-content')
            </div>
        </main>
    </div>
    <script>
        function goBackToPreviousPage() {
            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            window.location.href = @js(route('dashboard'));
        }

        const savedTheme = localStorage.getItem('atlost-theme');
        if (savedTheme) document.body.classList.add(`theme-${savedTheme}`);
        // Les actions recherche/signalement restent toujours dans l'espace connecté.
        document.querySelectorAll('.dash-nav a[href^="#"]').forEach((link) => {
            link.addEventListener('click', () => {
                document.querySelectorAll('.dash-nav a').forEach((item) => item.classList.remove('active'));
                link.classList.add('active');
            });
        });
        document.querySelectorAll('a[href]:not([href^="#"]):not([target])').forEach((link) => {
            link.addEventListener('click', () => document.body.classList.add('page-leaving'));
        });
    </script>
</body>
</html>
