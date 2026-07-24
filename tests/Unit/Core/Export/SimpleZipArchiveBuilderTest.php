<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Export;

use App\Core\Export\Services\SimpleZipArchiveBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SimpleZipArchiveBuilderTest extends TestCase
{
    public function test_builds_zip_package_without_external_extension(): void
    {
        $archive = (new SimpleZipArchiveBuilder)->build([
            'docs/readme.txt' => 'Prazzu',
        ]);

        self::assertStringStartsWith('PK', $archive);
        self::assertStringContainsString('docs/readme.txt', $archive);
        self::assertStringContainsString('Prazzu', $archive);
    }

    public function test_rejects_path_without_safe_file_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new SimpleZipArchiveBuilder)->build(['../' => 'x']);
    }
}
