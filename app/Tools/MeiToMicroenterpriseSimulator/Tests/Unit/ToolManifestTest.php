<?php

declare(strict_types=1);
namespace App\Tools\MeiToMicroenterpriseSimulator\Tests\Unit;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Tools\MeiToMicroenterpriseSimulator\Tool;
use PHPUnit\Framework\TestCase;
final class ToolManifestTest extends TestCase
{
    public function test_manifest_preserves_essential_and_plus_scope(): void
    {
        $manifest=(new Tool())->manifest();
        self::assertSame('simulador-mei-microempresa',$manifest->slug);
        self::assertSame('contabilidade',$manifest->vertical);
        self::assertNotEmpty(array_filter($manifest->features, fn($f)=>$f->tier===ToolFeatureTier::Essential));
        self::assertNotEmpty(array_filter($manifest->features, fn($f)=>$f->tier===ToolFeatureTier::Plus));
    }
}
