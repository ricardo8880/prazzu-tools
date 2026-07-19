<?php

namespace App\Core\Access\Services;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Enums\CommercialAccessMode;
use Illuminate\Contracts\Config\Repository;

final readonly class ConfigCommercialAccessPolicy implements CommercialAccessPolicy
{
    public function __construct(private Repository $config) {}

    public function grantsPublicCapabilitiesWithoutAuthentication(): bool
    {
        return $this->mode()->grantsPublicCapabilitiesWithoutAuthentication();
    }

    private function mode(): CommercialAccessMode
    {
        return CommercialAccessMode::fromConfiguration(
            $this->config->get('access.commercial_mode', CommercialAccessMode::LaunchFree->value),
        );
    }
}
