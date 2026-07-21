<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Tools\Enums\ToolCapability;
use App\Tools\TaxRegimeComparator\Tool;
use PHPUnit\Framework\TestCase;

final class ToolInfrastructurePolicyTest extends TestCase
{
    public function test_manifest_declares_versioned_history_and_core_exports(): void
    {
        $tool = new Tool;
        $manifest = $tool->manifest();

        self::assertTrue($manifest->supportsHistory);
        self::assertContains(ToolCapability::History, $manifest->capabilities);
        self::assertContains(ToolCapability::VersionedPersistence, $manifest->capabilities);
        self::assertContains(ToolCapability::Export, $manifest->capabilities);
        self::assertTrue($manifest->persistence->enabled);
        self::assertSame(1, $manifest->persistence->schemaVersion);
        self::assertSame(365, $manifest->persistence->retentionDays);
        self::assertSame(['csv', 'json', 'pdf', 'print'], $manifest->export->formats);
    }

    public function test_history_policy_projects_only_declared_fields(): void
    {
        $policy = (new Tool)->historyPolicy();

        self::assertTrue($policy->enabled);
        self::assertSame(365, $policy->retentionDays);
        self::assertContains('reference_date', $policy->inputFields);
        self::assertContains('ranking', $policy->resultFields);
        self::assertSame([], $policy->sensitiveFields);
    }
}
