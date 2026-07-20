<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator;

use App\Core\Taxation\Contracts\TaxEstimateProviderRegistry;
use App\Tools\SimplesNacionalCalculator\Application\Taxation\SimplesNacionalTaxEstimateProvider;
use Illuminate\Support\ServiceProvider;

final class SimplesNacionalServiceProvider extends ServiceProvider
{
    public function boot(TaxEstimateProviderRegistry $registry, SimplesNacionalTaxEstimateProvider $provider): void
    {
        $registry->register($provider);
    }
}
