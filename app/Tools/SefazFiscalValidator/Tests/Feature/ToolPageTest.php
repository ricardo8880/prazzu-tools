<?php

declare(strict_types=1);
namespace App\Tools\SefazFiscalValidator\Tests\Feature;
use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\SefazFiscalValidator\Tool;
use PHPUnit\Framework\TestCase;
final class ToolPageTest extends TestCase {
    #[CoversPlusFeature('validador-fiscal-sefaz', 'key_breakdown')]
    public function test_plus_capability_is_declared_as_concrete_feature(): void { $keys = array_map(static fn ($feature): string => $feature->key, (new Tool)->manifest()->features); self::assertContains('key_breakdown', $keys); }
}
