<?php

namespace App\Core\Organizations\Services;

use App\Core\Organizations\Contracts\EnterpriseAccessResolver;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Core\Organizations\Enums\OrganizationSubscriptionStatus;
use App\Models\OrganizationSeat;

final class DatabaseEnterpriseAccessResolver implements EnterpriseAccessResolver
{
    public function grantsPlusAccessTo(int $userId): bool
    {
        return OrganizationSeat::query()
            ->whereNull('released_at')
            ->whereHas('member', fn ($query) => $query
                ->where('user_id', $userId)
                ->where('status', OrganizationMemberStatus::Active->value)
                ->whereNull('left_at'))
            ->whereHas('subscription', fn ($query) => $query
                ->where('status', OrganizationSubscriptionStatus::Active->value)
                ->where(fn ($dates) => $dates->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->where(fn ($dates) => $dates->whereNull('ends_at')->orWhere('ends_at', '>', now())))
            ->exists();
    }
}
