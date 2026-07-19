<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

use App\Core\Tools\History\Contracts\ToolRunHistory;

final readonly class DeleteValidationHistory
{
    private const TOOL_SLUG = 'validador-de-cnpj';

    public function __construct(private ToolRunHistory $history) {}

    public function execute(string $runId, int $userId): void
    {
        $this->history->deleteSucceededOwned(self::TOOL_SLUG, $runId, $userId);
    }
}
