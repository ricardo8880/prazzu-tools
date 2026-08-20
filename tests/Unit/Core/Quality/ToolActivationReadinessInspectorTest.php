<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Quality;

use App\Core\Quality\Services\ToolActivationReadinessInspector;
use App\Tools\AdmissionSimulator\Tool as AdmissionTool;
use App\Tools\DigitalCertificateAnalyzer\Tool as ActiveTool;
use PHPUnit\Framework\TestCase;

final class ToolActivationReadinessInspectorTest extends TestCase
{
    public function test_it_detects_scaffold_golden_cases_and_open_checklist(): void
    {
        $root = dirname(__DIR__, 4).'/app/Tools/AdmissionSimulator';
        $report = (new ToolActivationReadinessInspector)->inspect('AdmissionSimulator', new AdmissionTool, $root);

        self::assertFalse($report->isReady());
        self::assertTrue($report->hasSyntheticGoldenCases);
        self::assertGreaterThan(0, $report->openChecklistItems);
        self::assertContains('synthetic_golden_cases', $report->blockers);
    }

    public function test_it_accepts_a_current_active_tool_with_complete_evidence(): void
    {
        $root = dirname(__DIR__, 4).'/app/Tools/DigitalCertificateAnalyzer';
        $report = (new ToolActivationReadinessInspector)->inspect('DigitalCertificateAnalyzer', new ActiveTool, $root);

        self::assertTrue($report->isReady(), implode(', ', $report->blockers));
    }
}
