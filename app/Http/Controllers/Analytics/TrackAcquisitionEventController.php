<?php

namespace App\Http\Controllers\Analytics;

use App\Core\Acquisition\Contracts\AcquisitionAnalyticsContextResolver;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class TrackAcquisitionEventController extends Controller
{
    public function __invoke(
        Request $request,
        PlatformAnalytics $analytics,
        AcquisitionAnalyticsContextResolver $contexts,
    ): JsonResponse {
        $context = $contexts->resolve($request);
        abort_if($context === null, 422, 'Nenhum contexto de aquisição ativo foi encontrado.');

        $data = $request->validate([
            'event' => ['required', Rule::in([
                AnalyticsEventName::AcquisitionContextResolved->value,
                AnalyticsEventName::AcquisitionHeroViewed->value,
                AnalyticsEventName::AcquisitionCtaViewed->value,
                AnalyticsEventName::AcquisitionCtaClicked->value,
                AnalyticsEventName::AcquisitionToolImpression->value,
                AnalyticsEventName::AcquisitionToolClicked->value,
            ])],
            'tool_slug' => ['nullable', 'required_if:event,'.AnalyticsEventName::AcquisitionToolImpression->value.','.AnalyticsEventName::AcquisitionToolClicked->value, 'string', 'max:255'],
            'placement' => ['nullable', 'required_if:event,'.AnalyticsEventName::AcquisitionToolImpression->value.','.AnalyticsEventName::AcquisitionToolClicked->value, Rule::in(['primary', 'featured', 'cta'])],
            'position' => ['nullable', 'integer', 'min:1', 'max:100'],
            'destination' => ['nullable', 'string', 'max:2048'],
        ]);

        $analytics->record($data['event'], 'acquisition', $request, array_filter([
            'context_id' => $context->contextId,
            'context_name' => $context->contextName,
            'context_keyword' => $context->keyword,
            'campaign_identifier' => $context->campaignIdentifier,
            'primary_tool_slug' => $context->primaryToolSlug,
            'tool_slug' => $data['tool_slug'] ?? null,
            'placement' => $data['placement'] ?? null,
            'position' => $data['position'] ?? null,
            'destination' => $data['destination'] ?? null,
        ], static fn (mixed $value): bool => $value !== null));

        return response()->json(['recorded' => true]);
    }
}
