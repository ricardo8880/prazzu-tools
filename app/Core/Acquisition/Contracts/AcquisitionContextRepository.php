<?php

namespace App\Core\Acquisition\Contracts;

use App\Core\Acquisition\Domain\Data\AcquisitionContext;

interface AcquisitionContextRepository
{
    public function activeByKeyword(string $keyword): ?AcquisitionContext;
}
