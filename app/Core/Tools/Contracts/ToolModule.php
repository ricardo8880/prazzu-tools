<?php

namespace App\Core\Tools\Contracts;

use App\Core\Tools\Data\ToolManifest;

interface ToolModule
{
    public function manifest(): ToolManifest;
}
