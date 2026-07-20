<?php

declare(strict_types=1);

namespace App\Core\Taxation\Contracts;

use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Core\Taxation\Data\TaxEstimateResult;

interface TaxEstimateProvider
{
    public function regime(): string;

    public function supports(TaxEstimateRequest $request): bool;

    public function estimate(TaxEstimateRequest $request): TaxEstimateResult;
}
