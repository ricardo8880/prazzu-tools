<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Export;

use App\Core\Export\Services\SimpleZipArchiveBuilder;
use App\Core\Export\Services\TabularExportService;
use Tests\TestCase;

final class TabularExportServiceTest extends TestCase
{
    public function test_it_generates_a_real_xlsx_package_with_escaped_content(): void
    {
        $response = (new TabularExportService(new SimpleZipArchiveBuilder))->xlsx(
            'relatorio.xlsx',
            ['Nome', 'Valor'],
            [['Ana & Cia', 'R$ 1.000,00']],
            'Custos CLT',
        );

        $content = (string) $response->getContent();

        self::assertStringStartsWith('PK', $content);
        self::assertStringContainsString('[Content_Types].xml', $content);
        self::assertStringContainsString('xl/worksheets/sheet1.xml', $content);
        self::assertStringContainsString('Ana &amp; Cia', $content);
        self::assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type'),
        );
    }
}
