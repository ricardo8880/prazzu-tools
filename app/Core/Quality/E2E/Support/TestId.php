<?php

namespace App\Core\Quality\E2E\Support;

use Illuminate\Support\Str;

final class TestId
{
    public static function make(string $prefix, string|int|null $value = null): string
    {
        $segments = [$prefix];

        if ($value !== null && trim((string) $value) !== '') {
            $segments[] = (string) $value;
        }

        return collect($segments)
            ->map(static fn (string $segment): string => Str::of($segment)
                ->ascii()
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/', '-')
                ->trim('-')
                ->toString())
            ->filter()
            ->implode('-');
    }

    public static function field(string $name): string
    {
        return self::make('field', $name);
    }
}
