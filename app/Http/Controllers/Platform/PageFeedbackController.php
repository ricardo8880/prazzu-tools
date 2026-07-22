<?php

namespace App\Http\Controllers\Platform;

use App\Core\Feedback\Models\PageFeedback;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\StorePageFeedbackRequest;
use Illuminate\Http\JsonResponse;

final class PageFeedbackController extends Controller
{
    public function store(StorePageFeedbackRequest $request): JsonResponse
    {
        $data = $request->validated();

        PageFeedback::query()->create([
            ...$data,
            'comment' => filled($data['comment'] ?? null) ? trim((string) $data['comment']) : null,
            'user_id' => $request->user()?->getAuthIdentifier(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1024),
        ]);

        return response()->json([
            'message' => 'Obrigado! Sua avaliação foi enviada.',
        ], 201);
    }
}
