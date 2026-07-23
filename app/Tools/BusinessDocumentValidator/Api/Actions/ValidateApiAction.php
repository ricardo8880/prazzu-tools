<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Api\Actions;

use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\BusinessDocumentValidator\Application\Actions\ValidateBusinessDocument;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\ValidateBusinessDocumentRequest;

final readonly class ValidateApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private ValidateBusinessDocument $action) {}

    public function name(): string { return 'validate'; }

    protected function requestClass(): string { return ValidateBusinessDocumentRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        return $this->action->execute($input)->toArray();
    }
}
