<?php

namespace App\Core\Usage\Data;

use InvalidArgumentException;

final readonly class UsageLimit
{
    public function __construct(
        public int $maxExecutions,
        public int $windowSeconds,
    ) {
        if ($this->maxExecutions < 1 || $this->windowSeconds < 1) {
            throw new InvalidArgumentException('Limite e janela de uso devem ser maiores que zero.');
        }
    }
}
