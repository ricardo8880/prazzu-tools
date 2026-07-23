<?php

namespace App\Core\Tools\Api\Http\Middleware;

use App\Core\Tools\Api\Auth\ApiClient;
use App\Core\Tools\Api\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureApiClientAbility
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $client = $request->attributes->get(AuthenticateApiClient::REQUEST_ATTRIBUTE);

        if (! $client instanceof ApiClient) {
            return ApiResponse::error(
                code: 'invalid_api_credentials',
                message: 'As credenciais da API são inválidas ou não foram informadas.',
                status: 401,
            );
        }

        foreach ($abilities as $ability) {
            if ($client->can($ability)) {
                return $next($request);
            }
        }

        return ApiResponse::error(
            code: 'api_ability_denied',
            message: 'A aplicação não possui permissão para executar esta operação.',
            status: 403,
            details: ['required_abilities' => $abilities],
        );
    }
}
