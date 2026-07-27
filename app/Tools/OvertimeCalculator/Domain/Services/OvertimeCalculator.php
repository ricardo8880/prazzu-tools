<?php

declare(strict_types=1);

namespace App\Tools\OvertimeCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Math\IntegerRounding;
use App\Core\Math\RoundingMode;
use App\Core\Money\Money;
use App\Core\Normative\NormativeRuleResolver;
use App\Core\Normative\NormativeRuleSnapshot;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Tools\OvertimeCalculator\Domain\Data\OvertimeInput;
use App\Tools\OvertimeCalculator\Domain\Data\OvertimeResult;
use App\Tools\OvertimeCalculator\Domain\Rules\LaborCompensationRule;
use App\Tools\OvertimeCalculator\Domain\Rules\RuleCatalog;

final class OvertimeCalculator
{
    public function calculate(OvertimeInput $input): OvertimeResult
    {
        $date = ReferenceDate::fromString($input->competence->toString().'-01');
        $rule = (new NormativeRuleResolver)->resolveCurrent(RuleCatalog::laborCompensation(), 'overtime.labor_compensation', $date);
        assert($rule instanceof LaborCompensationRule);
        $hourly = $input->baseSalary->divide($input->monthlyHours);
        $ot50 = $this->hoursWithPremium($hourly, $input->overtime50Thousandths, 50_000_000);
        $ot100 = $this->hoursWithPremium($hourly, $input->overtime100Thousandths, 100_000_000);
        $custom = $this->hoursWithPremium($hourly, $input->customOvertimeThousandths, $input->customPremium->millionthsOfPercent());

        // 52m30s = 3150s. Clock-hours are converted to statutory night-hours by 3600/3150 = 8/7.
        $nightEquivalent = IntegerRounding::divide($input->nightClockThousandths * 3600, $rule->reducedNightHourSeconds, RoundingMode::HalfUp);
        $nightBase = $this->hoursValue($hourly, $nightEquivalent);
        $nightPremium = $nightBase->percentage($rule->minimumNightPremium);
        $nightOtBase = $this->hoursWithPremium($hourly, $input->nightOvertimeThousandths, $input->nightOvertimePremium->millionthsOfPercent());
        $nightOvertime = $nightOtBase->add($nightOtBase->percentage($rule->minimumNightPremium));

        $variable = $ot50->add($ot100)->add($custom)->add($nightPremium)->add($nightOvertime);
        $dsr = Money::zero();
        if ($input->includeDsr && $input->workingDays > 0 && $input->restDays > 0) {
            $dsr = Money::fromMinor(IntegerRounding::divide($variable->minorAmount() * $input->restDays, $input->workingDays, RoundingMode::HalfUp));
        }
        $monthly = $variable->add($dsr);
        $thirteenth = $input->includeReflexes ? $monthly->divide(12) : Money::zero();
        $vacation = $input->includeReflexes ? $monthly->divide(12) : Money::zero();
        $vacationThird = $input->includeReflexes ? $vacation->divide(3) : Money::zero();
        $fgts = $input->includeReflexes ? $monthly->add($thirteenth)->percentage(\App\Core\Money\Percentage::fromString('8')) : Money::zero();

        $memory = new CalculationMemory('1.0.0', [
            new CalculationMemoryStep('hourly_rate', 'Valor da hora normal', 'salário-base ÷ divisor mensal', ['salary_minor'=>$input->baseSalary->minorAmount(),'monthly_hours'=>$input->monthlyHours], $hourly->minorAmount(), 'HalfUp em centavos.'),
            new CalculationMemoryStep('overtime', 'Horas extras', 'hora normal × horas × (1 + adicional)', ['h50'=>$input->overtime50Thousandths,'h100'=>$input->overtime100Thousandths,'custom'=>$input->customOvertimeThousandths], $ot50->add($ot100)->add($custom)->minorAmount(), 'HalfUp em centavos.'),
            new CalculationMemoryStep('night', 'Adicional noturno', 'horas-relógio × 3600/3150 × hora normal × 20%', ['night_clock_thousandths'=>$input->nightClockThousandths,'reduced_hour_seconds'=>$rule->reducedNightHourSeconds], $nightPremium->minorAmount(), 'Conversão da hora reduzida e HalfUp.'),
            new CalculationMemoryStep('dsr', 'DSR sobre variáveis', 'variáveis ÷ dias úteis × repousos/feriados informados', ['variable_minor'=>$variable->minorAmount(),'working_days'=>$input->workingDays,'rest_days'=>$input->restDays], $dsr->minorAmount(), 'Estimativa conforme calendário informado.'),
            new CalculationMemoryStep('monthly_total', 'Total variável do mês', 'horas extras + adicional noturno + DSR', ['variable_minor'=>$variable->minorAmount(),'dsr_minor'=>$dsr->minorAmount()], $monthly->minorAmount()),
        ], [NormativeRuleSnapshot::fromRule($rule, $date)], [
            'Aplicável ao empregado urbano CLT comum; categorias especiais e normas coletivas podem estabelecer regras superiores ou diferentes.',
            'O adicional de hora extra informado nunca deve ser inferior ao mínimo legal aplicável; convenção/acordo coletivo pode prever percentual maior.',
            'O cálculo de DSR usa os dias úteis e repousos/feriados informados pelo usuário e pressupõe habitualidade quando o reflexo for devido.',
            'Reflexos de 13º, férias + 1/3 e FGTS são projeções de conveniência e não substituem apuração de folha ou análise da norma coletiva.',
            'A ferramenta não decide banco de horas, compensação, jornada 12x36, trabalho rural, doméstico ou categorias com regramento especial.',
        ], true);

        return new OvertimeResult($hourly,$ot50,$ot100,$custom,$nightPremium,$nightOvertime,$variable,$dsr,$monthly,$thirteenth,$vacation,$vacationThird,$fgts,$memory);
    }

    private function hoursValue(Money $hourly, int $thousandths): Money
    { return Money::fromMinor(IntegerRounding::divide($hourly->minorAmount() * $thousandths, 1000, RoundingMode::HalfUp)); }

    private function hoursWithPremium(Money $hourly, int $thousandths, int $premiumMillionths): Money
    {
        $base = $this->hoursValue($hourly, $thousandths);
        return $base->add(Money::fromMinor(IntegerRounding::divide($base->minorAmount() * $premiumMillionths, 100_000_000, RoundingMode::HalfUp)));
    }
}
