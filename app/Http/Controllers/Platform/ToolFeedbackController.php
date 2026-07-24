<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\Core\Feedback\Application\StoreToolFeedback;
use App\Core\Feedback\Data\ToolFeedbackSubmission;
use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\StoreToolFeedbackRequest;
use Illuminate\Http\JsonResponse;

final class ToolFeedbackController extends Controller
{
    public function store(StoreToolFeedbackRequest $request, StoreToolFeedback $storeToolFeedback): JsonResponse
    {
        $data = $request->validated();

        $context = array_filter([
            'source' => 'right-sidebar',
            'route_name' => $data['route_name'] ?? null,
            'page_title' => filled($data['page_title'] ?? null) ? trim((string) $data['page_title']) : null,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $storeToolFeedback->execute(new ToolFeedbackSubmission(
            toolSlug: (string) $data['tool_slug'],
            type: ToolFeedbackType::from((string) $data['type']),
            message: (string) $data['message'],
            attemptedAction: $data['attempted_action'] ?? null,
            path: (string) $data['path'],
            url: (string) $data['url'],
            userId: ($userId = $request->user()?->getAuthIdentifier()) !== null ? (int) $userId : null,
            sessionId: $request->hasSession() ? $request->session()->getId() : null,
            userAgent: mb_substr((string) $request->userAgent(), 0, 1024),
            context: $context,
        ));

        return response()->json([
            'message' => 'Obrigado! Seu feedback foi enviado para análise.',
        ], 201);
    }
}
