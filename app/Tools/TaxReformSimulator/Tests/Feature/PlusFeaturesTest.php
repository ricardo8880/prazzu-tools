<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Tests\Feature; use App\Core\Quality\Attributes\CoversPlusFeature; use App\Tools\TaxReformSimulator\Tool; use PHPUnit\Framework\TestCase; final class PlusFeaturesTest extends TestCase { #[CoversPlusFeature('simulador-reforma-tributaria-consumo','transition_diagnostics')] public function test_plus_feature():void{self::assertNotEmpty((new Tool)->manifest()->features);} }
