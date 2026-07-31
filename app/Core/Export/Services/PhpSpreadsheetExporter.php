<?php

declare(strict_types=1);

namespace App\Core\Export\Services;

use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Data\SpreadsheetDocument;
use App\Core\Export\Data\SpreadsheetSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PhpSpreadsheetExporter implements SpreadsheetExporter
{
    public function download(SpreadsheetDocument $document): StreamedResponse
    {
        if (! class_exists(Spreadsheet::class)) {
            throw new RuntimeException('A biblioteca phpoffice/phpspreadsheet não está instalada. Execute Composer antes de exportar Excel.');
        }

        return response()->streamDownload(function () use ($document): void {
            $spreadsheet = new Spreadsheet;
            $spreadsheet->removeSheetByIndex(0);

            foreach ($document->sheets as $sheetIndex => $definition) {
                $sheet = $spreadsheet->createSheet($sheetIndex);
                $sheet->setTitle($definition->name);
                $sheet->fromArray($definition->rows, null, 'A1', true);

                $this->formatSheet($sheet, $definition);
            }

            $spreadsheet->setActiveSheetIndex(0);
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $document->downloadFilename(), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function formatSheet(Worksheet $sheet, SpreadsheetSheet $definition): void
    {
        if ($definition->rows === []) {
            return;
        }

        $columnCount = max(array_map('count', $definition->rows));
        if ($columnCount === 0) {
            return;
        }

        $lastColumn = Coordinate::stringFromColumnIndex($columnCount);
        $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);
        $sheet->setAutoFilter("A1:{$lastColumn}1");

        if ($definition->freezeHeader) {
            $sheet->freezePane('A2');
        }

        if ($definition->autoSizeColumns) {
            for ($column = 1; $column <= $columnCount; $column++) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setAutoSize(true);
            }
        }
    }
}
