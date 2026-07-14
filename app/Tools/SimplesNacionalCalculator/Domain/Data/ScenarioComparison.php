<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Data;

final readonly class ScenarioComparison
{
    /** @param list<array<string, int|string>> $scenarios */
    public function __construct(public array $scenarios) {}

    /** @return array{scenarios: list<array<string, int|string>>} */
    public function toArray(): array
    {
        return ['scenarios' => $this->scenarios];
    }
}
