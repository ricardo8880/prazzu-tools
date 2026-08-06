<?php

declare(strict_types=1);

namespace App\Core\Quality\E2E\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\JsonFormatter;

final class ConfigureE2EJsonLogging
{
    public function __invoke(Logger $logger): void
    {
        $formatter = new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, true, true, true);

        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($formatter);
        }
    }
}
