<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\PartnerProfitDistribution;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\PartnerProfitShare;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProfitDistributionInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProfitDistributionResult;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\ProfitDistributionCriterion;

final readonly class ProfitDistributionCalculator
{
    public function calculate(ProfitDistributionInput $input): ProfitDistributionResult
    {
        $this->validateNonNegativeAmounts($input);
        $this->validatePartners($input->partners);

        $availableMinor = $input->accountingProfit->minorAmount()
            - $input->accumulatedLosses->minorAmount()
            - $input->reservesAndUnavailableAmounts->minorAmount()
            + $input->adjustments->minorAmount()
            - $input->priorDistributions->minorAmount();

        $maximumAvailable = Money::fromMinor(max(0, $availableMinor));
        $requested = $input->intendedDistribution ?? $maximumAvailable;

        if ($requested->minorAmount() < 0) {
            throw new InvalidValue('O valor pretendido de distribuição não pode ser negativo.');
        }

        if ($requested->minorAmount() > $maximumAvailable->minorAmount()) {
            throw new InvalidValue('A distribuição pretendida não pode superar o lucro máximo disponível.');
        }

        $partners = $input->criterion === ProfitDistributionCriterion::Proportional
            ? $this->distributeProportionally($input->partners, $requested)
            : $this->distributeDefinedAmounts($input->partners, $requested);

        $distributed = Money::fromMinor(array_sum(array_map(
            static fn (PartnerProfitDistribution $partner): int => $partner->distributedAmount->minorAmount(),
            $partners,
        )));

        $warnings = [];
        if ($availableMinor <= 0) {
            $warnings[] = 'Não há lucro disponível para distribuição com as premissas informadas.';
        }
        if ($distributed->minorAmount() < $maximumAvailable->minorAmount()) {
            $warnings[] = 'Parte do lucro disponível permanecerá sem distribuição.';
        }

        return new ProfitDistributionResult(
            accountingProfit: $input->accountingProfit,
            accumulatedLosses: $input->accumulatedLosses,
            reservesAndUnavailableAmounts: $input->reservesAndUnavailableAmounts,
            adjustments: $input->adjustments,
            priorDistributions: $input->priorDistributions,
            maximumAvailableProfit: $maximumAvailable,
            distributedAmount: $distributed,
            undistributedBalance: $maximumAvailable->subtract($distributed),
            partners: $partners,
            memory: [
                ['step' => 'accounting_profit', 'result_minor' => $input->accountingProfit->minorAmount()],
                ['step' => 'subtract_accumulated_losses', 'result_minor' => $input->accumulatedLosses->minorAmount()],
                ['step' => 'subtract_reserves_and_unavailable', 'result_minor' => $input->reservesAndUnavailableAmounts->minorAmount()],
                ['step' => 'apply_adjustments', 'result_minor' => $input->adjustments->minorAmount()],
                ['step' => 'subtract_prior_distributions', 'result_minor' => $input->priorDistributions->minorAmount()],
                ['step' => 'maximum_available_profit', 'result_minor' => $maximumAvailable->minorAmount()],
                ['step' => 'distribution', 'criterion' => $input->criterion->value, 'result_minor' => $distributed->minorAmount()],
            ],
            warnings: $warnings,
        );
    }

    private function validateNonNegativeAmounts(ProfitDistributionInput $input): void
    {
        foreach ([
            $input->accountingProfit,
            $input->accumulatedLosses,
            $input->reservesAndUnavailableAmounts,
            $input->priorDistributions,
        ] as $amount) {
            if ($amount->minorAmount() < 0) {
                throw new InvalidValue('Lucro, prejuízos, reservas e antecipações devem ser informados como valores não negativos.');
            }
        }
    }

    /** @param list<PartnerProfitShare> $partners */
    private function validatePartners(array $partners): void
    {
        if ($partners === []) {
            throw new InvalidValue('Informe pelo menos um sócio para distribuir o lucro.');
        }

        $keys = [];
        $totalOwnership = 0;
        foreach ($partners as $partner) {
            if (isset($keys[$partner->key])) {
                throw new InvalidValue('Cada sócio deve possuir uma chave única na simulação.');
            }
            $keys[$partner->key] = true;

            $ownership = $partner->ownershipPercentage->millionthsOfPercent();
            if ($ownership < 0 || $ownership > 100_000_000) {
                throw new InvalidValue('A participação societária deve estar entre 0% e 100%.');
            }
            $totalOwnership += $ownership;
        }

        if ($totalOwnership !== 100_000_000) {
            throw new InvalidValue('A soma das participações societárias deve ser exatamente 100%.');
        }
    }

    /**
     * @param list<PartnerProfitShare> $partners
     * @return list<PartnerProfitDistribution>
     */
    private function distributeProportionally(array $partners, Money $requested): array
    {
        $result = [];
        $allocated = 0;
        $lastIndex = array_key_last($partners);

        foreach ($partners as $index => $partner) {
            $amount = $index === $lastIndex
                ? Money::fromMinor($requested->minorAmount() - $allocated)
                : $requested->percentage($partner->ownershipPercentage);
            $allocated += $amount->minorAmount();
            $result[] = new PartnerProfitDistribution(
                key: $partner->key,
                ownershipPercentage: $partner->ownershipPercentage,
                distributedAmount: $amount,
                label: $partner->label,
            );
        }

        return $result;
    }

    /**
     * @param list<PartnerProfitShare> $partners
     * @return list<PartnerProfitDistribution>
     */
    private function distributeDefinedAmounts(array $partners, Money $requested): array
    {
        $result = [];
        $total = 0;

        foreach ($partners as $partner) {
            if ($partner->definedAmount === null || $partner->definedAmount->minorAmount() < 0) {
                throw new InvalidValue('Informe um valor não negativo para cada sócio na distribuição por valores definidos.');
            }
            $total += $partner->definedAmount->minorAmount();
            $result[] = new PartnerProfitDistribution(
                key: $partner->key,
                ownershipPercentage: $partner->ownershipPercentage,
                distributedAmount: $partner->definedAmount,
                label: $partner->label,
            );
        }

        if ($total !== $requested->minorAmount()) {
            throw new InvalidValue('A soma dos valores definidos por sócio deve ser igual ao valor pretendido de distribuição.');
        }

        return $result;
    }
}
