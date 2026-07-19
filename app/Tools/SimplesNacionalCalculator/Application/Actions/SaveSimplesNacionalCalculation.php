<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Dates\ReferenceDate;
use App\Core\Tools\History\Contracts\ToolRunHistory;
use App\Core\Tools\History\Data\RuleVersion;
use App\Tools\SimplesNacionalCalculator\Tool;

final readonly class SaveSimplesNacionalCalculation
{
    public function __construct(
        private CalculateSimplesNacional $calculate,
        private ToolRunHistory $history,
        private Tool $module,
    ) {}

    /** @param array{reference_month: string, annex: string, rbt12: string, monthly_revenue: string} $input */
    public function execute(array $input, int $userId): void
    {
        $result = $this->calculate->execute([
            'annex' => $input['annex'],
            'rbt12' => $input['rbt12'],
            'monthly_revenue' => $input['monthly_revenue'],
        ]);

        $this->history->recordSucceeded(
            module: $this->module,
            ruleVersion: new RuleVersion(Tool::HISTORY_RULE_VERSION),
            referenceDate: ReferenceDate::fromString($input['reference_month'].'-01'),
            input: [
                'reference_month' => $input['reference_month'],
                'annex' => $result->annex->value,
                'rbt12' => $input['rbt12'],
                'monthly_revenue' => $input['monthly_revenue'],
            ],
            result: $result->toArray(),
            userId: $userId,
        );
    }
}
