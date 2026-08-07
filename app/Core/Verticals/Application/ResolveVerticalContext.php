<?php

namespace App\Core\Verticals\Application;

use App\Core\Verticals\Contracts\VerticalContextSource;
use App\Core\Verticals\Domain\Data\Vertical;
use Illuminate\Http\Request;

final readonly class ResolveVerticalContext
{
    /** @param iterable<VerticalContextSource> $sources */
    public function __construct(
        private iterable $sources,
    ) {}

    public function execute(Request $request): ?Vertical
    {
        foreach ($this->sources as $source) {
            $vertical = $source->resolve($request);

            if ($vertical !== null) {
                return $vertical;
            }
        }

        return null;
    }
}
