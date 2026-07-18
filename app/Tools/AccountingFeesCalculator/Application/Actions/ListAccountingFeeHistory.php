<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\AccountingFeeCalculationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListAccountingFeeHistory
{
    public function __construct(private AccountingFeeCalculationRepository $calculations) {}

    public function execute(AccountingFeesOwner $owner, bool $favorite): LengthAwarePaginator
    {
        return $this->calculations->paginate($owner, $favorite);
    }
}
