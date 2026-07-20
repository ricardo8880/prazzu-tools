<?php

namespace Tests\Unit\Logging;

use App\Logging\SanitizeLogContext;
use Illuminate\Log\Logger as IlluminateLogger;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Tests\TestCase;

final class SanitizeLogContextTest extends TestCase
{
    public function test_it_registers_the_sensitive_data_processor_on_the_logger(): void
    {
        $handler = new TestHandler;
        $monolog = new MonologLogger('test', [$handler]);
        $logger = new IlluminateLogger($monolog);

        (new SanitizeLogContext)($logger);

        $logger->info('test', [
            'cpf' => '123',
            'nested' => ['password' => 'secret'],
            'safe' => 'ok',
        ]);

        self::assertTrue($handler->hasRecordThatPasses(
            static fn ($record): bool => $record->level === Level::Info
                && $record->context['cpf'] === '[REDACTED]'
                && $record->context['nested']['password'] === '[REDACTED]'
                && $record->context['safe'] === 'ok',
            Level::Info,
        ));
    }
}
