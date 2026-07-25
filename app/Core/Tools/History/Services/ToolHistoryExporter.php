<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Services;

use App\Core\Export\Data\PrintableDocument;
use App\Core\Export\Services\BrowserPrintExporter;
use App\Core\Export\Services\TabularExportService;
use App\Core\Tools\History\Data\ToolRunEntry;
use App\Core\Tools\ToolRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class ToolHistoryExporter
{
    public function __construct(
        private ToolRegistry $registry,
        private TabularExportService $tabular,
        private BrowserPrintExporter $print,
    ) {}

    public function export(ToolRunEntry $entry, string $format): View|Response|StreamedResponse
    {
        $manifest = $this->registry->findManifest($entry->toolSlug)
            ?? throw new InvalidArgumentException('Ferramenta não encontrada.');
        $basename = Str::slug($manifest->name).'-'.$entry->referenceDate->format('Y-m-d');
        $rows = $this->rows($entry);

        return match ($format) {
            'csv' => $this->tabular->csv($basename.'.csv', ['Seção', 'Campo', 'Valor'], $rows),
            'xlsx' => $this->tabular->xlsx($basename.'.xlsx', ['Seção', 'Campo', 'Valor'], $rows, 'Resultado'),
            'pdf' => $this->print->render(new PrintableDocument(
                title: $manifest->name,
                subtitle: 'Relatório de cálculo salvo',
                contentView: 'exports.partials.tool-history',
                data: ['entry' => $entry],
                generatedAt: now()->format('d/m/Y H:i'),
                summaryLabel: 'Data de referência',
                summaryValue: $entry->referenceDate->format('d/m/Y'),
            )),
            default => throw new InvalidArgumentException('Formato de exportação não suportado.'),
        };
    }

    /**
     * @return list<list<string>>
     */
    private function rows(ToolRunEntry $entry): array
    {
        $rows = [];

        foreach ($this->flatten($entry->input) as $field => $value) {
            $rows[] = ['Entradas', $field, $value];
        }

        foreach ($this->flatten($entry->result) as $field => $value) {
            $rows[] = ['Resultados', $field, $value];
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, string>
     */
    private function flatten(array $payload, string $prefix = ''): array
    {
        $flat = [];

        foreach ($payload as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $flat = [...$flat, ...$this->flatten($value, $path)];

                continue;
            }

            $flat[$path] = match (true) {
                $value === null => '',
                is_bool($value) => $value ? 'Sim' : 'Não',
                is_scalar($value) => (string) $value,
                default => '[valor não tabular]',
            };
        }

        return $flat;
    }
}
