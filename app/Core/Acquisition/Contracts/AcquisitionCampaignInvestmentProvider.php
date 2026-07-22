<?php

namespace App\Core\Acquisition\Contracts;

interface AcquisitionCampaignInvestmentProvider
{
    /** @param list<int> $contextIds @return array<int, array{monthly_investment_cents:int,currency:string}> */
    public function forContextIds(array $contextIds): array;
}
