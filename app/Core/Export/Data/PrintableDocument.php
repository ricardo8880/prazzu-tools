<?php

declare(strict_types=1);

namespace App\Core\Export\Data;

final readonly class PrintableDocument
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public string $title,
        public string $contentView,
        public array $data = [],
        public string $applicationName = 'Prazzu Tools',
        public ?string $subtitle = null,
        public ?string $generatedAt = null,
        public ?string $summaryLabel = null,
        public ?string $summaryValue = null,
        public string $backLabel = 'Voltar',
        public string $printLabel = 'Imprimir / Salvar como PDF',
    ) {}
}
