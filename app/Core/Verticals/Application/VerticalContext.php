<?php

namespace App\Core\Verticals\Application;

use App\Core\Verticals\Domain\Data\Vertical;

final class VerticalContext
{
    private ?Vertical $active = null;

    public function activate(?Vertical $vertical): void
    {
        $this->active = $vertical;
    }

    public function active(): ?Vertical
    {
        return $this->active;
    }

    public function slug(): ?string
    {
        return $this->active?->slug;
    }
}
