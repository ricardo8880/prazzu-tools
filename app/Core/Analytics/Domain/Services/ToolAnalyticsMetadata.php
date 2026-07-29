<?php

declare(strict_types=1);

namespace App\Core\Analytics\Domain\Services;

final class ToolAnalyticsMetadata
{
    /** @var list<string> */
    public const ALLOWED_KEYS = [
        'journey_id', 'form', 'step', 'field', 'action', 'completion_percentage',
        'filled_fields', 'total_fields', 'validation_error', 'execution_time_ms',
        'calculation_success', 'time_spent_seconds', 'abandoned_after_seconds', 'export_format', 'share_method',
    ];

    /** @param array<string, mixed> $metadata
     *  @return array<string, bool|int|string>
     */
    public function sanitize(array $metadata): array
    {
        $safe = [];

        foreach (self::ALLOWED_KEYS as $key) {
            $value = $metadata[$key] ?? null;

            if (is_string($value)) {
                $value = trim($value);
                if ($value !== '') {
                    $safe[$key] = substr($value, 0, 120);
                }
            } elseif (is_int($value) || is_bool($value)) {
                $safe[$key] = $value;
            }
        }

        return $safe;
    }
}
