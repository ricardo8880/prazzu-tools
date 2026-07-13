<?php

namespace App\Http\Controllers\Platform;

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
        $request->validated();

        return back()->with('status', 'Sugestão enviada. Obrigado por ajudar a construir o Prazzu Tools.');
    }
}
