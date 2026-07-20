<?php

namespace App\Core\Tools\Infrastructure\Enums;

enum SensitiveDataMode: string
{
    case None = 'none';
    case Encrypted = 'encrypted';
    case Redacted = 'redacted';
}
