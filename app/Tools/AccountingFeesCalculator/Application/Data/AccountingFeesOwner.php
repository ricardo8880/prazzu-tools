<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Data;

final readonly class AccountingFeesOwner
{
    public function __construct(
        public ?int $userId,
        public string $sessionKey,
    ) {}
}
