<?php

namespace App\Core\Access\Contracts;

interface CommercialAccessPolicy
{
    public function grantsPublicCapabilitiesWithoutAuthentication(): bool;

    public function enforcesUsageLimits(): bool;
}
