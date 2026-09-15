<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AiChatController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard.ai-chat', ['messages' => $request->session()->get('ai_chat', [])]);
    }

    public function message(Request $request): View
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);
        abort_unless(config('services.gemini.key'), 503, 'GEMINI_API_KEY n’est pas configurée.');
        try {
            $response = Http::withHeaders(['x-goog-api-key' => config('services.gemini.key')])->post('https://generativelanguage.googleapis.com/v1beta/models/'.config('services.gemini.model', 'gemini-3.8-flash').':generateContent', [
                'system_instruction' => ['parts' => [['text' => 'Tu es Gemini, l’assistant ATLost. Réponds en français, simplement et brièvement. Aide sur la recherche, le signalement et la récupération de documents au Cameroun. Ne demande jamais de mot de passe ni de paiement par chat.']]],
                'contents' => array_map(fn ($message) => ['role' => $message['role'], 'parts' => [['text' => $message['text']]]], [...$request->session()->get('ai_chat', []), ['role' => 'user', 'text' => $data['message']]]),
            ])->throw()->json();
        } catch (RequestException $exception) {
            $message = $exception->response?->status() === 403 ? 'Gemini est connecté, mais votre projet Google n’a pas accès à ce modèle ou a dépassé ses limites.' : 'Gemini est momentanément indisponible. Vérifiez votre clé API et réessayez.';

            return view('dashboard.ai-chat', ['error' => $message, 'question' => $data['message']]);
        }
        $answer = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Je n’ai pas pu générer une réponse.';
        $messages = [...$request->session()->get('ai_chat', []), ['role' => 'user', 'text' => $data['message']], ['role' => 'model', 'text' => $answer]];
        $request->session()->put('ai_chat', array_slice($messages, -20));

        return view('dashboard.ai-chat', ['messages' => $messages, 'answer' => $answer, 'question' => $data['message']]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->session()->forget('ai_chat');

        return redirect()->route('assistant');
    }
}
