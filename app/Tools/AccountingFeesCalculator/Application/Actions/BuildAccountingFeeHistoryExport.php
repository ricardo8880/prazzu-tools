<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

final readonly class BuildAccountingFeeHistoryExport
{
    public function __construct(private ManageAccountingFeesHistory $history) {}

    /** @return list<array<int,int|string|null>> */
    public function execute(int $userId): array
    {
        return collect($this->history->allCalculations($userId))
            ->filter(static fn ($run): bool => data_get($run->input, 'run_type') === ManageAccountingFeesHistory::TYPE_CALCULATION)
            ->map(static fn ($run): array => [
                $run->finishedAt->format('d/m/Y H:i'),
                data_get($run->input, 'monthly_revenue'),
                data_get($run->input, 'tax_regime'),
                data_get($run->input, 'employees'),
                data_get($run->input, 'monthly_invoices'),
                data_get($run->result, 'complexity_level'),
                data_get($run->result, 'minimum_fee'),
                data_get($run->result, 'recommended_fee'),
                data_get($run->result, 'upper_reference_fee'),
            ])->values()->all();
    }
}
