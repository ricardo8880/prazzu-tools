<?php

namespace App\Core\Acquisition\Domain\Data;

final readonly class AcquisitionHero
{
    public function __construct(
        public ?string $titleBefore,
        public ?string $titleLine,
        public ?string $titleHighlight,
        public ?string $description,
        public ?string $searchPlaceholder,
    ) {}
}
