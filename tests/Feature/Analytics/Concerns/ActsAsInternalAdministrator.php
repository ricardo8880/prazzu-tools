<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics\Concerns;

use App\Core\Access\Enums\AccountRole;
use App\Models\User;

trait ActsAsInternalAdministrator
{
    protected function signInAsInternalAdministrator(): User
    {
        $administrator = User::factory()->create([
            'role' => AccountRole::Administrator,
        ]);

        $this->actingAs($administrator);

        return $administrator;
    }
}
