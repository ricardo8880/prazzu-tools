<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Domain\Data;

use App\Tools\BusinessDocumentValidator\Domain\Enums\InconsistencySeverity;

final readonly class CompanyConsistencyAnalysisResult
{
    /** @param list<Inconsistency> $inconsistencies */
    public function __construct(
        public bool $completed,
        public string $message,
        public array $inconsistencies,
        public ?CompanyRegistryData $company = null,
    ) {}

    public function errorCount(): int
    {
        return $this->countBySeverity(InconsistencySeverity::Error);
    }

    public function warningCount(): int
    {
        return $this->countBySeverity(InconsistencySeverity::Warning);
    }

    public function informationCount(): int
    {
        return $this->countBySeverity(InconsistencySeverity::Information);
    }

    public function hasProblems(): bool
    {
        return $this->errorCount() > 0 || $this->warningCount() > 0;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'completed' => $this->completed,
            'message' => $this->message,
            'has_problems' => $this->hasProblems(),
            'summary' => [
                'errors' => $this->errorCount(),
                'warnings' => $this->warningCount(),
                'information' => $this->informationCount(),
                'total' => count($this->inconsistencies),
            ],
            'inconsistencies' => array_map(
                static fn (Inconsistency $inconsistency): array => $inconsistency->toArray(),
                $this->inconsistencies,
            ),
            'company' => $this->company?->toArray(),
        ];
    }

    private function countBySeverity(InconsistencySeverity $severity): int
    {
        return count(array_filter(
            $this->inconsistencies,
            static fn (Inconsistency $inconsistency): bool => $inconsistency->severity === $severity,
        ));
    }
}
