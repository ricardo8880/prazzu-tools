<?php

declare(strict_types=1);

namespace App\Core\Taxation\Services;

use App\Core\Taxation\Contracts\TaxEstimateProvider;
use App\Core\Taxation\Contracts\TaxEstimateProviderRegistry;
use App\Core\Taxation\Data\TaxEstimateRequest;

final class InMemoryTaxEstimateProviderRegistry implements TaxEstimateProviderRegistry
{
    /** @var array<string, list<TaxEstimateProvider>> */
    private array $providers = [];

    public function register(TaxEstimateProvider $provider): void
    {
        $this->providers[$provider->regime()] ??= [];
        $this->providers[$provider->regime()][] = $provider;
    }

    public function resolve(string $regime, TaxEstimateRequest $request): ?TaxEstimateProvider
    {
        foreach ($this->providers[$regime] ?? [] as $provider) {
            if ($provider->supports($request)) {
                return $provider;
            }
        }

        return null;
    }
}
