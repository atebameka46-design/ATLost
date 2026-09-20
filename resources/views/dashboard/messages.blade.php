@extends('layouts.dashboard', ['title' => __('messages.messages').' — ATLost', 'heading' => __('messages.messages')])

@section('dashboard-content')
<div class="messenger-page">
    <div class="messenger-shell">
        <aside class="messenger-list">
            <div class="messenger-list-head">
                <h2>{{ __('messages.messages') }}</h2>
                <button type="button" class="messenger-new-btn" id="newMessageBtn" aria-label="{{ __('messages.new_message') }}"><i class="fa-solid fa-pen-to-square"></i></button>
            </div>
            <div class="messenger-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchPeople" placeholder="{{ __('messages.search_people') }}" autocomplete="off">
            </div>
            <div class="messenger-conversations" id="conversationList">
                @forelse ($conversations as $conversation)
                    @php($other = $conversation->getOtherUserFor(auth()->user()))
                    @php($lastMessage = $conversation->messages->last())
                    @php($unread = $conversation->unreadCountFor(auth()->user()))
                    <a href="{{ route('messages', ['conversation' => $conversation->id]) }}" class="messenger-conversation {{ ($activeConversation && $activeConversation->id === $conversation->id) ? 'active' : '' }}">
                        <div class="messenger-avatar">
                            @if ($other && $other->avatar_url)
                                <img src="{{ $other->avatar_url }}" alt="">
                            @else
                                <span>{{ strtoupper(substr($other->name ?? '?', 0, 1)) }}</span>
                            @endif
                            @if ($unread > 0)<b>{{ $unread }}</b>@endif
                        </div>
                        <div class="messenger-conversation-meta">
                            <strong>{{ $other->name ?? __('messages.messages') }}</strong>
                            <p>{{ $lastMessage ? $lastMessage->body : '' }}</p>
                            <small>{{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}</small>
                        </div>
                        @if ($conversation->document_report_id)
                            <span class="messenger-doc-badge"><i class="fa-solid fa-file-lines"></i></span>
                        @endif
                    </a>
                @empty
                    <p class="messenger-empty">{{ __('messages.no_conversation') }}</p>
                @endforelse
            </div>
        </aside>

        <section class="messenger-chat">
            @if ($activeConversation && $otherUser)
                <div class="messenger-chat-header">
                    <div class="messenger-chat-user">
                        <div class="messenger-avatar">
                            @if ($otherUser->avatar_url)
                                <img src="{{ $otherUser->avatar_url }}" alt="">
                            @else
                                <span>{{ strtoupper(substr($otherUser->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div>
                            <strong>{{ $otherUser->name }}</strong>
                            <small>{{ $otherUser->email }}</small>
                        </div>
                    </div>
                    @if ($document)
                        <div class="messenger-doc-context">
                            <i class="fa-solid fa-file-lines"></i>
                            <span>{{ __('messages.about_document') }} {{ $document->owner_name }} ({{ $document->document_type }})</span>
                        </div>
                    @endif
                </div>

                <div class="messenger-messages" id="messageList">
                    @forelse ($messages as $message)
                        <div class="messenger-bubble {{ $message->sender_id === auth()->id() ? 'mine' : 'theirs' }}">
                            <div class="messenger-bubble-body">
                                <p>{{ $message->body }}</p>
                                <small>{{ $message->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="messenger-empty">{{ __('messages.no_messages') }}</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('messages.send', $activeConversation) }}" class="messenger-input" id="messageForm">
                    @csrf
                    <input type="text" name="body" placeholder="{{ __('messages.type_message') }}" required autocomplete="off">
                    <button type="submit" aria-label="{{ __('messages.send') }}"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            @else
                <div class="messenger-placeholder">
                    <i class="fa-solid fa-comments"></i>
                    <p>{{ __('messages.no_conversation') }}</p>
                </div>
            @endif
        </section>
    </div>
</div>

<div class="messenger-modal" id="newMessageModal" hidden>
    <div class="messenger-modal-box">
        <div class="messenger-modal-head">
            <h3>{{ __('messages.new_message') }}</h3>
            <button type="button" id="closeModal" aria-label="{{ __('messages.close') }}"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="messenger-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchPeopleModal" placeholder="{{ __('messages.search_people') }}" autocomplete="off">
        </div>
        <div class="messenger-search-results" id="searchResults"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const messageForm = document.getElementById('messageForm');
    const messageList = document.getElementById('messageList');
    const conversationId = @json($activeConversation?->id);

    if (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    }

    if (messageForm) {
        messageForm.addEventListener('submit', function () {
            const input = messageForm.querySelector('input[name="body"]');
            if (!input.value.trim()) return;
            const btn = messageForm.querySelector('button');
            btn.disabled = true;
        });
    }

    @if ($activeConversation)
        const lastMessageId = @json($messages->last()?->id ?? 0);
        setInterval(() => {
            if (!conversationId) return;
            fetch(`{{ route('messages.poll', $activeConversation) }}?last_id=${lastMessageId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.messages && data.messages.length) {
                        const fragment = document.createDocumentFragment();
                        data.messages.forEach(msg => {
                            const div = document.createElement('div');
                            div.className = 'messenger-bubble ' + (msg.is_mine ? 'mine' : 'theirs');
                            div.innerHTML = `<div class="messenger-bubble-body"><p>${msg.body}</p><small>${msg.created_at}</small></div>`;
                            fragment.appendChild(div);
                        });
                        messageList.appendChild(fragment);
                        messageList.scrollTop = messageList.scrollHeight;
                    }
                });
        }, 3000);
    @endif

    const newMessageBtn = document.getElementById('newMessageBtn');
    const modal = document.getElementById('newMessageModal');
    const closeModal = document.getElementById('closeModal');
    const searchPeopleModal = document.getElementById('searchPeopleModal');
    const searchResults = document.getElementById('searchResults');

    if (newMessageBtn && modal) {
        newMessageBtn.addEventListener('click', () => { modal.hidden = false; searchPeopleModal.focus(); });
        closeModal.addEventListener('click', () => { modal.hidden = true; searchResults.innerHTML = ''; searchPeopleModal.value = ''; });
    }

    if (searchPeopleModal) {
        let timer;
        searchPeopleModal.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const q = searchPeopleModal.value.trim();
                if (q.length < 2) { searchResults.innerHTML = ''; return; }
                fetch(`{{ route('messages.search') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(users => {
                        searchResults.innerHTML = users.length ? '' : '<p class="messenger-empty">{{ __('messages.no_conversation') }}</p>';
                        users.forEach(u => {
                            const div = document.createElement('div');
                            div.className = 'messenger-search-result';
                            div.innerHTML = `<strong>${u.name}</strong><small>${u.email}</small>`;
                            div.addEventListener('click', () => {
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '{{ route('messages.start') }}';
                                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="user_id" value="${u.id}">`;
                                document.body.appendChild(form);
                                form.submit();
                            });
                            searchResults.appendChild(div);
                        });
                    });
            }, 250);
        });
    }
</script>
@endpush
