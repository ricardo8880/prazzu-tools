<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Unit;

use App\Core\Export\Services\SimpleZipArchiveBuilder;
use App\Tools\ContractGenerator\Infrastructure\Export\ContractDocxExporter;
use PHPUnit\Framework\TestCase;

final class ContractDocxExporterTest extends TestCase
{
    public function test_builds_openxml_package_with_escaped_contract_content(): void
    {
        $content = "CONTRATO DE TESTE\n\nCláusula com acentuação & símbolo <seguro>.";
        $binary = (new ContractDocxExporter(new SimpleZipArchiveBuilder))->build('Contrato de Teste', $content);

        self::assertStringStartsWith('PK', $binary);
        self::assertStringContainsString('[Content_Types].xml', $binary);
        self::assertStringContainsString('word/document.xml', $binary);
        self::assertStringContainsString('CONTRATO DE TESTE', $binary);
        self::assertStringContainsString('Cláusula com acentuação &amp; símbolo &lt;seguro&gt;.', $binary);
    }
}
