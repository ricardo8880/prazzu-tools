<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Export;

use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Data\SpreadsheetDocument;
use App\Core\Export\Data\SpreadsheetSheet;
use App\Core\Export\Services\DompdfPdfExporter;
use App\Core\Export\Services\PhpSpreadsheetExporter;
use Dompdf\Dompdf;
use Illuminate\Contracts\View\Factory as ViewFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

final class LibraryExportersTest extends TestCase
{
    public function test_dompdf_exporter_returns_a_real_pdf_download(): void
    {
        if (! class_exists(Dompdf::class)) {
            self::markTestSkipped('dompdf/dompdf ainda não está disponível no vendor deste ambiente.');
        }

        $views = $this->app->make(ViewFactory::class);
        $views->addNamespace('export-tests', __DIR__.'/fixtures');

        $response = (new DompdfPdfExporter($views))->download(new PdfDocument(
            'resultado.pdf',
            'export-tests::result',
            ['value' => 'R$ 1.250,00'],
        ));

        self::assertStringStartsWith('%PDF-', (string) $response->getContent());
        self::assertSame('application/pdf', $response->headers->get('Content-Type'));
        self::assertStringContainsString('attachment;', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_phpspreadsheet_exporter_returns_a_real_xlsx_download(): void
    {
        if (! class_exists(Spreadsheet::class)) {
            self::markTestSkipped('phpoffice/phpspreadsheet ainda não está disponível no vendor deste ambiente.');
        }

        $response = (new PhpSpreadsheetExporter)->download(new SpreadsheetDocument(
            'resultado.xlsx',
            [new SpreadsheetSheet('Resultados', [
                ['Campo', 'Valor'],
                ['Total', 1250.00],
            ])],
        ));

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        self::assertStringStartsWith('PK', $content);

        $temporary = tempnam(sys_get_temp_dir(), 'xlsx-test-');
        file_put_contents($temporary, $content);
        $spreadsheet = IOFactory::load($temporary);

        self::assertSame('Total', $spreadsheet->getActiveSheet()->getCell('A2')->getValue());
        self::assertSame(1250.0, $spreadsheet->getActiveSheet()->getCell('B2')->getValue());

        unlink($temporary);
        $spreadsheet->disconnectWorksheets();
    }
}
