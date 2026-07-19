<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Unit;

use App\Core\Imports\Exceptions\InvalidImportFile;
use App\Core\Imports\Services\CsvTabularFileReader;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

final class CsvTabularFileReaderTest extends TestCase
{
    public function test_it_reads_semicolon_separated_csv(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'prazzu-csv-');
        file_put_contents($path, "CNPJ;Razão Social;UF\n04.252.011/0001-10;Empresa Exemplo;SP\n");
        $file = new UploadedFile($path, 'empresas.csv', 'text/csv', null, true);

        $dataset = (new CsvTabularFileReader)->read($file, 500);

        self::assertSame(['CNPJ', 'Razão Social', 'UF'], $dataset->headers);
        self::assertSame('04.252.011/0001-10', $dataset->rows[0]['CNPJ']);
        self::assertSame('Empresa Exemplo', $dataset->rows[0]['Razão Social']);
        @unlink($path);
    }

    public function test_it_rejects_files_above_the_row_limit(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'prazzu-csv-');
        file_put_contents($path, "Documento\n1\n2\n");
        $file = new UploadedFile($path, 'documentos.csv', 'text/csv', null, true);

        $this->expectException(InvalidImportFile::class);
        (new CsvTabularFileReader)->read($file, 1);
    }
}
