<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Versioning;

use App\Core\Versioning\SemanticVersion;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SemanticVersionTest extends TestCase
{
    public function test_it_accepts_semantic_versions(): void
    {
        self::assertSame('2.1.0-beta.1', (string) new SemanticVersion('2.1.0-beta.1'));
    }

    public function test_it_rejects_invalid_versions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SemanticVersion('2026.07');
    }
}
