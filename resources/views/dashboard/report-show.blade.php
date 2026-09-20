@extends('layouts.dashboard', ['title' => 'Détail du document', 'heading' => 'Détail du document'])

@section('dashboard-content')
<div class="dashboard-page-content section-page report-detail-page">
    @php($myClaim = $documentReport->claims()->where('claimant_id', auth()->id())->latest()->first())
    <a href="{{ route('citizen.search') }}" class="report-back-link">← Retour aux résultats</a>

    <div class="report-detail-grid">
        <section class="dashboard-card report-photo-card">
            <div class="report-photo-frame">
                @if ($documentReport->photo_path)
                    <img src="{{ asset('storage/'.$documentReport->photo_path) }}" alt="Photo du document">
                @else
                    <span><i class="fa-solid fa-file-lines"></i></span>
                @endif
            </div>
            <span class="report-privacy-note"><i class="fa-solid fa-lock"></i> Données protégées</span>
        </section>

        <section class="dashboard-card report-info-card">
            <div class="report-detail-header">
                <div>
                    <p class="dashboard-eyebrow">Document retrouvé · {{ $documentReport->document_type }}</p>
                    <h1>{{ $documentReport->owner_name }}</h1>
                </div>
                <span class="report-status status-{{ $documentReport->status }}">{{ $documentReport->statusLabel() }}</span>
            </div>
            <p class="report-intro">Ce document a été signalé à <strong>{{ $documentReport->location }}</strong>.</p>
            <dl class="report-meta">
                <div><dt>Type</dt><dd>{{ $documentReport->document_type }}</dd></div>
                <div><dt>Lieu</dt><dd>{{ $documentReport->location }}</dd></div>
                <div><dt>Signalé le</dt><dd>{{ $documentReport->created_at->format('d/m/Y') }}</dd></div>
            </dl>

            @if ((float) $documentReport->reward_amount > 0)
                <div class="report-description">
                    <span>Montant de restitution</span>
                    <p>{{ number_format((float) $documentReport->reward_amount, 0, ',', ' ') }} FCFA</p>
                </div>
            @endif
            @if ($documentReport->description)
                <div class="report-description"><span>Description</span><p>{{ $documentReport->description }}</p></div>
            @endif
            @if (session('status'))<div class="profile-status">{{ session('status') }}</div>@endif

            @if ($documentReport->user_id !== auth()->id())
                <div class="report-action-box" style="box-shadow:none;border:1px dashed var(--n-300);background:var(--n-50)">
                    <strong>Contacter le signaleur</strong>
                    <p>Une question sur ce document ? Discutez directement avec la personne qui l’a signalé.</p>
                    <a href="{{ route('messages', ['document' => $documentReport->id]) }}" class="messenger-contact-btn"><i class="fa-solid fa-envelope"></i> {{ __('messages.contact_about_document') }}</a>
                </div>
            @endif

            @if ($documentReport->user_id !== auth()->id() && (!$myClaim || $myClaim->status === 'cancelled') && $documentReport->status !== 'resolved')
                <div class="report-action-box">
                    <strong>Ce document vous appartient ?</strong>
                    <p>Envoyez une demande au déclarant pour organiser sa restitution.</p>
                    <form method="POST" action="{{ route('reports.claim', $documentReport) }}">@csrf<button class="dashboard-green-button">Demander la récupération</button></form>
                </div>
            @elseif ($myClaim)
                <div class="report-action-box">
                    <strong>Votre demande</strong>
                    <p>Statut : {{ $myClaim->statusLabel() }} · Paiement : {{ ucfirst($myClaim->payment_status) }}</p>

                    @if ($myClaim->status === 'accepted' && $myClaim->payment_status !== 'verified')
                        <form method="POST" action="{{ route('claims.appointment', $myClaim) }}" class="internal-search-form">
                            @csrf @method('PATCH')
                            <label>Date et heure du rendez-vous<input type="datetime-local" name="appointment_at" value="{{ old('appointment_at', optional($myClaim->appointment_at)->format('Y-m-d\TH:i')) }}" required></label>
                            <button class="dashboard-outline-button" type="submit">Proposer le rendez-vous</button>
                        </form>
                        <form method="POST" action="{{ route('claims.pay', $myClaim) }}" enctype="multipart/form-data" class="internal-search-form">
                            @csrf
                            <label>Mode de paiement<select name="payment_method" required><option value="">Sélectionnez</option><option value="mobile_money">Mobile Money — {{ config('services.atlost.mobile_money_number') }}</option><option value="bank_transfer">Virement bancaire</option></select></label>
                            <label>Référence de paiement<input name="payment_reference" required placeholder="Référence de transaction"></label>
                            <label>Justificatif (obligatoire pour un virement)<input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp,.pdf"></label>
                            <small>Montant : {{ number_format((float) $myClaim->report->reward_amount, 0, ',', ' ') }} FCFA · Frais ATLost ({{ config('services.atlost.service_fee_percentage') }} %) : {{ number_format((float) $myClaim->report->reward_amount * (float) config('services.atlost.service_fee_percentage') / 100, 0, ',', ' ') }} FCFA</small>
                            <button class="dashboard-green-button" type="submit">Déclarer le paiement</button>
                        </form>
                    @elseif ($myClaim->payment_status === 'submitted')
                        <p>Votre paiement est en attente de vérification par ATLost.</p>
                    @elseif ($myClaim->payment_status === 'verified')
                        <p>Paiement validé. Attendez la confirmation de la restitution.</p>
                    @endif

                    @if (! in_array($myClaim->status, ['completed', 'cancelled'], true))
                        <form method="POST" action="{{ route('claims.cancel', $myClaim) }}" class="mt-2">@csrf @method('DELETE')<button class="profile-text-link" type="submit" onclick="return confirm('Annuler cette demande ?')">Annuler la demande</button></form>
                    @endif
                </div>
            @elseif ($documentReport->status === 'resolved')
                <div class="profile-status">Ce document est marqué comme restitué.</div>
            @endif
        </section>
    </div>

    @if ($documentReport->user_id === auth()->id() && $documentReport->claims->count())
        <section class="dashboard-card report-claims-card">
            <p class="dashboard-eyebrow">Demandes reçues</p>
            <h2 class="dashboard-title">Personnes ayant reconnu ce document</h2>
            @foreach ($documentReport->claims as $claim)
                <div class="report-claim-row">
                    <div>
                        <strong>{{ $claim->claimant->name }}</strong>
                        <small>{{ $claim->statusLabel() }} · Paiement : {{ ucfirst($claim->payment_status) }}</small>
                        @if ($claim->appointment_at)<small>Rendez-vous : {{ $claim->appointment_at->format('d/m/Y à H:i') }}</small>@endif
                    </div>
                    <div class="row-inline-actions">
                        @if ($claim->status === 'pending')
                            <form method="POST" action="{{ route('claims.accept', $claim) }}">@csrf<button class="dashboard-green-button">Accepter</button></form>
                        @elseif ($claim->status === 'accepted' && $claim->payment_status !== 'verified')
                            <form method="POST" action="{{ route('claims.appointment', $claim) }}" class="internal-search-form">
                                @csrf @method('PATCH')
                                <label>Rendez-vous<input type="datetime-local" name="appointment_at" value="{{ optional($claim->appointment_at)->format('Y-m-d\TH:i') }}" required></label>
                                <button class="dashboard-outline-button" type="submit">Enregistrer</button>
                            </form>
                        @elseif ($claim->status === 'accepted' && $claim->payment_status === 'verified' && $claim->appointment_at)
                            <form method="POST" action="{{ route('claims.complete', $claim) }}">@csrf<button class="dashboard-green-button">Confirmer la restitution</button></form>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>
    @endif
</div>
@endsection
