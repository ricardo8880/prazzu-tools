<?php

namespace App\Http\Middleware;

use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Domain\Services\ToolAnalyticsEventClassifier;
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
        private ToolAnalyticsEventClassifier $toolEvents,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('analytics.enabled', true) || $this->excluded($request) || $this->isPrefetchRequest($request)) {
            return $next($request);
        }
        $context = $this->contextResolver->resolve($request);
        $request->attributes->set('analytics.visitor_id', $context->visitorId);
        $request->attributes->set('analytics.session_id', $context->analyticsSessionId);
        $response = $next($request);

        if ($response->getStatusCode() < 400) {
            if (config('analytics.capture_page_views', true) && $this->isPageViewRequest($request)) {
                $this->analytics->track(AnalyticsEvent::make(AnalyticsEventName::PageViewed->value, 'platform'), $request);
            }
            $this->captureToolEvent($request, $response);
        }

        if ($context->visitorId !== null && ! $request->cookies->has((string) config('analytics.visitor_cookie'))) {
            $response->headers->setCookie(cookie(name: (string) config('analytics.visitor_cookie'), value: $context->visitorId, minutes: (int) config('analytics.visitor_cookie_days', 730) * 1440, secure: $request->isSecure(), httpOnly: true, raw: false, sameSite: 'lax'));
        }

        return $response;
    }

    private function isPageViewRequest(Request $request): bool
    {
        if (! $request->isMethod('GET') || $request->expectsJson() || $request->ajax()) {
            return false;
        }

        if ($this->isPrefetchRequest($request)) {
            return false;
        }

        $destination = strtolower((string) $request->header('Sec-Fetch-Dest'));

        return $destination === '' || $destination === 'document';
    }


    private function isPrefetchRequest(Request $request): bool
    {
        return strtolower((string) $request->header('Purpose')) === 'prefetch'
            || strtolower((string) $request->header('Sec-Purpose')) === 'prefetch';
    }

    private function captureToolEvent(Request $request, Response $response): void
    {
        $routeName = (string) optional($request->route())->getName();
        if (! str_starts_with($routeName, 'tools.')) {
            return;
        }
        $parts = explode('.', $routeName);
        $slug = $parts[1] ?? null;
        if (! $slug || $this->tools->find($slug) === null) {
            return;
        }
        $action = implode('.', array_slice($parts, 2));
        $eventName = $this->toolEvents->classify($action, $request->method());

        if ($eventName === AnalyticsEventName::ToolCalculationCompleted && ! $this->deliveredToolResult($request, $response)) {
            return;
        }

        if ($eventName === AnalyticsEventName::ToolResultExported && ! $this->deliveredToolExport($request, $response)) {
            return;
        }

        if ($eventName !== null) {
            $properties = ['route' => $routeName, 'method' => $request->method()];
            if ($eventName === AnalyticsEventName::ToolResultExported) {
                $properties['export_format'] = $this->exportFormat($action, $response);
            }

            $this->analytics->track(new AnalyticsEvent(
                name: $eventName->value,
                channel: 'tool',
                properties: $properties,
                subjectType: 'tool',
                subjectSlug: $slug,
            ), $request);
        }
    }



    private function deliveredToolExport(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() >= 300) {
            return false;
        }

        if ($request->hasSession()) {
            $newFlashKeys = (array) $request->session()->get('_flash.new', []);
            if (in_array('errors', $newFlashKeys, true)) {
                return false;
            }
        }

        // Empty successful responses do not represent a file/document delivered.
        if ($response->getStatusCode() === 204) {
            return false;
        }

        return true;
    }

    private function exportFormat(string $action, Response $response): string
    {
        foreach (['pdf', 'csv', 'xlsx', 'xls', 'docx', 'json', 'xml', 'zip', 'print'] as $format) {
            if (preg_match('/(^|\.)'.preg_quote($format, '/').'($|\.)/', $action) === 1) {
                return $format;
            }
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type'));
        $formats = [
            'application/pdf' => 'pdf',
            'text/csv' => 'csv',
            'spreadsheetml' => 'xlsx',
            'ms-excel' => 'xls',
            'wordprocessingml' => 'docx',
            'application/json' => 'json',
            'application/xml' => 'xml',
            'text/xml' => 'xml',
            'application/zip' => 'zip',
        ];

        foreach ($formats as $needle => $format) {
            if (str_contains($contentType, $needle)) {
                return $format;
            }
        }

        return 'unknown';
    }

    private function deliveredToolResult(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() >= 400) {
            return false;
        }

        if (! $request->hasSession()) {
            return true;
        }

        // Laravel validation redirects are HTTP 302 as well. Only errors flashed
        // by this response indicate that the calculation did not deliver a result;
        // errors left from the previous request must not suppress a later success.
        $newFlashKeys = (array) $request->session()->get('_flash.new', []);

        return ! in_array('errors', $newFlashKeys, true);
    }

    private function excluded(Request $request): bool
    {
        foreach ((array) config('analytics.excluded_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
