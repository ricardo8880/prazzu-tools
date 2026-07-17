<?php

namespace Tests\Unit\Core\Organizations;

use App\Core\Organizations\Enums\OrganizationSubscriptionStatus;
use PHPUnit\Framework\TestCase;

final class OrganizationSubscriptionStatusTest extends TestCase
{
    public function test_only_active_subscription_grants_plus_access(): void
    {
        foreach (OrganizationSubscriptionStatus::cases() as $status) {
            self::assertSame($status === OrganizationSubscriptionStatus::Active, $status->grantsPlusAccess());
        }
    }
}
