<?php

namespace App\Http\Middleware;

use App\Core\Acquisition\Application\Session\AcquisitionContextSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final readonly class ShareActiveAcquisitionContext
{
    public function __construct(
        private AcquisitionContextSession $contexts,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $context = $request->hasSession()
            ? $this->contexts->active($request->session())
            : null;

        $request->attributes->set('acquisition.context', $context);
        View::share('activeAcquisitionContext', $context);
        View::share('activeAcquisitionContextMode', $request->hasSession()
            ? $this->contexts->mode($request->session())
            : AcquisitionContextSession::MODE_CONTEXTUAL);

        return $next($request);
    }
}
