<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Tests\Unit;

use App\Core\Tools\Enums\ToolStatus;
use App\Tools\EcadRoyaltySimulator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_exposes_expected_public_identity(): void
    {
        $manifest = (new Tool)->manifest();
        self::assertSame('simulador-ecad-direitos-autorais', $manifest->slug);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertSame('contabilidade', $manifest->vertical);
        self::assertFalse($manifest->supportsHistory);
    }
}
