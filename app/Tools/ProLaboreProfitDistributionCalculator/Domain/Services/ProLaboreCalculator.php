<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services;

use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Normative\NormativeRuleResolver;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProLaboreInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProLaboreResult;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Rules\MonthlyIrrfRule;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Rules\RuleCatalog;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Rules\SocialSecurityRule;

final readonly class ProLaboreCalculator
{
    public function __construct(private ?NormativeRuleResolver $resolver = null) {}

    public function calculate(ProLaboreInput $input): ProLaboreResult
    {
        $resolver = $this->resolver ?? new NormativeRuleResolver;
        /** @var SocialSecurityRule $socialRule */
        $socialRule = $resolver->resolveCurrent(RuleCatalog::socialSecurity(), 'pro_labore.social_security', $input->competence->lastDay());
        /** @var MonthlyIrrfRule $irrfRule */
        $irrfRule = $resolver->resolveCurrent(RuleCatalog::monthlyIrrf(), 'pro_labore.monthly_irrf', $input->competence->lastDay());

        $otherSocialSecurity = $input->otherOfficialSocialSecurity ?? Money::zero();
        $remainingCeiling = max(0, $socialRule->maximumContributionBase->minorAmount() - $otherSocialSecurity->minorAmount());
        $socialBase = Money::fromMinor(min($input->grossAmount->minorAmount(), $remainingCeiling));
        $socialWithheld = $socialBase->percentage($socialRule->withholdingRate);

        $dependentDeduction = $irrfRule->dependentDeduction->multiply($input->dependents);
        $legalDeductions = $socialWithheld->add($dependentDeduction);
        $useSimplified = $irrfRule->simplifiedDeduction->minorAmount() > $legalDeductions->minorAmount();
        $selectedDeduction = $useSimplified ? $irrfRule->simplifiedDeduction : $legalDeductions;
        $irrfBase = Money::fromMinor(max(0, $input->grossAmount->minorAmount() - $selectedDeduction->minorAmount()));

        $bracket = $irrfRule->brackets[array_key_last($irrfRule->brackets)];
        foreach ($irrfRule->brackets as $candidate) {
            if ($candidate->contains($irrfBase)) {
                $bracket = $candidate;
                break;
            }
        }

        $irrfBeforeReduction = $irrfBase->percentage($bracket->rate)->subtract($bracket->deduction);
        if ($irrfBeforeReduction->minorAmount() < 0) {
            $irrfBeforeReduction = Money::zero();
        }

        $reduction = Money::zero();
        $grossMinor = $input->grossAmount->minorAmount();
        if ($grossMinor <= $irrfRule->fullReductionIncomeLimit->minorAmount()) {
            $reduction = Money::fromMinor(min($irrfBeforeReduction->minorAmount(), $irrfRule->fullReductionCap->minorAmount()));
        } elseif ($grossMinor <= $irrfRule->partialReductionIncomeLimit->minorAmount()) {
            $variable = IntegerRounding::divide(
                $grossMinor * $irrfRule->partialReductionCoefficientMillionths,
                1_000_000,
                RoundingMode::HalfUp,
            );
            $reduction = Money::fromMinor(max(0, min($irrfBeforeReduction->minorAmount(), $irrfRule->partialReductionFixedAmount->minorAmount() - $variable)));
        }

        $irrfWithheld = $irrfBeforeReduction->subtract($reduction);
        $net = $input->grossAmount->subtract($socialWithheld)->subtract($irrfWithheld);
        $employerContribution = $input->companyRegime->employerContributionApplies()
            ? $input->grossAmount->percentage($socialRule->employerRate)
            : Money::zero();
        $companyCost = $input->grossAmount->add($employerContribution);

        return new ProLaboreResult(
            grossAmount: $input->grossAmount,
            socialSecurityBase: $socialBase,
            socialSecurityWithheld: $socialWithheld,
            legalIrrfDeductions: $legalDeductions,
            simplifiedIrrfDeduction: $irrfRule->simplifiedDeduction,
            irrfDeductionMethod: $useSimplified ? 'simplified' : 'legal',
            irrfBase: $irrfBase,
            irrfBeforeReduction: $irrfBeforeReduction,
            irrfReduction: $reduction,
            irrfWithheld: $irrfWithheld,
            netAmount: $net,
            employerContribution: $employerContribution,
            companyTotalCost: $companyCost,
            memory: [
                ['step' => 'social_security', 'base_minor' => $socialBase->minorAmount(), 'rate' => $socialRule->withholdingRate->toDecimalString(), 'result_minor' => $socialWithheld->minorAmount()],
                ['step' => 'irrf_deduction', 'method' => $useSimplified ? 'simplified' : 'legal', 'result_minor' => $selectedDeduction->minorAmount()],
                ['step' => 'irrf', 'base_minor' => $irrfBase->minorAmount(), 'rate' => $bracket->rate->toDecimalString(), 'deduction_minor' => $bracket->deduction->minorAmount(), 'before_reduction_minor' => $irrfBeforeReduction->minorAmount(), 'reduction_minor' => $reduction->minorAmount(), 'result_minor' => $irrfWithheld->minorAmount()],
                ['step' => 'net', 'result_minor' => $net->minorAmount()],
                ['step' => 'employer_contribution', 'applies' => $input->companyRegime->employerContributionApplies(), 'result_minor' => $employerContribution->minorAmount()],
            ],
            normativeRules: [$socialRule->normativeMetadata()->toArray(), $irrfRule->normativeMetadata()->toArray()],
        );
    }
}
