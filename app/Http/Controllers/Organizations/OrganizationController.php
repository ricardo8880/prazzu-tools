<?php

namespace App\Http\Controllers\Organizations;

use App\Core\Organizations\Actions\CreateOrganization;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizations\StoreOrganizationRequest;
use App\Http\Requests\Organizations\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class OrganizationController extends Controller
{
    public function create(): View
    {
        return view('organizations.create');
    }

    public function store(StoreOrganizationRequest $request, CreateOrganization $action): RedirectResponse
    {
        $organization = $action->execute($request->user(), $request->string('name')->toString());

        return redirect()->route('organizations.show', $organization)
            ->with('status', 'Empresa criada. Sua conta foi definida como responsável.');
    }

    public function show(Request $request, Organization $organization): View
    {
        abort_unless(
            $organization->members()->where('user_id', $request->user()->getKey())->where('status', OrganizationMemberStatus::Active->value)->exists(),
            403,
        );

        $organization->load([
            'owner',
            'members' => fn ($query) => $query
                ->with(['user', 'seats' => fn ($seatQuery) => $seatQuery->whereNull('released_at')->with('subscription')])
                ->orderBy('created_at'),
            'invitations' => fn ($query) => $query->with('acceptedBy')->latest()->limit(20),
        ]);

        $membership = $organization->members->firstWhere('user_id', $request->user()->getKey());
        $subscription = $organization->subscriptions()
            ->with(['seats' => fn ($query) => $query->whereNull('released_at')])
            ->latest('id')
            ->first();

        return view('organizations.show', compact('organization', 'membership', 'subscription'));
    }

    public function update(
        UpdateOrganizationRequest $request,
        Organization $organization,
    ): RedirectResponse {
        $membership = $organization->members()
            ->where('user_id', $request->user()->getKey())
            ->where('status', OrganizationMemberStatus::Active->value)
            ->firstOrFail();

        abort_unless($membership->role->canManageOrganization(), 403);

        $organization->update([
            'name' => $request->string('name')->trim()->toString(),
        ]);

        return back()->with('status', 'Dados da empresa atualizados.');
    }
}
