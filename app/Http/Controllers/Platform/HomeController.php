<?php

namespace App\Http\Controllers\Platform;

use App\Core\Acquisition\Application\Home\BuildContextualHome;
use App\Core\Acquisition\Application\ResolveAcquisitionContext;
use App\Core\Acquisition\Application\Session\AcquisitionContextSession;
use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(
        Request $request,
        BuildContextualHome $home,
        ResolveAcquisitionContext $resolveContext,
        AcquisitionContextSession $contextSession,
        PlatformAnalytics $analytics,
    ): View|RedirectResponse {
        $queryContext = $request->query('context');
        $context = null;

        if (is_string($queryContext) && trim($queryContext) !== '') {
            $context = $resolveContext->execute($queryContext);

            if ($context !== null) {
                $contextSession->activate($request->session(), $context);
                $analytics->track(new AnalyticsEvent(
                    name: AnalyticsEventName::AcquisitionContextEntered->value,
                    channel: 'acquisition',
                    properties: [
                        'context_keyword' => $context->keyword,
                        'context_name' => $context->name,
                        'campaign_identifier' => $context->campaignIdentifier,
                    ],
                    subjectType: 'acquisition_context',
                    subjectId: $context->id,
                    subjectSlug: $context->keyword,
                ), $request);
            }

            return redirect()->route('home');
        }

        if (! $context instanceof AcquisitionContext) {
            $context = $request->attributes->get('acquisition.context');
        }

        if (! $context instanceof AcquisitionContext) {
            $context = $contextSession->active($request->session());
        }

        $request->attributes->set('acquisition.context', $context);
        ViewFacade::share('activeAcquisitionContext', $context);
        ViewFacade::share('activeAcquisitionContextMode', $contextSession->mode($request->session()));

        return view('welcome', $home->execute(
            keyword: $context !== null && $contextSession->mode($request->session()) === AcquisitionContextSession::MODE_CONTEXTUAL
                ? $context->keyword
                : null,
            defaultHome: config('home'),
        ));
    }
}
