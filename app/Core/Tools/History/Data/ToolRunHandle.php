<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Data;

final readonly class ToolRunHandle
{
    public function __construct(public string $id)
    {
        if ($id === '') {
            throw new \InvalidArgumentException('O identificador da execução não pode ser vazio.');
        }
    }
}
