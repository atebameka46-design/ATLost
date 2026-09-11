<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ATLost — Retrouvez et signalez vos documents égarés</title>
    <meta name="description" content="Plateforme nationale de recherche et de restitution de documents officiels égarés (CNI, Permis, Passeports). Simple, rapide et 100% sécurisé.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Ajustements propres à la landing page : lisibilité renforcée sans
           modifier les styles globaux de l'application. */
        .landing-page .text-\[9px\] {
            font-size: 0.6875rem;
            line-height: 1.35;
        }

        .landing-page .text-\[10px\] {
            font-size: 0.75rem;
            line-height: 1.4;
        }

        .landing-page .text-\[11px\] {
            font-size: 0.8125rem;
            line-height: 1.45;
        }

        .landing-page input,
        .landing-page select,
        .landing-page textarea {
            font-size: 0.875rem;
            line-height: 1.4;
        }

        /* Les sections après le hero gagnent légèrement en confort de lecture,
           sans toucher à la taille déjà équilibrée du hero et de la navbar. */
        .landing-content section:not(.landing-hero) .text-xs {
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .landing-content section:not(.landing-hero) .text-sm {
            font-size: 1rem;
            line-height: 1.55;
        }

        .landing-content section:not(.landing-hero) .text-\[9px\] {
            font-size: 0.75rem;
            line-height: 1.4;
        }

        .landing-content section:not(.landing-hero) .text-\[10px\] {
            font-size: 0.8125rem;
            line-height: 1.45;
        }

        .landing-content section:not(.landing-hero) .text-\[11px\] {
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* Contenu interne des sections : cartes, descriptions et FAQ */
        .landing-content section:not(.landing-hero) p {
            font-size: 0.9375rem !important;
            line-height: 1.6;
        }

        .landing-content section:not(.landing-hero) h3,
        .landing-content section:not(.landing-hero) h4 {
            font-size: 1rem;
            line-height: 1.35;
        }

        .landing-content section:not(.landing-hero) label,
        .landing-content section:not(.landing-hero) input,
        .landing-content section:not(.landing-hero) select,
        .landing-content section:not(.landing-hero) textarea,
        .landing-content section:not(.landing-hero) button {
            font-size: 0.875rem;
            line-height: 1.45;
        }

        /* Titres et introductions des sections principales */
        .landing-content section:not(.landing-hero) h2 {
            font-size: 2rem;
            line-height: 1.1;
        }

        .landing-content section:not(.landing-hero) h2 + p,
        .landing-content section:not(.landing-hero) h2 + .w-14 + p {
            font-size: 1rem;
            line-height: 1.65;
        }

        @media (min-width: 640px) {
            .landing-content section:not(.landing-hero) h2 {
                font-size: 2.5rem;
            }
        }

        @media (min-width: 640px) {
            .landing-page .text-\[9px\] {
                font-size: 0.75rem;
            }

            .landing-page .text-\[10px\] {
                font-size: 0.8125rem;
            }
        }

        /* Motif décoratif repris de la carte profil : anneaux fins et halos
           orange, placés en arrière-plan de quelques sections. */
        .landing-content > section:not(.landing-hero) {
            position: relative;
            isolation: isolate;
        }

        .landing-content > section:not(.landing-hero)::before,
        .landing-content > section:not(.landing-hero)::after {
            content: '';
            position: absolute;
            pointer-events: none;
            z-index: -1;
            border: 1px solid rgba(247, 140, 31, .16);
            border-radius: 9999px;
        }

        .landing-content > section:not(.landing-hero)::before {
            width: 430px;
            height: 430px;
            right: -190px;
            top: 4%;
            box-shadow: 0 0 0 18px rgba(247, 140, 31, .045),
                        0 0 0 42px rgba(247, 140, 31, .032),
                        0 0 0 78px rgba(247, 140, 31, .018);
        }

        .landing-content > section:nth-of-type(2n)::after {
            width: 280px;
            height: 280px;
            left: -135px;
            bottom: 8%;
            border-color: rgba(1, 22, 39, .11);
            box-shadow: 0 0 0 16px rgba(1, 22, 39, .025),
                        0 0 0 38px rgba(1, 22, 39, .021),
                        0 0 0 68px rgba(1, 22, 39, .014);
        }

        .landing-content > section:nth-of-type(3n)::before {
            right: auto;
            left: 2%;
            top: 2%;
            opacity: .7;
        }

        .landing-content > section:nth-of-type(3n)::after {
            right: 3%;
            left: auto;
            bottom: 9%;
        }

        .landing-content > section:nth-of-type(4n) {
            background: linear-gradient(180deg, rgba(255,255,255,.18), transparent 42%);
            border-radius: 2rem;
        }

        .landing-content .restitution-title {
            font-size: clamp(3.2rem, 5vw, 5rem) !important;
            line-height: .98 !important;
            letter-spacing: -.055em;
        }

        .landing-content .security-title {
            font-size: clamp(3.2rem, 5vw, 5rem) !important;
            line-height: .98 !important;
            letter-spacing: -.055em;
        }

        .landing-content > section:not(.landing-hero) h2 {
            font-size: clamp(3rem, 4.8vw, 4.75rem) !important;
            line-height: .98 !important;
            letter-spacing: -.055em;
        }

        .landing-nav nav a {
            position: relative;
            transition: color .2s ease;
        }

        .landing-nav nav a.active {
            color: var(--accent);
        }

        .landing-nav nav a.active::after {
            content: '';
            position: absolute;
            right: 0;
            bottom: -9px;
            left: 0;
            height: 2px;
            border-radius: 999px;
            background: var(--accent);
        }

        .landing-hero { position: relative; }
        .landing-hero { min-height: calc(100vh - 57px) !important; height: calc(100vh - 57px); }
        .hero-shell {
            min-height: 0;
            background: linear-gradient(135deg, #d7e2e8 0%, #e7ecef 58%, #f7f9fa 100%);
        }
        .hero-shell::before {
            content: '';
            position: absolute;
            width: 520px;
            height: 520px;
            right: -190px;
            top: -220px;
            border: 1px solid rgba(247, 140, 31, .17);
            border-radius: 50%;
            box-shadow: 0 0 0 24px rgba(247, 140, 31, .045), 0 0 0 54px rgba(247, 140, 31, .025);
            pointer-events: none;
        }
        .hero-copy { position: relative; z-index: 2; }
        .hero-copy h1 { font-size: clamp(4rem, 7vw, 6.8rem); line-height: .84; letter-spacing: -.065em; }
        .hero-copy > p { max-width: 600px; font-size: clamp(.95rem, 1.3vw, 1.1rem); }
        .hero-actions { flex-wrap: wrap; }
        .hero-actions > a:first-child { min-height: 48px; display: inline-flex; align-items: center; }
        .hero-visual { z-index: 2; }
        .hero-visual > div { min-height: clamp(350px, 55vh, 490px); border: 1px solid rgba(255,255,255,.55); }
        .hero-visual > div::after { content:''; position:absolute; inset:0; border-radius:2rem; background:linear-gradient(180deg,rgba(1,22,39,.04),rgba(1,22,39,.2)); pointer-events:none; }
        .hero-visual img { z-index:0; }
        .hero-visual .glass-pill, .hero-visual .glass-card, .hero-visual button { z-index:3; }
        .hero-visual button { width:64px; height:64px; border:4px solid rgba(255,255,255,.72); }
        .hero-visual button svg { width:22px; height:22px; }
        @media (max-width: 1023px) {
            .landing-hero { height:auto; min-height:calc(100vh - 57px) !important; }
            .hero-shell { min-height:0; }
            .hero-copy h1 { font-size:clamp(4rem, 12vw, 6rem); }
        }
        @media (max-width: 640px) {
            .hero-shell { border-radius:1.5rem; padding:1.25rem; }
            .hero-copy h1 { font-size:clamp(3.7rem, 19vw, 5.2rem); }
            .hero-actions { align-items:stretch; flex-direction:column; gap:.8rem; }
            .hero-actions > a { justify-content:center; width:100%; }
            .hero-visual > div { min-height:390px; border-radius:1.5rem; }
            .hero-visual > div::after { border-radius:1.5rem; }
            .hero-visual .absolute.-top-3 { right:-.35rem; }
            .hero-visual .absolute.-bottom-4 { right:-.35rem; }
            .hero-visual .absolute.top-1\/3 { left:-.4rem; }
        }

        @media (max-width: 640px) {
            .landing-content > section:not(.landing-hero)::before {
                width: 300px;
                height: 300px;
                right: -145px;
            }

            .landing-content > section:not(.landing-hero)::after {
                width: 200px;
                height: 200px;
                left: -105px;
            }
        }
    </style>
</head>
<body class="landing-page bg-page-bg text-n-900 antialiased font-sans min-h-screen selection:bg-accent selection:text-white">

    <!-- STICKY TOP NAVBAR (Fond teinté vers le noir / Dark Navy #011627 avec ligne de progression) -->
    <header class="landing-nav sticky top-0 z-50 bg-white/90 text-p-950 backdrop-blur-xl border-b border-n-200/70 shadow-sm transition-all">
        <div class="max-w-[1320px] mx-auto px-4 sm:px-6 py-2 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="#" class="flex items-center gap-2.5 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo ATLost" class="brand-logo brand-logo--lg shadow-md ring-2 ring-white/70">
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="font-bold text-base tracking-tight text-p-950">
                        ATLost<span class="text-accent text-xs font-extrabold"></span>
                    
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden lg:flex items-center gap-6 text-sm font-bold text-n-700">
                <a href="#comment-ca-marche" class="hover:text-accent transition-colors">Comment ça marche</a>
                <span class="text-n-300">•</span>
                <a href="#rechercher" class="hover:text-accent transition-colors">Rechercher</a>
                <span class="text-n-300">•</span>
                <a href="#securite" class="hover:text-accent transition-colors">Sécurité</a>
                <span class="text-n-300">•</span>
                <a href="#contact" class="hover:text-accent transition-colors">Contact</a>
                <span class="text-n-300">•</span>
                <a href="#faq" class="hover:text-accent transition-colors">FAQ</a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:inline-flex text-sm font-bold text-n-600 hover:text-accent transition-colors">
                    Se connecter
                </a>
                <a href="{{ route('register') }}" class="text-sm font-bold text-accent border border-accent/80 hover:bg-accent hover:text-p-950 px-4 py-2 rounded-full transition-all duration-300 shadow-sm">
                    Signalement Express <span class="text-accent-light">—</span> Gratuit
                </a>
            </div>
        </div>

        <!-- Symmetrical Center-Outward Scroll Progress Line -->
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] bg-accent transition-all duration-75 rounded-full shadow-sm" id="scroll-progress" style="width: 0%;"></div>
    </header>

    <!-- MAIN CONTAINER -->
    <div class="landing-content max-w-[1320px] mx-auto px-3 sm:px-5">

        <!-- 1. HERO SECTION (100vh / Full Viewport) -->
        <section class="landing-hero min-h-[calc(100vh-60px)] lg:h-[calc(100vh-60px)] lg:min-h-0 flex flex-col justify-between py-5 sm:py-7 overflow-hidden">
            
            <!-- Main Hero Box Card -->
            <div class="hero-shell bg-[#d7e2e8] rounded-[2rem] p-5 sm:p-9 lg:p-12 border border-white/70 shadow-lg relative overflow-hidden flex-1 flex flex-col justify-center">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center relative z-10">
                    
                    <!-- Left Hero Column -->
                    <div class="hero-copy lg:col-span-7 space-y-5 pr-0 lg:pr-8">
                        
                        <!-- Badge Top Left -->
                        <div class="flex items-center gap-2.5">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo ATLost" class="brand-logo brand-logo--sm">
                            <div class="text-[15px] sm:text-base text-n-800">
                                <span class="font-bold text-p-950">50K+ Utilisateurs</span>
                                <span class="mx-1.5 text-n-400">/</span>
                                <a href="#securite" class="font-bold text-p-950 underline hover:text-accent">Voir nos Histoires de Succès</a>
                            </div>
                        </div>

                        <!-- Title Grow+ style -->
                        <div>
                            <h1 class="font-serif text-[clamp(3.5rem,6vw,5.75rem)] text-p-950 tracking-tight font-normal leading-[0.9]">
                                Retrouver<sup class="text-p-950 font-sans font-light text-[clamp(2.5rem,4vw,4.5rem)]">+</sup>
                            </h1>
                        </div>

                        <!-- Line Divider 1 -->
                        <div class="w-full border-t border-n-400/40 my-3"></div>

                        <!-- Subtitle -->
                        <p class="text-n-800 text-sm sm:text-base lg:text-[17px] font-medium leading-relaxed max-w-xl">
                            Retrouvez vos cartes d'identité, permis et passeports égarés — Ou signalez un document trouvé <span class="font-bold text-p-950">Jusqu'à 50× plus vite</span>.
                        </p>

                        <!-- Social Proof -->
                        <div class="flex items-center gap-3 py-1">
                            <img class="w-8 h-8 rounded-full ring-2 ring-white object-cover shadow-sm" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80" alt="Utilisatrice">
                            <div class="text-sm text-n-800 font-medium">
                                <p class="font-semibold text-p-950">CNI restituée hier à Douala <span class="text-n-400 mx-1">/</span> <span class="text-p-950 font-bold">★ 4.9</span></p>
                                <p class="text-xs text-n-600">100% Satisfait & Anonymisé</p>
                            </div>
                        </div>

                        <!-- Line Divider 2 -->
                        <div class="w-full border-t border-n-400/40 my-3"></div>

                        <!-- Action Buttons -->
                        <div class="hero-actions flex items-center gap-5 pt-1">
                            <a href="{{ route('register') }}" class="bg-p-950 hover:bg-p-800 text-white font-bold px-6 py-3 rounded-full transition-all duration-300 shadow-md hover:-translate-y-0.5 text-sm">
                                Signaler — C'est gratuit
                            </a>
                            <a href="#comment-ca-marche" class="text-p-950 hover:text-accent font-bold text-sm inline-flex items-center gap-1 transition-colors border border-p-950/20 hover:border-accent px-5 py-3 rounded-full shadow-sm">
                                Rechercher un document ↗
                            </a>
                        </div>
                    </div>

                    <!-- Right Orange Card -->
                    <div class="hero-visual lg:col-span-5 relative mt-4 lg:mt-0">
                        
                        <div class="bg-accent rounded-[2rem] p-4 shadow-xl relative min-h-[410px] sm:min-h-[460px] flex flex-col justify-between overflow-visible group">
                            
                            <img src="/images/hero_person.png" alt="Retrouver document égaré ATLost" class="absolute inset-0 w-full h-full object-cover object-center rounded-[2rem] transition-transform duration-700 group-hover:scale-105">

                            <!-- Top Right Glass Box -->
                            <div class="absolute -top-3 -right-3 z-30">
                                <div class="glass-pill rounded-2xl p-3.5 text-p-950 max-w-[160px] shadow-2xl border border-white/90 animate-float">
                                    <span class="text-[10px] font-bold tracking-wider text-n-600 uppercase block">— JUSQU'À</span>
                                    <div class="text-2xl font-extrabold text-p-950 tracking-tight my-0.5">
                                        98%
                                    </div>
                                    <p class="text-xs font-medium text-n-700 leading-tight">
                                        Taux de restitution sous 48h
                                    </p>
                                </div>
                            </div>

                            <!-- Speech Bubbles -->
                            <div class="absolute top-1/3 -left-6 z-30 space-y-2 max-w-[220px]">
                                <div class="glass-pill rounded-full px-3.5 py-1.5 text-xs font-bold text-p-950 flex items-center gap-1.5 shadow-xl animate-float" style="animation-delay: 0.3s;">
                                    <span class="w-4 h-4 rounded-full bg-accent text-white flex items-center justify-center text-[9px] font-bold">✓</span>
                                    <span class="text-[13px]">Vous avez retrouvé ma CNI ?</span>
                                </div>
                                <div class="glass-pill rounded-full px-3.5 py-1.5 text-xs font-bold text-p-950 flex items-center gap-1.5 shadow-xl animate-float" style="animation-delay: 1s;">
                                    <span class="w-4 h-4 rounded-full bg-p-600 text-white flex items-center justify-center text-[9px] font-bold">✓</span>
                                    <span class="text-[13px]">Oui ! Restitution effectuée</span>
                                </div>
                            </div>

                            <!-- Play Button -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                                <button onclick="playDemo()" class="pointer-events-auto w-14 h-14 rounded-full bg-white text-p-950 flex items-center justify-center shadow-2xl hover:scale-110 hover:bg-accent hover:text-white transition-all duration-300">
                                    <svg class="w-5 h-5 fill-current translate-x-0.5" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Bottom Right Card Overlay -->
                            <div class="absolute -bottom-4 -right-4 z-30">
                                <div class="glass-card rounded-2xl p-3 flex items-center gap-2.5 shadow-2xl border border-white/90 backdrop-blur-xl animate-float-reverse hover:scale-105 transition-transform duration-300 max-w-[230px]">
                                    <img src="/images/id_card.png" alt="Aperçu CNI" class="w-14 h-12 rounded-xl object-cover shadow-sm border border-white/60">
                                    <div class="space-y-0.5">
                                        <h4 class="font-bold text-p-950 text-xs leading-tight">Carte Nationale CNI</h4>
                                        <p class="text-[11px] font-bold text-p-950">Vérifiée & Retrouvée</p>
                                        <div class="flex items-center gap-1 text-[10px] font-bold text-n-700">
                                            <span class="text-warning">★ 4.9</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <!-- BRAND LOGOS BANNER -->
            <div class="py-3 px-2 mt-4 border-y border-n-300/50 shrink-0">
                <div class="flex flex-wrap items-center justify-between gap-4 sm:gap-8 opacity-75 max-w-4xl mx-auto">
                    <span class="font-black text-xs sm:text-sm tracking-widest text-n-700 hover:text-p-600 transition-colors uppercase">GENDARMERIE</span>
                    <span class="font-extrabold text-xs sm:text-sm tracking-wider text-n-700 hover:text-p-600 transition-colors uppercase">EXPRESS<span class="text-accent">RELAIS</span></span>
                    <span class="font-black text-xs sm:text-sm tracking-widest text-n-700 hover:text-p-600 transition-colors uppercase">LA POSTE</span>
                    <span class="font-bold text-xs sm:text-sm tracking-wider text-n-700 hover:text-p-600 transition-colors uppercase">POLICE<span class="text-p-500">SECURE</span></span>
                    <span class="font-black text-xs sm:text-sm tracking-wider text-n-700 hover:text-p-600 transition-colors uppercase">CNI<span class="text-accent">DIRECT</span></span>
                </div>
            </div>

        </section>

        <!-- SECTION DIVIDER -->
        <div id="comment-ca-marche" class="my-2 flex items-center justify-center gap-4 max-w-4xl mx-auto" style="scroll-margin-top: 64px;">
            <div class="h-px bg-n-300/60 flex-1"></div>
            <span class="text-[10px] font-bold text-n-500 uppercase tracking-widest">✦ ATLost Restitution ✦</span>
            <div class="h-px bg-n-300/60 flex-1"></div>
        </div>

        <!-- 2. SECTION COMMENT ÇA MARCHE -->
        <section class="min-h-screen flex flex-col justify-center py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                
                <!-- Left Side -->
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-accent font-extrabold text-base sm:text-lg uppercase tracking-widest block">
                        Procédure Simplifiée
                    </span>
                    <h2 class="restitution-title font-serif text-5xl sm:text-[3.6rem] lg:text-[4.25rem] font-normal text-p-950 tracking-tight leading-[.98]">
                        Comment fonctionne la restitution ?
                    </h2>
                    <div class="w-14 h-1 bg-accent rounded-full"></div>
                    <p class="text-n-700 text-lg sm:text-xl leading-relaxed">
                        Notre protocole sécurisé permet de mettre en relation l'inventeur d'un document égaré et son titulaire légitime sans exposer aucune donnée personnelle sensible.
                    </p>

                    <div class="pt-2">
                        <a href="{{ route('register') }}" class="bg-p-950 hover:bg-p-800 text-white font-bold px-6 py-2.5 rounded-full transition-all duration-300 shadow-md text-xs inline-flex items-center gap-2">
                            <span>Lancer un signalement</span>
                            <span class="text-accent">→</span>
                        </a>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="lg:col-span-7 space-y-3.5 max-w-lg lg:ml-auto w-full">
                    <div class="p-4 rounded-xl bg-white/70 border border-n-200/80 shadow-xs hover:border-p-300 transition-all duration-300">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-full bg-p-950 text-accent font-bold flex items-center justify-center text-xs">01</span>
                                <h3 class="text-sm font-bold text-p-950">1. Déclaration ou Recherche</h3>
                            </div>
                            <span class="text-[9px] font-extrabold text-p-700 bg-p-50 px-2 py-0.5 rounded-full border border-p-200">⏱ 60 sec</span>
                        </div>
                        <p class="text-n-600 text-xs leading-relaxed pl-9">
                            Renseignez le nom figurant sur la pièce. Les photos et identifiants uniques restent 100% cryptés.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-white/70 border border-n-200/80 shadow-xs hover:border-p-300 transition-all duration-300">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-full bg-p-950 text-accent font-bold flex items-center justify-center text-xs">02</span>
                                <h3 class="text-sm font-bold text-p-950">2. Vérification & Notification</h3>
                            </div>
                            <span class="text-[9px] font-extrabold text-accent-dark bg-accent-light px-2 py-0.5 rounded-full">🔒 Anonyme</span>
                        </div>
                        <p class="text-n-600 text-xs leading-relaxed pl-9">
                            Notre algorithme croise les dépôts et transmet une alerte sécurisée exigeant une preuve légale.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-white/70 border border-n-200/80 shadow-xs hover:border-p-300 transition-all duration-300">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-full bg-p-950 text-accent font-bold flex items-center justify-center text-xs">03</span>
                                <h3 class="text-sm font-bold text-p-950">3. Restitution au Point Relais</h3>
                            </div>
                            <span class="text-[9px] font-extrabold text-success bg-success-light px-2 py-0.5 rounded-full">🏢 Certifié</span>
                        </div>
                        <p class="text-n-600 text-xs leading-relaxed pl-9">
                            Récupérez votre pièce contre présentation du code unique dans un point relais agréé.
                        </p>
                    </div>
                </div>

            </div>
        </section>

        <!-- SECTION DIVIDER -->
        <div id="rechercher" class="my-2 flex items-center justify-center gap-4 max-w-4xl mx-auto" style="scroll-margin-top: 64px;">
            <div class="h-px bg-n-300/60 flex-1"></div>
            <span class="text-[10px] font-bold text-n-500 uppercase tracking-widest">✦ Base Nationale ✦</span>
            <div class="h-px bg-n-300/60 flex-1"></div>
        </div>

        <!-- 3. SECTION RECHERCHER & DÉCLARER -->
        <section class="min-h-screen flex flex-col justify-center py-6">
            <div class="space-y-6">
                
                <div class="max-w-2xl mx-auto text-center space-y-2">
                    <span class="text-accent font-extrabold text-xs uppercase tracking-widest">
                        Base Nationale en Direct
                    </span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-normal text-p-950 tracking-tight leading-[1.1]">
                        Rechercher ou déclarer un document
                    </h2>
                    <p class="text-n-600 text-sm sm:text-base leading-relaxed">
                        Consultez immédiatement la base des pièces retrouvées sur l'ensemble du territoire.
                    </p>
                </div>

                <!-- Tabs Switcher -->
                <div class="flex justify-center mb-4">
                    <div class="bg-n-200/70 p-1 rounded-full inline-flex gap-1 border border-n-300/50">
                        <button id="tab-search-btn" onclick="switchTab('search')" class="px-5 py-2 rounded-full text-xs font-bold transition-all duration-300 bg-p-950 text-white shadow-xs">
                            🔍 Rechercher un document
                        </button>
                        <button id="tab-report-btn" onclick="switchTab('report')" class="px-5 py-2 rounded-full text-xs font-bold transition-all duration-300 text-n-800 hover:text-p-950">
                            📢 Déclarer un document trouvé
                        </button>
                    </div>
                </div>

                <!-- Tab Search Form -->
                <div id="tab-search" class="max-w-3xl mx-auto space-y-4">
                    <form onsubmit="handleSearch(event)" class="grid grid-cols-1 sm:grid-cols-12 gap-3 bg-white p-4 rounded-2xl border border-n-300/60 shadow-xs">
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Nom sur la pièce</label>
                            <input type="text" id="search-name" placeholder="Ex: Kamga, Mbida, Ngo..." required class="w-full bg-n-50 border border-n-300 focus:border-p-600 rounded-lg px-3 py-2 text-xs font-medium outline-none transition">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Type de document</label>
                            <select id="search-type" class="w-full bg-n-50 border border-n-300 focus:border-p-600 rounded-lg px-3 py-2 text-xs font-medium outline-none transition">
                                <option value="all">Tous les types</option>
                                <option value="CNI">Carte Nationale (CNI)</option>
                                <option value="Permis">Permis de Conduire</option>
                                <option value="Passeport">Passeport</option>
                            </select>
                        </div>
                        <div class="sm:col-span-3 flex items-end">
                            <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-p-950 font-extrabold px-4 py-2 text-xs rounded-lg transition-all duration-300 shadow-xs flex items-center justify-center gap-1">
                                <span>Rechercher</span>
                                <span>➔</span>
                            </button>
                        </div>
                    </form>

                    <!-- Results list -->
                    <div id="search-results" class="pt-2 space-y-3">
                        <div class="flex items-center justify-between text-[11px] font-bold text-n-600 uppercase tracking-wider px-1">
                            <span>Documents récemment enregistrés</span>
                            <span id="results-count">3 résultats récents</span>
                        </div>

                        <div id="cards-container" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="doc-card bg-white p-4 rounded-xl border border-n-200/80 hover:border-p-300 transition-all duration-300 shadow-xs" data-type="CNI" data-name="kamga">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="bg-p-50 text-p-800 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border border-p-200">CNI</span>
                                    <span class="text-[10px] font-bold text-success flex items-center gap-1">● Disponible</span>
                                </div>
                                <h3 class="font-bold text-p-950 text-sm mb-0.5">KAMGA T. Jean-Paul</h3>
                                <p class="text-[11px] text-n-500 mb-2.5">Lieu: <span class="font-semibold text-n-800">Yaoundé (Bastos)</span></p>
                                <button onclick="claimDoc('CNI - KAMGA T. Jean-Paul')" class="w-full bg-p-950 hover:bg-p-800 text-white font-bold py-2 rounded-lg text-xs transition-colors shadow-xs">
                                    Réclamer ce document
                                </button>
                            </div>

                            <div class="doc-card bg-white p-4 rounded-xl border border-n-200/80 hover:border-p-300 transition-all duration-300 shadow-xs" data-type="Permis" data-name="mbida">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="bg-accent-light text-accent-dark text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">Permis</span>
                                    <span class="text-[10px] font-bold text-success flex items-center gap-1">● Disponible</span>
                                </div>
                                <h3 class="font-bold text-p-950 text-sm mb-0.5">MBIDA A. Carine</h3>
                                <p class="text-[11px] text-n-500 mb-2.5">Lieu: <span class="font-semibold text-n-800">Douala (Akwa)</span></p>
                                <button onclick="claimDoc('Permis - MBIDA A. Carine')" class="w-full bg-p-950 hover:bg-p-800 text-white font-bold py-2 rounded-lg text-xs transition-colors shadow-xs">
                                    Réclamer ce document
                                </button>
                            </div>

                            <div class="doc-card bg-white p-4 rounded-xl border border-n-200/80 hover:border-p-300 transition-all duration-300 shadow-xs" data-type="Passeport" data-name="ngo">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="bg-p-100 text-p-900 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">Passeport</span>
                                    <span class="text-[10px] font-bold text-success flex items-center gap-1">● Disponible</span>
                                </div>
                                <h3 class="font-bold text-p-950 text-sm mb-0.5">NGO B. Samuel</h3>
                                <p class="text-[11px] text-n-500 mb-2.5">Lieu: <span class="font-semibold text-n-800">Bafoussam (Centre)</span></p>
                                <button onclick="claimDoc('Passeport - NGO B. Samuel')" class="w-full bg-p-950 hover:bg-p-800 text-white font-bold py-2 rounded-lg text-xs transition-colors shadow-xs">
                                    Réclamer ce document
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Report Form -->
                <div id="tab-report" class="max-w-2xl mx-auto hidden">
                    <form onsubmit="handleReport(event)" class="bg-white p-5 rounded-2xl border border-n-300/60 shadow-xs space-y-3">
                        <div class="text-center mb-3">
                            <h3 class="text-base font-bold text-p-950">Signalement citoyen d'un document trouvé</h3>
                            <p class="text-[11px] text-n-600 mt-0.5">Vos coordonnées restent 100% confidentielles.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Nom sur la pièce</label>
                                <input type="text" required placeholder="Ex: ETO'O Francis" class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Type de document</label>
                                <select required class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600">
                                    <option value="">Sélectionnez</option>
                                    <option value="CNI">Carte Nationale (CNI)</option>
                                    <option value="Permis">Permis de Conduire</option>
                                    <option value="Passeport">Passeport</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Lieu de la découverte</label>
                                <input type="text" required placeholder="Ex: Douala - Bonanjo" class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Votre Téléphone</label>
                                <input type="tel" required placeholder="Ex: +237 6xx xx xx xx" class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-p-950 hover:bg-p-800 text-white font-extrabold py-2.5 rounded-lg shadow-xs transition-all duration-300 text-xs">
                            Publier le signalement sécurisé →
                        </button>
                    </form>
                </div>

            </div>
        </section>

        <!-- SECTION DIVIDER -->
        <div id="securite" class="my-2 flex items-center justify-center gap-4 max-w-4xl mx-auto" style="scroll-margin-top: 64px;">
            <div class="h-px bg-n-300/60 flex-1"></div>
            <span class="text-[10px] font-bold text-n-500 uppercase tracking-widest">✦ Confidentialité ✦</span>
            <div class="h-px bg-n-300/60 flex-1"></div>
        </div>

        <!-- 4. SECTION SÉCURITÉ -->
        <section class="min-h-screen flex flex-col justify-center py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                
                <!-- Left Side: Serif Title & Description -->
                <div class="lg:order-2 lg:col-span-5 space-y-4">
                    <span class="text-accent font-extrabold text-base sm:text-lg uppercase tracking-widest block">
                        Engagements & Sécurité
                    </span>
                    <h2 class="security-title font-serif text-3xl sm:text-4xl font-normal text-p-950 tracking-tight leading-[1.05]">
                        Pourquoi la plateforme ATLost est-elle 100% sûre ?
                    </h2>
                    <div class="w-14 h-1 bg-accent rounded-full"></div>
                    <p class="text-n-700 text-lg sm:text-xl leading-relaxed">
                        Nous appliquons un protocole d'anonymisation zéro-risque pour prévenir la fraude et garantir que seul le titulaire légitime puisse reprendre possession de ses pièces.
                    </p>

                    <div class="pt-2">
                        <a href="#contact" class="bg-p-950 hover:bg-p-800 text-white font-bold px-6 py-2.5 rounded-full transition-all duration-300 shadow-md text-xs inline-flex items-center gap-2">
                            <span>Nos garanties de sécurité</span>
                            <span class="text-accent">→</span>
                        </a>
                    </div>
                </div>

                <!-- Right Side: Security Cards Stack -->
                <div class="lg:order-1 lg:col-span-7 space-y-3.5 max-w-lg lg:mr-auto w-full">
                    <div class="p-4 rounded-xl bg-white/70 border border-n-200/80 shadow-xs hover:border-p-300 transition-all duration-300">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="w-7 h-7 rounded-full bg-p-950 text-accent font-bold flex items-center justify-center text-xs shrink-0">🔒</span>
                            <h3 class="text-sm font-bold text-p-950">Chiffrement SSL 256-bit</h3>
                        </div>
                        <p class="text-n-600 text-xs leading-relaxed pl-10">
                            Toutes les données nominatives et photos d'actes sont chiffrées selon les normes de sécurité bancaire les plus réelles.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-white/70 border border-n-200/80 shadow-xs hover:border-p-300 transition-all duration-300">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="w-7 h-7 rounded-full bg-p-950 text-accent font-bold flex items-center justify-center text-xs shrink-0">🔑</span>
                            <h3 class="text-sm font-bold text-p-950">Vérification par Code QR Unique</h3>
                        </div>
                        <p class="text-n-600 text-xs leading-relaxed pl-10">
                            Chaque restitution génère un jeton QR sécurisé unique requis au guichet du point relais.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-white/70 border border-n-200/80 shadow-xs hover:border-p-300 transition-all duration-300">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="w-7 h-7 rounded-full bg-p-950 text-accent font-bold flex items-center justify-center text-xs shrink-0">🛡️</span>
                            <h3 class="text-sm font-bold text-p-950">Protection contre l'usurpation d'identité</h3>
                        </div>
                        <p class="text-n-600 text-xs leading-relaxed pl-10">
                            Aucune donnée bancaire ni mot de passe n'est exigé. Une preuve légale d'identité est vérifiée avant chaque remise.
                        </p>
</div>
                </div>

            </div>
        </section>

        <!-- SECTION DIVIDER -->
        <div id="contact" class="my-2 flex items-center justify-center gap-4 max-w-4xl mx-auto" style="scroll-margin-top: 64px;">
            <div class="h-px bg-n-300/60 flex-1"></div>
            <span class="text-[10px] font-bold text-n-500 uppercase tracking-widest">✦ Support ✦</span>
            <div class="h-px bg-n-300/60 flex-1"></div>
        </div>

        <!-- 5. SECTION CONTACT & SUPPORT -->
        <section class="min-h-screen flex flex-col justify-center py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                
                <!-- Left Side: Serif Title & Description -->
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-accent font-extrabold text-base sm:text-lg uppercase tracking-widest block">
                        Assistance Citoyenne
                    </span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-normal text-p-950 tracking-tight leading-[1.05]">
                        Une question ou une demande spéciale ?
                    </h2>
                    <div class="w-14 h-1 bg-accent rounded-full"></div>
                    <p class="text-n-700 text-lg sm:text-xl leading-relaxed">
                        Notre équipe de support citoyen vous accompagne 7j/7 pour la recherche, le signalement ou le déblocage d'un dossier de restitution.
                    </p>

                    <div class="space-y-2.5 pt-2 max-w-md">
                        <div class="flex items-center gap-3 p-3 bg-white/80 rounded-xl border border-n-200/80 shadow-xs">
                            <span class="w-7 h-7 rounded-lg bg-p-50 text-p-700 flex items-center justify-center font-bold text-xs shrink-0">✉️</span>
                            <div class="text-xs">
                                <span class="text-[10px] text-n-500 font-bold uppercase block">Email officiel</span>
                                <a href="mailto:contact@atlost.com" class="font-bold text-p-950 hover:text-accent">contact@atlost.com</a>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-white/80 rounded-xl border border-n-200/80 shadow-xs">
                            <span class="w-7 h-7 rounded-lg bg-p-50 text-p-700 flex items-center justify-center font-bold text-xs shrink-0">📞</span>
                            <div class="text-xs">
                                <span class="text-[10px] text-n-500 font-bold uppercase block">Permanence Téléphonique</span>
                                <span class="font-bold text-p-950">+237 600 00 00 00 / 222 00 00 00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Contact Form -->
                <div class="lg:col-span-7 max-w-lg lg:ml-auto w-full">
                    <form onsubmit="handleContactSubmit(event)" class="bg-white p-5 sm:p-6 rounded-2xl border border-n-300/70 shadow-xs space-y-3">
                        <div class="mb-2">
                            <h3 class="text-base font-bold text-p-950">Envoyez un message direct</h3>
                            <p class="text-[11px] text-n-600">Réponse garantie en moins de 15 minutes.</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Votre Nom</label>
                                <input type="text" required placeholder="Ex: Paul Mbarga" class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600 transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Votre Email</label>
                                <input type="email" required placeholder="Ex: paul@gmail.com" class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Sujet de la demande</label>
                            <select required class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600 transition">
                                <option value="Aide restitution">Aide pour la récupération d'un document</option>
                                <option value="Signalement bloqué">Signalement de fraude ou erreur</option>
                                <option value="Partenariat relais">Devenir Point Relais Partenaire</option>
                                <option value="Autre">Autre question</option>
                            </select>
                        </div>
                        <div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-n-700 uppercase mb-1">Votre Message</label>
                            <textarea required rows="3" placeholder="Expliquez brièvement votre situation..." class="w-full bg-n-50 border border-n-300 rounded-lg px-3 py-2 text-xs font-medium outline-none focus:border-p-600 transition"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-p-950 hover:bg-p-800 text-white font-extrabold py-2.5 rounded-lg transition-all duration-300 shadow-xs text-xs">
                            Envoyer le message au support →
                        </button>
                    </form>
                </div>

            </div>
        </section>

        <!-- SECTION DIVIDER ENTRE FAQ ET FOOTER -->
        <div id="faq" class="my-2 flex items-center justify-center gap-4 max-w-4xl mx-auto" style="scroll-margin-top: 64px;">
            <div class="h-px bg-n-300/60 flex-1"></div>
            <span class="text-[10px] font-bold text-n-500 uppercase tracking-widest">✦ Foire Aux Questions & Pied de Page ✦</span>
            <div class="h-px bg-n-300/60 flex-1"></div>
        </div>

        <!-- 6. SECTION FAQ ET FOOTER HAUTEMENT AMÉLIORÉ -->
        <section class="min-h-screen flex flex-col justify-between py-6 space-y-6">
            
            <!-- FAQ Content -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start my-auto">
                
                <!-- Left Side: Serif Heading & Description -->
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-accent font-extrabold text-base sm:text-lg uppercase tracking-widest block">
                        Aide & Précisions
                    </span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-normal text-p-950 tracking-tight leading-[1.05]">
                        Questions fréquentes
                    </h2>
                    <div class="w-14 h-1 bg-accent rounded-full"></div>
                    <p class="text-n-700 text-lg sm:text-xl leading-relaxed">
                        Des doutes sur la sécurité, les tarifs ou la remise d'un document ? Retrouvez ici toutes les réponses essentielles.
                    </p>

                    <div class="bg-white p-4 rounded-2xl border border-n-200 space-y-2 shadow-xs">
                        <h4 class="font-bold text-p-950 text-sm">Encore une interrogation ?</h4>
                        <p class="text-xs text-n-600">Notre équipe de support est disponible pour vous accompagner pas à pas.</p>
                        <a href="#contact" class="text-p-700 font-bold text-xs hover:text-accent inline-flex items-center gap-1 pt-1">
                            Contactez notre équipe support ➔
                        </a>
                    </div>
                </div>

                <!-- Right Side: Accordion List -->
                <div class="lg:col-span-7 space-y-2.5 max-w-lg lg:ml-auto w-full">
                    
                    <!-- Q1 -->
                    <div class="bg-white rounded-xl border border-n-200/80 p-3.5 shadow-xs hover:border-p-300 transition-all duration-300">
                        <button onclick="toggleFaq('faq-1')" class="w-full flex justify-between items-center text-left font-bold text-p-950 text-xs sm:text-sm gap-3">
                            <span>Le service ATLost est-il payant pour les citoyens ?</span>
                            <span id="faq-1-icon" class="w-5 h-5 rounded-full bg-n-100 text-p-950 flex items-center justify-center font-bold text-xs shrink-0">+</span>
                        </button>
                        <div id="faq-1" class="hidden mt-2 text-n-600 text-[11px] leading-relaxed border-t border-n-100 pt-2">
                            Non, le signalement et la recherche basique sont 100% gratuits pour tous les citoyens. Aucun frais n'est exigé pour déclarer ou chercher une pièce d'identité.
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="bg-white rounded-xl border border-n-200/80 p-3.5 shadow-xs hover:border-p-300 transition-all duration-300">
                        <button onclick="toggleFaq('faq-2')" class="w-full flex justify-between items-center text-left font-bold text-p-950 text-xs sm:text-sm gap-3">
                            <span>Comment prouver qu'un document m'appartient ?</span>
                            <span id="faq-2-icon" class="w-5 h-5 rounded-full bg-n-100 text-p-950 flex items-center justify-center font-bold text-xs shrink-0">+</span>
                        </button>
                        <div id="faq-2" class="hidden mt-2 text-n-600 text-[11px] leading-relaxed border-t border-n-100 pt-2">
                            Lors de la récupération au point relais certifié, vous devez présenter une preuve légale d'appartenance (déclaration officielle de perte, ancienne photocopie certifiée ou réponse à la question de sécurité unique).
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="bg-white rounded-xl border border-n-200/80 p-3.5 shadow-xs hover:border-p-300 transition-all duration-300">
                        <button onclick="toggleFaq('faq-3')" class="w-full flex justify-between items-center text-left font-bold text-p-950 text-xs sm:text-sm gap-3">
                            <span>Où dois-je déposer un document que j'ai trouvé ?</span>
                            <span id="faq-3-icon" class="w-5 h-5 rounded-full bg-n-100 text-p-950 flex items-center justify-center font-bold text-xs shrink-0">+</span>
                        </button>
                        <div id="faq-3" class="hidden mt-2 text-n-600 text-[11px] leading-relaxed border-t border-n-100 pt-2">
                            Vous pouvez le déposer directement dans l'un de nos Points Relais partenaires agréés (postes de gendarmerie/police, agences de la poste ou guichets partenaires certifiés ATLost).
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="bg-white rounded-xl border border-n-200/80 p-3.5 shadow-xs hover:border-p-300 transition-all duration-300">
                        <button onclick="toggleFaq('faq-4')" class="w-full flex justify-between items-center text-left font-bold text-p-950 text-xs sm:text-sm gap-3">
                            <span>Mes informations confidentielles sont-elles protégées ?</span>
                            <span id="faq-4-icon" class="w-5 h-5 rounded-full bg-n-100 text-p-950 flex items-center justify-center font-bold text-xs shrink-0">+</span>
                        </button>
                        <div id="faq-4" class="hidden mt-2 text-n-600 text-[11px] leading-relaxed border-t border-n-100 pt-2">
                            Absolument. Nous appliquons les normes strictes RGPD. Seul le nom figurant sur la pièce est recherchable publiques. Les photos et numéros d'acte sont masqués.
                        </div>
                    </div>

                </div>

            </div>

            <!-- FOOTER AMÉLIORÉ ET ÉLÉGANT AVEC ESPACEMENT GÉNÉREUX -->
            <footer class="bg-p-950 text-n-300 rounded-[2rem] p-6 sm:p-10 lg:p-12 mt-20 border border-p-900 shadow-2xl shrink-0 space-y-8 relative overflow-hidden">
                
                <!-- Ambient glow in footer background -->
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Top Row: Brand & Socials -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pb-8 border-b border-p-800/80">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-white text-p-950 flex items-center justify-center font-bold text-base shadow-sm">
                                ●●
                            </div>
                            <span class="font-bold text-2xl tracking-tight text-white">
                                ATLost<span class="text-accent">.cm</span>
                            </span>
                        </div>
                        <p class="text-xs text-n-400 max-w-md">
                            La plateforme citoyenne nationale pour la recherche et la restitution sécurisée de documents égarés.
                        </p>
                    </div>

                    <!-- Social Networks -->
                    <div class="flex items-center gap-2.5">
                        <a href="#" class="w-9 h-9 rounded-full bg-p-900 hover:bg-accent hover:text-p-950 text-n-200 flex items-center justify-center text-xs font-bold transition-all shadow-sm">FB</a>
                        <a href="#" class="w-9 h-9 rounded-full bg-p-900 hover:bg-accent hover:text-p-950 text-n-200 flex items-center justify-center text-xs font-bold transition-all shadow-sm">TW</a>
                        <a href="#" class="w-9 h-9 rounded-full bg-p-900 hover:bg-accent hover:text-p-950 text-n-200 flex items-center justify-center text-xs font-bold transition-all shadow-sm">LN</a>
                        <a href="#" class="w-9 h-9 rounded-full bg-p-900 hover:bg-accent hover:text-p-950 text-n-200 flex items-center justify-center text-xs font-bold transition-all shadow-sm">WA</a>
                    </div>
                </div>

                <!-- Main Navigation Grid Columns -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-8 pb-8 border-b border-p-800/80">
                    
                    <!-- Col 1: Navigation -->
                    <div class="md:col-span-3 space-y-3">
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest text-accent">Navigation</h4>
                        <ul class="space-y-2 text-xs">
                            <li><a href="#" class="hover:text-white transition-colors">Accueil</a></li>
                            <li><a href="#comment-ca-marche" class="hover:text-white transition-colors">Comment ça marche</a></li>
                            <li><a href="#rechercher" class="hover:text-white transition-colors">Rechercher une pièce</a></li>
                            <li><a href="#securite" class="hover:text-white transition-colors">Garanties & Sécurité</a></li>
                            <li><a href="#contact" class="hover:text-white transition-colors">Support Citoyen</a></li>
                        </ul>
                    </div>

                    <!-- Col 2: Categories -->
                    <div class="md:col-span-3 space-y-3">
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest text-accent">Pièces Gérées</h4>
                        <ul class="space-y-2 text-xs text-n-400">
                            <li><a href="#rechercher" class="hover:text-white transition-colors">Cartes Nationales d'Identité</a></li>
                            <li><a href="#rechercher" class="hover:text-white transition-colors">Permis de Conduire</a></li>
                            <li><a href="#rechercher" class="hover:text-white transition-colors">Passeports Biométriques</a></li>
                            <li><a href="#rechercher" class="hover:text-white transition-colors">Cartes d'Étudiants</a></li>
                            <li><a href="#rechercher" class="hover:text-white transition-colors">Actes de Naissance</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: Partners -->
                    <div class="md:col-span-3 space-y-3">
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest text-accent">Points Relais Agréés</h4>
                        <ul class="space-y-2 text-xs text-n-400">
                            <li><span class="text-n-300 font-semibold">Postes de Gendarmerie</span></li>
                            <li><span class="text-n-300 font-semibold">Commissariats de Police</span></li>
                            <li><span class="text-n-300 font-semibold">Guichets La Poste</span></li>
                            <li><span class="text-n-300 font-semibold">Agences Express Relais</span></li>
                        </ul>
                    </div>

                    <!-- Col 4: Newsletter Alert Subscription -->
                    <div class="md:col-span-3 space-y-3">
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest text-accent">Alertes Instantanées</h4>
                        <p class="text-xs text-n-400 leading-relaxed">
                            Soyez automatiquement notifié par e-mail si un document correspondant à votre nom est signalé.
                        </p>
                        <form onsubmit="handleAlert(event)" class="space-y-2">
                            <input type="email" required placeholder="Votre adresse e-mail..." class="w-full bg-p-900 border border-p-800 rounded-xl px-3 py-2 text-xs text-white placeholder-n-500 outline-none focus:border-accent">
                            <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-p-950 font-bold py-2 rounded-xl text-xs transition-colors shadow-sm">
                                Activer les alertes →
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Bottom Row: Copyright & Back to Top -->
                <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-n-500 gap-4 pt-2">
                    <p>© 2026 ATLost.cm — Tous droits réservés. Service citoyen sécurisé de restitution de documents.</p>
                    <div class="flex items-center gap-6">
                        <a href="#" class="hover:text-n-300 transition-colors">Mentions Légales</a>
                        <a href="#" class="hover:text-n-300 transition-colors">Confidentialité RGPD</a>
                        <button onclick="window.scrollTo({top:0, behavior:'smooth'})" class="text-accent font-bold hover:underline flex items-center gap-1">
                            Haut de page ↑
                        </button>
                    </div>
                </div>

            </footer>

        </section>

    </div>

    <!-- Notification Toast Modal -->
    <div id="toast" class="fixed bottom-5 right-5 bg-p-950 text-white px-5 py-3 rounded-xl shadow-2xl border border-p-600 flex items-center gap-2.5 translate-y-24 opacity-0 transition-all duration-500 z-50">
        <span class="text-accent text-lg font-bold">✓</span>
        <span id="toast-msg" class="text-xs font-semibold">Message de confirmation</span>
    </div>

    <!-- Client-side logic & Symmetrical Scroll Progress Line -->
    <script>
        // Symmetrical Center-Outward Scroll Progress Indicator
        window.addEventListener('scroll', () => {
            const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
            const progressLine = document.getElementById('scroll-progress');
            if (progressLine) {
                progressLine.style.width = scrolled + '%';
            }
        });

        function switchTab(tab) {
            const searchTab = document.getElementById('tab-search');
            const reportTab = document.getElementById('tab-report');
            const searchBtn = document.getElementById('tab-search-btn');
            const reportBtn = document.getElementById('tab-report-btn');

            if (tab === 'search') {
                searchTab.classList.remove('hidden');
                reportTab.classList.add('hidden');
                searchBtn.className = 'px-5 py-2 rounded-full text-xs font-bold transition-all duration-300 bg-p-950 text-white shadow-xs';
                reportBtn.className = 'px-5 py-2 rounded-full text-xs font-bold transition-all duration-300 text-n-800 hover:text-p-950';
            } else {
                reportTab.classList.remove('hidden');
                searchTab.classList.add('hidden');
                reportBtn.className = 'px-5 py-2 rounded-full text-xs font-bold transition-all duration-300 bg-p-950 text-white shadow-xs';
                searchBtn.className = 'px-5 py-2 rounded-full text-xs font-bold transition-all duration-300 text-n-800 hover:text-p-950';
            }
        }

        function handleSearch(e) {
            e.preventDefault();
            const nameInput = document.getElementById('search-name').value.toLowerCase().trim();
            const typeInput = document.getElementById('search-type').value;
            const cards = document.querySelectorAll('.doc-card');
            let count = 0;

            cards.forEach(card => {
                const cardName = card.getAttribute('data-name');
                const cardType = card.getAttribute('data-type');
                const nameMatch = !nameInput || cardName.includes(nameInput);
                const typeMatch = typeInput === 'all' || cardType === typeInput;

                if (nameMatch && typeMatch) {
                    card.style.display = 'block';
                    count++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('results-count').innerText = count + ' résultat(s) trouvé(s)';
            showToast('Recherche effectuée dans la base nationale.');
        }

        function handleReport(e) {
            e.preventDefault();
            showToast('Signalement enregistré avec succès ! Merci pour votre geste citoyen.');
            e.target.reset();
            switchTab('search');
        }

        function handleContactSubmit(e) {
            e.preventDefault();
            showToast('Message envoyé au support client ATLost. Réponse sous 15 minutes.');
            e.target.reset();
        }

        function claimDoc(docName) {
            showToast('Demande envoyée pour: ' + docName + '. Notre équipe vous contactera sous 30 minutes.');
        }

        function handleAlert(e) {
            e.preventDefault();
            showToast('Abonnement aux alertes confirmé !');
            e.target.reset();
        }

        function toggleFaq(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById(id + '-icon');
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.innerText = '−';
            } else {
                content.classList.add('hidden');
                icon.innerText = '+';
            }
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toast-msg');
            toastMsg.innerText = message;
            toast.classList.remove('translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 4000);
        }

        function playDemo() {
            showToast('▶ Lecture du témoignage audio : "Comment j\'ai retrouvé ma CNI en 24h"');
        }

        // État actif de la navigation principale selon la section visible.
        const landingNavLinks = [...document.querySelectorAll('.landing-nav nav a[href^="#"]')];
        const landingSections = landingNavLinks
            .map(link => document.querySelector(link.getAttribute('href')))
            .filter(Boolean);

        function updateLandingNav() {
            const marker = window.scrollY + 150;
            let current = landingSections[0];
            landingSections.forEach(section => {
                if (section.offsetTop <= marker) current = section;
            });
            landingNavLinks.forEach(link => {
                link.classList.toggle('active', link.getAttribute('href') === '#' + current.id);
            });
        }

        landingNavLinks.forEach(link => {
            link.addEventListener('click', () => {
                landingNavLinks.forEach(item => item.classList.remove('active'));
                link.classList.add('active');
            });
        });
        window.addEventListener('scroll', updateLandingNav, { passive: true });
        updateLandingNav();
    </script>
</body>
</html>
