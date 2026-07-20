<?php

namespace App\Core\Tools\Contracts;

use App\Core\ToolIntegration\Data\ToolIntegrationManifest;

interface HasToolIntegrations
{
    public function integrations(): ToolIntegrationManifest;
}
