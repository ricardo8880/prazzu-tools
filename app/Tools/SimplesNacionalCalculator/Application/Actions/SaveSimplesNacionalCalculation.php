<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Tools\SimplesNacionalCalculator\Infrastructure\Repositories\SimplesNacionalCalculationRepository;

final readonly class SaveSimplesNacionalCalculation
{
    public function __construct(
        private CalculateSimplesNacional $calculate,
        private SimplesNacionalCalculationRepository $calculations,
    ) {}

    /** @param array{company_name: string, reference_month: string, annex: string, rbt12: string, monthly_revenue: string} $input */
    public function execute(array $input, int $userId): void
    {
        $result = $this->calculate->execute($input);
        $payload = $result->toArray();

        $this->calculations->create([
            'user_id' => $userId,
            'session_key' => null,
            'company_name' => $input['company_name'],
            'reference_month' => $input['reference_month'].'-01',
            'annex' => $result->annex->value,
            'rbt12_cents' => $result->rbt12->minorAmount(),
            'monthly_revenue_cents' => $result->monthlyRevenue->minorAmount(),
            'estimated_das_cents' => $result->estimatedDas->minorAmount(),
            'effective_rate' => str_replace(['%', ','], ['', '.'], (string) $payload['effective_rate']),
            'payload' => $payload,
        ]);
    }
}
