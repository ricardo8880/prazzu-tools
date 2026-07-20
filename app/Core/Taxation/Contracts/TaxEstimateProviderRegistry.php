<?php

declare(strict_types=1);

namespace App\Core\Taxation\Contracts;

use App\Core\Taxation\Data\TaxEstimateRequest;

interface TaxEstimateProviderRegistry
{
    public function register(TaxEstimateProvider $provider): void;

    public function resolve(string $regime, TaxEstimateRequest $request): ?TaxEstimateProvider;
}
