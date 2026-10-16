<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Domain\Enums;

enum PartyDocumentType: string
{
    case Ein = 'ein';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Ein => 'EIN',
            self::Other => 'Other ID',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return [self::Ein->value => self::Ein->label(), self::Other->value => self::Other->label()];
    }
}
