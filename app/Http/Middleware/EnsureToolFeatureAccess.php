<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Tools\ToolRegistry;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureToolFeatureAccess
{
    public function __construct(
        private ToolRegistry $tools,
        private ToolFeatureAccessGate $gate,
    ) {}

    public function handle(Request $request, Closure $next, string $toolSlug, string $featureKey): Response
    {
        $manifest = $this->tools->findManifest($toolSlug);
        abort_if($manifest === null || $manifest->feature($featureKey) === null, 404);

        $decision = $this->gate->decide($manifest, $featureKey, $request->user());

        if ($decision->allowed) {
            return $next($request);
        }

        if (in_array($decision->reason, ['tool.feature_disabled', 'tool.status_blocks_execution', 'feature.disabled'], true)) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'message' => 'Este recurso está temporariamente indisponível.',
                    'reason' => $decision->reason,
                ], 503);
            }

            abort(503, 'Este recurso está temporariamente indisponível.');
        }

        $message = match ($decision->reason) {
            'feature.authentication_required' => 'Entre na sua conta e assine o Prazzu Plus para usar este recurso avançado.',
            'feature.plus_required' => 'Este recurso avançado faz parte do Prazzu Plus.',
            default => 'Este recurso não está disponível no momento.',
        };

        if ($request->expectsJson()) {
            return new JsonResponse(['message' => $message, 'reason' => $decision->reason], 403);
        }

        $response = redirect()
            ->route($manifest->routeName)
            ->with('access_warning', $message);
        $response->headers->set('X-Prazzu-Access-Reason', $decision->reason);

        return $response;
    }
}
