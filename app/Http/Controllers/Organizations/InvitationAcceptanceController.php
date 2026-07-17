<?php

namespace App\Http\Controllers\Organizations;

use App\Core\Organizations\Actions\AcceptOrganizationInvitation;
use App\Http\Controllers\Controller;
use App\Models\OrganizationInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class InvitationAcceptanceController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $invitation = $this->findInvitation($token);

        if (! $request->user()) {
            $request->session()->put('url.intended', route('organizations.invitations.show', $token));
        }

        return view('organizations.invitations.show', compact('invitation'));
    }

    public function accept(Request $request, string $token, AcceptOrganizationInvitation $action): RedirectResponse
    {
        $invitation = $this->findInvitation($token);
        $action->execute($invitation, $request->user());

        return redirect()->route('organizations.show', $invitation->organization)
            ->with('status', 'Convite aceito. Seu acesso foi vinculado sem compartilhar seus dados pessoais.');
    }

    private function findInvitation(string $token): OrganizationInvitation
    {
        return OrganizationInvitation::query()->with('organization')->where('token', $token)->firstOrFail();
    }
}
