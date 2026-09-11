@extends('layouts.auth', ['title' => 'Se connecter'])

@section('content')
    <h1 class="font-serif text-5xl leading-none text-p-950">Bon retour<span class="text-accent">.</span></h1>
    <p class="mt-3 text-sm leading-relaxed text-n-600">Connectez-vous pour suivre vos signalements et retrouver vos documents.</p>

    @if (session('status')) <p class="mb-4 rounded-xl bg-success-light px-4 py-3 text-xs font-semibold text-success">{{ session('status') }}</p> @endif
    @if ($errors->any()) <p class="mb-4 rounded-xl bg-danger-light px-4 py-3 text-xs font-semibold text-danger">{{ $errors->first() }}</p> @endif
    <form class="auth-form auth-login-form mt-5 gap-2" method="POST" action="{{ route('login.store') }}">
        @csrf
        <div>
            <label for="email" class="mb-2 block text-xs font-bold text-p-950">Adresse e-mail</label>
            <input id="email" name="email" type="email" autocomplete="email" required placeholder="vous@exemple.com" class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-3 text-sm outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/10">
        </div>
        <div>
            <div class="mb-2 flex items-center justify-between">
                <label for="password" class="text-xs font-bold text-p-950">Mot de passe</label>
                <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-accent hover:text-accent-dark">Mot de passe oublié ?</a>
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Votre mot de passe" class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-3 text-sm outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/10">
        </div>
        <label class="flex items-center gap-2 text-xs text-n-500"><input type="checkbox" name="remember" value="1" class="accent-accent"> Se souvenir de moi</label>
        <button type="submit" class="auth-primary-button w-full rounded-full bg-p-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-p-950/15 transition hover:-translate-y-0.5 hover:bg-p-800">Se connecter <span class="ml-1 text-accent">↗</span></button>
    </form>

    <div class="my-3 flex items-center gap-3 text-[11px] text-n-400"><span class="h-px flex-1 bg-n-200"></span><span>ou continuer avec</span><span class="h-px flex-1 bg-n-200"></span></div>
    <div class="grid grid-cols-2 gap-3">
        <a href="#" class="auth-social-button flex items-center justify-center gap-2 rounded-full border border-n-200 px-3 py-2.5 text-xs font-bold text-p-950 transition hover:border-p-300 hover:bg-p-50"><span class="text-base font-extrabold">G</span> Google</a>
        <a href="#" class="auth-social-button flex items-center justify-center gap-2 rounded-full border border-n-200 px-3 py-2.5 text-xs font-bold text-p-950 transition hover:border-p-300 hover:bg-p-50"><span class="text-base">●</span> Apple</a>
    </div>

    <p class="mt-4 text-center text-xs text-n-500">Pas encore de compte ? <a href="{{ route('register') }}" class="font-bold text-accent hover:text-accent-dark">Créer un compte</a></p>
@endsection
