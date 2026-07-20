<?php

declare(strict_types=1);

namespace App\Core\Tools\Contracts;

interface ToolCalculationInput
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
