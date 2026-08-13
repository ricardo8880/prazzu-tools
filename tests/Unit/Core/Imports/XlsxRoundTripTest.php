<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Imports;

use App\Core\Imports\Services\XlsxTabularFileReader;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

final class XlsxRoundTripTest extends TestCase
{
    public function test_core_generated_xlsx_can_be_read_by_the_core_importer(): void
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nome', 'Salário'],
            ['Ana', '5000,00'],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'prazzu-xlsx-');
        self::assertNotFalse($path);

        try {
            (new Xlsx($spreadsheet))->save($path);
            $file = new UploadedFile(
                $path,
                'funcionarios.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true,
            );

            $dataset = (new XlsxTabularFileReader)->read($file, 10);

            self::assertSame(['Nome', 'Salário'], $dataset->headers);
            self::assertSame('Ana', $dataset->rows[0]['Nome']);
            self::assertSame('5000,00', $dataset->rows[0]['Salário']);
        } finally {
            $spreadsheet->disconnectWorksheets();
            @unlink($path);
        }
    }
}
