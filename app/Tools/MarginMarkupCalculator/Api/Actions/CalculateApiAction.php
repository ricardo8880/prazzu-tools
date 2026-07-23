<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Api\Actions;

use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\MarginMarkupCalculator\Application\Actions\CalculateMarginMarkup;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CalculateMarginMarkupRequest;

final readonly class CalculateApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private CalculateMarginMarkup $action) {}

    public function name(): string { return 'calculate'; }

    protected function requestClass(): string { return CalculateMarginMarkupRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        return $this->action->execute($input)->toArray();
    }
}
