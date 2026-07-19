<?php

declare(strict_types=1);

namespace App\Core\Access\Services;

use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Tools\Contracts\ToolModule;
use Illuminate\Contracts\Auth\Authenticatable;

final readonly class ToolPersistenceAuthorizer
{
    public function __construct(private ToolFeatureAccessGate $featureAccess) {}

    public function allowsHistory(
        ToolModule $module,
        ?Authenticatable $user,
        string $featureKey = 'history',
    ): bool {
        return $user !== null
            && $this->featureAccess->decide($module->manifest(), $featureKey, $user)->allowed;
    }
}
