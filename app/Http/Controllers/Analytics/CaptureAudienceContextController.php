<?php

namespace App\Http\Controllers\Analytics;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CaptureAudienceContextController extends Controller
{
    public function __invoke(Request $request, PlatformAnalytics $analytics): JsonResponse
    {
        $data = $request->validate([
            'timezone' => ['nullable', 'string', 'max:80'],
            'screen_resolution' => ['nullable', 'regex:/^\d{2,5}x\d{2,5}$/'],
            'language' => ['nullable', 'string', 'max:20'],
        ]);

        foreach (['timezone' => 'X-Timezone', 'screen_resolution' => 'X-Screen-Resolution', 'language' => 'X-Analytics-Language'] as $key => $header) {
            if (! empty($data[$key])) {
                $request->headers->set($header, (string) $data[$key]);
            }
        }

        $analytics->track(new AnalyticsEvent(
            name: 'audience.context_captured',
            channel: 'audience',
            properties: array_filter($data, fn ($value) => $value !== null && $value !== ''),
        ), $request);

        return response()->json(['captured' => true]);
    }
}
