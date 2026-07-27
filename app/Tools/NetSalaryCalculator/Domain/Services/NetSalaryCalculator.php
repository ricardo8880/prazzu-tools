<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Normative\NormativeRuleResolver;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Tools\NetSalaryCalculator\Domain\Data\NetSalaryInput;
use App\Tools\NetSalaryCalculator\Domain\Data\NetSalaryResult;
use App\Tools\NetSalaryCalculator\Domain\Rules\EmployeeSocialSecurityRule;
use App\Core\Tax\Normative\MonthlyPersonalIncomeTaxRule;
use App\Tools\NetSalaryCalculator\Domain\Rules\RuleCatalog;

final class NetSalaryCalculator
{
    public function calculate(NetSalaryInput $input): NetSalaryResult
    {
        $referenceDate = ReferenceDate::fromString($input->competence->toString().'-01');
        $resolver = new NormativeRuleResolver;
        $socialRule = $resolver->resolveCurrent(RuleCatalog::employeeSocialSecurity(), 'net_salary.employee_social_security', $referenceDate);
        $irrfRule = $resolver->resolveCurrent(RuleCatalog::monthlyIrrf(), 'tax.irrf.monthly', $referenceDate);

        assert($socialRule instanceof EmployeeSocialSecurityRule);
        assert($irrfRule instanceof MonthlyPersonalIncomeTaxRule);

        $taxableGross = $input->taxableGross();
        $totalEarnings = $input->totalEarnings();
        $socialBase = Money::fromMinor(min($taxableGross->minorAmount(), $socialRule->maximumContributionBase->minorAmount()));
        $socialWithheld = Money::zero();
        $socialSteps = [];
        $cumulativeContributionNumerator = 0;
        $previousRoundedContributionMinor = 0;

        foreach ($socialRule->brackets as $index => $bracket) {
            $lower = $bracket->lowerLimit->minorAmount();
            $upper = min($socialBase->minorAmount(), $bracket->upperLimit->minorAmount());
            $bandMinor = max(0, $upper - $lower);

            if ($bandMinor === 0) {
                continue;
            }

            $cumulativeContributionNumerator += $bandMinor * $bracket->rate->numerator();
            $cumulativeRoundedContributionMinor = IntegerRounding::divide(
                $cumulativeContributionNumerator,
                $bracket->rate->denominator(),
                RoundingMode::HalfUp,
            );
            $contribution = Money::fromMinor($cumulativeRoundedContributionMinor - $previousRoundedContributionMinor);
            $previousRoundedContributionMinor = $cumulativeRoundedContributionMinor;
            $socialWithheld = Money::fromMinor($cumulativeRoundedContributionMinor);
            $socialSteps[] = new CalculationMemoryStep(
                key: 'inss_band_'.($index + 1),
                label: 'INSS progressivo — faixa '.($index + 1),
                formula: 'parcela da faixa × alíquota da faixa',
                inputs: [
                    'band_minor' => $bandMinor,
                    'rate' => $bracket->rate->toDecimalString(),
                ],
                result: $contribution->minorAmount(),
                roundingPolicy: 'Cálculo progressivo acumulado e arredondamento HalfUp em centavos.',
            );
        }

        $dependentDeductions = $irrfRule->dependentDeduction->multiply($input->dependents);
        $legalDeductions = $socialWithheld->add($dependentDeductions)->add($input->judicialPension);
        $useSimplified = $irrfRule->simplifiedDeduction->minorAmount() > $legalDeductions->minorAmount();
        $selectedDeduction = $useSimplified ? $irrfRule->simplifiedDeduction : $legalDeductions;
        $irrfBase = Money::fromMinor(max(0, $taxableGross->minorAmount() - $selectedDeduction->minorAmount()));

        $irrfBracket = null;
        foreach ($irrfRule->brackets as $candidate) {
            if ($candidate->contains($irrfBase)) {
                $irrfBracket = $candidate;
                break;
            }
        }
        assert($irrfBracket !== null);

        $irrfBeforeReduction = $irrfBase->percentage($irrfBracket->rate)->subtract($irrfBracket->deduction);
        if ($irrfBeforeReduction->minorAmount() < 0) {
            $irrfBeforeReduction = Money::zero();
        }

        $reduction = Money::zero();
        if ($taxableGross->minorAmount() <= $irrfRule->fullReductionIncomeLimit->minorAmount()) {
            $reduction = Money::fromMinor(min($irrfBeforeReduction->minorAmount(), $irrfRule->fullReductionCap->minorAmount()));
        } elseif ($taxableGross->minorAmount() <= $irrfRule->partialReductionIncomeLimit->minorAmount()) {
            $variable = IntegerRounding::divide(
                $taxableGross->minorAmount() * $irrfRule->partialReductionCoefficientMillionths,
                1_000_000,
                RoundingMode::HalfUp,
            );
            $reduction = Money::fromMinor(max(0, min(
                $irrfBeforeReduction->minorAmount(),
                $irrfRule->partialReductionFixedAmount->minorAmount() - $variable,
            )));
        }

        $irrfWithheld = $irrfBeforeReduction->subtract($reduction);
        $userDiscounts = $input->userDiscounts();
        $totalDiscounts = $socialWithheld->add($irrfWithheld)->add($userDiscounts);
        $netSalary = $totalEarnings->subtract($totalDiscounts);

        $memory = new CalculationMemory(
            schemaVersion: '1.0.0',
            steps: [
                new CalculationMemoryStep(
                    key: 'taxable_gross',
                    label: 'Remuneração tributável mensal',
                    formula: 'salário-base + proventos tributáveis adicionais',
                    inputs: [
                        'base_salary_minor' => $input->baseSalary->minorAmount(),
                        'taxable_additions_minor' => $input->taxableAdditionalEarnings->minorAmount(),
                    ],
                    result: $taxableGross->minorAmount(),
                ),
                ...$socialSteps,
                new CalculationMemoryStep(
                    key: 'inss',
                    label: 'INSS do empregado',
                    formula: 'soma das contribuições progressivas por faixa, limitada ao teto previdenciário',
                    inputs: [
                        'contribution_base_minor' => $socialBase->minorAmount(),
                        'maximum_base_minor' => $socialRule->maximumContributionBase->minorAmount(),
                    ],
                    result: $socialWithheld->minorAmount(),
                    roundingPolicy: 'As faixas são acumuladas em inteiros escalados e o total é arredondado HalfUp em centavos.',
                ),
                new CalculationMemoryStep(
                    key: 'irrf_deduction',
                    label: 'Dedução usada no IRRF',
                    formula: 'maior entre deduções legais e desconto simplificado mensal',
                    inputs: [
                        'inss_minor' => $socialWithheld->minorAmount(),
                        'dependent_deduction_minor' => $dependentDeductions->minorAmount(),
                        'judicial_pension_minor' => $input->judicialPension->minorAmount(),
                        'legal_deductions_minor' => $legalDeductions->minorAmount(),
                        'simplified_deduction_minor' => $irrfRule->simplifiedDeduction->minorAmount(),
                        'method' => $useSimplified ? 'simplified' : 'legal',
                    ],
                    result: $selectedDeduction->minorAmount(),
                ),
                new CalculationMemoryStep(
                    key: 'irrf',
                    label: 'IRRF mensal',
                    formula: '(base do IRRF × alíquota − parcela a deduzir) − redução mensal de 2026',
                    inputs: [
                        'taxable_income_minor' => $taxableGross->minorAmount(),
                        'base_minor' => $irrfBase->minorAmount(),
                        'rate' => $irrfBracket->rate->toDecimalString(),
                        'bracket_deduction_minor' => $irrfBracket->deduction->minorAmount(),
                        'before_reduction_minor' => $irrfBeforeReduction->minorAmount(),
                        'reduction_minor' => $reduction->minorAmount(),
                    ],
                    result: $irrfWithheld->minorAmount(),
                    roundingPolicy: 'Valores monetários calculados em centavos com HalfUp.',
                ),
                new CalculationMemoryStep(
                    key: 'net_salary',
                    label: 'Salário líquido estimado',
                    formula: 'proventos totais − INSS − IRRF − descontos informados',
                    inputs: [
                        'total_earnings_minor' => $totalEarnings->minorAmount(),
                        'inss_minor' => $socialWithheld->minorAmount(),
                        'irrf_minor' => $irrfWithheld->minorAmount(),
                        'user_discounts_minor' => $userDiscounts->minorAmount(),
                    ],
                    result: $netSalary->minorAmount(),
                ),
            ],
            normativeRules: [
                NormativeRuleSnapshot::fromRule($socialRule, $referenceDate),
                NormativeRuleSnapshot::fromRule($irrfRule, $referenceDate),
            ],
            assumptions: [
                'Cálculo mensal para empregado CLT em um único vínculo empregatício.',
                'Proventos informados como tributáveis integram as bases de INSS e IRRF; proventos não tributáveis não integram essas bases.',
                'Pensão alimentícia só deve ser informada quando dedutível segundo as regras legais aplicáveis.',
                'Vale-transporte, alimentação, plano de saúde e outros descontos são valores já apurados pelo usuário; esta ferramenta não determina sua incidência jurídica.',
                'Não inclui férias, 13º salário, rescisão, múltiplos vínculos ou regimes previdenciários especiais.',
            ],
            isEstimate: true,
        );

        return new NetSalaryResult(
            taxableGross: $taxableGross,
            totalEarnings: $totalEarnings,
            socialSecurityBase: $socialBase,
            socialSecurityWithheld: $socialWithheld,
            legalIrrfDeductions: $legalDeductions,
            simplifiedIrrfDeduction: $irrfRule->simplifiedDeduction,
            irrfDeductionMethod: $useSimplified ? 'simplified' : 'legal',
            irrfBase: $irrfBase,
            irrfBeforeReduction: $irrfBeforeReduction,
            irrfReduction: $reduction,
            irrfWithheld: $irrfWithheld,
            userDiscounts: $userDiscounts,
            totalDiscounts: $totalDiscounts,
            netSalary: $netSalary,
            memory: $memory,
        );
    }
}
