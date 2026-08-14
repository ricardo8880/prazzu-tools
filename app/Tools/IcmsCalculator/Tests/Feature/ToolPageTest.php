<?php

declare(strict_types=1);
namespace App\Tools\IcmsCalculator\Tests\Feature;
use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\IcmsCalculator\Tool;
use PHPUnit\Framework\TestCase;
final class ToolPageTest extends TestCase {
    #[CoversPlusFeature('calculadora-icms-proprio', 'inside_calculation')]
    public function test_plus_capability_is_declared_as_concrete_feature(): void { $keys = array_map(static fn ($feature): string => $feature->key, (new Tool)->manifest()->features); self::assertContains('inside_calculation', $keys); }
}
