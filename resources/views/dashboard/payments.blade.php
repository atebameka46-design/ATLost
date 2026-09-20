@extends('layouts.dashboard', ['title' => 'Paiements — ATLost', 'heading' => 'Paiements'])

@section('dashboard-content')
<div class="dashboard-page-content section-page">
    <div class="section-page-hero">
        <div>
            <p class="dashboard-eyebrow">Administration</p>
            <h1>Paiements à vérifier</h1>
            <p>Contrôlez les références et justificatifs avant de permettre la confirmation de restitution.</p>
        </div>
    </div>

    @if (session('status'))<div class="dashboard-card" role="status"><strong>{{ session('status') }}</strong></div>@endif

    <section class="dashboard-card content-card">
        <div class="content-list">
            @forelse ($claims as $claim)
                <div class="dashboard-project-row">
                    <div>
                        <strong>#{{ $claim->id }} · {{ $claim->claimant->name }}</strong>
                        <small>{{ $claim->report->document_type }} — {{ $claim->report->owner_name }} · {{ number_format((float) $claim->paid_amount, 0, ',', ' ') }} FCFA + {{ number_format((float) $claim->service_fee_amount, 0, ',', ' ') }} FCFA de frais</small>
                        <small>Mode : {{ $claim->payment_method }} · Référence : {{ $claim->payment_reference }}</small>
                        @if ($claim->payment_proof_path)<a class="profile-text-link" href="{{ asset('storage/'.$claim->payment_proof_path) }}" target="_blank" rel="noopener">Voir le justificatif</a>@endif
                    </div>
                    <div class="row-inline-actions">
                        <span class="report-status status-{{ $claim->payment_status }}">{{ ucfirst($claim->payment_status) }}</span>
                        @if ($claim->payment_status === 'submitted')
                            <form method="POST" action="{{ route('admin.payments.verify', $claim) }}">@csrf @method('PATCH')<button class="dashboard-green-button" type="submit">Valider</button></form>
                            <form method="POST" action="{{ route('admin.payments.reject', $claim) }}">@csrf @method('PATCH')<button class="dashboard-outline-button" type="submit">Rejeter</button></form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="content-list-empty"><strong>Aucun paiement à vérifier</strong><small>Les nouveaux paiements déclarés apparaîtront ici.</small></div>
            @endforelse
        </div>
    </section>
</div>
@endsection
