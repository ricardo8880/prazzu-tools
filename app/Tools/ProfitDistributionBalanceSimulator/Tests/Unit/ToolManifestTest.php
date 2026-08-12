<?php

declare(strict_types=1);
namespace App\Tools\ProfitDistributionBalanceSimulator\Tests\Unit;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\ProfitDistributionBalanceSimulator\Tool;
use PHPUnit\Framework\TestCase;
final class ToolManifestTest extends TestCase
{
    public function test_manifest_has_expected_identity_and_tiers(): void
    {
        $manifest=(new Tool())->manifest(); self::assertSame('simulador-distribuicao-lucros-balanco',$manifest->slug); self::assertSame('tools.simulador-distribuicao-lucros-balanco.index',$manifest->routeName); self::assertSame(ToolStatus::Beta,$manifest->status); self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Essential)); self::assertNotEmpty($manifest->featuresFor(ToolFeatureTier::Plus));
    }
}
