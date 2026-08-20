<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Services;

use App\Core\Tools\History\Contracts\ProvidesHistoryContext;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\ToolRegistry;
use DateTimeImmutable;
use Throwable;

final readonly class ToolHistoryContextResolver
{
    public function __construct(private ToolRegistry $registry) {}

    /** @param array<string, mixed> $input */
    public function resolve(string $toolSlug, array $input, DateTimeImmutable $referenceDate): ?string
    {
        $module = $this->registry->findModule($toolSlug);

        if (! $module instanceof ProvidesHistoryContext) {
            return null;
        }

        try {
            $label = trim((string) $module->historyContext($input, $referenceDate));
        } catch (Throwable) {
            return null;
        }

        return $label !== '' ? $label : null;
    }

    public function resolveEntry(ToolRunEntry $entry): ?string
    {
        return $this->resolve($entry->toolSlug, $entry->input, $entry->referenceDate);
    }
}
