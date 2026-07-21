<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Money\Percentage;
use App\Core\Normative\NormativeRuleResolver;
use App\Tools\FederalPaymentGuideGenerator\Domain\Data\LatePaymentInput;
use App\Tools\FederalPaymentGuideGenerator\Domain\Data\LatePaymentResult;
use App\Tools\FederalPaymentGuideGenerator\Domain\Rules\LatePaymentRule;
use App\Tools\FederalPaymentGuideGenerator\Domain\Rules\RuleCatalog;

final class LatePaymentCalculator
{
    public function __construct(private readonly ?NormativeRuleResolver $resolver = null) {}

    public function calculate(LatePaymentInput $input): LatePaymentResult
    {
        $resolver = $this->resolver ?? new NormativeRuleResolver;
        $resolved = $resolver->resolveCurrent(
            RuleCatalog::latePaymentCharges(),
            RuleCatalog::LATE_PAYMENT_IDENTIFIER,
            ReferenceDate::fromDateTime($input->dueDate),
        );

        /** @var LatePaymentRule $rule */
        $rule = $resolved;
        $days = max(0, (int) $input->dueDate->diff($input->paymentDate)->format('%a'));
        $dailyMillionths = $rule->dailyPenaltyRate->millionthsOfPercent();
        $maximumMillionths = $rule->maximumPenaltyRate->millionthsOfPercent();
        $penaltyMillionths = min($maximumMillionths, $days * $dailyMillionths);
        $penaltyPercent = $this->formatHundredths(intdiv($penaltyMillionths, 10_000));
        $penalty = Percentage::fromString($penaltyPercent);
        $interest = $days === 0 ? Percentage::zero() : Percentage::fromString($input->selicAccumulatedPercent);

        $penaltyAmount = $input->principal->percentage($penalty);
        $interestAmount = $input->principal->percentage($interest);
        $total = $input->principal->add($penaltyAmount)->add($interestAmount);

        return new LatePaymentResult(
            principal: $input->principal,
            calendarDaysLate: $days,
            penaltyPercent: $penaltyPercent,
            penalty: $penaltyAmount,
            interestPercent: $interest->toDecimalString(),
            interest: $interestAmount,
            total: $total,
            warnings: [
                'A taxa Selic acumulada deve ser conferida na fonte oficial para o período informado.',
                'O cálculo não substitui a emissão e validação final no SicalcWeb ou sistema oficial aplicável.',
            ],
            normativeRule: $rule->normativeMetadata()->toArray(),
        );
    }

    private function formatHundredths(int $value): string
    {
        return intdiv($value, 100).'.'.str_pad((string) ($value % 100), 2, '0', STR_PAD_LEFT);
    }
}
