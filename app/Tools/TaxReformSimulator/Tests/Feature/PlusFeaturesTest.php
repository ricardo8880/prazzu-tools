<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\TaxReformSimulator\Tool;
use PHPUnit\Framework\TestCase;

final class PlusFeaturesTest extends TestCase
{
    #[CoversPlusFeature('sales-tax-calculator', 'combined_rate_diagnostics')]
    public function test_combined_rate_diagnostics_is_a_plus_feature(): void
    {
        $keys = array_map(static fn ($feature) => $feature->key, (new Tool)->manifest()->features);
        self::assertContains('combined_rate_diagnostics', $keys);
    }
}
