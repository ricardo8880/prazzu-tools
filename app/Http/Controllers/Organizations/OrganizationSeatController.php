<?php

namespace App\Http\Controllers\Organizations;

use App\Core\Organizations\Actions\AssignOrganizationSeat;
use App\Core\Organizations\Actions\ReleaseOrganizationSeat;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationSeat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class OrganizationSeatController extends Controller
{
    public function store(
        Request $request,
        Organization $organization,
        OrganizationMember $member,
        AssignOrganizationSeat $action,
    ): RedirectResponse {
        $this->ensureManager($request, $organization);
        abort_unless($member->organization_id === $organization->getKey(), 404);

        $subscription = $organization->subscriptions()->latest('id')->first();

        if ($subscription === null) {
            throw ValidationException::withMessages([
                'seat' => 'A empresa ainda não possui uma assinatura empresarial configurada.',
            ]);
        }

        $action->execute($subscription, $member);

        return back()->with('status', 'Acesso Plus atribuído ao membro.');
    }

    public function destroy(
        Request $request,
        Organization $organization,
        OrganizationSeat $seat,
        ReleaseOrganizationSeat $action,
    ): RedirectResponse {
        $this->ensureManager($request, $organization);
        $seat->loadMissing('subscription');
        abort_unless($seat->subscription->organization_id === $organization->getKey(), 404);

        $action->execute($seat);

        return back()->with('status', 'Acesso Plus liberado para outra pessoa.');
    }

    private function ensureManager(Request $request, Organization $organization): void
    {
        $membership = $organization->members()
            ->where('user_id', $request->user()->getKey())
            ->where('status', OrganizationMemberStatus::Active->value)
            ->firstOrFail();

        abort_unless($membership->role->canManageOrganization(), 403);
    }
}
