<?php

namespace App\Core\Tools\Api\Http\Controllers;

use App\Core\Tools\Api\Auth\ApiClient;
use App\Core\Tools\Api\Http\Middleware\AuthenticateApiClient;
use App\Core\Tools\Api\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ApiStatusController
{
    public function __invoke(Request $request): JsonResponse
    {
        $client = $request->attributes->get(AuthenticateApiClient::REQUEST_ATTRIBUTE);
        return ApiResponse::success([
            'name' => config('tools-api.name'),
            'version' => config('tools-api.version'),
            'status' => 'available',
            'client' => $client instanceof ApiClient ? [
                'id' => $client->id,
                'name' => $client->name,
                'abilities' => $client->abilities,
            ] : null,
        ]);
    }
}
