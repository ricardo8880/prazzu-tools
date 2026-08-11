<?php

namespace App\Core\Analytics\Infrastructure\Http;

use Illuminate\Http\Request;

final class AnalyticsCollectionPolicy
{
    public function shouldCollect(?Request $request): bool
    {
        if (! config('analytics.enabled', true)) {
            return false;
        }

        if ($request === null) {
            return true;
        }

        if ($request->user()?->isInternalAdministrator()) {
            return false;
        }

        if ($this->isAutomatedTraffic($request)) {
            return false;
        }

        $cookieName = (string) config('analytics.internal_access.cookie', 'prazzu_internal_access');
        $cookieValue = (string) config('analytics.internal_access.cookie_value', 'enabled');

        return ! ($cookieName !== '' && hash_equals($cookieValue, (string) $request->cookie($cookieName, '')));
    }
    private function isAutomatedTraffic(Request $request): bool
    {
        $userAgent = strtolower((string) $request->userAgent());
        if ($userAgent === '') {
            return false;
        }

        return preg_match('/(?:bot|crawler|spider|slurp|bingpreview|facebookexternalhit|headless|lighthouse|pagespeed|google-inspectiontool|ahrefs|semrush|mj12bot|dotbot|petalbot|bytespider|yandexbot|baiduspider)/i', $userAgent) === 1;
    }
}
