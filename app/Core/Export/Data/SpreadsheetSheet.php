<?php

declare(strict_types=1);

namespace App\Core\Export\Data;

use InvalidArgumentException;

final readonly class SpreadsheetSheet
{
    /**
     * @param list<list<string|int|float|bool|null>> $rows
     */
    public function __construct(
        public string $name,
        public array $rows,
        public bool $freezeHeader = true,
        public bool $autoSizeColumns = true,
    ) {
        if (trim($this->name) === '') {
            throw new InvalidArgumentException('O nome da planilha não pode ser vazio.');
        }

        if (preg_match_all('/./u', $this->name) > 31) {
            throw new InvalidArgumentException('O nome da planilha não pode exceder 31 caracteres.');
        }

        if (preg_match('~[\\\\/?:*\[\]]~', $this->name) === 1) {
            throw new InvalidArgumentException('O nome da planilha contém caracteres inválidos.');
        }
    }
}
