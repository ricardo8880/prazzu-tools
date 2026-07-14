<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Middleware;

use App\Tools\SimplesNacionalCalculator\Application\Access\SimplesNacionalFeatureGate;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureSimplesNacionalFeatureAccess
{
    public function __construct(private SimplesNacionalFeatureGate $gate) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $resolved = SimplesNacionalFeature::tryFrom($feature);
        abort_if($resolved === null, 404);

        $decision = $this->gate->decide($resolved, $request->user());

        if (! $decision->allowed) {
            return redirect()
                ->route('tools.calculadora-simples-nacional.index')
                ->with('access_warning', 'Este recurso faz parte do plano Plus.');
        }

        return $next($request);
    }
}
