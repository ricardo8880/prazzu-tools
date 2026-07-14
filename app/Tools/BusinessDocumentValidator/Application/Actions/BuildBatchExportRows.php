<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Application\Actions;

final class BuildBatchExportRows
{
    /** @return list<string> */
    public function headers(): array
    {
        return ['Linha', 'Documento', 'Tipo', 'Válido', 'Duplicado', 'Situação cadastral', 'Empresa', 'UF', 'Inconsistências', 'Mensagem'];
    }

    /**
     * @param array<string, mixed> $result
     * @return list<list<string|int|bool|null>>
     */
    public function execute(array $result, bool $onlyIssues = false): array
    {
        $export = [];
        foreach (($result['rows'] ?? []) as $row) {
            $issues = array_filter(
                $row['inconsistencies'] ?? [],
                static fn (array $issue): bool => in_array($issue['severity'] ?? null, ['error', 'warning'], true),
            );

            $hasIssue = ! ($row['valid'] ?? false) || ($row['duplicate'] ?? false) || $issues !== [];
            if ($onlyIssues && ! $hasIssue) {
                continue;
            }

            $company = $row['company'] ?? [];
            $export[] = [
                $row['line'] ?? '',
                $row['formatted_document'] ?: ($row['document'] ?? ''),
                $row['type'] ?? '',
                (bool) ($row['valid'] ?? false),
                (bool) ($row['duplicate'] ?? false),
                $row['registry_status'] ?? 'não consultada',
                $company['legal_name'] ?? '',
                $company['state'] ?? '',
                implode(' | ', array_map(static fn (array $issue): string => (string) ($issue['message'] ?? ''), $issues)),
                $row['message'] ?? ($row['registry_message'] ?? ''),
            ];
        }

        return $export;
    }
}
