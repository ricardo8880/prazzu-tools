<?php

namespace App\Core\Usage\Data;

final readonly class UsageDecision
{
    public function __construct(
        public bool $allowed,
        public int $remaining,
        public int $retryAfterSeconds = 0,
    ) {}
}
