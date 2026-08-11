<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SessionHeartbeatController extends Controller
{
    public function __invoke(Request $request): Response
    {
        // CaptureAnalyticsContext resolves/touches the analytics session before
        // this controller runs. Returning 204 avoids creating a pageview/event.
        return response()->noContent();
    }
}
