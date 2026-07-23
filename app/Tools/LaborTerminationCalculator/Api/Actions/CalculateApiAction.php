<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Api\Actions;

use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\LaborTerminationCalculator\Application\Actions\CalculateLaborTermination;
use App\Tools\LaborTerminationCalculator\Presentation\Requests\CalculateLaborTerminationRequest;

final readonly class CalculateApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private CalculateLaborTermination $action) {}

    public function name(): string { return 'calculate'; }

    protected function requestClass(): string { return CalculateLaborTerminationRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        return $this->action->execute($input)->toArray();
    }
}
