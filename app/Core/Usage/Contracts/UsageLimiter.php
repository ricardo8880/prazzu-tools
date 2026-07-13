<?php

namespace App\Core\Usage\Contracts;

use App\Core\Usage\Data\UsageDecision;
use App\Core\Usage\Data\UsageLimit;

interface UsageLimiter
{
    public function consume(string $toolSlug, string $subjectKey, UsageLimit $limit): UsageDecision;
}
