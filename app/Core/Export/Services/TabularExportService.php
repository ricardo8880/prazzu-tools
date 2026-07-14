<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class TabularExportService
{
    /**
     * @param list<string> $headers
     * @param iterable<int, list<string|int|float|bool|null>> $rows
     */
    public function csv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headers, ';');

            foreach ($rows as $row) {
                fputcsv($stream, array_map([$this, 'stringify'], $row), ';');
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Exporta SpreadsheetML, formato XML aberto pelo Excel sem dependências adicionais.
     *
     * @param list<string> $headers
     * @param iterable<int, list<string|int|float|bool|null>> $rows
     */
    public function excel(string $filename, array $headers, iterable $rows, string $worksheet = 'Resultados'): Response
    {
        $allRows = [$headers];
        foreach ($rows as $row) {
            $allRows[] = array_map([$this, 'stringify'], $row);
        }

        $xmlRows = '';
        foreach ($allRows as $row) {
            $cells = '';
            foreach ($row as $value) {
                $cells .= '<Cell><Data ss:Type="String">'.htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</Data></Cell>';
            }
            $xmlRows .= '<Row>'.$cells.'</Row>';
        }

        $content = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<?mso-application progid="Excel.Sheet"?>'
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            .'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Worksheet ss:Name="'.htmlspecialchars($worksheet, ENT_XML1 | ENT_QUOTES, 'UTF-8').'">'
            .'<Table>'.$xmlRows.'</Table></Worksheet></Workbook>';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function stringify(string|int|float|bool|null $value): string
    {
        return match (true) {
            $value === null => '',
            is_bool($value) => $value ? 'Sim' : 'Não',
            default => (string) $value,
        };
    }
}
