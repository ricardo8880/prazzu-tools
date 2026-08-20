<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Contracts;

use DateTimeImmutable;

interface ProvidesHistoryContext
{
    /** @param array<string, mixed> $input */
    public function historyContext(array $input, DateTimeImmutable $referenceDate): ?string;
}
