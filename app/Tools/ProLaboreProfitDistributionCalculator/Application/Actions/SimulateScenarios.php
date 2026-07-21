<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Data\SimulationInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\ProfitDistributionCalculator;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\ProLaboreCalculator;

final readonly class SimulateScenarios
{
    public function __construct(
        private ProLaboreCalculator $proLaboreCalculator,
        private ProfitDistributionCalculator $profitDistributionCalculator,
    ) {}

    /** @param array<string, mixed> $data
     *  @return array<string, mixed>
     */
    public function execute(array $data): array
    {
        $input = new SimulationInput($data['scenarios']);
        $scenarios = [];

        foreach ($input->scenarios as $scenarioIndex => $scenario) {
            $scenarioTotals = $this->emptyTotals();
            $partnerTotals = [];
            $periods = [];
            $ruleVersions = [];

            foreach ($scenario['periods'] as $period) {
                $profit = $this->profitDistributionCalculator->calculate($input->profitInput($period));
                $periodTotals = $this->emptyTotals();
                $partners = [];

                foreach ($period['partners'] as $partnerIndex => $partnerData) {
                    $proLabore = $this->proLaboreCalculator->calculate($input->proLaboreInput($period, $partnerData));
                    $distribution = $profit->partners[$partnerIndex]->distributedAmount;
                    $received = $proLabore->netAmount->add($distribution);
                    $label = $profit->partners[$partnerIndex]->label;
                    $key = mb_strtolower(trim($label));

                    $partners[] = [
                        'label' => $label,
                        'ownership_percentage' => $profit->partners[$partnerIndex]->ownershipPercentage->toDecimalString(),
                        'gross_pro_labore_minor' => $proLabore->grossAmount->minorAmount(),
                        'inss_minor' => $proLabore->socialSecurityWithheld->minorAmount(),
                        'irrf_minor' => $proLabore->irrfWithheld->minorAmount(),
                        'net_pro_labore_minor' => $proLabore->netAmount->minorAmount(),
                        'profit_distribution_minor' => $distribution->minorAmount(),
                        'total_received_minor' => $received->minorAmount(),
                        'company_cost_minor' => $proLabore->companyTotalCost->minorAmount(),
                    ];

                    $this->add($periodTotals, 'gross_pro_labore_minor', $proLabore->grossAmount);
                    $this->add($periodTotals, 'inss_minor', $proLabore->socialSecurityWithheld);
                    $this->add($periodTotals, 'irrf_minor', $proLabore->irrfWithheld);
                    $this->add($periodTotals, 'net_pro_labore_minor', $proLabore->netAmount);
                    $this->add($periodTotals, 'profit_distribution_minor', $distribution);
                    $this->add($periodTotals, 'total_received_minor', $received);
                    $this->add($periodTotals, 'company_cost_minor', $proLabore->companyTotalCost);

                    $partnerTotals[$key] ??= ['label' => $label, ...$this->emptyTotals()];
                    $partnerTotals[$key]['gross_pro_labore_minor'] += $proLabore->grossAmount->minorAmount();
                    $partnerTotals[$key]['inss_minor'] += $proLabore->socialSecurityWithheld->minorAmount();
                    $partnerTotals[$key]['irrf_minor'] += $proLabore->irrfWithheld->minorAmount();
                    $partnerTotals[$key]['net_pro_labore_minor'] += $proLabore->netAmount->minorAmount();
                    $partnerTotals[$key]['profit_distribution_minor'] += $distribution->minorAmount();
                    $partnerTotals[$key]['total_received_minor'] += $received->minorAmount();
                    $partnerTotals[$key]['company_cost_minor'] += $proLabore->companyTotalCost->minorAmount();
                    foreach ($proLabore->normativeRules as $rule) {
                        $ruleVersions[(string) ($rule['key'] ?? serialize($rule))] = $rule;
                    }
                }
                $periodTotals['distributed_profit_minor'] = $profit->distributedAmount->minorAmount();
                $periodTotals['undistributed_profit_minor'] = $profit->undistributedBalance->minorAmount();
                foreach (array_keys($this->emptyTotals()) as $field) {
                    $scenarioTotals[$field] += $periodTotals[$field];
                }

                $periods[] = [
                    'competence' => (string) $period['competence'],
                    'partners' => $partners,
                    'totals' => $periodTotals,
                    'maximum_available_profit_minor' => $profit->maximumAvailableProfit->minorAmount(),
                    'undistributed_profit_minor' => $profit->undistributedBalance->minorAmount(),
                    'warnings' => $profit->warnings,
                ];
            }

            $scenarios[] = [
                'name' => trim((string) $scenario['name']) ?: 'Cenário '.($scenarioIndex + 1),
                'periods' => $periods,
                'partner_totals' => array_values($partnerTotals),
                'totals' => $scenarioTotals,
                'normative_rules' => array_values($ruleVersions),
            ];
        }

        return [
            'schema_version' => '4.0.0',
            'scenarios' => $scenarios,
            'comparison' => $this->comparison($scenarios),
        ];
    }

    /** @return array<string, int> */
    private function emptyTotals(): array
    {
        return ['gross_pro_labore_minor' => 0, 'inss_minor' => 0, 'irrf_minor' => 0, 'net_pro_labore_minor' => 0, 'profit_distribution_minor' => 0, 'total_received_minor' => 0, 'company_cost_minor' => 0];
    }

    /** @param array<string, int> $totals */
    private function add(array &$totals, string $field, Money $money): void
    {
        $totals[$field] += $money->minorAmount();
    }

    /** @param array<int, array<string, mixed>> $scenarios
     *  @return array<int, array<string, mixed>>
     */
    private function comparison(array $scenarios): array
    {
        if ($scenarios === []) {
            return [];
        }
        $base = $scenarios[0]['totals'];

        return array_map(static fn (array $scenario): array => [
            'name' => $scenario['name'],
            'total_received_minor' => $scenario['totals']['total_received_minor'],
            'company_cost_minor' => $scenario['totals']['company_cost_minor'],
            'difference_received_minor' => $scenario['totals']['total_received_minor'] - $base['total_received_minor'],
            'difference_company_cost_minor' => $scenario['totals']['company_cost_minor'] - $base['company_cost_minor'],
        ], $scenarios);
    }
}
