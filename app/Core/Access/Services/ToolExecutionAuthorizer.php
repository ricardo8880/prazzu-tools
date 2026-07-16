<?php

namespace App\Core\Access\Services;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessGate;
use App\Core\Access\Data\AccessDecision;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Usage\Contracts\UsageLimiter;
use App\Core\Usage\Data\UsageLimit;

final readonly class ToolExecutionAuthorizer
{
    public function __construct(
        private ToolAccessGate $accessGate,
        private UsageLimiter $usageLimiter,
        private CommercialAccessPolicy $commercialPolicy,
    ) {}

    public function authorize(
        ToolManifest $manifest,
        ToolAccessContext $context,
        string $subjectKey,
        UsageLimit $limit,
    ): AccessDecision {
        $access = $this->accessGate->decide($manifest, $context);

        if (! $access->allowed) {
            return $access;
        }

        if (! $this->commercialPolicy->enforcesUsageLimits()) {
            return AccessDecision::allow('tool.launch_free_execution_allowed');
        }

        $usage = $this->usageLimiter->consume($manifest->slug, $subjectKey, $limit);

        return $usage->allowed
            ? AccessDecision::allow('tool.execution_allowed')
            : AccessDecision::deny('tool.usage_limit_reached');
    }
}
