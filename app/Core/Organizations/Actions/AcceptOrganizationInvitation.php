<?php

namespace App\Core\Organizations\Actions;

use App\Core\Organizations\Enums\OrganizationInvitationStatus;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AcceptOrganizationInvitation
{
    public function execute(OrganizationInvitation $invitation, User $user): void
    {
        if (! $invitation->canBeAccepted()) {
            throw ValidationException::withMessages(['invitation' => 'Este convite não está mais disponível.']);
        }

        $isActiveMember = $invitation->organization->members()
            ->where('user_id', $user->getKey())
            ->where('status', OrganizationMemberStatus::Active->value)
            ->exists();

        if ($isActiveMember) {
            throw ValidationException::withMessages(['invitation' => 'Sua conta já faz parte desta empresa.']);
        }

        DB::transaction(function () use ($invitation, $user): void {
            $lockedInvitation = OrganizationInvitation::query()
                ->whereKey($invitation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedInvitation->canBeAccepted()) {
                throw ValidationException::withMessages(['invitation' => 'Este convite já foi utilizado ou não está mais disponível.']);
            }

            $lockedInvitation->organization->members()->updateOrCreate(
                ['user_id' => $user->getKey()],
                [
                    'role' => $lockedInvitation->role,
                    'status' => OrganizationMemberStatus::Active,
                    'joined_at' => now(),
                    'left_at' => null,
                ],
            );

            $lockedInvitation->forceFill([
                'status' => OrganizationInvitationStatus::Accepted,
                'accepted_by_user_id' => $user->getKey(),
                'accepted_at' => now(),
            ])->save();
        });
    }
}
