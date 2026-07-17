<?php

namespace App\Core\Organizations\Actions;

use App\Core\Organizations\Enums\OrganizationInvitationStatus;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Support\Str;

final class GenerateOrganizationInvitationLink
{
    public function execute(Organization $organization, User $inviter): OrganizationInvitation
    {
        return $organization->invitations()->create([
            'email' => null,
            'role' => OrganizationMemberRole::Member,
            'status' => OrganizationInvitationStatus::Pending,
            'token' => hash('sha256', Str::random(64)),
            'invited_by_user_id' => $inviter->getKey(),
            'expires_at' => now()->addDays(7),
        ]);
    }
}
