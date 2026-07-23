<?php

namespace App\Core\Tools\Api\Http\Middleware;

use App\Core\Tools\Api\Auth\ApiClientRegistry;
use App\Core\Tools\Api\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class AuthenticateApiClient
{
    public const REQUEST_ATTRIBUTE = 'prazzu_tools_api_client';

    public function __construct(private ApiClientRegistry $clients) {}

    public function handle(Request $request, Closure $next): Response
    {
        $client = $this->clients->authenticate($request->bearerToken());

        if ($client === null) {
            return ApiResponse::error(
                code: 'invalid_api_credentials',
                message: 'As credenciais da API são inválidas ou não foram informadas.',
                status: 401,
            );
        }

        $request->attributes->set(self::REQUEST_ATTRIBUTE, $client);

        return $next($request);
    }
}
