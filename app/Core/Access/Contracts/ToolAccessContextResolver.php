<?php

declare(strict_types=1);

namespace App\Core\Access\Contracts;

use App\Core\Access\Data\ToolAccessContext;
use Illuminate\Contracts\Auth\Authenticatable;

interface ToolAccessContextResolver
{
    public function resolve(?Authenticatable $user): ToolAccessContext;
}
