<?php

declare(strict_types=1);

namespace App\Core\Identifiers;

final class Digits
{
    public static function only(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    public static function hasAllEqual(string $value): bool
    {
        return $value !== '' && str_repeat($value[0], strlen($value)) === $value;
    }
}
