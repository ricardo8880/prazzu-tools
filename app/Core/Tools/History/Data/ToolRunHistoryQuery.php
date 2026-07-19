<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Data;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class ToolRunHistoryQuery
{
    public function __construct(
        public string $toolSlug,
        public int $userId,
        public int $page = 1,
        public int $perPage = 12,
        public ?DateTimeImmutable $from = null,
        public ?DateTimeImmutable $to = null,
        public bool $favoritesOnly = false,
    ) {
        if (trim($this->toolSlug) === '') {
            throw new InvalidArgumentException('O slug da ferramenta é obrigatório.');
        }

        if ($this->userId < 1) {
            throw new InvalidArgumentException('O usuário do histórico é inválido.');
        }

        if ($this->page < 1) {
            throw new InvalidArgumentException('A página do histórico deve ser maior que zero.');
        }

        if ($this->perPage < 1 || $this->perPage > 100) {
            throw new InvalidArgumentException('A quantidade por página deve estar entre 1 e 100.');
        }

        if ($this->from !== null && $this->to !== null && $this->from > $this->to) {
            throw new InvalidArgumentException('A data inicial do histórico não pode ser posterior à data final.');
        }
    }
}
