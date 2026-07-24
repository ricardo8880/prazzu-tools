<?php

namespace App\Http\Controllers\Platform;

use App\Core\Feedback\Models\ToolSuggestion;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\SuggestToolRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SuggestToolController extends Controller
{
    public function create(): View
    {
        return view('pages.suggest-tool');
    }

    public function store(SuggestToolRequest $request): RedirectResponse
    {
        $data = $request->validated();

        ToolSuggestion::query()->create([
            ...$data,
            'name' => trim((string) $data['name']),
            'email' => trim((string) $data['email']),
            'problem' => trim((string) $data['problem']),
            'user_id' => $request->user()?->getAuthIdentifier(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
        ]);

        return back()->with('status', 'Sugestão enviada. Obrigado por ajudar a construir o Prazzu Tools.');
    }
}
