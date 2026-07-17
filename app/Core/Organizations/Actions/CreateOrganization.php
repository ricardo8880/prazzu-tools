<?php

namespace App\Core\Organizations\Actions;

use App\Core\Organizations\Enums\OrganizationMemberRole;
use App\Core\Organizations\Enums\OrganizationMemberStatus;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateOrganization
{
    public function execute(User $owner, string $name): Organization
    {
        return DB::transaction(function () use ($owner, $name): Organization {
            $organization = Organization::query()->create([
                'name' => $name,
                'slug' => $this->uniqueSlug($name),
                'owner_user_id' => $owner->getKey(),
            ]);

            $organization->members()->create([
                'user_id' => $owner->getKey(),
                'role' => OrganizationMemberRole::Owner,
                'status' => OrganizationMemberStatus::Active,
                'joined_at' => now(),
            ]);

            return $organization;
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'empresa';
        $slug = $base;
        $suffix = 2;

        while (Organization::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
