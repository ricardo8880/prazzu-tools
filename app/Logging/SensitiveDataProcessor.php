<?php

namespace App\Logging;

use Monolog\LogRecord;

final class SensitiveDataProcessor
{
    /** @var list<string> */
    private array $sensitiveKeys;

    public function __construct()
    {
        $this->sensitiveKeys = array_map(
            static fn (mixed $key): string => strtolower((string) $key),
            (array) config('operations.sensitive_log_keys', [])
        );
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(
            context: $this->sanitize($record->context),
            extra: $this->sanitize($record->extra),
        );
    }

    /** @param array<array-key, mixed> $data
     *  @return array<array-key, mixed>
     */
    private function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), $this->sensitiveKeys, true)) {
                $data[$key] = '[REDACTED]';

                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            }
        }

        return $data;
    }
}
