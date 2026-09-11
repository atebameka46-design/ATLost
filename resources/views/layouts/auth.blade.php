<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Authentification' }} — ATLost</title>
    <meta name="description" content="Accédez à votre espace ATLost pour retrouver ou signaler un document.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="auth-page h-[100dvh] overflow-hidden bg-page-bg text-p-950 antialiased font-sans">
    <div class="auth-decor auth-decor-one"></div>
    <div class="auth-decor auth-decor-two"></div>
    <div class="auth-decor auth-decor-three"></div>

    <main class="flex h-[100dvh] items-center justify-center px-3 py-3 sm:px-6 sm:py-5">
        <div class="auth-shell h-full max-h-[720px] w-full max-w-[1120px] overflow-hidden rounded-[2rem] bg-white shadow-2xl shadow-p-950/10 lg:grid lg:grid-cols-[0.92fr_1.08fr]">
            <section class="flex h-full min-h-0 flex-col overflow-hidden px-6 py-6 sm:px-12 sm:py-8 lg:px-16">
                <div class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold tracking-tight text-p-950">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo ATLost" class="brand-logo brand-logo--sm">
                        <span>ATLost<span class="text-accent">.cm</span></span>
                    </a>
                    <span class="rounded-full bg-p-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-p-700">Cameroun</span>
                </div>

                <div class="flex min-h-0 flex-1 items-center py-6 sm:py-8">
                    <div class="auth-form-content min-w-0 w-full max-w-[390px]">
                        @yield('content')
                    </div>
                </div>

                <p class="text-[11px] text-n-400">© {{ date('Y') }} ATLost · Vos documents, en sécurité.</p>
            </section>

            <section class="auth-visual relative hidden h-full min-h-0 overflow-hidden lg:block">
                <img src="/images/hero_person.png" alt="Une personne retrouve son document avec ATLost" class="absolute inset-0 h-full w-full object-cover object-center">
                <div class="absolute inset-0 bg-gradient-to-br from-p-950/30 via-transparent to-accent/35"></div>
                <div class="absolute right-8 top-8 rounded-2xl border border-white/50 bg-white/75 p-4 shadow-xl backdrop-blur-xl">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-n-600">ATLost / confiance</p>
                    <p class="mt-1 text-2xl font-extrabold text-p-950">98%</p>
                    <p class="max-w-[130px] text-xs leading-snug text-n-700">des documents sont restitués sous 48h.</p>
                </div>
                <div class="absolute bottom-9 left-8 max-w-[310px] rounded-2xl border border-white/40 bg-p-950/75 p-5 text-white shadow-2xl backdrop-blur-xl">
                    <p class="text-lg font-bold leading-tight">Un document perdu n'est pas forcément perdu pour toujours.</p>
                    <p class="mt-2 text-xs leading-relaxed text-white/75">Retrouvez votre communauté et donnez une seconde chance aux documents égarés.</p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
