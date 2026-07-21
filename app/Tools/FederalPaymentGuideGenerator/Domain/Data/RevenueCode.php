<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Data;

use App\Tools\FederalPaymentGuideGenerator\Domain\Enums\GuideType;

final readonly class RevenueCode
{
    public function __construct(
        public GuideType $guideType,
        public string $code,
        public string $description,
        public string $periodicity,
        public string $officialReference,
        public bool $requiresProfessionalConfirmation = true,
    ) {}
}
