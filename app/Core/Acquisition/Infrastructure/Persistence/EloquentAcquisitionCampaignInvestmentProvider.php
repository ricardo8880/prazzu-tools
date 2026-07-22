<?php

namespace App\Core\Acquisition\Infrastructure\Persistence;

use App\Core\Acquisition\Contracts\AcquisitionCampaignInvestmentProvider;

final class EloquentAcquisitionCampaignInvestmentProvider implements AcquisitionCampaignInvestmentProvider
{
    public function forContextIds(array $contextIds): array
    {
        if ($contextIds === []) {
            return [];
        }

        return AcquisitionContextRecord::query()
            ->whereIn('id', array_values(array_unique($contextIds)))
            ->get(['id', 'monthly_investment_cents', 'investment_currency'])
            ->mapWithKeys(static fn (AcquisitionContextRecord $context): array => [
                (int) $context->getKey() => [
                    'monthly_investment_cents' => max(0, (int) $context->monthly_investment_cents),
                    'currency' => (string) ($context->investment_currency ?: 'BRL'),
                ],
            ])->all();
    }
}
