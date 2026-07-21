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
use App\Tools\TaxRegimeComparator\Domain\Rules\PresumedProfitTaxRule;
use InvalidArgumentException;

final readonly class PresumedProfitTaxEstimateProvider implements TaxEstimateProvider
{
    public const REGIME = 'lucro_presumido';

    public function __construct(private PresumedProfitTaxRule $rule) {}

    public function regime(): string
    {
        return self::REGIME;
    }

    public function supports(TaxEstimateRequest $request): bool
    {
        $activity = BusinessActivity::tryFrom($request->activity);

        return $activity !== null
            && $request->indirectTaxRate !== null
            && $this->rule->supports(
                $request->referenceDate,
                $activity,
                $request->revenueLastTwelveMonths->minorAmount(),
            );
    }

    public function estimate(TaxEstimateRequest $request): TaxEstimateResult
    {
        if (! $this->supports($request)) {
            throw new InvalidArgumentException('O cenário informado não é suportado pelo estimador do Lucro Presumido.');
        }

        $activity = BusinessActivity::from($request->activity);
        $irpjBase = $request->monthlyRevenue->percentage($this->rule->irpjPresumption($activity));
        $csllBase = $request->monthlyRevenue->percentage($this->rule->csllPresumption($activity));

        $irpj = $irpjBase->percentage($this->rule->irpjRate());
        $additionalBaseMinor = max(0, $irpjBase->minorAmount() - 2_000_000);
        $irpjAdditional = Money::fromMinor($additionalBaseMinor, $irpjBase->currency())
            ->percentage($this->rule->irpjAdditionalRate());
        $csll = $csllBase->percentage($this->rule->csllRate());
        $pis = $request->monthlyRevenue->percentage($this->rule->pisRate());
        $cofins = $request->monthlyRevenue->percentage($this->rule->cofinsRate());
        $indirectTaxes = $request->monthlyRevenue->percentage($request->indirectTaxRate);

        $items = [
            $this->item('IRPJ', 'Imposto de Renda da Pessoa Jurídica', $irpj, $this->rule->irpjRate()),
            $this->item('IRPJ_ADDITIONAL', 'Adicional de IRPJ', $irpjAdditional, $this->rule->irpjAdditionalRate()),
            $this->item('CSLL', 'Contribuição Social sobre o Lucro Líquido', $csll, $this->rule->csllRate()),
            $this->item('PIS', 'PIS/Pasep cumulativo', $pis, $this->rule->pisRate()),
            $this->item('COFINS', 'Cofins cumulativa', $cofins, $this->rule->cofinsRate()),
            $this->item('INDIRECT_TAXES', $this->indirectTaxLabel($activity), $indirectTaxes, $request->indirectTaxRate),
        ];

        $monthlyTotal = Money::zero($request->monthlyRevenue->currency());
        foreach ($items as $item) {
            $monthlyTotal = $monthlyTotal->add($item->monthlyAmount);
        }

        return new TaxEstimateResult(
            regime: self::REGIME,
            monthlyTotal: $monthlyTotal,
            annualTotal: $monthlyTotal->multiply(12),
            items: $items,
            assumptions: [
                'IRPJ presumido em '.$this->rule->irpjPresumption($activity)->toDecimalString().'% da receita e tributado à alíquota de 15%.',
                'CSLL presumida em '.$this->rule->csllPresumption($activity)->toDecimalString().'% da receita e tributada à alíquota de 9%.',
                'PIS/Pasep e Cofins estimados pelo regime cumulativo, às alíquotas de 0,65% e 3%.',
                'Tributos indiretos calculados pela alíquota efetiva informada de '.$request->indirectTaxRate->toDecimalString().'%.',
                'Projeção anual calculada pela repetição do cenário mensal por 12 meses.',
                'Regra '.$this->rule::VERSION.' com vigência base desde '.$this->rule::VALID_FROM.'.',
            ],
            warnings: [
                'A alíquota efetiva de tributos indiretos deve considerar benefícios, créditos, retenções e regras locais aplicáveis.',
                'Receitas financeiras, ganhos de capital e demais receitas não operacionais não estão incluídos neste cenário.',
                'A estimativa não substitui apuração trimestral nem revisão profissional do enquadramento.',
            ],
        );
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
