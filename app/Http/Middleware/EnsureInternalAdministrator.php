<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureInternalAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local') && config('blog.admin.allow_local_access', true)) {
            return $next($request);
        }

        abort_unless($request->user()?->isInternalAdministrator(), 403);

        return $next($request);
    }
}
