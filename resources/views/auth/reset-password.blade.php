@extends('layouts.auth', ['title' => 'Nouveau mot de passe'])

@section('content')
    <h1 class="font-serif text-5xl leading-none text-p-950">Nouveau mot de passe<span class="text-accent">.</span></h1>
    <p class="mt-3 text-sm leading-relaxed text-n-600">Choisissez un nouveau mot de passe sécurisé pour votre compte.</p>
    @if ($errors->any()) <p class="mt-4 rounded-xl bg-danger-light px-4 py-3 text-xs font-semibold text-danger">{{ $errors->first() }}</p> @endif
    <form class="auth-form mt-6 gap-3" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div><label for="email" class="mb-1.5 block text-xs font-bold text-p-950">Adresse e-mail</label><input id="email" name="email" type="email" required class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-3 text-sm outline-none focus:border-accent focus:ring-4 focus:ring-accent/10"></div>
        <div><label for="password" class="mb-1.5 block text-xs font-bold text-p-950">Nouveau mot de passe</label><input id="password" name="password" type="password" required class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-3 text-sm outline-none focus:border-accent focus:ring-4 focus:ring-accent/10"></div>
        <div><label for="password_confirmation" class="mb-1.5 block text-xs font-bold text-p-950">Confirmer</label><input id="password_confirmation" name="password_confirmation" type="password" required class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-3 text-sm outline-none focus:border-accent focus:ring-4 focus:ring-accent/10"></div>
        <button type="submit" class="auth-primary-button mt-2 w-full rounded-full bg-p-950 px-5 py-3 text-sm font-bold text-white">Enregistrer le mot de passe <span class="ml-1 text-accent">↗</span></button>
    </form>
@endsection
