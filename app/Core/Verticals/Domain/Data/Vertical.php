<?php

namespace App\Core\Verticals\Domain\Data;

final readonly class Vertical
{
    public function __construct(
        public string $slug,
        public string $name,
        public string $publicSlug = '',
    ) {}
}
