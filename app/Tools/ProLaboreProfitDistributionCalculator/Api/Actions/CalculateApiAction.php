<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Api\Actions;

use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\ProLaboreProfitDistributionCalculator\Application\Actions\CalculateTool;
use App\Tools\ProLaboreProfitDistributionCalculator\Presentation\Requests\ExecuteToolRequest;

final readonly class CalculateApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private CalculateTool $action) {}

    public function name(): string { return 'calculate'; }

    protected function requestClass(): string { return ExecuteToolRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        return $this->action->execute($input)->toArray();
    }
}
