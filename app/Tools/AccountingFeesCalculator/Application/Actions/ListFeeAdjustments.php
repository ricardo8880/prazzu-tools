<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\FeeAdjustmentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListFeeAdjustments
{
    public function __construct(private FeeAdjustmentRepository $adjustments) {}

    public function execute(AccountingFeesOwner $owner): LengthAwarePaginator
    {
        return $this->adjustments->paginate($owner);
    }
}
