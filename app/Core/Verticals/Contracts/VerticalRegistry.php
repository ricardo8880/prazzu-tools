<?php

namespace App\Core\Verticals\Contracts;

use App\Core\Verticals\Domain\Data\Vertical;

interface VerticalRegistry
{
    /** @return list<Vertical> */
    public function all(): array;

    public function find(string $slug): ?Vertical;

    public function default(): ?Vertical;
}
