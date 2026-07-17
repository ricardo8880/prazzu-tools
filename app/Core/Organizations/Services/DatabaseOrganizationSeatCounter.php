<?php

namespace App\Core\Organizations\Services;

use App\Core\Organizations\Contracts\OrganizationSeatCounter;
use App\Models\OrganizationSeat;

final class DatabaseOrganizationSeatCounter implements OrganizationSeatCounter
{
    public function occupiedSeats(int $organizationId): int
    {
        return OrganizationSeat::query()
            ->whereNull('released_at')
            ->whereHas('subscription', fn ($query) => $query->where('organization_id', $organizationId))
            ->count();
    }
}
