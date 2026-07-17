<?php

namespace Tests\Feature\Core\Organizations;

use App\Core\Organizations\Actions\CreateOrganization;
use App\Core\Organizations\Enums\OrganizationInvitationStatus;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OrganizationRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_creates_company_as_owner(): void
    {
        $user = User::factory()->create();

        $organization = app(CreateOrganization::class)->execute(
            $user,
            'Escritório Contábil Exemplo',
        );

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Escritório Contábil Exemplo',
            'owner_user_id' => $user->id,
        ]);
        $this->assertSame($user->id, $organization->owner_user_id);
        $this->assertDatabaseHas('organization_members', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => OrganizationMemberRole::Owner->value,
            'status' => OrganizationMemberStatus::Active->value,
        ]);
    }

    public function test_owner_can_generate_a_single_use_invitation_link(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();

        $response = $this->actingAs($owner)->post(route('organizations.invitations.store', $organization));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors()->assertSessionHas('invitation_link');
        $invitation = OrganizationInvitation::query()->firstOrFail();
        $this->assertNull($invitation->email);
        $this->assertSame(OrganizationInvitationStatus::Pending, $invitation->status);
        $this->assertTrue($invitation->expires_at->isFuture());
        $this->assertStringContainsString($invitation->token, session('invitation_link'));
    }

    public function test_any_authenticated_account_with_the_link_can_accept_it_once(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $collaborator = User::factory()->create();

        $this->actingAs($owner)->post(route('organizations.invitations.store', $organization));
        $invitation = OrganizationInvitation::query()->firstOrFail();

        $response = $this->actingAs($collaborator)->post(route('organizations.invitations.accept', $invitation->token));

        $response->assertRedirect(route('organizations.show', $organization));
        $this->assertDatabaseHas('organization_members', [
            'organization_id' => $organization->id,
            'user_id' => $collaborator->id,
            'role' => OrganizationMemberRole::Member->value,
            'status' => OrganizationMemberStatus::Active->value,
        ]);
        $this->assertDatabaseHas('organization_invitations', [
            'id' => $invitation->id,
            'status' => OrganizationInvitationStatus::Accepted->value,
            'accepted_by_user_id' => $collaborator->id,
        ]);
    }

    public function test_used_link_cannot_be_accepted_by_another_user(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->actingAs($owner)->post(route('organizations.invitations.store', $organization));
        $invitation = OrganizationInvitation::query()->firstOrFail();
        $this->actingAs($first)->post(route('organizations.invitations.accept', $invitation->token));

        $response = $this->actingAs($second)
            ->from(route('organizations.invitations.show', $invitation->token))
            ->post(route('organizations.invitations.accept', $invitation->token));

        $response->assertSessionHasErrors('invitation');
        $this->assertDatabaseMissing('organization_members', [
            'organization_id' => $organization->id,
            'user_id' => $second->id,
        ]);
    }

    public function test_guest_is_returned_to_the_invitation_after_authentication(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $this->actingAs($owner)->post(route('organizations.invitations.store', $organization));
        $invitation = OrganizationInvitation::query()->firstOrFail();
        $this->post(route('logout'));

        $response = $this->get(route('organizations.invitations.show', $invitation->token));

        $response->assertOk();
        $this->assertSame(route('organizations.invitations.show', $invitation->token), session('url.intended'));
    }

    public function test_owner_can_restore_a_revoked_invitation_with_a_new_link(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $this->actingAs($owner)->post(route('organizations.invitations.store', $organization));

        $invitation = OrganizationInvitation::query()->firstOrFail();
        $originalToken = $invitation->token;

        $this->actingAs($owner)->delete(route('organizations.invitations.destroy', [$organization, $invitation]));
        $response = $this->actingAs($owner)->patch(route('organizations.invitations.restore', [$organization, $invitation]));

        $response->assertSessionHasNoErrors()->assertSessionHas('invitation_link');
        $invitation->refresh();
        $this->assertSame(OrganizationInvitationStatus::Pending, $invitation->status);
        $this->assertNull($invitation->revoked_at);
        $this->assertNotSame($originalToken, $invitation->token);
        $this->assertTrue($invitation->expires_at->isFuture());
    }

    public function test_owner_can_permanently_delete_a_revoked_invitation(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $this->actingAs($owner)->post(route('organizations.invitations.store', $organization));

        $invitation = OrganizationInvitation::query()->firstOrFail();
        $this->actingAs($owner)->delete(route('organizations.invitations.destroy', [$organization, $invitation]));
        $response = $this->actingAs($owner)->delete(route('organizations.invitations.purge', [$organization, $invitation]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('organization_invitations', ['id' => $invitation->id]);
    }

    public function test_pending_invitation_cannot_be_permanently_deleted(): void
    {
        [$owner, $organization] = $this->organizationWithOwner();
        $this->actingAs($owner)->post(route('organizations.invitations.store', $organization));

        $invitation = OrganizationInvitation::query()->firstOrFail();
        $response = $this->actingAs($owner)->delete(route('organizations.invitations.purge', [$organization, $invitation]));

        $response->assertStatus(422);
        $this->assertDatabaseHas('organization_invitations', [
            'id' => $invitation->id,
            'status' => OrganizationInvitationStatus::Pending->value,
        ]);
    }

    private function organizationWithOwner(): array
    {
        $owner = User::factory()->create();
        $organization = app(CreateOrganization::class)->execute($owner, 'Empresa Teste');

        return [$owner, $organization];
    }
}
