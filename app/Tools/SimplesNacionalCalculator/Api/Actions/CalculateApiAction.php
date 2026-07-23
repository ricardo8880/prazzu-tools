<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Api\Actions;

use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CalculateSimplesNacional;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CalculateSimplesNacionalRequest;

final readonly class CalculateApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private CalculateSimplesNacional $action) {}

    public function name(): string { return 'calculate'; }

    protected function requestClass(): string { return CalculateSimplesNacionalRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        return $this->action->execute($input)->toArray();
    }
}
