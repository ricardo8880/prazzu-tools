<?php

namespace App\Core\Organizations\Contracts;

interface EnterpriseAccessResolver
{
    public function grantsPlusAccessTo(int $userId): bool;
}
