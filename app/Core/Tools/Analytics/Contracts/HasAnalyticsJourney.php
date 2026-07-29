<?php

declare(strict_types=1);

namespace App\Core\Tools\Analytics\Contracts;

use App\Core\Tools\Analytics\Data\ToolAnalyticsJourney;

interface HasAnalyticsJourney
{
    public function analyticsJourney(): ToolAnalyticsJourney;
}
