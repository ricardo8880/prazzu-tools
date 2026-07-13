<?php

namespace App\Core\Access\Data;

use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;

final readonly class ToolAccessContext
{
    public function __construct(
        public ?int $userId = null,
        public ?int $organizationId = null,
        public AccountRole $role = AccountRole::User,
        public SubscriptionPlan $plan = SubscriptionPlan::Free,
    ) {}

    public function authenticated(): bool
    {
        return $this->userId !== null;
    }
}
