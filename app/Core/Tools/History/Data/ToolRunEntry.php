<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Data;

use DateTimeImmutable;

final readonly class ToolRunEntry
{
    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $result
     */
    public function __construct(
        public string $id,
        public string $toolSlug,
        public DateTimeImmutable $referenceDate,
        public array $input,
        public array $result,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $finishedAt,
        public string $toolVersion,
        public string $ruleVersion,
        public bool $favorite = false,
    ) {}
}
