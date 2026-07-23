<?php

namespace App\Core\Tools\Api\Services;

use App\Core\Tools\Api\Exceptions\UnsupportedToolApiResult;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class ToolApiResultNormalizer
{
    /** @return array<string, mixed> */
    public function normalize(mixed $result): array
    {
        if ($result instanceof Arrayable) {
            $result = $result->toArray();
        } elseif ($result instanceof JsonSerializable) {
            $result = $result->jsonSerialize();
        }

        if (is_array($result)) {
            return $result;
        }

        if ($result === null) {
            return [];
        }

        throw UnsupportedToolApiResult::from($result);
    }
}
