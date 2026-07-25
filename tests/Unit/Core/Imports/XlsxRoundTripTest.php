<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Imports;

use App\Core\Export\Services\SimpleZipArchiveBuilder;
use App\Core\Export\Services\TabularExportService;
use App\Core\Imports\Services\XlsxTabularFileReader;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class XlsxRoundTripTest extends TestCase
{
    public function test_core_generated_xlsx_can_be_read_by_the_core_importer(): void
    {
        $response = (new TabularExportService(new SimpleZipArchiveBuilder))->xlsx(
            'funcionarios.xlsx',
            ['Nome', 'Salário'],
            [['Ana', '5000,00']],
        );
        $path = tempnam(sys_get_temp_dir(), 'prazzu-xlsx-');
        self::assertNotFalse($path);

        try {
            file_put_contents($path, (string) $response->getContent());
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
            @unlink($path);
        }
    }
}
