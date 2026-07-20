<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Dates\ReferenceDate;
use App\Core\Money\Money;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Tools\AccountingFeesCalculator\Tool;
use Throwable;

final readonly class CalculateAndStoreFeeAdjustment
{
    public function __construct(
        private CalculateFeeAdjustment $calculate,
        private ToolRunRecorder $recorder,
        private Tool $module,
    ) {}

    /** @param array<string,mixed> $input @return array{result:array<string,int|string>,saved:bool} */
    public function execute(array $input, ?int $userId, bool $persist): array
    {
        $run = null;
        $historyInput = ['run_type' => ManageAccountingFeesHistory::TYPE_ADJUSTMENT, ...$input];

        try {
            if ($persist && $userId !== null) {
                $run = $this->recorder->start(
                    $this->module,
                    new RuleVersion(ManageAccountingFeesHistory::RULE_VERSION_ADJUSTMENT),
                    ReferenceDate::fromString($input['reference_period'].'-01'),
                    $historyInput,
                    $userId,
                );
            }

            $currentValue = Money::fromDecimal((string) $input['current_value']);
            $result = $this->calculate->execute($currentValue->minorAmount(), (string) $input['percentage'])->toArray();
            if ($run !== null) {
                $this->recorder->succeed($run, $result);
            }

            return ['result' => $result, 'saved' => $run !== null];
        } catch (Throwable $exception) {
            if ($run !== null) {
                $this->recorder->fail($run, 'accounting_fee.adjustment_failed');
            }
            throw $exception;
        }
    }
}
