<?php

namespace App\Core\Identity\Contracts;

use App\Models\User;

interface PrazzuIdentityLinker
{
    public function link(User $user, string $prazzuAccountId): User;
}
