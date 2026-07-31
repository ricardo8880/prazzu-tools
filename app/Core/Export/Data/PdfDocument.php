<?php

declare(strict_types=1);

namespace App\Core\Export\Data;

use InvalidArgumentException;

final readonly class PdfDocument
{
    /**
     * @param array<string, mixed> $data
     * @param 'portrait'|'landscape' $orientation
     */
    public function __construct(
        public string $filename,
        public string $view,
        public array $data = [],
        public string $paper = 'a4',
        public string $orientation = 'portrait',
    ) {
        if (trim($this->filename) === '') {
            throw new InvalidArgumentException('O nome do arquivo PDF não pode ser vazio.');
        }

        if (trim($this->view) === '') {
            throw new InvalidArgumentException('A view do PDF não pode ser vazia.');
        }

        if (! in_array($this->orientation, ['portrait', 'landscape'], true)) {
            throw new InvalidArgumentException('A orientação do PDF deve ser portrait ou landscape.');
        }
    }

    public function downloadFilename(): string
    {
        $filename = trim($this->filename);

        return str_ends_with(strtolower($filename), '.pdf') ? $filename : $filename.'.pdf';
    }
}
