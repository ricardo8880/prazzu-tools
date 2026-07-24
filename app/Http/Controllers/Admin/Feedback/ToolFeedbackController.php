<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Feedback;

use App\Core\Feedback\Enums\ToolFeedbackStatus;
use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Core\Feedback\Models\ToolFeedback;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Feedback\UpdateToolFeedbackStatusRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ToolFeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'tool' => trim((string) $request->query('tool', '')),
            'type' => trim((string) $request->query('type', '')),
            'status' => trim((string) $request->query('status', '')),
        ];

        $feedback = ToolFeedback::query()
            ->with('user')
            ->when($filters['tool'] !== '', fn ($query) => $query->where('tool_slug', $filters['tool']))
            ->when($filters['type'] !== '', fn ($query) => $query->where('type', $filters['type']))
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.feedback.index', [
            'feedback' => $feedback,
            'filters' => $filters,
            'tools' => ToolFeedback::query()
                ->select(['tool_slug', 'tool_name'])
                ->distinct()
                ->orderBy('tool_name')
                ->get(),
            'types' => ToolFeedbackType::cases(),
            'statuses' => ToolFeedbackStatus::cases(),
        ]);
    }

    public function show(ToolFeedback $toolFeedback): View
    {
        $toolFeedback->load('user');

        return view('admin.feedback.show', [
            'feedback' => $toolFeedback,
            'statuses' => ToolFeedbackStatus::cases(),
        ]);
    }

    public function updateStatus(
        UpdateToolFeedbackStatusRequest $request,
        ToolFeedback $toolFeedback,
    ): RedirectResponse {
        $status = ToolFeedbackStatus::from($request->validated('status'));

        $toolFeedback->forceFill([
            'status' => $status,
            'reviewed_at' => $status === ToolFeedbackStatus::New ? null : now(),
        ])->save();

        return back()->with('status', 'Status do feedback atualizado.');
    }
}
