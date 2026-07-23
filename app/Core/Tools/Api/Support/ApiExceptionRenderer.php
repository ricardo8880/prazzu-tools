<?php

namespace App\Core\Tools\Api\Support;

use App\Core\Tools\Api\Exceptions\ToolApiActionNotFound;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class ApiExceptionRenderer
{
    public function render(Throwable $exception, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        if ($exception instanceof ToolApiActionNotFound) {
            return ApiResponse::error(
                code: 'tool_action_not_found',
                message: 'A ferramenta ou ação solicitada não está disponível na API.',
                status: 404,
            );
        }

        if ($exception instanceof ValidationException) {
            return ApiResponse::error(
                code: 'validation_failed',
                message: 'Os dados enviados são inválidos.',
                status: $exception->status,
                details: ['fields' => $exception->errors()],
            );
        }

        if ($exception instanceof AuthenticationException) {
            return ApiResponse::error(
                code: 'unauthenticated',
                message: 'Autenticação necessária.',
                status: 401,
            );
        }

        if ($exception instanceof AuthorizationException) {
            return ApiResponse::error(
                code: 'forbidden',
                message: 'A operação não foi autorizada.',
                status: 403,
            );
        }

        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();

            return ApiResponse::error(
                code: $this->httpErrorCode($status),
                message: $this->httpErrorMessage($status),
                status: $status,
            );
        }

        return ApiResponse::error(
            code: 'internal_error',
            message: 'Não foi possível processar a solicitação.',
            status: 500,
        );
    }

    private function httpErrorCode(int $status): string
    {
        return match ($status) {
            404 => 'resource_not_found',
            405 => 'method_not_allowed',
            429 => 'rate_limit_exceeded',
            default => 'http_error',
        };
    }

    private function httpErrorMessage(int $status): string
    {
        return match ($status) {
            404 => 'O recurso solicitado não foi encontrado.',
            405 => 'O método HTTP não é permitido para este recurso.',
            429 => 'O limite de solicitações foi excedido.',
            default => 'A solicitação não pôde ser processada.',
        };
    }
}
