<?php

use App\Core\Tools\Api\Http\Middleware\AuthenticateApiClient;
use App\Core\Tools\Api\Http\Middleware\EnsureApiClientAbility;
use App\Core\Tools\Api\Support\ApiExceptionRenderer;
use App\Http\Middleware\ApplySecurityHeaders;
use App\Http\Middleware\CaptureAnalyticsContext;
use App\Http\Middleware\EnsureAuthenticatedForPersistence;
use App\Http\Middleware\EnsureInternalAdministrator;
use App\Http\Middleware\EnsureToolFeatureAccess;
use App\Http\Middleware\ShareActiveAcquisitionContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/tools-api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(fn (\Throwable $exception, Request $request) => app(ApiExceptionRenderer::class)->render($exception, $request));
    })->create();
