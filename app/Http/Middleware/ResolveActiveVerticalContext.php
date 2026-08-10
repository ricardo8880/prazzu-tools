<?php

namespace App\Http\Middleware;

use App\Core\Verticals\Application\ResolveVerticalContext;
use App\Core\Verticals\Application\Session\VerticalContextSession;
use App\Core\Verticals\Application\VerticalContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolveActiveVerticalContext
{
    public function __construct(
        private ResolveVerticalContext $resolver,
        private VerticalContext $context,
        private VerticalContextSession $sessionContext,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Validate persisted context before route resolution. If a stale value
        // existed, VerticalContextSession::active() clears it. In that request we
        // must not immediately recreate the session from the canonical route.
        $hadPersistedContext = false;
        $persistedVertical = null;

        if ($request->hasSession()) {
            $sessionKey = (string) config('verticals.session_key', 'vertical.context');
            $hadPersistedContext = $request->session()->has($sessionKey);
            $persistedVertical = $this->sessionContext->active($request->session());
        }

        $vertical = $this->resolver->execute($request);

        $this->context->activate($vertical);
        $request->attributes->set('vertical.context', $vertical);
        View::share('activeVertical', $vertical);
        Log::withContext(['vertical' => $vertical?->slug ?? 'global']);

        if ($vertical !== null) {
            URL::defaults(['vertical' => $vertical->publicSlug]);

            if ($request->route('vertical') !== null
                && $request->hasSession()
                && (! $hadPersistedContext || $persistedVertical !== null)) {
                $this->sessionContext->activate($request->session(), $vertical);
            }
        }

        return $next($request);
    }
}
