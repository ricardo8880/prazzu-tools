<?php

namespace Tests\Unit\Core\Tools\History;

use App\Core\Tools\History\Data\RuleVersion;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RuleVersionTest extends TestCase
{
    public function test_semantic_version_is_accepted(): void
    {
        self::assertSame('2.1.0', (string) new RuleVersion('2.1.0'));
    }

    public function test_invalid_version_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RuleVersion('2026');
    }
}
