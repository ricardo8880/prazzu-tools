<?php

declare(strict_types=1);

namespace App\Core\Access\Services;

use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Organizations\Contracts\EnterpriseAccessResolver;
use Illuminate\Contracts\Auth\Authenticatable;

final readonly class DefaultToolAccessContextResolver implements ToolAccessContextResolver
{
    public function __construct(private EnterpriseAccessResolver $enterpriseAccess) {}

    public function resolve(?Authenticatable $user): ToolAccessContext
    {
        if ($user === null) {
            return new ToolAccessContext;
        }

        $userId = $this->userId($user);
        $plan = $this->subscriptionPlan(data_get($user, 'subscription_plan'));

        if ($userId !== null && $this->enterpriseAccess->grantsPlusAccessTo($userId)) {
            $plan = SubscriptionPlan::Premium;
        }

        return new ToolAccessContext(
            userId: $userId,
            role: $this->accountRole(data_get($user, 'role')),
            plan: $plan,
        );
    }

    private function userId(Authenticatable $user): ?int
    {
        $identifier = $user->getAuthIdentifier();

        return is_numeric($identifier) ? (int) $identifier : null;
    }

    private function accountRole(mixed $role): AccountRole
    {
        if ($role instanceof AccountRole) {
            return $role;
        }

        return is_string($role) ? (AccountRole::tryFrom($role) ?? AccountRole::User) : AccountRole::User;
    }

    private function subscriptionPlan(mixed $plan): SubscriptionPlan
    {
        if ($plan instanceof SubscriptionPlan) {
            return $plan;
        }

        return is_string($plan) ? (SubscriptionPlan::tryFrom($plan) ?? SubscriptionPlan::Free) : SubscriptionPlan::Free;
    }
}
