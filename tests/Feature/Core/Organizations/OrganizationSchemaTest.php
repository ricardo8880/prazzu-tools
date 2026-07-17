<?php

namespace Tests\Feature\Core\Organizations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

final class OrganizationSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_enterprise_account_foundation_tables_exist(): void
    {
        self::assertTrue(Schema::hasColumns('organizations', [
            'id',
            'name',
            'slug',
            'owner_user_id',
        ]));

        self::assertTrue(Schema::hasColumns('organization_members', [
            'organization_id',
            'user_id',
            'role',
            'status',
            'joined_at',
            'left_at',
        ]));

        self::assertTrue(Schema::hasColumns('organization_invitations', [
            'organization_id',
            'email',
            'role',
            'status',
            'token',
            'expires_at',
        ]));

        self::assertTrue(Schema::hasColumns('organization_subscriptions', [
            'organization_id',
            'status',
            'seat_limit',
            'billing_provider',
            'billing_reference',
            'starts_at',
            'ends_at',
        ]));

        self::assertTrue(Schema::hasColumns('organization_seats', [
            'organization_subscription_id',
            'organization_member_id',
            'assigned_at',
            'released_at',
        ]));
    }
}
