<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Repositories\FeeAdjustmentRepository;

final readonly class CalculateAndStoreFeeAdjustment
{
    public function __construct(
        private CalculateFeeAdjustment $calculate,
        private FeeAdjustmentRepository $adjustments,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array{result: array<string, int|string>, saved: bool}
     */
    public function execute(array $input, AccountingFeesOwner $owner, bool $persist): array
    {
        $currentValue = Money::fromDecimal((string) $input['current_value']);
        $result = $this->calculate->execute(
            $currentValue->minorAmount(),
            (string) $input['percentage'],
        );
        $saved = $persist && $owner->userId !== null;

        if ($saved) {
            $this->adjustments->store($owner, $input, $result);
        }

        return ['result' => $result->toArray(), 'saved' => $saved];
    }
}
