<?php

declare(strict_types=1);

namespace App\Tools\IcmsStCalculator\Application\Actions;

use App\Core\Tools\Data\ToolManifest;
use App\Tools\IcmsStCalculator\Tool;

final readonly class ShowToolPage
{
    public function __construct(private Tool $tool) {}

    /** @return array{tool: ToolManifest} */
    public function execute(): array
    {
        return ['tool' => $this->tool->manifest()];
    }
}
