<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EnableInternalAnalyticsAccessController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect('/')
            ->withCookie(cookie(
                name: (string) config('analytics.internal_access.cookie', 'prazzu_internal_access'),
                value: (string) config('analytics.internal_access.cookie_value', 'enabled'),
                minutes: (int) config('analytics.internal_access.cookie_days', 400) * 1440,
                secure: $request->isSecure(),
                httpOnly: true,
                raw: false,
                sameSite: 'lax',
            ));
    }
}
