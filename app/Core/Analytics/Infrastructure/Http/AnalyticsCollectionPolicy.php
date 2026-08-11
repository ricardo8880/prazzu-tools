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

        $cookieName = (string) config('analytics.internal_access.cookie', 'prazzu_internal_access');
        $cookieValue = (string) config('analytics.internal_access.cookie_value', 'enabled');

        return ! ($cookieName !== '' && hash_equals($cookieValue, (string) $request->cookie($cookieName, '')));
    }
}
