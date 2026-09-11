<!doctype html>
<html lang="fr">
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
            <a href="{{ url('/') }}" class="dash-brand">
                <img src="{{ asset('images/logo.png') }}" alt="ATLost">
                <span>ATLost</span>
            </a>

            <p class="dash-label">MENU</p>
            <nav class="dash-nav" aria-label="Navigation principale">
                <a class="{{ request()->routeIs('dashboard', 'citizen.dashboard', 'admin.dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i>Dashboard</a>
                @if (auth()->user()->isAdmin())
                    <a class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}"><i class="fa-solid fa-flag" aria-hidden="true"></i>Signalements <b>12+</b></a>
                    <a class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}"><i class="fa-solid fa-chart-column" aria-hidden="true"></i>Analytics</a>
                    <a class="{{ request()->routeIs('admin.team') ? 'active' : '' }}" href="{{ route('admin.team') }}"><i class="fa-solid fa-users" aria-hidden="true"></i>Équipe</a>
                @else
                    <a class="{{ request()->routeIs('citizen.documents') ? 'active' : '' }}" href="{{ route('citizen.documents') }}"><i class="fa-solid fa-folder-open" aria-hidden="true"></i>Mes documents</a>
                    <a class="{{ request()->routeIs('citizen.search') ? 'active' : '' }}" href="{{ route('citizen.search') }}"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>Rechercher</a>
                    <a class="{{ request()->routeIs('citizen.activity') ? 'active' : '' }}" href="{{ route('citizen.activity') }}"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>Activité</a>
                @endif
            </nav>

            <p class="dash-label general">GENERAL</p>
            <nav class="dash-nav" aria-label="Navigation secondaire">
                <a class="{{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}"><i class="fa-solid fa-user" aria-hidden="true"></i>Profil</a>
            </nav>

            <div class="dash-download">
                <small><i class="fa-solid fa-circle" aria-hidden="true"></i></small>
                <strong>Download our<br>Mobile App</strong>
                <em>Get tasks done on the go</em>
                <a href="#"><i class="fa-solid fa-download"></i> Download</a>
            </div>
        </aside>

        <main class="dash-main">
            <header class="dash-top">
                <div class="dash-search" role="search">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <span>Search task</span>
                    <kbd>⌘ F</kbd>
                </div>
                <div class="dash-tools">
                    <button type="button" aria-label="Messages"><i class="fa-solid fa-envelope"></i></button>
                    <button type="button" aria-label="Notifications"><i class="fa-solid fa-bell"></i></button>
                    <div class="dash-user">
                        <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <div>
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>{{ auth()->user()->email }}</small>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dash-content">
                @yield('dashboard-content')
            </div>
        </main>
    </div>
    <script>
        const savedTheme = localStorage.getItem('atlost-theme');
        if (savedTheme) document.body.classList.add(`theme-${savedTheme}`);
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
