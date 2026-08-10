<?php

namespace App\Http\Middleware;

use App\Core\Tools\ToolRegistry;
use App\Core\Verticals\Application\VerticalContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureToolBelongsToActiveVertical
{
    public function __construct(
        private ToolRegistry $tools,
        private VerticalContext $verticalContext,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $segments = is_string($routeName) ? explode('.', $routeName) : [];

        // Canonical routes are tools.{tool-slug}.*. Legacy aliases are
        // legacy.tools.{tool-slug}.* and use the already-resolved default/session context.
        $toolSlug = null;
        if (($segments[0] ?? null) === 'tools') {
            $toolSlug = $segments[1] ?? null;
        } elseif (($segments[0] ?? null) === 'legacy' && ($segments[1] ?? null) === 'tools') {
            $toolSlug = $segments[2] ?? null;
        }

        if (is_string($toolSlug) && ! in_array($toolSlug, ['index', 'category'], true)) {
            $manifest = $this->tools->findManifest($toolSlug);
            $active = $this->verticalContext->active();
            $rejected = $manifest === null || $active === null || $manifest->vertical !== $active->slug;

            if ($rejected) {
                Log::warning('tools.vertical_rejected', [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'route_name' => $routeName,
                    'route_parameters' => $request->route()?->parameters() ?? [],
                    'public_vertical' => $request->attributes->get('vertical.public_slug'),
                    'active_vertical' => $active?->slug,
                    'tool_slug' => $toolSlug,
                    'tool_registered' => $manifest !== null,
                    'tool_vertical' => $manifest?->vertical,
                ]);

                abort(404);
            }
        }

        return $next($request);
    }
}
