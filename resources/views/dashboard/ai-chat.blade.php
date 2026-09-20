@extends('layouts.dashboard', ['title' => 'Assistant ATBot', 'heading' => 'Assistant ATBot'])
@section('dashboard-content')
<div class="dashboard-page-content section-page ai-page">
    <div class="section-page-hero"><div><p class="dashboard-eyebrow">Assistant ATBot · ATLost</p><h1>{{ isset($answer) ? 'Voici ce que j’ai trouvé' : 'Comment puis-je vous aider ?' }}</h1><p>Recherche, signalement et récupération de documents au Cameroun.</p></div><span class="profile-section-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span></div>
    <section class="dashboard-card ai-chat-card">
        @if(isset($messages) && count($messages))<div class="ai-conversation">@foreach($messages as $message)<div class="ai-message ai-message-{{ $message['role'] }}"><span class="ai-message-avatar"><i class="fa-solid fa-{{ $message['role'] === 'user' ? 'user' : 'wand-magic-sparkles' }}"></i></span><div><small>{{ $message['role'] === 'user' ? 'Vous' : 'ATBot' }}</small><p>{!! nl2br(e($message['text'])) !!}</p></div></div>@endforeach</div><form method="POST" action="{{ route('assistant.reset') }}" class="ai-reset-form">@csrf @method('DELETE')<button class="profile-text-link" type="submit">Nouvelle conversation</button></form>@endif
        @if(isset($answer) || isset($error))
            <div class="ai-question"><span><i class="fa-solid fa-user"></i></span><div><small>Votre question</small><strong>{{ $question }}</strong></div></div>
            @if(isset($error))<div class="ai-answer ai-error"><div class="ai-answer-heading"><span><i class="fa-solid fa-circle-exclamation"></i></span><div><strong>ATBot n’est pas disponible</strong><small>Assistant ATLost</small></div></div><div class="ai-answer-content">{{ $error }}</div></div>@else<div class="ai-answer"><div class="ai-answer-heading"><span><i class="fa-solid fa-wand-magic-sparkles"></i></span><div><strong>Réponse de ATBot</strong><small>Assistant ATLost</small></div></div><div class="ai-answer-content">{!! nl2br(e($answer)) !!}</div></div>@endif
            <div class="ai-suggestions"><p>Vous pouvez aussi :</p><a href="{{ route('citizen.search') }}"><i class="fa-solid fa-magnifying-glass"></i> Rechercher un document</a><a href="{{ route('reports.create') }}"><i class="fa-solid fa-file-circle-plus"></i> Signaler un document</a></div>
            <button type="button" id="ask-again" class="dashboard-outline-button ai-ask-again"><i class="fa-solid fa-rotate"></i> Demander autre chose</button>
        @else
            <div class="ai-chat-welcome"><span><i class="fa-solid fa-robot"></i></span><div><strong>Assistant ATBot</strong><small>Posez votre première question</small></div></div>
        @endif
        <form method="POST" action="{{ route('assistant.message') }}" class="ai-chat-form {{ isset($answer) ? 'is-hidden' : '' }}">@csrf<textarea name="message" required maxlength="2000" placeholder="Ex. Comment retrouver mon document ?">{{ old('message') }}</textarea><button class="dashboard-green-button" type="submit"><i class="fa-solid fa-paper-plane"></i> Envoyer</button></form>
    </section>
</div>
@if(isset($answer))<script>document.getElementById('ask-again')?.addEventListener('click',()=>{document.querySelector('.ai-chat-form').classList.remove('is-hidden');document.getElementById('ask-again').remove();document.querySelector('.ai-chat-form textarea').focus()});</script>@endif
@endsection
