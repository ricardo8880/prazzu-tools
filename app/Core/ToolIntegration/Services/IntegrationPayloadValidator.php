<?php

namespace App\Core\ToolIntegration\Services;

use App\Core\ToolIntegration\Data\IntegrationContract;
use App\Core\ToolIntegration\Data\IntegrationField;
use App\Core\ToolIntegration\Data\IntegrationPayload;
use App\Core\ToolIntegration\Exceptions\InvalidIntegrationPayload;
use DateTimeInterface;

final class IntegrationPayloadValidator
{
    public function validate(IntegrationPayload $payload, IntegrationContract $contract): void
    {
        $knownFields = [];

        foreach ($contract->fields as $field) {
            $knownFields[$field->name] = true;

            if ($field->required && ! array_key_exists($field->name, $payload->data)) {
                throw new InvalidIntegrationPayload("O campo obrigatório [{$field->name}] não foi informado.");
            }

            if (array_key_exists($field->name, $payload->data) && ! $this->matches($payload->data[$field->name], $field)) {
                throw new InvalidIntegrationPayload("O campo [{$field->name}] não corresponde ao tipo [{$field->type}].");
            }
        }

        foreach (array_keys($payload->data) as $fieldName) {
            if (! isset($knownFields[$fieldName])) {
                throw new InvalidIntegrationPayload("O campo [{$fieldName}] não pertence ao contrato [{$contract->key()}].");
            }
        }
    }

    private function matches(mixed $value, IntegrationField $field): bool
    {
        if ($value === null) {
            return ! $field->required;
        }

        return match ($field->type) {
            'string' => is_string($value),
            'integer' => is_int($value),
            'float' => is_float($value) || is_int($value),
            'boolean' => is_bool($value),
            'array' => is_array($value),
            'date', 'datetime' => is_string($value) || $value instanceof DateTimeInterface,
            default => false,
        };
    }
}
