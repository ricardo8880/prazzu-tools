<?php

declare(strict_types=1);

namespace Tests\Unit\Analytics;

use App\Core\Analytics\Application\Services\SimpleZipArchiveBuilder;
use PHPUnit\Framework\TestCase;

final class SimpleZipArchiveBuilderTest extends TestCase
{
    public function test_builds_zip_without_native_zip_extension(): void
    {
        $content = (new SimpleZipArchiveBuilder)->build([
            'LEIA-ME.md' => '# Teste',
            'dados/metricas.json' => '{"ok":true}',
        ]);

        self::assertStringStartsWith("PK\x03\x04", $content);
        self::assertStringContainsString('LEIA-ME.md', $content);
        self::assertStringContainsString('dados/metricas.json', $content);
        self::assertSame(pack('V', 0x06054B50), substr($content, -22, 4));
    }

    public function test_rejects_invalid_file_name(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new SimpleZipArchiveBuilder)->build(['../' => 'x']);
    }
}
