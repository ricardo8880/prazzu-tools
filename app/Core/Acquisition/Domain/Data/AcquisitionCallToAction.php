<?php

namespace App\Core\Acquisition\Domain\Data;

final readonly class AcquisitionCallToAction
{
    public function __construct(
        public ?string $title,
        public ?string $description,
        public ?string $label,
        public ?string $url,
        public ?string $toolSlug,
    ) {}
}
