<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingFeeCalculationRepository;

final readonly class BuildAccountingFeeHistoryExport
{
    public function __construct(private AccountingFeeCalculationRepository $calculations) {}

    /** @return list<array<int, int|string|null>> */
    public function execute(AccountingFeesOwner $owner): array
    {
        return $this->calculations->all($owner)
            ->map(static fn ($calculation): array => [
                $calculation->created_at?->format('d/m/Y H:i'),
                data_get($calculation->input, 'monthly_revenue'),
                data_get($calculation->input, 'tax_regime'),
                data_get($calculation->input, 'employees'),
                data_get($calculation->input, 'monthly_invoices'),
                data_get($calculation->result, 'complexity_level'),
                data_get($calculation->result, 'minimum_fee'),
                data_get($calculation->result, 'recommended_fee'),
                data_get($calculation->result, 'upper_reference_fee'),
            ])
            ->values()
            ->all();
    }
}
