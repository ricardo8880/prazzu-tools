<?php

declare(strict_types=1);

namespace App\Core\Access\Contracts;

use App\Core\Access\Data\AccessDecision;
use App\Core\Tools\Data\ToolManifest;
use Illuminate\Contracts\Auth\Authenticatable;

interface ToolFeatureAccessGate
{
    public function decide(ToolManifest $manifest, string $featureKey, ?Authenticatable $user = null): AccessDecision;
}
