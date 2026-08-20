<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Feedback\Application\StoreToolFeedback;
use App\Core\Feedback\Application\StoreToolResolutionFeedback;
use App\Core\Feedback\Data\ToolFeedbackSubmission;
use App\Core\Feedback\Data\ToolResolutionSubmission;
use App\Core\Feedback\Enums\ToolFeedbackType;
use App\Core\Feedback\Enums\ToolResolution;
use App\Core\Feedback\Enums\ToolResolutionReason;
use App\Core\Tools\ToolRegistry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\StoreToolFeedbackRequest;
use Illuminate\Http\JsonResponse;

final class ToolFeedbackController extends Controller
{
    public function store(
        StoreToolFeedbackRequest $request,
        StoreToolFeedback $storeToolFeedback,
        ToolRegistry $tools,
        PlatformAnalytics $analytics,
        StoreToolResolutionFeedback $storeToolResolutionFeedback,
    ): JsonResponse {
        $data = $request->validated();

        if (($data['feedback_kind'] ?? 'qualitative') === 'resolution') {
            return $this->storeResolution($request, $data, $tools, $analytics, $storeToolResolutionFeedback);
        }

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

    /** @param array<string, mixed> $data */
    private function storeResolution(
        StoreToolFeedbackRequest $request,
        array $data,
        ToolRegistry $tools,
        PlatformAnalytics $analytics,
        StoreToolResolutionFeedback $storeToolResolutionFeedback,
    ): JsonResponse {
        $manifest = $tools->findManifest((string) $data['tool_slug']);
        abort_if($manifest === null, 404);

        $resolution = ToolResolution::from((string) $data['resolution']);
        $reason = in_array($resolution, [ToolResolution::Partially, ToolResolution::No], true)
            ? ToolResolutionReason::from((string) $data['reason'])
            : null;

        $storeToolResolutionFeedback->execute(new ToolResolutionSubmission(
            toolSlug: $manifest->slug,
            resolution: $resolution,
            reason: $reason,
            comment: filled($data['comment'] ?? null) ? (string) $data['comment'] : null,
            path: (string) $data['path'],
            url: (string) $data['url'],
            userId: ($userId = $request->user()?->getAuthIdentifier()) !== null ? (int) $userId : null,
            sessionId: $request->hasSession() ? $request->session()->getId() : null,
            userAgent: mb_substr((string) $request->userAgent(), 0, 1024),
        ));

        try {
            $analytics->track(new AnalyticsEvent(
                name: AnalyticsEventName::ToolResolutionSubmitted->value,
                channel: 'tool',
                properties: array_filter([
                    'resolution' => $resolution->value,
                    'reason' => $reason?->value,
                ], static fn (mixed $value): bool => $value !== null && $value !== ''),
                subjectType: 'tool',
                subjectSlug: $manifest->slug,
            ), $request);
        } catch (\Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'message' => $resolution === ToolResolution::Yes
                ? 'Obrigado! Que bom saber que a ferramenta resolveu seu problema.'
                : 'Obrigado! Sua resposta vai ajudar a melhorar esta ferramenta.',
        ], 201);
    }

}
