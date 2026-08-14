<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class MultiVerticalConstitutionTest extends TestCase
{
    public function test_root_readme_defines_multi_vertical_constitution(): void
    {
        $readme = $this->read('README.md');

        self::assertStringContainsString('verticais de negócio', $readme);
        self::assertStringContainsString('Contabilidade é a primeira vertical oficial', $readme);
        self::assertStringContainsString('# Verticais e contexto da plataforma', $readme);
        self::assertStringContainsString('**VerticalContext**', $readme);
        self::assertStringContainsString('VerticalContext = null', $readme);
        self::assertStringContainsString('`VerticalContext` e `AcquisitionContext` são conceitos diferentes', $readme);
        self::assertStringContainsString('A inclusão de uma nova vertical não pode exigir um novo Core', $readme);
    }

    public function test_architecture_document_preserves_single_shared_platform(): void
    {
        $architecture = $this->read('docs/ARCHITECTURE.md');

        self::assertStringContainsString('arquiteturalmente **multi-nicho**', $architecture);
        self::assertStringContainsString('## Vertical e VerticalContext', $architecture);
        self::assertStringContainsString('### Infraestrutura global', $architecture);
        self::assertStringContainsString('### Regra de não duplicação', $architecture);
        self::assertStringContainsString('`VerticalContext = null`', $architecture);
    }

    public function test_lot_one_is_documented_without_changing_official_inventory(): void
    {
        $continuity = $this->read('docs/IMPLEMENTATION-LOTS.md');
        $inventory = require dirname(__DIR__, 2).'/config/product_tools.php';

        self::assertStringContainsString('# Evolução multi-nicho', $continuity);
        self::assertStringContainsString('| 1 | Constituição, contratos arquiteturais e regras Global x Vertical | Concluído |', $continuity);
        self::assertStringContainsString('docs/MULTI-VERTICAL-LOT-1-CONSTITUTION.md', $continuity);
        self::assertSame(50, $inventory['expected_module_count']);
        self::assertCount(50, $inventory['official']);
    }

    private function read(string $relativePath): string
    {
        $contents = file_get_contents(dirname(__DIR__, 2).'/'.$relativePath);

        self::assertIsString($contents, "Não foi possível ler [{$relativePath}].");

        return $contents;
    }
}
