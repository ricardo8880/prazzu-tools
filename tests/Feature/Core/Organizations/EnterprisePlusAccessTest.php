<?php

namespace Tests\Feature\Core\Organizations;

use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Organizations\Actions\AssignOrganizationSeat;
use App\Core\Organizations\Actions\ReleaseOrganizationSeat;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Core\Organizations\Enums\OrganizationSubscriptionStatus;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class EnterprisePlusAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_enterprise_seat_grants_plus_without_changing_individual_plan(): void
    {
        [$user, $member, $subscription] = $this->enterpriseMember(1);
        app(AssignOrganizationSeat::class)->execute($subscription, $member);

        self::assertSame(SubscriptionPlan::Free, $user->fresh()->subscription_plan);
        self::assertTrue($user->fresh()->hasPlusAccess());
        self::assertSame(SubscriptionPlan::Plus, $user->fresh()->effectiveSubscriptionPlan());
    }

    public function test_released_seat_removes_only_enterprise_benefit(): void
    {
        [$user, $member, $subscription] = $this->enterpriseMember(1);
        $seat = app(AssignOrganizationSeat::class)->execute($subscription, $member);
        app(ReleaseOrganizationSeat::class)->execute($seat);

        self::assertFalse($user->fresh()->hasPlusAccess());
        self::assertDatabaseHas('users', ['id' => $user->id, 'email' => $user->email]);
    }

    public function test_seat_limit_cannot_be_exceeded(): void
    {
        [, $firstMember, $subscription, $organization] = $this->enterpriseMember(1);
        app(AssignOrganizationSeat::class)->execute($subscription, $firstMember);

        $other = User::factory()->create();
        $otherMember = OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $other->id,
            'role' => OrganizationMemberRole::Member,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);

        $this->expectException(ValidationException::class);
        app(AssignOrganizationSeat::class)->execute($subscription, $otherMember);
    }

    public function test_suspended_subscription_does_not_grant_plus(): void
    {
        [$user, $member, $subscription] = $this->enterpriseMember(1);
        app(AssignOrganizationSeat::class)->execute($subscription, $member);
        $subscription->update(['status' => OrganizationSubscriptionStatus::Suspended]);

        self::assertFalse($user->fresh()->hasPlusAccess());
    }

    private function enterpriseMember(int $seatLimit): array
    {
        $owner = User::factory()->create();
        $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
        $organization = Organization::query()->create([
            'name' => 'Empresa Teste',
            'slug' => 'empresa-teste-'.fake()->unique()->numerify('####'),
            'owner_user_id' => $owner->id,
        ]);
        $member = OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => OrganizationMemberRole::Member,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);
        $subscription = OrganizationSubscription::query()->create([
            'organization_id' => $organization->id,
            'status' => OrganizationSubscriptionStatus::Active,
            'seat_limit' => $seatLimit,
            'starts_at' => now()->subMinute(),
        ]);

        return [$user, $member, $subscription, $organization];
    }
}
