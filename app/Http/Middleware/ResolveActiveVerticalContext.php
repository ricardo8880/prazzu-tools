<?php

namespace App\Http\Middleware;

use App\Core\Verticals\Application\ResolveVerticalContext;
use App\Core\Verticals\Application\VerticalContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolveActiveVerticalContext
{
    public function __construct(
        private ResolveVerticalContext $resolver,
        private VerticalContext $context,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $vertical = $this->resolver->execute($request);

        $this->context->activate($vertical);
        $request->attributes->set('vertical.context', $vertical);
        View::share('activeVertical', $vertical);
        Log::withContext(['vertical' => $vertical?->slug ?? 'global']);

        return $next($request);
    }
}
