<?php

namespace App\Core\Tools\History\Services;

final class PayloadProjector
{
    /**
     * @param array<string, mixed> $payload
     * @param array<int, string> $allowedFields
     * @return array<string, mixed>
     */
    public function project(array $payload, array $allowedFields): array
    {
        $projected = [];

        foreach ($allowedFields as $field) {
            $value = data_get($payload, $field, new MissingPayloadValue);
            if ($value instanceof MissingPayloadValue) {
                continue;
            }

            data_set($projected, $field, $value);
        }

        return $projected;
    }
}

final class MissingPayloadValue {}
