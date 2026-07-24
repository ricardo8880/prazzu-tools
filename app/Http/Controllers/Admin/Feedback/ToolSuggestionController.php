<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Core\Feedback\Models\ToolSuggestion;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class ToolSuggestionController extends Controller
{
    public function index(): View
    {
        return view('admin.feedback.suggestions.index', [
            'suggestions' => ToolSuggestion::query()
                ->with('user')
                ->latest()
                ->paginate(25),
        ]);
    }

    public function show(ToolSuggestion $toolSuggestion): View
    {
        $toolSuggestion->load('user');

        return view('admin.feedback.suggestions.show', [
            'suggestion' => $toolSuggestion,
        ]);
    }
}
