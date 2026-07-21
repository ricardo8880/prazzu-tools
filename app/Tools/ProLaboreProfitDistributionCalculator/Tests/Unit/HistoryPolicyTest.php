<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolCapability;
use App\Tools\ProLaboreProfitDistributionCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class HistoryPolicyTest extends TestCase
{
    public function test_manifest_and_history_policy_are_consistent(): void
    {
        $tool = new Tool;
        $manifest = $tool->manifest();
        $policy = $tool->historyPolicy();

        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->persistence->enabled);
        self::assertContains(ToolCapability::History, $manifest->capabilities);
        self::assertContains(ToolCapability::VersionedPersistence, $manifest->capabilities);
        self::assertTrue($policy->enabled);
        self::assertSame(180, $policy->retentionDays);
        self::assertContains('competence', $policy->inputFields);
        self::assertContains('summary', $policy->resultFields);
    }
}
