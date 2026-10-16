<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Domain\Services;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\ProfitDistributionCalculator\Domain\Data\PartnerProfitDistribution;
use App\Tools\ProfitDistributionCalculator\Domain\Data\PartnerProfitShare;
use App\Tools\ProfitDistributionCalculator\Domain\Data\ProfitDistributionInput;
use App\Tools\ProfitDistributionCalculator\Domain\Data\ProfitDistributionResult;
use App\Tools\ProfitDistributionCalculator\Domain\Enums\ProfitDistributionCriterion;

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
            throw new InvalidValue('The planned distribution cannot be negative.');
        }

        if ($requested->minorAmount() > $maximumAvailable->minorAmount()) {
            throw new InvalidValue('The planned distribution cannot exceed the available amount in this simplified model.');
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
            $warnings[] = 'No amount is available for distribution under the supplied assumptions.';
        }
        if ($distributed->minorAmount() < $maximumAvailable->minorAmount()) {
            $warnings[] = 'Part of the available amount will remain undistributed.';
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
                throw new InvalidValue('Profit, losses, reserves, and prior distributions must be nonnegative.');
            }
        }
    }

    /** @param list<PartnerProfitShare> $partners */
    private function validatePartners(array $partners): void
    {
        if ($partners === []) {
            throw new InvalidValue('Enter at least one owner.');
        }

        $keys = [];
        $totalOwnership = 0;
        foreach ($partners as $partner) {
            if (isset($keys[$partner->key])) {
                throw new InvalidValue('Each owner must have a unique key.');
            }
            $keys[$partner->key] = true;

            $ownership = $partner->ownershipPercentage->millionthsOfPercent();
            if ($ownership < 0 || $ownership > 100_000_000) {
                throw new InvalidValue('Ownership percentage must be between 0% and 100%.');
            }
            $totalOwnership += $ownership;
        }

        if ($totalOwnership !== 100_000_000) {
            throw new InvalidValue('Ownership percentages must total exactly 100%.');
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
                throw new InvalidValue('Enter a nonnegative amount for each owner.');
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
            throw new InvalidValue('Owner distribution amounts must equal the planned total distribution.');
        }

        return $result;
    }
}
