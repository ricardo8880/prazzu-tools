<?php

namespace App\Core\Organizations\Actions;

use App\Models\OrganizationSeat;

final class ReleaseOrganizationSeat
{
    public function execute(OrganizationSeat $seat): void
    {
        if ($seat->released_at !== null) {
            return;
        }

        $seat->forceFill(['released_at' => now()])->save();
    }
}
