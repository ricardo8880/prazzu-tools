<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Domain\Services\ToolAnalyticsMetadata;
use PHPUnit\Framework\TestCase;

final class ToolAnalyticsMetadataTest extends TestCase
{
    public function test_only_allowlisted_non_sensitive_metadata_is_kept(): void
    {
        $metadata = (new ToolAnalyticsMetadata)->sanitize([
            'form' => 'main',
            'field' => 'monthly_revenue',
            'completion_percentage' => 50,
            'cpf' => '12345678900',
            'value' => '1000,00',
            'nested' => ['secret' => true],
        ]);

        self::assertSame([
            'form' => 'main',
            'field' => 'monthly_revenue',
            'completion_percentage' => 50,
        ], $metadata);
    }
}
