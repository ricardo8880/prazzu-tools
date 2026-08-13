<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Seo;

use PHPUnit\Framework\TestCase;

final class ToolSeoCoverageTest extends TestCase
{
    public function test_all_official_tool_index_views_receive_shared_seo_and_trust_infrastructure(): void
    {
        $inventory = require dirname(__DIR__, 4).'/config/product_tools.php';
        $root = dirname(__DIR__, 4);
        $pageComponent = file_get_contents($root.'/resources/views/components/tools/page.blade.php');

        self::assertIsString($pageComponent);
        self::assertStringContainsString('<x-tools.trust-seo :slug="$slug" />', $pageComponent);

        foreach ($inventory['official'] as $tool) {
            $view = $root.'/app/Tools/'.$tool['module'].'/Resources/views/index.blade.php';
            self::assertFileExists($view, 'View ausente para '.$tool['module']);

            $contents = file_get_contents($view);
            self::assertIsString($contents);
            self::assertTrue(
                str_contains($contents, '<x-tools.page') || str_contains($contents, '<x-tools.trust-seo'),
                sprintf('A ferramenta [%s] ficou fora da infraestrutura compartilhada de SEO/confiança.', $tool['slug']),
            );
        }
    }
}
