<?php

namespace App\Core\Acquisition\Application;

use App\Core\Acquisition\Contracts\AcquisitionContextRepository;
use App\Core\Acquisition\Domain\Data\AcquisitionContext;

final readonly class ResolveAcquisitionContext
{
    public function __construct(
        private AcquisitionContextRepository $contexts,
    ) {}

    public function execute(?string $keyword): ?AcquisitionContext
    {
        $normalizedKeyword = trim((string) $keyword);

        if ($normalizedKeyword === '') {
            return null;
        }

        return $this->contexts->activeByKeyword($normalizedKeyword);
    }
}
