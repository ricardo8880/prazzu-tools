<?php

declare(strict_types=1);

namespace App\Core\Quality\E2E\Contracts;

use App\Core\Quality\E2E\Data\ToolScenario;

interface ProvidesE2EScenarios
{
    /** @return list<ToolScenario> */
    public static function e2eScenarios(): array;
}
