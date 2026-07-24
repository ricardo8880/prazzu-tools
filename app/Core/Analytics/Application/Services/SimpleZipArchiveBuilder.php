<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

use App\Core\Export\Services\SimpleZipArchiveBuilder as CoreSimpleZipArchiveBuilder;

/**
 * @deprecated Use App\Core\Export\Services\SimpleZipArchiveBuilder.
 */
final class SimpleZipArchiveBuilder
{
    public function __construct(
        private readonly CoreSimpleZipArchiveBuilder $builder = new CoreSimpleZipArchiveBuilder,
    ) {}

    /** @param array<string, string> $files */
    public function build(array $files): string
    {
        return $this->builder->build($files);
    }
}
