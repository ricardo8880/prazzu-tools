<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Tests\Feature;
use App\Core\Quality\Attributes\CoversPlusFeature; use App\Tools\ActualProfitCalculator\Tool; use PHPUnit\Framework\TestCase;
final class PlusFeaturesTest extends TestCase { #[CoversPlusFeature('calculadora-lucro-real','tax_base_diagnostics')] public function test_plus_feature():void{self::assertNotEmpty((new Tool)->manifest()->features);} }
