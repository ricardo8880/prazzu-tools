<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Taxation;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Taxation\Contracts\TaxEstimateProvider;
use App\Core\Taxation\Data\TaxEstimateItem;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Core\Taxation\Data\TaxEstimateResult;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Domain\Rules\ActualProfitTaxRule;
use InvalidArgumentException;

final readonly class ActualProfitTaxEstimateProvider implements TaxEstimateProvider
{
    public const REGIME = 'lucro_real';

    public function __construct(private ActualProfitTaxRule $rule) {}

    public function regime(): string
    {
        return self::REGIME;
    }

    public function supports(TaxEstimateRequest $request): bool
    {
        return BusinessActivity::tryFrom($request->activity) !== null
            && BusinessActivity::tryFrom($request->activity) !== BusinessActivity::Mixed
            && $request->indirectTaxRate !== null
            && $request->monthlyOperatingCosts !== null
            && $request->monthlyDeductibleExpenses !== null
            && $request->monthlyPisCofinsCreditBase !== null
            && $this->rule->supports($request->referenceDate);
    }

    public function estimate(TaxEstimateRequest $request): TaxEstimateResult
    {
        if (! $this->supports($request)) {
            throw new InvalidArgumentException('O cenário informado não é suportado pelo estimador do Lucro Real.');
        }

        $activity = BusinessActivity::from($request->activity);
        $taxableProfit = $this->taxableProfit($request);
        $irpj = $taxableProfit->percentage($this->rule->irpjRate());
        $additionalBase = Money::fromMinor(
            max(0, $taxableProfit->minorAmount() - 2_000_000),
            $taxableProfit->currency(),
        );
        $irpjAdditional = $additionalBase->percentage($this->rule->irpjAdditionalRate());
        $csll = $taxableProfit->percentage($this->rule->csllRate());

        $pisDebit = $request->monthlyRevenue->percentage($this->rule->pisRate());
        $pisCredit = $request->monthlyPisCofinsCreditBase->percentage($this->rule->pisRate());
        $pis = Money::fromMinor(max(0, $pisDebit->minorAmount() - $pisCredit->minorAmount()), $pisDebit->currency());

        $cofinsDebit = $request->monthlyRevenue->percentage($this->rule->cofinsRate());
        $cofinsCredit = $request->monthlyPisCofinsCreditBase->percentage($this->rule->cofinsRate());
        $cofins = Money::fromMinor(max(0, $cofinsDebit->minorAmount() - $cofinsCredit->minorAmount()), $cofinsDebit->currency());

        $indirectTaxes = $request->monthlyRevenue->percentage($request->indirectTaxRate);

        $items = [
            $this->item('IRPJ', 'IRPJ sobre o lucro tributável estimado', $irpj, $this->rule->irpjRate()),
            $this->item('IRPJ_ADDITIONAL', 'Adicional de IRPJ', $irpjAdditional, $this->rule->irpjAdditionalRate()),
            $this->item('CSLL', 'CSLL sobre o lucro tributável estimado', $csll, $this->rule->csllRate()),
            $this->item('PIS', 'PIS/Pasep não cumulativo líquido estimado', $pis, $this->rule->pisRate()),
            $this->item('COFINS', 'Cofins não cumulativa líquida estimada', $cofins, $this->rule->cofinsRate()),
            $this->item('INDIRECT_TAXES', $this->indirectTaxLabel($activity), $indirectTaxes, $request->indirectTaxRate),
        ];

        $monthlyTotal = Money::zero($request->monthlyRevenue->currency());
        foreach ($items as $item) {
            $monthlyTotal = $monthlyTotal->add($item->monthlyAmount);
        }

        $warnings = [
            'O lucro tributável foi aproximado pela receita menos custos operacionais e despesas dedutíveis informadas, sem adições, exclusões ou compensações do e-Lalur/e-Lacs.',
            'A base de créditos de PIS/Cofins deve conter somente aquisições que efetivamente gerem crédito no regime não cumulativo.',
            'Prejuízo fiscal, base negativa de CSLL, limitações de compensação, incentivos, retenções e receitas não operacionais não foram considerados.',
            'A estimativa não substitui escrituração contábil, apuração trimestral ou anual e revisão profissional.',
        ];

        if ($request->referenceDate->format('Y') === '2026') {
            $warnings[] = 'Em 2026, CBS e IBS estão em fase de teste e os valores eventualmente recolhidos são compensados com PIS/Cofins; este estimador não os soma como ônus adicional.';
        }

        return new TaxEstimateResult(
            regime: self::REGIME,
            monthlyTotal: $monthlyTotal,
            annualTotal: $monthlyTotal->multiply(12),
            items: $items,
            assumptions: [
                'Lucro tributável mensal estimado em '.$taxableProfit->formatPtBr().'.',
                'IRPJ calculado a 15%, com adicional de 10% sobre a parcela mensal do lucro que exceder R$ 20.000,00.',
                'CSLL calculada a 9% para pessoa jurídica em geral.',
                'PIS/Pasep e Cofins estimados no regime não cumulativo pelas alíquotas de 1,65% e 7,6%, descontando créditos sobre a base mensal informada.',
                'Tributos indiretos calculados pela alíquota efetiva informada de '.$request->indirectTaxRate->toDecimalString().'%.',
                'Projeção anual calculada pela repetição do cenário mensal por 12 meses.',
                'Regra '.$this->rule::VERSION.' vigente de '.$this->rule::VALID_FROM.' até '.$this->rule::VALID_UNTIL.'.',
            ],
            warnings: $warnings,
        );
    }

    private function taxableProfit(TaxEstimateRequest $request): Money
    {
        $profitMinor = $request->monthlyRevenue->minorAmount()
            - $request->monthlyOperatingCosts->minorAmount()
            - $request->monthlyDeductibleExpenses->minorAmount();

        return Money::fromMinor(max(0, $profitMinor), $request->monthlyRevenue->currency());
    }

    private function item(string $code, string $label, Money $monthly, Percentage $rate): TaxEstimateItem
    {
        return new TaxEstimateItem($code, $label, $monthly, $monthly->multiply(12), $rate);
    }

    private function indirectTaxLabel(BusinessActivity $activity): string
    {
        return match ($activity) {
            BusinessActivity::Commerce => 'ICMS estimado pela alíquota efetiva informada',
            BusinessActivity::Industry => 'ICMS e IPI estimados pela alíquota efetiva informada',
            BusinessActivity::Services, BusinessActivity::AccountingServices => 'ISS estimado pela alíquota efetiva informada',
            BusinessActivity::Mixed => throw new InvalidArgumentException('Atividades mistas exigem receitas segregadas.'),
        };
    }
}
