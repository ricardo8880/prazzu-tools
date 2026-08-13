<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;

final readonly class ProjectScenarios
{
    public const FEATURE = 'projections';

    public function __construct(private CalculateTool $calculate) {}

    /** @param array<string, string> $data
     *  @return array<int, array{name:string,result:ToolCalculationResult,input:array<string,string>}>
     */
    public function execute(array $data): array
    {
        $base = $this->baseInput($data);
        $assetRate = Percentage::fromString((string) ($data['asset_growth_rate'] ?? '10'));
        $operatingLiabilityRate = Percentage::fromString((string) ($data['operating_liability_growth_rate'] ?? '5'));
        $financialLiabilityRate = Percentage::fromString((string) ($data['financial_liability_growth_rate'] ?? '0'));

        $projected = $base;
        foreach (['cash', 'receivables', 'inventory', 'other_current_assets'] as $field) {
            $projected[$field] = $this->adjust($base[$field], $assetRate);
        }
        foreach (['suppliers', 'other_operating_liabilities'] as $field) {
            $projected[$field] = $this->adjust($base[$field], $operatingLiabilityRate);
        }
        foreach (['loans', 'other_current_liabilities'] as $field) {
            $projected[$field] = $this->adjust($base[$field], $financialLiabilityRate);
        }

        return [
            ['name' => 'Posição atual', 'result' => $this->calculate->execute($base), 'input' => $base],
            ['name' => 'Cenário projetado', 'result' => $this->calculate->execute($projected), 'input' => $projected],
        ];
    }

    /** @param array<string,string> $data @return array<string,string> */
    private function baseInput(array $data): array
    {
        $fields = ['cash', 'receivables', 'inventory', 'other_current_assets', 'suppliers', 'other_operating_liabilities', 'loans', 'other_current_liabilities'];
        $input = [];
        foreach ($fields as $field) {
            $input[$field] = (string) ($data[$field] ?? '0');
        }

        return $input;
    }

    private function adjust(string $amount, Percentage $rate): string
    {
        $money = Money::fromDecimal($amount);
        $adjusted = $money->add($money->percentage($rate));

        return $adjusted->formatPtBr();
    }
}
