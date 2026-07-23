<?php

namespace App\Core\Tools\Api\Support;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    /** @param array<string, mixed> $data */
    public static function success(array $data = [], int $status = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'data' => $data,
        ];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    /**
     * @param array<string, mixed> $details
     * @param array<string, mixed> $meta
     */
    public static function error(
        string $code,
        string $message,
        int $status,
        array $details = [],
        array $meta = [],
    ): JsonResponse {
        $error = [
            'code' => $code,
            'message' => $message,
        ];

        if ($details !== []) {
            $error['details'] = $details;
        }

        $payload = [
            'success' => false,
            'error' => $error,
        ];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }
}
