<?php

namespace App\Http\Controllers\Organizations;

use App\Core\Organizations\Actions\GenerateOrganizationInvitationLink;
use App\Core\Organizations\Enums\OrganizationInvitationStatus;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class OrganizationInvitationController extends Controller
{
    public function store(Request $request, Organization $organization, GenerateOrganizationInvitationLink $action): RedirectResponse
    {
        $this->authorizeManagement($request, $organization);

        $invitation = $action->execute($organization, $request->user());

        return back()
            ->with('status', 'Link de convite gerado. Envie-o somente para a pessoa que deve entrar na empresa.')
            ->with('invitation_link', route('organizations.invitations.show', $invitation->token));
    }

    public function destroy(Request $request, Organization $organization, OrganizationInvitation $invitation): RedirectResponse
    {
        $this->authorizeManagement($request, $organization, $invitation);

        if ($invitation->status === OrganizationInvitationStatus::Pending) {
            $invitation->forceFill([
                'status' => OrganizationInvitationStatus::Revoked,
                'revoked_at' => now(),
            ])->save();
        }

        return back()->with('status', 'Link de convite cancelado.');
    }

    public function restore(Request $request, Organization $organization, OrganizationInvitation $invitation): RedirectResponse
    {
        $this->authorizeManagement($request, $organization, $invitation);

        abort_unless($invitation->status === OrganizationInvitationStatus::Revoked, 422);

        $invitation->forceFill([
            'status' => OrganizationInvitationStatus::Pending,
            'token' => hash('sha256', Str::random(64)),
            'invited_by_user_id' => $request->user()->getKey(),
            'expires_at' => now()->addDays(7),
            'revoked_at' => null,
        ])->save();

        return back()
            ->with('status', 'Link restaurado com um novo endereço e validade de 7 dias.')
            ->with('invitation_link', route('organizations.invitations.show', $invitation->token));
    }

    public function purge(Request $request, Organization $organization, OrganizationInvitation $invitation): RedirectResponse
    {
        $this->authorizeManagement($request, $organization, $invitation);

        abort_unless($invitation->status === OrganizationInvitationStatus::Revoked, 422);

        $invitation->delete();

        return back()->with('status', 'Convite apagado definitivamente.');
    }

    private function authorizeManagement(Request $request, Organization $organization, ?OrganizationInvitation $invitation = null): void
    {
        if ($invitation !== null) {
            abort_unless($invitation->organization_id === $organization->getKey(), 404);
        }

        $user = $request->user();

        abort_unless($user !== null, 403);

        if ($organization->owner_user_id === $user->getKey()) {
            return;
        }

        $member = $organization->members()
            ->where('user_id', $user->getKey())
            ->first();

        abort_unless(
            $member !== null
                && $member->status === OrganizationMemberStatus::Active
                && $member->role->canManageOrganization(),
            403,
        );
    }
}
