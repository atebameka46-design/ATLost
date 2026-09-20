<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\DocumentReport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessagingController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        /** @var User $user */
        $user = $user;

        $conversations = Conversation::query()
            ->forUser($user)
            ->with(['userOne', 'userTwo', 'documentReport', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('last_message_at')
            ->get();

        $activeConversation = null;
        $messages = collect();
        $otherUser = null;
        $document = null;

        if ($request->has('conversation')) {
            $activeConversation = $conversations->firstWhere('id', (int) $request->query('conversation'));
            if ($activeConversation) {
                $messages = $activeConversation->messages()->with('sender')->orderByDesc('created_at')->get()->reverse()->values();
                $otherUser = $activeConversation->getOtherUserFor($user);
                $document = $activeConversation->documentReport;
                $activeConversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
            }
        }

        if ($request->has('document')) {
            $document = DocumentReport::findOrFail((int) $request->query('document'));
            $owner = $document->user;
            if ($owner && $owner->id !== $user->id) {
                $activeConversation = Conversation::query()
                    ->forUser($user)
                    ->where('document_report_id', $document->id)
                    ->first();
                if (! $activeConversation) {
                    $activeConversation = Conversation::create([
                        'user_one_id' => min($user->id, $owner->id),
                        'user_two_id' => max($user->id, $owner->id),
                        'document_report_id' => $document->id,
                    ]);
                    $messages = collect();
                } else {
                    $messages = $activeConversation->messages()->with('sender')->orderByDesc('created_at')->get()->reverse()->values();
                }
                $otherUser = $activeConversation->getOtherUserFor($user);
                $activeConversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
            }
        }

        return view('dashboard.messages', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'otherUser' => $otherUser,
            'document' => $document,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id, 403);

        $messages = $conversation->messages()->with('sender')->orderByDesc('created_at')->get()->reverse()->values();
        $conversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);

        return view('dashboard.messages', [
            'conversations' => Conversation::query()->forUser($user)->with(['userOne', 'userTwo', 'documentReport', 'messages' => fn ($q) => $q->latest()->limit(1)])->orderByDesc('last_message_at')->get(),
            'activeConversation' => $conversation,
            'messages' => $messages,
            'otherUser' => $conversation->getOtherUserFor($user),
            'document' => $conversation->documentReport,
        ]);
    }

    public function send(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id, 403);

        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        return back();
    }

    public function start(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'document_report_id' => ['nullable', 'exists:document_reports,id'],
        ]);

        $otherId = (int) $data['user_id'];
        $documentId = $data['document_report_id'] ?? null;

        abort_if($otherId === $user->id, 422, 'Vous ne pouvez pas discuter avec vous-même.');

        $conversation = Conversation::query()
            ->forUser($user)
            ->when($documentId, fn ($q) => $q->where('document_report_id', $documentId))
            ->where(function ($q) use ($user, $otherId) {
                $q->where('user_one_id', $user->id)->where('user_two_id', $otherId);
            })
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'user_one_id' => min($user->id, $otherId),
                'user_two_id' => max($user->id, $otherId),
                'document_report_id' => $documentId,
            ]);
        }

        return redirect()->route('messages', ['conversation' => $conversation->id]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->where('id', '!=', $user->id)
            ->when($q, fn ($builder) => $builder->where(function ($builder) use ($q) {
                $like = '%'.mb_strtolower($q).'%';
                $builder->whereRaw('LOWER(name) LIKE ?', [$like])->orWhereRaw('LOWER(email) LIKE ?', [$like]);
            }))
            ->limit(20)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function poll(Conversation $conversation)
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        abort_unless($conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id, 403);

        $lastId = (int) ($_GET['last_id'] ?? 0);
        $query = $conversation->messages()->with('sender')->orderByDesc('created_at');

        if ($lastId > 0) {
            $query->where('id', '>', $lastId);
        }

        $newMessages = $query->limit(50)->get()->reverse()->values();

        return response()->json([
            'messages' => $newMessages->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->name ?? 'Utilisateur',
                'created_at' => $m->created_at->diffForHumans(),
                'is_mine' => $m->sender_id === $user->id,
            ]),
        ]);
    }
}
