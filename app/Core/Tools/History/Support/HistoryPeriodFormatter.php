<?php

declare(strict_types=1);

namespace App\Core\Tools\History\Support;

final class HistoryPeriodFormatter
{
    /** @var array<int, string> */
    private const MONTHS = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
    ];

    public static function yearMonth(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^(\d{4})-(\d{2})(?:-\d{2})?$/', $value, $matches)) {
            return null;
        }

        $month = (int) $matches[2];

        return isset(self::MONTHS[$month]) ? self::MONTHS[$month].'/'.$matches[1] : null;
    }

    public static function monthNumber(mixed $value, int $year): ?string
    {
        if (! is_numeric($value)) {
            return null;
        }

        $month = (int) $value;

        return isset(self::MONTHS[$month]) ? self::MONTHS[$month].'/'.$year : null;
    }

    public static function quarter(mixed $value, int $year): ?string
    {
        if (! is_numeric($value)) {
            return null;
        }

        $quarter = (int) $value;

        return $quarter >= 1 && $quarter <= 4 ? $quarter.'º trimestre/'.$year : null;
    }
}
