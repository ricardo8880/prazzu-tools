<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class LogExportRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isExportRequest($request)) {
            return $next($request);
        }

        $context = $this->context($request);
        Log::info('Exportação iniciada.', $context);

        try {
            /** @var Response $response */
            $response = $next($request);
        } catch (ValidationException $exception) {
            Log::warning('Exportação rejeitada por validação.', [
                ...$context,
                'errors' => $exception->errors(),
                'input_keys' => array_keys($request->except(['_token', 'password', 'password_confirmation'])),
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Falha ao gerar arquivo de exportação.', [
                ...$context,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            throw $exception;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        $disposition = (string) $response->headers->get('Content-Disposition', '');
        $isFile = str_contains(strtolower($disposition), 'attachment')
            || str_contains($contentType, 'application/pdf')
            || str_contains($contentType, 'spreadsheet')
            || str_contains($contentType, 'application/octet-stream')
            || str_contains($contentType, 'text/csv');

        $logContext = [
            ...$context,
            'status' => $response->getStatusCode(),
            'content_type' => $contentType,
            'content_disposition' => $disposition,
            'is_file_response' => $isFile,
        ];

        if ($isFile) {
            Log::info('Exportação concluída com resposta de arquivo.', $logContext);
        } else {
            Log::warning('Exportação terminou sem retornar arquivo.', $logContext);
        }

        return $response;
    }

    private function isExportRequest(Request $request): bool
    {
        $routeName = (string) ($request->route()?->getName() ?? '');
        $path = strtolower($request->path());

        return preg_match('/(?:export|download|baixar|pdf|excel|xlsx|csv)/i', $routeName.' '.$path) === 1;
    }

    /** @return array<string, mixed> */
    private function context(Request $request): array
    {
        return [
            'request_id' => $request->headers->get('X-Request-ID') ?: bin2hex(random_bytes(8)),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'user_id' => $request->user()?->getAuthIdentifier(),
            'ip' => $request->ip(),
            'expects_json' => $request->expectsJson(),
        ];
    }
}
