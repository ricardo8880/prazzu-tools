<?php

namespace App\Core\Access\Contracts;

use App\Core\Access\Data\AccessDecision;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Tools\Data\ToolManifest;

interface ToolAccessGate
{
    public function decide(ToolManifest $manifest, ToolAccessContext $context): AccessDecision;
}
