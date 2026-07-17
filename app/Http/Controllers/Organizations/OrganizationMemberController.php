<?php

namespace App\Http\Controllers\Organizations;

use App\Core\Organizations\Actions\UpdateOrganizationMember;
use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizations\UpdateOrganizationMemberRequest;
use App\Models\Organization;
use App\Models\OrganizationMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class OrganizationMemberController extends Controller
{
    public function update(
        UpdateOrganizationMemberRequest $request,
        Organization $organization,
        OrganizationMember $member,
        UpdateOrganizationMember $action,
    ): RedirectResponse {
        $manager = $this->managerMembership($request, $organization);
        abort_unless($member->organization_id === $organization->getKey(), 404);

        $newRole = OrganizationMemberRole::from($request->string('role')->toString());
        $newStatus = OrganizationMemberStatus::from($request->string('status')->toString());

        if ($manager->role !== OrganizationMemberRole::Owner) {
            abort_if($member->role !== OrganizationMemberRole::Member, 403);
            abort_if($newRole !== OrganizationMemberRole::Member, 403);
        }

        $action->execute($member, $newRole, $newStatus);

        return back()->with('status', 'Dados do membro atualizados.');
    }

    private function managerMembership(Request $request, Organization $organization): OrganizationMember
    {
        $membership = $organization->members()
            ->where('user_id', $request->user()->getKey())
            ->where('status', OrganizationMemberStatus::Active->value)
            ->firstOrFail();

        abort_unless($membership->role->canManageOrganization(), 403);

        return $membership;
    }
}
