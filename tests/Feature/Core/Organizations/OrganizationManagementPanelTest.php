<?php

namespace Tests\Feature\Core\Organizations;

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

final class OrganizationManagementPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_company_profile(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();

        $response = $this->actingAs($owner)->patch(route('organizations.update', $organization), [
            'name' => 'Novo Nome Empresarial',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Novo Nome Empresarial',
        ]);
    }

    public function test_manager_can_assign_and_release_a_plus_seat(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $member = $this->member($organization);
        $subscription = OrganizationSubscription::query()->create([
            'organization_id' => $organization->id,
            'status' => OrganizationSubscriptionStatus::Active,
            'seat_limit' => 1,
            'starts_at' => now()->subMinute(),
        ]);

        $assign = $this->actingAs($owner)->post(route('organizations.seats.store', [$organization, $member]));
        $assign->assertRedirect();
        $assign->assertSessionHasNoErrors();

        $seat = $subscription->seats()->firstOrFail();
        $this->assertNull($seat->released_at);

        $release = $this->actingAs($owner)->delete(route('organizations.seats.destroy', [$organization, $seat]));
        $release->assertRedirect();
        $this->assertNotNull($seat->fresh()->released_at);
    }

    public function test_deactivating_member_releases_their_seat_without_deleting_account(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $member = $this->member($organization);
        $subscription = OrganizationSubscription::query()->create([
            'organization_id' => $organization->id,
            'status' => OrganizationSubscriptionStatus::Active,
            'seat_limit' => 1,
            'starts_at' => now()->subMinute(),
        ]);
        $seat = app(AssignOrganizationSeat::class)->execute($subscription, $member);

        $response = $this->actingAs($owner)->patch(
            route('organizations.members.update', [$organization, $member]),
            ['role' => 'member', 'status' => 'inactive'],
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['id' => $member->user_id]);
        $this->assertSame(OrganizationMemberStatus::Inactive, $member->fresh()->status);
        $this->assertNotNull($seat->fresh()->released_at);
    }

    public function test_administrator_cannot_change_another_administrator(): void
    {
        [, $organization] = $this->organizationWithOwner();
        $administrator = $this->member($organization, OrganizationMemberRole::Administrator);
        $otherAdministrator = $this->member($organization, OrganizationMemberRole::Administrator);

        $response = $this->actingAs($administrator->user)->patch(
            route('organizations.members.update', [$organization, $otherAdministrator]),
            ['role' => 'member', 'status' => 'inactive'],
        );

        $response->assertForbidden();
        $this->assertSame(OrganizationMemberRole::Administrator, $otherAdministrator->fresh()->role);
        $this->assertSame(OrganizationMemberStatus::Active, $otherAdministrator->fresh()->status);
    }

    public function test_unrelated_user_cannot_open_company_panel(): void
    {
        [, $organization] = $this->organizationWithOwner();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('organizations.show', $organization))
            ->assertForbidden();
    }

    private function organizationWithOwner(): array
    {
        $owner = User::factory()->create();
        $organization = Organization::query()->create([
            'name' => 'Empresa Teste',
            'slug' => 'empresa-teste-'.fake()->unique()->numerify('####'),
            'owner_user_id' => $owner->id,
        ]);
        OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'role' => OrganizationMemberRole::Owner,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);

        return [$owner, $organization];
    }

    private function member(
        Organization $organization,
        OrganizationMemberRole $role = OrganizationMemberRole::Member,
    ): OrganizationMember {
        $user = User::factory()->create();

        return OrganizationMember::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => $role,
            'status' => OrganizationMemberStatus::Active,
            'joined_at' => now(),
        ]);
    }
}
