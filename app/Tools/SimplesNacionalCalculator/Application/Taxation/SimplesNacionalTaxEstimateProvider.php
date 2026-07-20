<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Taxation;

use App\Core\Taxation\Contracts\TaxEstimateProvider;
use App\Core\Taxation\Data\TaxEstimateItem;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Core\Taxation\Data\TaxEstimateResult;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\FactorRCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class SimplesNacionalTaxEstimateProvider implements TaxEstimateProvider
{
    public const REGIME = 'simples_nacional';

    public function __construct(
        private SimplesNacionalCalculator $calculator,
        private FactorRCalculator $factorRCalculator,
    ) {}

    public function regime(): string
    {
        return self::REGIME;
    }

    public function supports(TaxEstimateRequest $request): bool
    {
        return $request->referenceDate >= new DateTimeImmutable('2018-01-01')
            && in_array($request->activity, ['commerce', 'industry', 'services', 'accounting_services'], true);
    }

    public function estimate(TaxEstimateRequest $request): TaxEstimateResult
    {
        if (! $this->supports($request)) {
            throw new InvalidArgumentException('O cenário informado não é suportado pelo estimador do Simples Nacional.');
        }

        $annex = $this->resolveAnnex($request);
        $result = $this->calculator->calculate($annex, $request->revenueLastTwelveMonths, $request->monthlyRevenue);

        return new TaxEstimateResult(
            regime: self::REGIME,
            monthlyTotal: $result->estimatedDas,
            annualTotal: $result->estimatedDas->multiply(12),
            items: [
                new TaxEstimateItem(
                    code: 'DAS',
                    label: 'Documento de Arrecadação do Simples Nacional',
                    monthlyAmount: $result->estimatedDas,
                    annualAmount: $result->estimatedDas->multiply(12),
                    effectiveRate: $result->effectiveRate,
                ),
            ],
            assumptions: [
                'Enquadramento estimado no '.$result->annex->label().'.',
                'RBT12 considerado: '.$result->rbt12->formatPtBr().'.',
                'Projeção anual calculada pela repetição do resultado mensal por 12 meses.',
                'Regra aplicada: '.$result->ruleVersion.' vigente desde '.$result->ruleValidFrom.'.',
            ],
            warnings: $request->activity === 'accounting_services'
                ? ['A atividade contábil foi estimada no Anexo III; confirme CNAE, segregações e particularidades da operação.']
                : [],
        );
    }

    private function resolveAnnex(TaxEstimateRequest $request): TaxAnnex
    {
        return match ($request->activity) {
            'commerce' => TaxAnnex::I,
            'industry' => TaxAnnex::II,
            'accounting_services' => TaxAnnex::III,
            'services' => $this->factorRCalculator
                ->calculate($request->payrollLastTwelveMonths, $request->revenueLastTwelveMonths)
                ->applicableAnnex,
            default => throw new InvalidArgumentException('Não foi possível determinar o anexo do Simples Nacional.'),
        };
    }
}
