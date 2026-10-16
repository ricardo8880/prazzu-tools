<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\BusinessDocumentValidator\Tool;
use PHPUnit\Framework\TestCase;

final class PlusFeaturesTest extends TestCase
{
    #[CoversPlusFeature('us-tax-id-validator', 'batch_validation')]
    public function test_batch_validation_is_declared_as_plus(): void
    {
        $keys = array_map(static fn ($feature) => $feature->key, (new Tool)->manifest()->features);
        self::assertContains('batch_validation', $keys);
    }
}
