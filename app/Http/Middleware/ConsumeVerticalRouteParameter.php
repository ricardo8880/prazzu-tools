<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The public vertical is part of the URL namespace, not a controller argument.
 *
 * ResolveActiveVerticalContext runs before this middleware and persists the
 * resolved vertical in VerticalContext / request attributes. Keeping the raw
 * {vertical} parameter after that point shifts every legacy controller scalar
 * argument (category, slug, run, format, etc.) one position to the right.
 */
final class ConsumeVerticalRouteParameter
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if ($route !== null && $route->hasParameter('vertical')) {
            $publicSlug = $route->parameter('vertical');

            if (is_string($publicSlug) && $publicSlug !== '') {
                $request->attributes->set('vertical.public_slug', $publicSlug);
            }

            $route->forgetParameter('vertical');
        }

        return $next($request);
    }
}
