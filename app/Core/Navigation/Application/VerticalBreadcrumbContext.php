<?php

namespace App\Core\Navigation\Application;

use App\Core\Verticals\Application\VerticalContext;

final readonly class VerticalBreadcrumbContext
{
    public function __construct(private VerticalContext $verticalContext) {}

    /** @return array{slug:?string,name:?string} */
    public function active(): array
    {
        $vertical = $this->verticalContext->active();

        return ['slug' => $vertical?->slug, 'name' => $vertical?->name];
    }
}
