<?php

namespace Tests\Unit\Core\Organizations;

use App\Core\Organizations\Enums\OrganizationInvitationStatus;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use PHPUnit\Framework\TestCase;

final class OrganizationEnumsTest extends TestCase
{
    public function test_only_management_roles_can_manage_an_organization(): void
    {
        self::assertTrue(OrganizationMemberRole::Owner->canManageOrganization());
        self::assertTrue(OrganizationMemberRole::Administrator->canManageOrganization());
        self::assertFalse(OrganizationMemberRole::Member->canManageOrganization());
    }

    public function test_only_pending_invitation_can_be_accepted(): void
    {
        self::assertTrue(OrganizationInvitationStatus::Pending->canBeAccepted());
        self::assertFalse(OrganizationInvitationStatus::Accepted->canBeAccepted());
        self::assertFalse(OrganizationInvitationStatus::Revoked->canBeAccepted());
        self::assertFalse(OrganizationInvitationStatus::Expired->canBeAccepted());
    }
}
