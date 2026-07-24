<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Core\Feedback\Models\PageFeedback;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class PageFeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'rating' => trim((string) $request->query('rating', '')),
            'path' => trim((string) $request->query('path', '')),
        ];

        $feedback = PageFeedback::query()
            ->with('user')
            ->when($filters['rating'] !== '', fn ($query) => $query->where('rating', (int) $filters['rating']))
            ->when($filters['path'] !== '', fn ($query) => $query->where('path', 'like', '%'.$filters['path'].'%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.feedback.pages.index', [
            'feedback' => $feedback,
            'filters' => $filters,
        ]);
    }

    public function show(PageFeedback $pageFeedback): View
    {
        $pageFeedback->load('user');

        return view('admin.feedback.pages.show', [
            'feedback' => $pageFeedback,
        ]);
    }
}
