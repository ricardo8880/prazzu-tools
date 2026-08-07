<?php

namespace App\Core\Verticals\Infrastructure\Http;

use App\Core\Verticals\Contracts\VerticalContextSource;
use App\Core\Verticals\Contracts\VerticalRegistry;
use App\Core\Verticals\Domain\Data\Vertical;
use Illuminate\Http\Request;

final readonly class DefaultVerticalContextSource implements VerticalContextSource
{
    public function __construct(
        private VerticalRegistry $verticals,
    ) {}

    public function resolve(Request $request): ?Vertical
    {
        return $this->verticals->default();
    }
}
