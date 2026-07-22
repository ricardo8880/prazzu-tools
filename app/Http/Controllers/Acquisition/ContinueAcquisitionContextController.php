<?php

namespace App\Http\Controllers\Acquisition;

use App\Core\Acquisition\Application\Session\AcquisitionContextSession;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ContinueAcquisitionContextController extends Controller
{
    public function __invoke(
        Request $request,
        AcquisitionContextSession $contexts,
        PlatformAnalytics $analytics,
    ): RedirectResponse {
        $context = $contexts->active($request->session());

        if ($context === null) {
            return redirect()->route('tools.index');
        }

        $contexts->continueContext($request->session());
        $destination = route('home');

        $analytics->track(new AnalyticsEvent(
            name: AnalyticsEventName::AcquisitionContextContinued->value,
            channel: 'acquisition',
            properties: [
                'context_keyword' => $context->keyword,
                'context_name' => $context->name,
                'campaign_identifier' => $context->campaignIdentifier,
                'destination' => $destination,
            ],
            subjectType: 'acquisition_context',
            subjectId: $context->id,
            subjectSlug: $context->keyword,
        ), $request);

        return redirect()->to($destination);
    }
}
