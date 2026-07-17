<?php

namespace Tests\Feature\Core\Organizations;

use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Organizations\Actions\AssignOrganizationSeat;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Core\Organizations\Enums\OrganizationSubscriptionStatus;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OrganizationLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_enterprise_lifecycle_preserves_personal_account_and_data(): void
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);

        $this->actingAs($owner)->post(route('organizations.store'), [
            'name' => 'Empresa Ciclo Completo',
        ])->assertSessionHasNoErrors();

        $organization = Organization::query()->firstOrFail();
        $subscription = OrganizationSubscription::query()->create([
            'organization_id' => $organization->id,
            'status' => OrganizationSubscriptionStatus::Active,
            'seat_limit' => 1,
            'starts_at' => now()->subMinute(),
        ]);
        $member = OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $collaborator->id,
            'role' => OrganizationMemberRole::Member,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);

        app(AssignOrganizationSeat::class)->execute($subscription, $member);

        self::assertTrue($collaborator->fresh()->hasPremiumAccess());
        self::assertSame(SubscriptionPlan::Free, $collaborator->fresh()->subscription_plan);

        $this->actingAs($owner)->patch(
            route('organizations.members.update', [$organization, $member]),
            ['role' => 'member', 'status' => 'inactive'],
        )->assertSessionHasNoErrors();

        self::assertFalse($collaborator->fresh()->hasPremiumAccess());
        self::assertSame(SubscriptionPlan::Free, $collaborator->fresh()->subscription_plan);
        $this->assertDatabaseHas('users', [
            'id' => $collaborator->id,
            'email' => $collaborator->email,
        ]);
    }

    public function test_member_can_move_from_an_old_subscription_to_the_current_active_subscription(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $organization = Organization::query()->create([
            'name' => 'Empresa Renovação',
            'slug' => 'empresa-renovacao',
            'owner_user_id' => $owner->id,
        ]);
        $member = OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => OrganizationMemberRole::Member,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);
        $oldSubscription = OrganizationSubscription::query()->create([
            'organization_id' => $organization->id,
            'status' => OrganizationSubscriptionStatus::Active,
            'seat_limit' => 1,
            'starts_at' => now()->subMonth(),
        ]);
        $oldSeat = app(AssignOrganizationSeat::class)->execute($oldSubscription, $member);
        $oldSubscription->update([
            'status' => OrganizationSubscriptionStatus::Canceled,
            'ends_at' => now(),
        ]);
        $currentSubscription = OrganizationSubscription::query()->create([
            'organization_id' => $organization->id,
            'status' => OrganizationSubscriptionStatus::Active,
            'seat_limit' => 1,
            'starts_at' => now()->subMinute(),
        ]);

        $currentSeat = app(AssignOrganizationSeat::class)->execute($currentSubscription, $member);

        self::assertNotSame($oldSeat->id, $currentSeat->id);
        self::assertNotNull($oldSeat->fresh()->released_at);
        self::assertNull($currentSeat->released_at);
        self::assertTrue($user->fresh()->hasPremiumAccess());
    }

    public function test_company_manager_never_receives_access_to_another_members_personal_area(): void
    {
        $owner = User::factory()->create();
        $collaborator = User::factory()->create();
        $organization = Organization::query()->create([
            'name' => 'Empresa Isolada',
            'slug' => 'empresa-isolada',
            'owner_user_id' => $owner->id,
        ]);
        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => OrganizationMemberRole::Owner,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);
        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $collaborator->id,
            'role' => OrganizationMemberRole::Member,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($owner)->get(route('account.show'));

        $response->assertOk();
        $response->assertSee($owner->email);
        $response->assertDontSee($collaborator->email);
    }
}
