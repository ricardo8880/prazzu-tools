<?php

namespace Tests\Unit\Logging;

use App\Logging\SensitiveDataProcessor;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\TestCase;

final class SensitiveDataProcessorTest extends TestCase
{
    public function test_it_redacts_sensitive_values_recursively(): void
    {
        $record = new LogRecord(
            datetime: new \DateTimeImmutable,
            channel: 'test',
            level: Level::Info,
            message: 'test',
            context: ['cpf' => '123', 'nested' => ['password' => 'secret'], 'safe' => 'ok'],
        );

        $processed = (new SensitiveDataProcessor)($record);

        self::assertSame('[REDACTED]', $processed->context['cpf']);
        self::assertSame('[REDACTED]', $processed->context['nested']['password']);
        self::assertSame('ok', $processed->context['safe']);
    }
}
