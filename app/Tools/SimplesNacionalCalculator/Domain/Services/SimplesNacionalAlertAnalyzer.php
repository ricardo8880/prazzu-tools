<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\FactorRCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Data\TaxAlert;
use App\Tools\SimplesNacionalCalculator\Domain\Data\TaxAlertAnalysis;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Rules\SimplesNacionalTaxTable;

final readonly class SimplesNacionalAlertAnalyzer
{
    private const LIMIT_MINOR = 480_000_000;
    private const LIMIT_WARNING_MINOR = 432_000_000;
    private const FACTOR_R_THRESHOLD_UNITS = 28_000_000;
    private const FACTOR_R_SENSITIVITY_UNITS = 3_000_000;

    public function __construct(
        private SimplesNacionalCalculator $calculator,
        private FactorRCalculator $factorRCalculator,
        private SimplesNacionalTaxTable $taxTable,
    ) {}

    public function analyze(
        TaxAnnex $annex,
        Money $rbt12,
        Money $monthlyRevenue,
        ?Money $payroll12 = null,
        ?Percentage $monthlyGrowth = null,
    ): TaxAlertAnalysis {
        $result = $this->calculator->calculate($annex, $rbt12, $monthlyRevenue);
        $alerts = [];
        $brackets = $this->taxTable->bracketsFor($annex);
        $current = $brackets[$result->bracket->number - 1];
        $distance = $current->revenueUntil->subtract($rbt12);
        $proximity = $current->revenueUntil->percentage(Percentage::fromString('10'));

        if ($result->bracket->number < 6 && $distance->minorAmount() <= max(1, $proximity->minorAmount())) {
            $remaining = Money::fromMinor(max(0, $distance->minorAmount()), $rbt12->currency());
            $alerts[] = new TaxAlert(
                'warning',
                'Mudança de faixa próxima',
                'Faltam '.$remaining->formatPtBr().' para alcançar a próxima faixa do '.$annex->label().'.',
            );
        }

        if ($rbt12->minorAmount() >= self::LIMIT_WARNING_MINOR) {
            $alerts[] = new TaxAlert(
                $rbt12->minorAmount() > self::LIMIT_MINOR ? 'danger' : 'warning',
                'Limite do Simples Nacional',
                $rbt12->minorAmount() > self::LIMIT_MINOR
                    ? 'O RBT12 informado ultrapassa o limite de R$ 4.800.000,00.'
                    : 'O RBT12 já atingiu 90% do limite de R$ 4.800.000,00.',
            );
        }

        if ($payroll12 !== null) {
            $factor = $this->factorRCalculator->calculate($payroll12, $rbt12);
            $gapUnits = self::FACTOR_R_THRESHOLD_UNITS - $factor->factorR->millionthsOfPercent();

            if (abs($gapUnits) <= self::FACTOR_R_SENSITIVITY_UNITS) {
                $requiredPayroll = $rbt12
                    ->percentage(Percentage::fromString('28'))
                    ->subtract($payroll12);
                $requiredPayroll = Money::fromMinor(max(0, $requiredPayroll->minorAmount()), $rbt12->currency());

                $alerts[] = new TaxAlert(
                    'info',
                    'Fator R sensível',
                    $gapUnits > 0
                        ? 'Faltam aproximadamente '.$requiredPayroll->formatPtBr().' de folha acumulada para atingir 28% e enquadrar no Anexo III.'
                        : 'O Fator R está próximo do limite de 28%; acompanhe folha e receita para evitar mudança ao Anexo V.',
                );
            }
        }

        if ($monthlyGrowth !== null && $monthlyGrowth->millionthsOfPercent() > 0) {
            $projectedRevenue = $monthlyRevenue->add($monthlyRevenue->percentage($monthlyGrowth));
            $variation = $projectedRevenue->subtract($monthlyRevenue);
            $projectedRbt12 = $rbt12->add($variation);

            if ($projectedRbt12->minorAmount() > self::LIMIT_MINOR) {
                $projectedRbt12 = Money::fromMinor(self::LIMIT_MINOR, $rbt12->currency());
            }

            $projected = $this->calculator->calculate($annex, $projectedRbt12, $projectedRevenue);

            if ($projected->bracket->number !== $result->bracket->number) {
                $alerts[] = new TaxAlert(
                    'warning',
                    'Crescimento altera a faixa',
                    'Com crescimento de '.str_replace('.', ',', $monthlyGrowth->toDecimalString()).'%, a estimativa passa da faixa '.$result->bracket->number.' para a faixa '.$projected->bracket->number.'.',
                );
            }

            $alerts[] = new TaxAlert(
                'primary',
                'Impacto estimado no DAS',
                'No próximo mês, o DAS estimado seria '.$projected->estimatedDas->formatPtBr().' para faturamento de '.$projectedRevenue->formatPtBr().'.',
            );
        }

        if ($alerts === []) {
            $alerts[] = new TaxAlert(
                'success',
                'Nenhum alerta crítico',
                'Os dados informados não indicam mudança próxima de faixa, limite ou enquadramento.',
            );
        }

        return new TaxAlertAnalysis($alerts);
    }
}
