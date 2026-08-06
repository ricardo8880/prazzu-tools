<?php

declare(strict_types=1);

namespace App\Core\Quality\E2E\Http\Middleware;

use App\Core\Quality\E2E\Support\E2ECorrelation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CorrelateE2ERequest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->enabled()) {
            return $next($request);
        }

        $context = E2ECorrelation::fromRequest($request);
        $startedAt = hrtime(true);
        Log::withContext($context);
        $request->attributes->add($context);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            Log::channel('e2e')->error('e2e.request.exception', $context + [
                'method' => $request->method(),
                'path' => $request->path(),
                'exception' => $exception::class,
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
                'duration_ms' => $this->durationMs($startedAt),
            ]);

            throw $exception;
        }

        $response->headers->set(E2ECorrelation::RUN_HEADER, $context['e2e_run_id']);
        $response->headers->set(E2ECorrelation::SCENARIO_HEADER, $context['e2e_scenario_id']);

        $completionContext = $context + [
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => $request->route()?->getName(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $this->durationMs($startedAt),
            'user_id' => $request->user()?->getAuthIdentifier(),
        ];

        Log::channel('e2e')->info('e2e.request.completed', $completionContext);

        if ($response->getStatusCode() >= 500) {
            Log::channel('single')->error('e2e.http.server_error', $completionContext + [
                'content_type' => $response->headers->get('Content-Type'),
            ]);
        }

        return $response;
    }

    private function enabled(): bool
    {
        return app()->environment('e2e') && (bool) config('e2e_observability.enabled', false);
    }

    private function durationMs(int $startedAt): float
    {
        return round((hrtime(true) - $startedAt) / 1_000_000, 3);
    }
}
