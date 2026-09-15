@extends('layouts.dashboard', ['title' => ucfirst($section) . ' — ATLost', 'heading' => ucfirst($section)])

@php
    $sections = [
        'documents' => ['eyebrow' => __('messages.citizen_space'), 'title' => __('messages.documents'), 'description' => __('messages.documents_description'), 'action' => __('messages.report_document')],
        'activity' => ['eyebrow' => __('messages.citizen_space'), 'title' => __('messages.activity'), 'description' => __('messages.activity_description'), 'action' => __('messages.search_start')],
        'search' => ['eyebrow' => __('messages.search'), 'title' => __('messages.search_document'), 'description' => __('messages.search_description'), 'action' => __('messages.search_start')],
        'reports' => ['eyebrow' => __('messages.moderation'), 'title' => __('messages.reports'), 'description' => __('messages.reports_description'), 'action' => __('messages.filter_reports')],
        'analytics' => ['eyebrow' => __('messages.performance'), 'title' => __('messages.analytics'), 'description' => __('messages.analytics_description'), 'action' => __('messages.export_report')],
        'team' => ['eyebrow' => __('messages.team'), 'title' => __('messages.team'), 'description' => __('messages.team_description'), 'action' => __('messages.invite_member')],
    ][$section];
@endphp

@section('dashboard-content')
<div class="dashboard-page-content section-page">
    <div class="section-page-hero"><div><p class="dashboard-eyebrow">{{ $sections['eyebrow'] }}</p><h1>{{ $sections['title'] }}</h1><p>{{ $sections['description'] }}</p></div>
        @if ($section === 'search')
            <button type="button" class="dashboard-green-button modal-trigger" data-target="search-create-modal">{{ $sections['action'] }}</button>
        @elseif ($section === 'documents')
            <button type="button" class="dashboard-green-button modal-trigger" data-target="document-create-modal">{{ $sections['action'] }}</button>
        @else
            <a href="#" class="dashboard-green-button">{{ $sections['action'] }}</a>
        @endif
    </div>
    @if (session('status'))<div class="dashboard-card" role="status"><strong>{{ session('status') }}</strong></div>@endif

    @if ($section === 'search')
        <div class="dashboard-modal" id="search-create-modal" aria-hidden="true">
            <div class="dashboard-modal-panel">
                <div class="dashboard-modal-header"><h2>{{ __('messages.new_search') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                <form method="POST" action="{{ route('searches.store') }}" class="internal-search-form">
                    @csrf
                    <label>{{ __('messages.document_name_or_reference') }}<input name="query" value="{{ old('query') }}" required placeholder="{{ __('messages.search_placeholder') }}"></label>
                    <label>{{ __('messages.document_type') }}<select name="document_type"><option value="">{{ __('messages.all_documents') }}</option><option value="CNI">Carte nationale d’identité</option><option value="Permis">Permis de conduire</option><option value="Passeport">Passeport</option></select></label>
                    <label>{{ __('messages.location') }}<input name="location" value="{{ old('location') }}" placeholder="{{ __('messages.location_example') }}"></label>
                    <label>{{ __('messages.status') }}<select name="status"><option value="">{{ __('messages.all_statuses') }}</option><option value="approved">{{ __('messages.available') }}</option><option value="resolved">{{ __('messages.resolved') }}</option></select></label>
                    <button class="dashboard-green-button" type="submit">{{ __('messages.search') }}</button>
                </form>
            </div>
        </div>

        @foreach ($searches ?? [] as $search)
            <div class="dashboard-modal" id="search-edit-modal-{{ $search->id }}" aria-hidden="true">
                <div class="dashboard-modal-panel">
                    <div class="dashboard-modal-header"><h2>{{ __('messages.edit_search') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                    <form method="POST" action="{{ route('searches.update', $search) }}" class="internal-search-form">
                        @csrf
                        @method('PATCH')
                        <label>{{ __('messages.document_name_or_reference') }}<input name="query" value="{{ old('query', $search->query) }}" required></label>
                        <label>{{ __('messages.document_type') }}<select name="document_type"><option value="">{{ __('messages.all_documents') }}</option><option value="CNI" @selected($search->document_type === 'CNI')>Carte nationale d’identité</option><option value="Permis" @selected($search->document_type === 'Permis')>Permis de conduire</option><option value="Passeport" @selected($search->document_type === 'Passeport')>Passeport</option></select></label>
                        <label>{{ __('messages.location') }}<input name="location" value="{{ old('location', $search->location) }}" placeholder="{{ __('messages.location_example') }}"></label>
                        <label>{{ __('messages.status') }}<select name="status"><option value="">{{ __('messages.all_statuses') }}</option><option value="approved" @selected($search->status === 'approved')>{{ __('messages.available') }}</option><option value="resolved" @selected($search->status === 'resolved')>{{ __('messages.resolved') }}</option></select></label>
                        <button class="dashboard-green-button icon-action-button" type="submit" title="{{ __('messages.save_changes') }}" aria-label="{{ __('messages.save_changes') }}"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>

            <div class="dashboard-modal" id="search-delete-modal-{{ $search->id }}" aria-hidden="true">
                <div class="dashboard-modal-panel dashboard-modal-panel-small">
                    <div class="dashboard-modal-header"><h2>{{ __('messages.confirm_delete') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                    <p>{{ __('messages.delete_warning') }}</p>
                    <form method="POST" action="{{ route('searches.destroy', $search) }}">
                        @csrf
                        @method('DELETE')
                        <div class="dashboard-modal-actions">
                            <button type="button" class="dashboard-outline-button dashboard-modal-close icon-action-button" title="{{ __('messages.cancel') }}" aria-label="{{ __('messages.cancel') }}"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                            <button type="submit" class="dashboard-green-button danger-button icon-action-button" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        @if(isset($lastSearch))
            <div class="dashboard-modal" id="alert-search-modal-{{ $lastSearch->id }}" aria-hidden="true">
                <div class="dashboard-modal-panel">
                    <div class="dashboard-modal-header"><h2>{{ __('messages.notify_me') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                    <form method="POST" action="{{ route('search-alerts.store') }}" class="internal-search-form">
                        @csrf
                        <input type="hidden" name="query" value="{{ $lastSearch->query }}">
                        <input type="hidden" name="document_type" value="{{ $lastSearch->document_type }}">
                        <p>{{ __('messages.notify_me') }}</p>
                        <button class="dashboard-green-button" type="submit">{{ __('messages.notify_me') }}</button>
                    </form>
                </div>
            </div>
        @endif

        <section class="dashboard-card content-card search-history-card"><div class="content-card-heading"><h2 class="dashboard-title">{{ __('messages.recent_searches') }}</h2><span class="content-period">{{ __('messages.last_5') }}</span></div><div id="search-history" class="content-list">@forelse ($searches ?? [] as $index => $search)<div class="dashboard-project-row search-history-item {{ $index >= 5 ? 'is-hidden' : '' }}"><div><strong>{{ $search->query }}</strong><small>{{ $search->document_type ?: __('messages.all_documents') }} · {{ $search->created_at->format('d/m/Y H:i') }}</small></div><div class="row-inline-actions"><button type="button" class="profile-text-link modal-trigger icon-action-button" data-target="search-edit-modal-{{ $search->id }}" title="{{ __('messages.edit') }}" aria-label="{{ __('messages.edit') }}"><i class="fa-solid fa-pen" aria-hidden="true"></i></button><button type="button" class="profile-text-link modal-trigger icon-action-button" data-target="search-delete-modal-{{ $search->id }}" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button></div></div>@empty<p>{{ __('messages.no_search_saved') }}</p>@endforelse</div>@if (($searches ?? collect())->count() > 5)<button id="show-more-searches" type="button" class="dashboard-outline-button">{{ __('messages.show_more') }}</button>@endif</section>

        @if(isset($lastSearch) || ($section === 'search' && ($results ?? collect())->isNotEmpty()))
            <section class="dashboard-card content-card search-results-card">
                <div class="content-card-heading"><h2 class="dashboard-title">{{ isset($lastSearch) ? __('messages.results_found') : __('messages.documents_available') }}</h2><span class="content-period">{{ $results->count() }} {{ __('messages.document_count_label') }}</span></div>
                <div class="content-list">
                    @forelse ($results as $result)
                        <div class="dashboard-project-row">
                            <div class="result-card-image" @if ($result->photo_path) style="background-image: url('{{ asset('storage/'.$result->photo_path) }}')" @endif>
                                @if (! $result->photo_path)
                                    <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="result-card-content"><span class="result-card-type">{{ $result->document_type }}</span><strong>{{ $result->owner_name }}</strong><small><i class="fa-solid fa-location-dot"></i> {{ $result->location }} · {{ $result->statusLabel() }}</small></div>
                            <div class="row-inline-actions">
                                <a class="profile-text-link icon-action-button" href="{{ route('reports.show', $result) }}" title="{{ __('messages.view_document') }}" aria-label="{{ __('messages.view_document') }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>
                                @if ($result->user_id !== auth()->id() && $result->status !== 'resolved')
                                    <form method="POST" action="{{ route('reports.claim', $result) }}" class="inline-action-form">@csrf<button type="submit" class="profile-text-link icon-action-button" title="Demander la récupération" aria-label="Demander la récupération"><i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i></button></form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="content-list-empty"><strong>{{ __('messages.no_document_found') }}</strong><small>{{ __('messages.check_spelling') }}</small><button type="button" class="dashboard-green-button modal-trigger" data-target="alert-search-modal-{{ $lastSearch->id }}">{{ __('messages.notify_me') }}</button></div>
                    @endforelse
                </div>
            </section>
        @endif
    @elseif ($section === 'documents')
        <div class="dashboard-modal" id="document-create-modal" aria-hidden="true">
            <div class="dashboard-modal-panel">
                <div class="dashboard-modal-header"><h2>{{ __('messages.new_report') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="internal-search-form">
                    @csrf
                    <label>{{ __('messages.document_name') }}<input name="owner_name" value="{{ old('owner_name') }}" required placeholder="{{ __('messages.document_example') }}"></label>
                    <label>{{ __('messages.document_type') }}<select name="document_type" required><option value="">{{ __('messages.select_one') }}</option><option value="CNI">Carte Nationale (CNI)</option><option value="Permis">Permis de conduire</option><option value="Passeport">Passeport</option></select></label>
                    <label>{{ __('messages.discovery_location') }}<input name="location" value="{{ old('location') }}" required placeholder="{{ __('messages.location_example') }}"></label>
                    <label>{{ __('messages.phone') }}<input name="phone" value="{{ old('phone') }}" required placeholder="+237 6xx xx xx xx"></label>
                    <label>{{ __('messages.document_photo') }}<input type="file" name="photo" accept="image/*" required></label>
                    <button class="dashboard-green-button" type="submit">{{ __('messages.publish_report') }}</button>
                </form>
            </div>
        </div>

        @foreach ($reports ?? [] as $report)
            <div class="dashboard-modal" id="report-edit-modal-{{ $report->id }}" aria-hidden="true">
                <div class="dashboard-modal-panel">
                    <div class="dashboard-modal-header"><h2>{{ __('messages.edit_report') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                    <form method="POST" action="{{ route('reports.update-details', $report) }}" enctype="multipart/form-data" class="internal-search-form">
                        @csrf
                        @method('PUT')
                        <label>{{ __('messages.document_name') }}<input name="owner_name" value="{{ old('owner_name', $report->owner_name) }}" required></label>
                        <label>{{ __('messages.document_type') }}<select name="document_type" required><option value="CNI" @selected($report->document_type === 'CNI')>Carte Nationale</option><option value="Permis" @selected($report->document_type === 'Permis')>Permis</option><option value="Passeport" @selected($report->document_type === 'Passeport')>Passeport</option></select></label>
                        <label>{{ __('messages.discovery_location') }}<input name="location" value="{{ old('location', $report->location) }}" required></label>
                        <label>{{ __('messages.phone') }}<input name="phone" value="{{ old('phone', $report->phone) }}" required></label>
                        <label>{{ __('messages.document_photo') }}<input type="file" name="photo" accept="image/*"></label>
                        <button class="dashboard-green-button icon-action-button" type="submit" title="{{ __('messages.save_changes') }}" aria-label="{{ __('messages.save_changes') }}"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i></button>
                    </form>
                </div>
            </div>

            <div class="dashboard-modal" id="report-delete-modal-{{ $report->id }}" aria-hidden="true">
                <div class="dashboard-modal-panel dashboard-modal-panel-small">
                    <div class="dashboard-modal-header"><h2>{{ __('messages.confirm_delete') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                    <p>{{ __('messages.delete_warning') }}</p>
                    <form method="POST" action="{{ route('reports.destroy', $report) }}">
                        @csrf
                        @method('DELETE')
                        <div class="dashboard-modal-actions">
                            <button type="button" class="dashboard-outline-button dashboard-modal-close">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="dashboard-green-button danger-button">{{ __('messages.delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <section class="dashboard-card content-card"><div class="content-card-heading"><div><p class="dashboard-eyebrow">{{ __('messages.personal_followup') }}</p><h2 class="dashboard-title">{{ __('messages.my_documents') }}</h2></div></div><div class="content-list">@forelse ($reports ?? [] as $report)<div class="dashboard-project-row"><span class="project-dot blue"><i class="fa-solid fa-file-lines"></i></span><div><strong>{{ $report->document_type }} — {{ $report->owner_name }}</strong><small>{{ $report->location }} · {{ $report->created_at->format('d/m/Y') }}</small></div><em>{{ $report->statusLabel() }}</em><div class="row-inline-actions"><a class="profile-text-link icon-action-button" href="{{ route('reports.show', $report) }}" title="{{ __('messages.view_document') }}" aria-label="{{ __('messages.view_document') }}"><i class="fa-solid fa-eye" aria-hidden="true"></i></a><button type="button" class="profile-text-link modal-trigger icon-action-button" data-target="report-edit-modal-{{ $report->id }}" title="{{ __('messages.edit') }}" aria-label="{{ __('messages.edit') }}"><i class="fa-solid fa-pen" aria-hidden="true"></i></button><button type="button" class="profile-text-link modal-trigger icon-action-button" data-target="report-delete-modal-{{ $report->id }}" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button></div></div>@empty<div class="content-list-empty"><span><i class="fa-solid fa-folder-open"></i></span><div><strong>{{ __('messages.no_documents') }}</strong><small>{{ __('messages.documents_empty') }}</small></div></div>@endforelse</div></section>
    @elseif ($section === 'activity')
        <section class="dashboard-card content-card"><div class="content-card-heading"><div><p class="dashboard-eyebrow">{{ __('messages.history') }}</p><h2 class="dashboard-title">{{ __('messages.recent_activity') }}</h2></div><span class="content-period">{{ __('messages.today') }}</span></div>@if (($activityTimeline ?? collect())->isNotEmpty())<div class="activity-timeline">@foreach ($activityTimeline as $item)<div><i class="fa-solid {{ $item['icon'] }}"></i><p><strong>{{ $item['title'] }}</strong><small>{{ $item['description'] }} · {{ $item['meta'] }}</small></p><a href="{{ $item['link'] }}" class="profile-text-link">Voir</a></div>@endforeach</div>@else<div class="activity-timeline"><div><i class="fa-solid fa-check"></i><p><strong>{{ __('messages.no_recent_activity') }}</strong><small>{{ __('messages.history_empty') }}</small></p></div><div><i class="fa-solid fa-clock-rotate-left"></i><p><strong>{{ __('messages.stay_informed') }}</strong><small>{{ __('messages.history_updates') }}</small></p></div></div>@endif</section>
    @elseif ($section === 'reports')
        <section class="dashboard-card content-card"><div class="content-card-heading"><div><p class="dashboard-eyebrow">{{ __('messages.moderation_queue') }}</p><h2 class="dashboard-title">{{ __('messages.reports_to_handle') }}</h2></div></div><div class="content-list">@forelse (\App\Models\DocumentReport::latest()->get() as $report)<div class="dashboard-project-row"><div><strong>#{{ $report->id }} · {{ $report->document_type }} — {{ $report->owner_name }}</strong><small>{{ $report->location }} · {{ $report->phone }}</small></div><form method="POST" action="{{ route('admin.reports.update', $report) }}">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()"><option value="pending" @selected($report->status === 'pending')>{{ __('messages.pending') }}</option><option value="approved" @selected($report->status === 'approved')>{{ __('messages.published') }}</option><option value="resolved" @selected($report->status === 'resolved')>{{ __('messages.resolved') }}</option><option value="rejected" @selected($report->status === 'rejected')>{{ __('messages.rejected') }}</option></select></form><button type="button" class="profile-text-link modal-trigger icon-action-button" data-target="admin-delete-modal-{{ $report->id }}" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button></div>@empty<div class="moderation-empty"><strong>{{ __('messages.no_reports') }}</strong><p>{{ __('messages.moderation_queue_up_to_date') }}</p></div>@endforelse</div></section>
        @foreach (\App\Models\DocumentReport::latest()->get() as $report)
            <div class="dashboard-modal" id="admin-delete-modal-{{ $report->id }}" aria-hidden="true">
                <div class="dashboard-modal-panel dashboard-modal-panel-small">
                    <div class="dashboard-modal-header"><h2>{{ __('messages.confirm_delete') }}</h2><button type="button" class="dashboard-modal-close" aria-label="{{ __('messages.close') }}">×</button></div>
                    <p>{{ __('messages.delete_warning') }}</p>
                    <form method="POST" action="{{ route('reports.destroy', $report) }}">
                        @csrf
                        @method('DELETE')
                        <div class="dashboard-modal-actions">
                            <button type="button" class="dashboard-outline-button dashboard-modal-close">{{ __('messages.cancel') }}</button>
                            <button type="submit" class="dashboard-green-button danger-button">{{ __('messages.delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @elseif ($section === 'analytics')
        <section class="analytics-content"><div class="dashboard-card analytics-chart"><div class="content-card-heading"><div><p class="dashboard-eyebrow">{{ __('messages.global_performance') }}</p><h2 class="dashboard-title">{{ __('messages.restitution_trend') }}</h2></div><span class="content-period">{{ __('messages.last_30_days') }}</span></div><div class="fake-chart"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div><div class="chart-labels"><span>S1</span><span>S2</span><span>S3</span><span>S4</span></div></div><div class="dashboard-card analytics-insight"><p class="dashboard-eyebrow">{{ __('messages.to_remember') }}</p><h2 class="dashboard-title">98% sous 48h</h2><p>{{ __('messages.good_restitution_rate') }}</p><a href="#" class="profile-text-link">{{ __('messages.download_report') }} →</a></div></section>
    @elseif ($section === 'team')
        <section class="dashboard-card content-card">
            <div class="content-card-heading">
                <div>
                    <p class="dashboard-eyebrow">{{ __('messages.collaborators') }}</p>
                    <h2 class="dashboard-title">{{ __('messages.team_members') }}</h2>
                </div>
                <span class="content-period">{{ ($users ?? collect())->count() }} {{ __('messages.team_members') }}</span>
            </div>

            <div class="content-list">
                @forelse ($users ?? [] as $teamUser)
                    <div class="dashboard-project-row">
                        <span class="project-dot {{ $teamUser->isAdmin() ? 'green' : 'blue' }}">
                            <i class="fa-solid {{ $teamUser->isAdmin() ? 'fa-user-shield' : 'fa-user' }}"></i>
                        </span>
                        <div>
                            <strong>{{ $teamUser->name }}</strong>
                            <small>{{ $teamUser->email }} · {{ $teamUser->created_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="row-inline-actions" style="gap: 0.75rem;">
                            <span class="role-pill {{ $teamUser->isAdmin() ? 'role-admin' : 'role-citizen' }}">
                                {{ $teamUser->isAdmin() ? __('messages.administrator') : __('messages.citizen') }}
                            </span>
                            @if ($teamUser->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.update-role', $teamUser) }}" class="inline-action-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" aria-label="Role de {{ $teamUser->name }}">
                                        <option value="citizen" @selected($teamUser->role === 'citizen')>{{ __('messages.citizen') }}</option>
                                        <option value="admin" @selected($teamUser->role === 'admin')>{{ __('messages.administrator') }}</option>
                                    </select>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="team-empty">
                        <div class="team-avatar">+</div>
                        <strong>{{ __('messages.start_collaborating') }}</strong>
                        <p>{{ __('messages.add_members_text') }}</p>
                    </div>
                @endforelse
            </div>
        </section>
    @endif
</div>

<style>
    .dashboard-page-content.section-page {
        animation: none;
    }

    .role-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .role-admin {
        background: rgba(17, 166, 99, 0.12);
        color: #0a7d4a;
    }

    .role-citizen {
        background: rgba(38, 120, 255, 0.12);
        color: #1d56d8;
    }

    .inline-action-form select {
        min-width: 120px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 0.7rem;
        background: #fff;
        padding: 0.35rem 0.5rem;
        font-size: 0.8rem;
    }

    .dashboard-modal {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(10, 15, 30, 0.56);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.2s ease, visibility 0.2s ease;
        z-index: 1000;
    }

    .dashboard-modal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .dashboard-modal-panel {
        width: min(100%, 620px);
        background: linear-gradient(180deg, #ffffff 0%, #f8fafb 100%);
        border: 1px solid rgba(120, 124, 146, 0.16);
        border-radius: 20px;
        box-shadow: 0 30px 90px rgba(10, 15, 30, 0.22);
        padding: 1.25rem 1.25rem 1.4rem;
        transform: translateY(10px) scale(0.98);
        transition: transform 0.2s ease;
    }

    .dashboard-modal.is-open .dashboard-modal-panel {
        transform: translateY(0) scale(1);
    }

    .dashboard-modal-panel-small {
        width: min(100%, 470px);
    }

    .dashboard-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--n-200);
    }

    .dashboard-modal-header h2 {
        margin: 0;
        color: var(--p-950);
        font-size: 1.15rem;
        letter-spacing: -0.04em;
    }

    .dashboard-modal-close {
        display: grid;
        place-items: center;
        width: 2.2rem;
        height: 2.2rem;
        border: 1px solid transparent;
        border-radius: 0.7rem;
        background: var(--n-100);
        color: var(--p-700);
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .dashboard-modal-close:hover {
        border-color: var(--accent);
        background: var(--accent-light);
        color: var(--accent-dark);
    }

    .dashboard-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .icon-action-button {
        display: inline-grid;
        width: 2.25rem;
        height: 2.25rem;
        place-items: center;
        padding: .45rem;
        border: 1px solid var(--n-200);
        border-radius: .55rem;
        background: #fff;
        color: var(--p-700);
        box-shadow: 0 2px 6px rgba(1, 22, 39, .05);
        transition: color .2s ease, background-color .2s ease, border-color .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .inline-action-form {
        display: inline-flex;
    }

    .search-results-card .row-inline-actions {
        display: flex;
        align-items: center;
        gap: .45rem;
        margin-top: auto;
        width: 100%;
    }

    .search-results-card .row-inline-actions .icon-action-button {
        flex: 0 0 2.25rem;
    }

    .icon-action-button:hover {
        transform: translateY(-1px);
        border-color: var(--accent);
        background: var(--accent-light);
        color: var(--accent-dark);
        box-shadow: 0 5px 12px rgba(1, 22, 39, .1);
    }

    .icon-action-button:focus-visible {
        outline: 3px solid rgba(247, 140, 31, .28);
        outline-offset: 2px;
    }

    .danger-button.icon-action-button {
        border-color: rgba(193, 18, 31, .25);
        background: var(--danger-light);
        color: var(--danger);
    }

    .danger-button.icon-action-button:hover {
        border-color: var(--danger);
        background: var(--danger);
        color: #fff;
    }

    .danger-button {
        background: #dc2626 !important;
        border-color: #dc2626 !important;
        color: #fff !important;
    }

    .row-inline-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dashboard-empty-copy {
        margin: 0 0 1rem;
        color: #5f6478;
    }

    .theme-dark .dashboard-modal-panel {
        background: linear-gradient(180deg, #22343d 0%, #1a2d35 100%);
        border-color: rgba(185, 199, 202, 0.2);
        box-shadow: 0 30px 90px rgba(1, 22, 39, 0.35);
    }

    .theme-dark .dashboard-modal-header {
        border-bottom-color: rgba(185, 199, 202, 0.2);
    }

    .theme-dark .dashboard-modal-header h2 {
        color: #f3f7f7;
    }

    .theme-dark .dashboard-modal-close {
        background: rgba(255, 255, 255, 0.05);
        color: #d2dcde;
    }
</style>

<script>
    document.querySelectorAll('.modal-trigger').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.target;
            const modal = document.getElementById(targetId);
            if (!modal) {
                return;
            }

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        });
    });

    document.querySelectorAll('.dashboard-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
            }
        });
    });

    document.querySelectorAll('.dashboard-modal-close').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = trigger.closest('.dashboard-modal');
            if (modal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.dashboard-modal.is-open').forEach((modal) => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            });
            document.body.classList.remove('modal-open');
        }
    });
</script>

@if ($section === 'search')
<script>
    document.getElementById('show-more-searches')?.addEventListener('click', (event) => {
        const button = event.currentTarget;
        const collapsed = button.dataset.expanded !== 'true';
        document.querySelectorAll('.search-history-item').forEach((item, index) => {
            if (index >= 5) item.classList.toggle('is-hidden', !collapsed);
        });
        button.dataset.expanded = collapsed ? 'true' : 'false';
        button.textContent = collapsed ? '{{ __('messages.show_less') }}' : '{{ __('messages.show_more') }}';
    });
</script>
@endif
@endsection
