<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Access;

use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessContextResolver;
use App\Core\Organizations\Contracts\EnterpriseAccessResolver;
use App\Models\User;
use PHPUnit\Framework\TestCase;

final class DefaultToolAccessContextResolverTest extends TestCase
{
    public function test_guest_context_uses_safe_defaults(): void
    {
        $context = $this->resolver(false)->resolve(null);

        self::assertFalse($context->authenticated());
        self::assertSame(AccountRole::User, $context->role);
        self::assertSame(SubscriptionPlan::Free, $context->plan);
    }

    public function test_it_normalizes_the_authenticated_users_role_and_plan(): void
    {
        $user = $this->user(AccountRole::Manager, SubscriptionPlan::Plus);

        $context = $this->resolver(false)->resolve($user);

        self::assertSame(10, $context->userId);
        self::assertSame(AccountRole::Manager, $context->role);
        self::assertSame(SubscriptionPlan::Plus, $context->plan);
    }

    public function test_enterprise_access_upgrades_the_effective_plan_in_core(): void
    {
        $user = $this->user(AccountRole::User, SubscriptionPlan::Free);

        self::assertSame(SubscriptionPlan::Plus, $this->resolver(true)->resolve($user)->plan);
    }

    private function user(AccountRole $role, SubscriptionPlan $plan): User
    {
        $user = new User;
        $user->setRawAttributes([
            'id' => 10,
            'role' => $role->value,
            'subscription_plan' => $plan->value,
        ]);

        return $user;
    }

    private function resolver(bool $enterpriseAccess): DefaultToolAccessContextResolver
    {
        $enterprise = new class($enterpriseAccess) implements EnterpriseAccessResolver
        {
            public function __construct(private readonly bool $allowed) {}

            public function grantsPlusAccessTo(int $userId): bool
            {
                return $this->allowed;
            }
        };

        return new DefaultToolAccessContextResolver($enterprise);
    }
}
