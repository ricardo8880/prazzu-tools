<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Export;

use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Data\SpreadsheetDocument;
use App\Core\Export\Data\SpreadsheetSheet;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ExportDocumentTest extends TestCase
{
    public function test_it_normalizes_download_extensions(): void
    {
        $pdf = new PdfDocument('resultado', 'tools.example.pdf');
        $spreadsheet = new SpreadsheetDocument('resultado', [
            new SpreadsheetSheet('Resultados', [['Campo', 'Valor']]),
        ]);

        self::assertSame('resultado.pdf', $pdf->downloadFilename());
        self::assertSame('resultado.xlsx', $spreadsheet->downloadFilename());
    }

    public function test_it_rejects_duplicate_sheet_names(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SpreadsheetDocument('resultado.xlsx', [
            new SpreadsheetSheet('Resumo', []),
            new SpreadsheetSheet('resumo', []),
        ]);
    }

    public function test_it_rejects_invalid_pdf_orientation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PdfDocument('resultado.pdf', 'tools.example.pdf', orientation: 'diagonal');
    }
}
