<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Tools\SimplesNacionalCalculator\Domain\Data\ScenarioComparison;

final readonly class CompareScenarios
{
    public function __construct(private CalculateSimplesNacional $calculate) {}

    /** @param list<array{name:string, annex:string, rbt12:string, monthly_revenue:string}> $scenarios */
    public function execute(array $scenarios): ScenarioComparison
    {
        $results = [];

        foreach ($scenarios as $scenario) {
            $result = $this->calculate->execute($scenario)->toArray();
            $result['name'] = $scenario['name'];
            $result['estimated_das_cents'] = $this->moneyToCents($result['estimated_das']);
            $results[] = $result;
        }

        usort($results, fn (array $a, array $b): int => $a['estimated_das_cents'] <=> $b['estimated_das_cents']);

        foreach ($results as $index => &$result) {
            $result['best'] = $index === 0;
        }

        return new ScenarioComparison($results);
    }

    private function moneyToCents(string $value): int
    {
        return (int) str_replace(['R$', '.', ',', ' '], ['', '', '', ''], $value);
    }
}
