<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Seo;

use App\Core\Seo\Application\ToolTrustContent;
use PHPUnit\Framework\TestCase;

final class ToolTrustContentTest extends TestCase
{
    public function test_fiscal_guidance_does_not_promise_automatic_official_assessment(): void
    {
        $content = (new ToolTrustContent)->for([
            'declared_category' => 'fiscal',
            'supports_history' => true,
            'version' => '2.3.0',
            'essential_features' => [
                ['name' => 'Memória de cálculo e fontes normativas'],
            ],
        ]);

        self::assertStringContainsString('ente competente', $content['precheck']);
        self::assertStringContainsString('memória', $content['transparency']);
        self::assertStringContainsString('sem cadastro', $content['continuity']);
        self::assertSame('2.3.0', $content['version']);
    }

    public function test_document_guidance_requires_human_review_before_formal_use(): void
    {
        $content = (new ToolTrustContent)->for([
            'declared_category' => 'documentos',
            'supports_history' => false,
            'version' => '1.0.0',
            'essential_features' => [],
        ]);

        self::assertStringContainsString('Revise nomes, valores, datas, cláusulas', $content['precheck']);
        self::assertStringContainsString('não desbloqueia uma fórmula melhor', $content['continuity']);
    }
}
