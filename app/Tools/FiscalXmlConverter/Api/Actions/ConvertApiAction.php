<?php

declare(strict_types=1);

namespace App\Tools\FiscalXmlConverter\Api\Actions;

use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\FiscalXmlConverter\Application\Actions\ConvertUploadedXml;
use App\Tools\FiscalXmlConverter\Presentation\Requests\ConvertFiscalXmlRequest;

final readonly class ConvertApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private ConvertUploadedXml $action) {}

    public function name(): string { return 'convert'; }

    protected function requestClass(): string { return ConvertFiscalXmlRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        return $this->action->execute($input['file'])->toArray();
    }
}
