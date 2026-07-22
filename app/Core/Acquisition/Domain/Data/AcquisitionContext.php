<?php

namespace App\Core\Acquisition\Domain\Data;

use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;

final readonly class AcquisitionContext
{
    /**
     * @param list<string> $featuredToolSlugs
     * @param list<string> $recommendedToolSlugs
     * @param list<string> $articleSlugs
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $keyword,
        public AcquisitionContextStatus $status,
        public ?string $campaignIdentifier,
        public AcquisitionHero $hero,
        public AcquisitionCallToAction $callToAction,
        public ?string $contextualMessage,
        public ?string $contextualContinueLabel,
        public ?string $contextualContinueUrl,
        public ?string $contextualContinueToolSlug,
        public ?string $toolsSectionTitle,
        public ?string $primaryToolSlug,
        public array $featuredToolSlugs,
        public array $recommendedToolSlugs,
        public array $articleSlugs,
    ) {}

    public function isAvailable(): bool
    {
        return $this->status->isAvailable();
    }
}
