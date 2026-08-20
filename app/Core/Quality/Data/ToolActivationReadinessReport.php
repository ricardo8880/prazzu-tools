<?php

declare(strict_types=1);

namespace App\Core\Quality\Data;

final readonly class ToolActivationReadinessReport
{
    /** @param array<int, string> $blockers */
    public function __construct(
        public string $module,
        public string $slug,
        public array $blockers,
        public int $openChecklistItems,
        public bool $hasSyntheticGoldenCases,
    ) {}

    public function isReady(): bool
    {
        return $this->blockers === [];
    }
}
