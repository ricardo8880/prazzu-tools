<?php

namespace App\Core\Access\Services;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessGate;
use App\Core\Access\Data\AccessDecision;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolAccess;

final readonly class DefaultToolAccessGate implements ToolAccessGate
{
    public function __construct(
        private FeatureFlagRepository $flags,
        private CommercialAccessPolicy $commercialPolicy,
    ) {}

    public function decide(ToolManifest $manifest, ToolAccessContext $context): AccessDecision
    {
        if (! $manifest->status->acceptsNewExecutions()) {
            return AccessDecision::deny('tool.status_blocks_execution');
        }

        if (! $this->flags->enabled("tools.{$manifest->slug}.enabled", true)) {
            return AccessDecision::deny('tool.feature_disabled');
        }

        if (
            $manifest->access === ToolAccess::Free
            && $this->commercialPolicy->grantsPublicCapabilitiesWithoutAuthentication()
        ) {
            return AccessDecision::allow('tool.launch_free_access');
        }

        return match ($manifest->access) {
            ToolAccess::Free => AccessDecision::allow(),
            ToolAccess::Internal => $context->authenticated() && $context->role->isInternal()
                ? AccessDecision::allow()
                : AccessDecision::deny('tool.internal_only'),
        };
    }
}
