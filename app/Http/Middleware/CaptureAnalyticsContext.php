<?php

namespace App\Http\Middleware;

use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Tools\ToolCatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CaptureAnalyticsContext
{
    public function __construct(
        private AnalyticsContextResolver $contextResolver,
        private PlatformAnalytics $analytics,
        private ToolCatalog $tools,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('analytics.enabled', true) || $this->excluded($request)) return $next($request);
        $context = $this->contextResolver->resolve($request);
        $request->attributes->set('analytics.visitor_id', $context->visitorId);
        $request->attributes->set('analytics.session_id', $context->analyticsSessionId);
        $response = $next($request);

        if ($response->getStatusCode() < 400) {
            if (config('analytics.capture_page_views', true) && $request->isMethod('GET')) $this->analytics->track(AnalyticsEvent::make('page.viewed', 'platform'), $request);
            $this->captureToolEvent($request);
        }

        if ($context->visitorId !== null && ! $request->cookies->has((string) config('analytics.visitor_cookie'))) {
            $response->headers->setCookie(cookie(name:(string)config('analytics.visitor_cookie'),value:$context->visitorId,minutes:(int)config('analytics.visitor_cookie_days',730)*1440,secure:$request->isSecure(),httpOnly:true,raw:false,sameSite:'lax'));
        }
        return $response;
    }

    private function captureToolEvent(Request $request): void
    {
        $routeName = (string) optional($request->route())->getName();
        if (! str_starts_with($routeName, 'tools.')) return;
        $parts = explode('.', $routeName); $slug = $parts[1] ?? null;
        if (! $slug || $this->tools->find($slug) === null) return;
        $action = implode('.', array_slice($parts, 2));
        $name = null;
        if ($request->isMethod('GET') && $action === 'index') $name = 'tool.opened';
        elseif (str_contains($action, 'history') && $request->isMethod('GET')) $name = 'tool.history_viewed';
        elseif (preg_match('/(^|\.)(export|pdf|print)(\.|$)/', $action)) $name = 'tool.exported';
        elseif (str_contains($action, 'share') && ! str_contains($action, 'revoke')) $name = 'tool.shared';
        elseif (str_contains($action, 'plus.')) $name = 'tool.plus_used';
        elseif (! $request->isMethod('GET') && ! in_array($request->method(), ['DELETE','PATCH','PUT'], true)) $name = 'tool.calculation_completed';
        if ($name) $this->analytics->track(new AnalyticsEvent(name:$name,channel:'tool',properties:['route'=>$routeName,'method'=>$request->method()],subjectType:'tool',subjectSlug:$slug),$request);
    }

    private function excluded(Request $request): bool { foreach ((array)config('analytics.excluded_paths',[]) as $pattern) if($request->is($pattern)) return true; return false; }
}
