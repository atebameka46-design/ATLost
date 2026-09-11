@extends('layouts.auth', ['title' => 'Mot de passe oublié'])

@section('content')
    <a href="{{ route('login') }}" class="mb-7 inline-flex items-center gap-1 text-xs font-bold text-n-500 transition hover:text-accent">← Retour à la connexion</a>
    <h1 class="font-serif text-5xl leading-none text-p-950">On vous aide<span class="text-accent">.</span></h1>
    <p class="mt-3 text-sm leading-relaxed text-n-600">Saisissez votre e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

    @if (session('status')) <p class="mb-4 rounded-xl bg-success-light px-4 py-3 text-xs font-semibold text-success">{{ session('status') }}</p> @endif
    @if ($errors->any()) <p class="mb-4 rounded-xl bg-danger-light px-4 py-3 text-xs font-semibold text-danger">{{ $errors->first() }}</p> @endif
    <form class="auth-form auth-forgot-form mt-5 gap-2" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <label for="email" class="mb-2 block text-xs font-bold text-p-950">Adresse e-mail</label>
            <input id="email" name="email" type="email" autocomplete="email" required placeholder="vous@exemple.com" class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-3 text-sm outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/10">
        </div>
        <button type="submit" class="auth-primary-button w-full rounded-full bg-p-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-p-950/15 transition hover:-translate-y-0.5 hover:bg-p-800">Envoyer le lien <span class="ml-1 text-accent">↗</span></button>
    </form>

    <p class="mt-4 text-center text-xs text-n-500">Nouveau sur ATLost ? <a href="{{ route('register') }}" class="font-bold text-accent hover:text-accent-dark">Créer un compte</a></p>
@endsection
