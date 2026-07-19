<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Tools\AccountingFeesCalculator\Domain\Data\AccountingFeesResult;
use App\Tools\AccountingFeesCalculator\Domain\Enums\BusinessSegment;
use App\Tools\AccountingFeesCalculator\Domain\Enums\OperationalComplexity;
use App\Tools\AccountingFeesCalculator\Domain\Enums\TaxRegime;

final class AccountingFeesCalculator
{
    public const RULE_VERSION = '1.0.0';

    public function calculate(
        Money $monthlyRevenue,
        int $employees,
        int $partners,
        int $monthlyInvoices,
        int $monthlyBankTransactions,
        TaxRegime $taxRegime,
        BusinessSegment $segment,
        OperationalComplexity $complexity,
    ): AccountingFeesResult {
        if ($monthlyRevenue->minorAmount() < 0 || min($employees, $partners, $monthlyInvoices, $monthlyBankTransactions) < 0) {
            throw new InvalidValue('Os valores informados não podem ser negativos.');
        }

        if ($partners < 1) {
            throw new InvalidValue('Informe pelo menos um sócio ou titular.');
        }

        $base = Money::fromMinor($taxRegime->baseFeeInCents());
        $revenueAddition = Money::fromMinor($this->revenueAddition($monthlyRevenue->minorAmount()));
        $staffAddition = Money::fromMinor($employees * 6_500);
        $partnersAddition = Money::fromMinor(max(0, $partners - 1) * 4_500);
        $invoiceAddition = Money::fromMinor($this->volumeAddition($monthlyInvoices, 50, 7_500));
        $bankAddition = Money::fromMinor($this->volumeAddition($monthlyBankTransactions, 100, 6_000));

        $subtotal = $base
            ->add($revenueAddition)
            ->add($staffAddition)
            ->add($partnersAddition)
            ->add($invoiceAddition)
            ->add($bankAddition);

        $afterSegment = $this->applyBasisPoints($subtotal, $segment->multiplierBasisPoints());
        $minimum = $this->applyBasisPoints($afterSegment, $complexity->multiplierBasisPoints());
        $recommended = $this->applyBasisPoints($minimum, 11_500);
        $upperReference = $this->applyBasisPoints($minimum, 13_000);
        $score = $this->complexityScore($employees, $monthlyInvoices, $monthlyBankTransactions, $taxRegime, $segment, $complexity);
        $breakdown = $this->buildBreakdown([
            ['label' => 'Base do regime tributário', 'value' => $base],
            ['label' => 'Porte por faturamento', 'value' => $revenueAddition],
            ['label' => 'Processamento de funcionários', 'value' => $staffAddition],
            ['label' => 'Sócios adicionais', 'value' => $partnersAddition],
            ['label' => 'Volume de notas fiscais', 'value' => $invoiceAddition],
            ['label' => 'Movimentações financeiras', 'value' => $bankAddition],
        ], $subtotal);

        return new AccountingFeesResult(
            minimumFee: $minimum,
            recommendedFee: $recommended,
            upperReferenceFee: $upperReference,
            complexityScore: $score,
            complexityLevel: $this->scoreLabel($score),
            breakdown: $breakdown,
            appliedFactors: [
                ['label' => $segment->label(), 'percentage' => $segment->multiplierBasisPoints() - 10_000],
                ['label' => 'Complexidade '.$complexity->label(), 'percentage' => $complexity->multiplierBasisPoints() - 10_000],
                ['label' => 'Margem operacional recomendada', 'percentage' => 1_500],
            ],
            recommendations: $this->recommendations(
                employees: $employees,
                invoices: $monthlyInvoices,
                transactions: $monthlyBankTransactions,
                regime: $taxRegime,
                segment: $segment,
                complexity: $complexity,
                score: $score,
            ),
            ruleVersion: self::RULE_VERSION,
        );
    }

    /**
     * @param array<int, array{label: string, value: Money}> $items
     * @return array<int, array{label: string, value: Money, percentage: int}>
     */
    private function buildBreakdown(array $items, Money $subtotal): array
    {
        $total = max(1, $subtotal->minorAmount());

        return array_map(
            static fn (array $item): array => [
                'label' => $item['label'],
                'value' => $item['value'],
                'percentage' => (int) round(($item['value']->minorAmount() / $total) * 100),
            ],
            $items,
        );
    }

    /** @return array<int, array{icon: string, title: string, description: string}> */
    private function recommendations(
        int $employees,
        int $invoices,
        int $transactions,
        TaxRegime $regime,
        BusinessSegment $segment,
        OperationalComplexity $complexity,
        int $score,
    ): array {
        $recommendations = [];

        if ($employees >= 5) {
            $recommendations[] = [
                'icon' => 'bi-people',
                'title' => 'Destaque o escopo trabalhista',
                'description' => 'A quantidade de funcionários aumenta a responsabilidade mensal. Detalhe admissões, folha, férias, desligamentos e obrigações acessórias na proposta.',
            ];
        }

        if ($invoices > 100 || $transactions > 200) {
            $recommendations[] = [
                'icon' => 'bi-receipt',
                'title' => 'Defina franquias de volume',
                'description' => 'Inclua no contrato os volumes mensais contemplados e uma regra objetiva para cobranças adicionais quando houver excesso.',
            ];
        }

        if ($regime === TaxRegime::LucroReal || $complexity === OperationalComplexity::VeryHigh) {
            $recommendations[] = [
                'icon' => 'bi-shield-check',
                'title' => 'Proteja a responsabilidade técnica',
                'description' => 'Considere revisão periódica do preço e cláusulas específicas para controles, conciliações, auditorias e demandas extraordinárias.',
            ];
        }

        if (in_array($segment, [BusinessSegment::Industry, BusinessSegment::Construction, BusinessSegment::Healthcare], true)) {
            $recommendations[] = [
                'icon' => 'bi-building-gear',
                'title' => 'Valorize a especialização setorial',
                'description' => 'O segmento exige conhecimento específico. Apresente esse domínio como diferencial e evite competir apenas por preço.',
            ];
        }

        if ($score >= 51) {
            $recommendations[] = [
                'icon' => 'bi-arrow-repeat',
                'title' => 'Preveja revisão recorrente',
                'description' => 'Para operações de complexidade alta, estabeleça revisão de escopo e honorários em intervalos menores que o reajuste anual.',
            ];
        }

        if ($recommendations === []) {
            $recommendations[] = [
                'icon' => 'bi-check2-circle',
                'title' => 'Mantenha o escopo objetivo',
                'description' => 'A operação é relativamente simples. Uma proposta clara, com serviços incluídos e limites definidos, ajuda a preservar a margem.',
            ];
        }

        return array_slice($recommendations, 0, 4);
    }

    private function revenueAddition(int $revenueCents): int
    {
        return match (true) {
            $revenueCents <= 8_100_000 => 0,
            $revenueCents <= 20_000_000 => 10_000,
            $revenueCents <= 50_000_000 => 25_000,
            $revenueCents <= 100_000_000 => 50_000,
            $revenueCents <= 300_000_000 => 90_000,
            default => 150_000,
        };
    }

    private function volumeAddition(int $quantity, int $included, int $pricePerBlock): int
    {
        $excess = max(0, $quantity - $included);
        $blocks = $excess === 0 ? 0 : intdiv($excess + $included - 1, $included);

        return $blocks * $pricePerBlock;
    }

    private function applyBasisPoints(Money $amount, int $basisPoints): Money
    {
        return Money::fromMinor(IntegerRounding::divide(
            $amount->minorAmount() * $basisPoints,
            10_000,
            RoundingMode::HalfUp,
        ));
    }

    private function complexityScore(
        int $employees,
        int $invoices,
        int $transactions,
        TaxRegime $regime,
        BusinessSegment $segment,
        OperationalComplexity $complexity,
    ): int {
        $score = match ($regime) {
            TaxRegime::Mei => 5,
            TaxRegime::SimplesNacional => 15,
            TaxRegime::LucroPresumido => 25,
            TaxRegime::LucroReal => 40,
        };
        $score += min(20, $employees * 2);
        $score += min(15, intdiv($invoices, 50) * 3);
        $score += min(10, intdiv($transactions, 100) * 2);
        $score += intdiv($segment->multiplierBasisPoints() - 10_000, 100);
        $score += intdiv($complexity->multiplierBasisPoints() - 10_000, 250);

        return min(100, $score);
    }

    private function scoreLabel(int $score): string
    {
        return match (true) {
            $score <= 25 => 'Baixa',
            $score <= 50 => 'Média',
            $score <= 75 => 'Alta',
            default => 'Muito alta',
        };
    }
}
