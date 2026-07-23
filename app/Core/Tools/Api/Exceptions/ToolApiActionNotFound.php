<?php

namespace App\Core\Tools\Api\Exceptions;

use RuntimeException;

final class ToolApiActionNotFound extends RuntimeException
{
    public static function for(string $tool, string $action): self
    {
        return new self("A ação [{$action}] não foi publicada pela ferramenta [{$tool}].");
    }
}
