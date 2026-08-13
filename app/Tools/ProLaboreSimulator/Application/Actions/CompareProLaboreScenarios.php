<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Application\Actions;

use App\Tools\ProLaboreSimulator\Application\Data\CalculationInput;
use App\Tools\ProLaboreSimulator\Domain\Services\Calculator;
use Illuminate\Validation\ValidationException;

final readonly class CompareProLaboreScenarios
{
    public const FEATURE = 'scenarios';

    public function __construct(private Calculator $calculator) {}

    /** @param array<string,mixed> $data @return array<int,array{name:string,months:array<int,mixed>,annual_net_minor:int,annual_company_cost_minor:int}> */
    public function execute(array $data): array
    {
        $values = preg_split('/\s*;\s*/', trim((string) ($data['scenario_values'] ?? '')), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($values) < 2 || count($values) > 4) {
            throw ValidationException::withMessages(['scenario_values' => 'Informe de 2 a 4 valores de pró-labore separados por ponto e vírgula.']);
        }
        $scenarios = [];
        foreach ($values as $index => $gross) {
            $months = [];
            $annualNet = 0;
            $annualCompanyCost = 0;
            foreach (range(1, 12) as $month) {
                $result = $this->calculator->calculate(new CalculationInput(
                    competence: sprintf('2026-%02d', $month),
                    companyRegime: (string) $data['company_regime'],
                    grossProLabore: $gross,
                    dependents: (int) ($data['dependents'] ?? 0),
                    otherOfficialSocialSecurity: (string) ($data['other_official_social_security'] ?? '0'),
                ));
                $months[] = $result;
                $annualNet += (int) ($result->details['net_minor'] ?? 0);
                $annualCompanyCost += (int) ($result->details['company_total_cost_minor'] ?? 0);
            }
            $scenarios[] = [
                'name' => 'Cenário '.($index + 1).' — R$ '.$gross,
                'months' => $months,
                'annual_net_minor' => $annualNet,
                'annual_company_cost_minor' => $annualCompanyCost,
            ];
        }

        return $scenarios;
    }
}
