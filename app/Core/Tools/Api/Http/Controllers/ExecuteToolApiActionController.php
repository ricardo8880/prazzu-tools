<?php

namespace App\Core\Tools\Api\Http\Controllers;

use App\Core\Tools\Api\Auth\ApiClient;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Core\Tools\Api\Http\Middleware\AuthenticateApiClient;
use App\Core\Tools\Api\Services\ToolApiExecutor;
use App\Core\Tools\Api\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class ExecuteToolApiActionController
{
    public function __construct(private ToolApiExecutor $executor) {}

    public function __invoke(Request $request, string $tool, string $action): JsonResponse
    {
        $client = $request->attributes->get(AuthenticateApiClient::REQUEST_ATTRIBUTE);

        abort_unless($client instanceof ApiClient, 401);

        $result = $this->executor->execute(
            tool: $tool,
            action: $action,
            input: $request->all(),
            context: new ToolExecutionContext(
                client: $client,
                userId: $this->optionalHeader($request, 'X-Prazzu-User-ID'),
                metadata: [
                    'request_id' => $this->optionalHeader($request, 'X-Request-ID'),
                    'ip' => $request->ip(),
                ],
            ),
        );

        return ApiResponse::success($result);
    }

    private function optionalHeader(Request $request, string $header): ?string
    {
        $value = trim((string) $request->header($header, ''));

        return $value === '' ? null : $value;
    }
}
