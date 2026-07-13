<?php

namespace App\Core\Usage\Contracts;

interface UsageMetrics
{
    public function record(string $toolSlug, string $event, ?int $userId = null, ?int $organizationId = null, ?int $durationMs = null): void;
}
