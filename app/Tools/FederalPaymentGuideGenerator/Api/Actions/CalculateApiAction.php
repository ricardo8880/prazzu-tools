<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Api\Actions;

use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\FederalPaymentGuideGenerator\Application\Actions\CalculateGuide;
use App\Tools\FederalPaymentGuideGenerator\Presentation\Requests\CalculateGuideRequest;

final readonly class CalculateApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private CalculateGuide $action) {}

    public function name(): string { return 'calculate'; }

    protected function requestClass(): string { return CalculateGuideRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        return $this->action->execute($input);
    }
}
