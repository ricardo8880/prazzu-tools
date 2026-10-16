<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolCategory;
use App\Core\Tools\Enums\ToolFeatureTier;
use App\Core\Tools\Enums\ToolStatus;
use App\Tools\AccountingFeesCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolManifestTest extends TestCase
{
    public function test_manifest_exposes_the_accounting_fees_tool(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame('accounting-fee-calculator', $manifest->slug);
        self::assertSame('Accounting Fee Calculator', $manifest->name);
        self::assertSame(ToolCategory::Calculators, $manifest->category);
        self::assertSame(ToolStatus::Active, $manifest->status);
        self::assertSame('1.2.0', $manifest->version);
        self::assertSame('tools.accounting-fee-calculator.index', $manifest->routeName);
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->storesSensitiveData);
        self::assertSame(
            ['calculate', 'adjust_fee'],
            array_column($manifest->featuresFor(ToolFeatureTier::Essential), 'key'),
        );
        self::assertSame(
            ['commercial_proposal', 'service_contract', 'history', 'history_export'],
            array_column($manifest->featuresFor(ToolFeatureTier::Plus), 'key'),
        );
    }
}
