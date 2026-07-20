<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Domain\Validators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonScenario;

final class TaxComparisonScenarioValidator
{
    public function validate(TaxComparisonScenario $scenario): void
    {
        $this->assertPositive($scenario->monthlyRevenue, 'O faturamento mensal deve ser maior que zero.');
        $this->assertPositive(
            $scenario->revenueLastTwelveMonths,
            'A receita acumulada dos últimos 12 meses deve ser maior que zero.',
        );
        $this->assertNotNegative(
            $scenario->payrollLastTwelveMonths,
            'A folha dos últimos 12 meses não pode ser negativa.',
        );
        $this->assertNotNegative(
            $scenario->monthlyOperatingCosts,
            'Os custos operacionais mensais não podem ser negativos.',
        );
        $this->assertNotNegative(
            $scenario->monthlyDeductibleExpenses,
            'As despesas dedutíveis mensais não podem ser negativas.',
        );
        if ($scenario->monthlyPisCofinsCreditBase !== null) {
            $this->assertNotNegative(
                $scenario->monthlyPisCofinsCreditBase,
                'A base mensal de créditos de PIS/Cofins não pode ser negativa.',
            );

            if ($scenario->monthlyPisCofinsCreditBase->minorAmount() > $scenario->monthlyRevenue->minorAmount()) {
                throw new InvalidValue('A base mensal de créditos de PIS/Cofins não pode superar o faturamento mensal.');
            }
        }
        $this->assertIndirectTaxRate($scenario);
        $this->assertSameCurrency($scenario);
        $this->assertLocation($scenario->state, $scenario->municipality);
    }

    private function assertPositive(Money $money, string $message): void
    {
        if ($money->minorAmount() <= 0) {
            throw new InvalidValue($message);
        }
    }

    private function assertNotNegative(Money $money, string $message): void
    {
        if ($money->minorAmount() < 0) {
            throw new InvalidValue($message);
        }
    }

    private function assertIndirectTaxRate(TaxComparisonScenario $scenario): void
    {
        if ($scenario->indirectTaxRate === null) {
            return;
        }

        $value = $scenario->indirectTaxRate->millionthsOfPercent();

        if ($value < 0 || $value > 100_000_000) {
            throw new InvalidValue('A alíquota efetiva de tributos indiretos deve estar entre 0% e 100%.');
        }
    }

    private function assertSameCurrency(TaxComparisonScenario $scenario): void
    {
        $currency = $scenario->monthlyRevenue->currency();
        $amounts = [
            $scenario->revenueLastTwelveMonths,
            $scenario->payrollLastTwelveMonths,
            $scenario->monthlyOperatingCosts,
            $scenario->monthlyDeductibleExpenses,
            ...($scenario->monthlyPisCofinsCreditBase === null ? [] : [$scenario->monthlyPisCofinsCreditBase]),
        ];

        foreach ($amounts as $amount) {
            if ($amount->currency() !== $currency) {
                throw new InvalidValue('Todos os valores do cenário devem utilizar a mesma moeda.');
            }
        }
    }

    private function assertLocation(?string $state, ?string $municipality): void
    {
        if ($state !== null && preg_match('/^[A-Z]{2}$/', $state) !== 1) {
            throw new InvalidValue('O estado deve ser informado pela sigla em duas letras maiúsculas.');
        }

        if ($municipality !== null && trim($municipality) === '') {
            throw new InvalidValue('O município não pode ser vazio quando informado.');
        }

        if ($municipality !== null && $state === null) {
            throw new InvalidValue('Informe o estado quando o município for preenchido.');
        }
    }
}
