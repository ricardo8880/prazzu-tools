<?php

namespace App\Core\Organizations\Contracts;

interface OrganizationSeatCounter
{
    public function occupiedSeats(int $organizationId): int;
}
