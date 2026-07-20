<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApplySecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        foreach ((array) config('operations.security_headers', []) as $name => $value) {
            if (is_string($value) && $value !== '') {
                $response->headers->set((string) $name, $value);
            }
        }

        if ((bool) config('operations.content_security_policy.enabled', false)) {
            $policy = (string) config('operations.content_security_policy.value', '');

            if ($policy !== '') {
                $response->headers->set('Content-Security-Policy', $policy);
            }
        }

        if ($request->isSecure()) {
            $hsts = (string) config('operations.hsts', '');
            if ($hsts !== '') {
                $response->headers->set('Strict-Transport-Security', $hsts);
            }
        }

        return $response;
    }
}
