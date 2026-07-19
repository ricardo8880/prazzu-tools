<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Data;

final readonly class ToolRunPage
{
    /** @param list<ToolRunEntry> $items */
    public function __construct(
        public array $items,
        public int $page,
        public int $perPage,
        public int $total,
        public int $lastPage,
    ) {}

    public function hasMorePages(): bool
    {
        return $this->page < $this->lastPage;
    }
}
