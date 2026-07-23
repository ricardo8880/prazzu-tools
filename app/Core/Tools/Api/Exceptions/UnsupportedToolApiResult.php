<?php

namespace App\Core\Tools\Api\Exceptions;

use RuntimeException;

final class UnsupportedToolApiResult extends RuntimeException
{
    public static function from(mixed $result): self
    {
        return new self(sprintf(
            'O resultado da ação de API possui um tipo não suportado: %s.',
            get_debug_type($result),
        ));
    }
}
