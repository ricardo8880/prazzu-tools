<?php

use App\Core\Quality\E2E\Http\Middleware\CorrelateE2ERequest;
use App\Core\Tools\Api\Http\Middleware\AuthenticateApiClient;
use App\Core\Tools\Api\Http\Middleware\EnsureApiClientAbility;
use App\Core\Tools\Api\Support\ApiExceptionRenderer;
use App\Http\Middleware\ApplySecurityHeaders;
use App\Http\Middleware\CaptureAnalyticsContext;
use App\Http\Middleware\EnsureAuthenticatedForPersistence;
use App\Http\Middleware\EnsureInternalAdministrator;
use App\Http\Middleware\EnsureTabularImportFeatureAccess;
use App\Http\Middleware\EnsureToolFeatureAccess;
use App\Http\Middleware\LogExportRequests;
use App\Http\Middleware\ShareActiveAcquisitionContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/tools-api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(CorrelateE2ERequest::class);

        $middleware->web(append: [
            LogExportRequests::class,
            ApplySecurityHeaders::class,
            ShareActiveAcquisitionContext::class,
            CaptureAnalyticsContext::class,
        ]);

        $middleware->alias([
            'api.client' => AuthenticateApiClient::class,
            'api.ability' => EnsureApiClientAbility::class,
            'internal.admin' => EnsureInternalAdministrator::class,
            'persistence.auth' => EnsureAuthenticatedForPersistence::class,
            'tool.feature' => EnsureToolFeatureAccess::class,
            'tool.import-feature' => EnsureTabularImportFeatureAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $exception): void {
            if (! app()->environment('e2e')) {
                return;
            }

            $request = app()->bound('request') ? request() : null;

            Log::channel('single')->error('e2e.unhandled_exception', [
                'method' => $request?->method(),
                'url' => $request?->fullUrl(),
                'path' => $request?->path(),
                'route' => $request?->route()?->getName(),
                'user_id' => $request?->user()?->getAuthIdentifier(),
                'e2e_run_id' => $request?->attributes->get('e2e_run_id'),
                'e2e_scenario_id' => $request?->attributes->get('e2e_scenario_id'),
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
                'exception' => $exception,
            ]);
        });

        $exceptions->render(fn (Throwable $exception, Request $request) => app(ApiExceptionRenderer::class)->render($exception, $request));
    })->create();
