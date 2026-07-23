<?php

namespace App\Core\Tools\Api\Exceptions;

use RuntimeException;

final class InvalidToolApiAction extends RuntimeException
{
    public static function duplicate(string $tool, string $action): self
    {
        return new self("A ação de API [{$action}] foi declarada mais de uma vez pela ferramenta [{$tool}].");
    }

    public static function name(string $class): self
    {
        return new self("A ação de API [{$class}] deve declarar um nome válido.");
    }
}
