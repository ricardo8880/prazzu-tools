<?php

namespace App\Core\Organizations\Actions;

use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Models\OrganizationMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class UpdateOrganizationMember
{
    public function __construct(private ReleaseOrganizationSeat $releaseSeat) {}

    public function execute(
        OrganizationMember $member,
        OrganizationMemberRole $role,
        OrganizationMemberStatus $status,
    ): OrganizationMember {
        if ($member->role === OrganizationMemberRole::Owner) {
            throw ValidationException::withMessages([
                'member' => 'O responsável pela empresa não pode ser alterado por este painel.',
            ]);
        }

        return DB::transaction(function () use ($member, $role, $status): OrganizationMember {
            $member->forceFill([
                'role' => $role,
                'status' => $status,
                'left_at' => $status === OrganizationMemberStatus::Inactive ? now() : null,
                'joined_at' => $status === OrganizationMemberStatus::Active
                    ? ($member->joined_at ?? now())
                    : $member->joined_at,
            ])->save();

            if ($status === OrganizationMemberStatus::Inactive) {
                $member->seats()->whereNull('released_at')->get()
                    ->each(fn ($seat) => $this->releaseSeat->execute($seat));
            }

            return $member->refresh();
        });
    }
}
