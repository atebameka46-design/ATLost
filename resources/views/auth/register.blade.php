@extends('layouts.auth', ['title' => 'Créer un compte'])

@section('content')
    <h1 class="font-serif text-5xl leading-none text-p-950">Bienvenue<span class="text-accent">.</span></h1>
    <p class="mt-3 text-sm leading-relaxed text-n-600">Créez votre espace ATLost et augmentez vos chances de retrouver un document.</p>

    @if ($errors->any()) <p class="mb-4 rounded-xl bg-danger-light px-4 py-3 text-xs font-semibold text-danger">{{ $errors->first() }}</p> @endif
    <div class="auth-stepper mt-5 flex items-center gap-2 text-[11px] font-bold text-n-400"><span class="auth-step-indicator auth-step-active">1</span><span class="h-px w-8 bg-n-200"></span><span class="auth-step-indicator">2</span><span id="register-step-label" class="ml-1">Informations personnelles</span></div>
    <form id="register-form" class="auth-form auth-register-form mt-4 gap-2" method="POST" action="{{ route('register.store') }}">
        @csrf
        <div data-register-step="1">
            <label for="name" class="mb-1.5 block text-xs font-bold text-p-950">Nom complet</label>
            <input id="name" name="name" type="text" autocomplete="name" required placeholder="Votre nom complet" class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-2.5 text-sm outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/10">
        </div>
        <div data-register-step="1">
            <label for="register-email" class="mb-1.5 block text-xs font-bold text-p-950">Adresse e-mail</label>
            <input id="register-email" name="email" type="email" autocomplete="email" required placeholder="vous@exemple.com" class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-2.5 text-sm outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/10">
        </div>
        <div data-register-step="2" class="hidden">
            <label for="register-password" class="mb-1.5 block text-xs font-bold text-p-950">Mot de passe</label>
            <input id="register-password" name="password" type="password" autocomplete="new-password" required placeholder="8 caractères minimum" class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-2.5 text-sm outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/10">
        </div>
        <div data-register-step="2" class="hidden">
            <label for="password_confirmation" class="mb-1.5 block text-xs font-bold text-p-950">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="Répétez votre mot de passe" class="auth-input w-full rounded-xl border border-n-200 bg-n-50 px-4 py-2.5 text-sm outline-none transition focus:border-accent focus:ring-4 focus:ring-accent/10">
        </div>
        <div class="mt-2 flex gap-2"><button id="register-back" type="button" class="hidden w-1/3 rounded-full border border-n-200 px-4 py-3 text-sm font-bold text-p-950 transition hover:bg-n-50">Retour</button><button id="register-next" type="button" class="auth-primary-button w-full rounded-full bg-p-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-p-950/15 transition hover:-translate-y-0.5 hover:bg-p-800">Continuer <span class="ml-1 text-accent">↗</span></button><button id="register-submit" type="submit" class="auth-primary-button hidden w-full rounded-full bg-p-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-p-950/15 transition hover:-translate-y-0.5 hover:bg-p-800">Créer mon compte <span class="ml-1 text-accent">↗</span></button></div>
    </form>

    <div class="my-2 flex items-center gap-3 text-[11px] text-n-400"><span class="h-px flex-1 bg-n-200"></span><span>ou s'inscrire avec</span><span class="h-px flex-1 bg-n-200"></span></div>
    <div class="grid grid-cols-2 gap-3">
        <a href="#" class="auth-social-button flex items-center justify-center gap-2 rounded-full border border-n-200 px-3 py-2 text-xs font-bold text-p-950 transition hover:border-p-300 hover:bg-p-50"><span class="text-base font-extrabold">G</span> Google</a>
        <a href="#" class="auth-social-button flex items-center justify-center gap-2 rounded-full border border-n-200 px-3 py-2 text-xs font-bold text-p-950 transition hover:border-p-300 hover:bg-p-50"><span class="text-base">●</span> Apple</a>
    </div>

    <p class="mt-4 text-center text-xs text-n-500">Vous avez déjà un compte ? <a href="{{ route('login') }}" class="font-bold text-accent hover:text-accent-dark">Se connecter</a></p>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('register-form');
        const next = document.getElementById('register-next');
        const back = document.getElementById('register-back');
        const submit = document.getElementById('register-submit');
        const label = document.getElementById('register-step-label');
        const indicators = document.querySelectorAll('.auth-step-indicator');
        let step = 1;

        const showStep = (value) => {
            step = value;
            document.querySelectorAll('[data-register-step]').forEach((field) => {
                field.classList.toggle('hidden', field.dataset.registerStep !== String(step));
                field.querySelector('input').required = field.dataset.registerStep === String(step);
            });
            back.classList.toggle('hidden', step === 1);
            next.classList.toggle('hidden', step === 2);
            submit.classList.toggle('hidden', step === 1);
            label.textContent = step === 1 ? 'Informations personnelles' : 'Sécuriser votre compte';
            indicators.forEach((indicator, index) => indicator.classList.toggle('auth-step-active', index < step));
        };

        next.addEventListener('click', () => {
            const fields = [...document.querySelectorAll('[data-register-step="1"] input')];
            if (fields.every((field) => field.reportValidity())) showStep(2);
        });
        back.addEventListener('click', () => showStep(1));
        form.addEventListener('submit', (event) => {
            if (step !== 2) event.preventDefault();
        });
    });
</script>
@endpush
