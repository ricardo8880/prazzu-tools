<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Domain\Enums;

enum AccountingService: string
{
    case Accounting = 'accounting';
    case Tax = 'tax';
    case Payroll = 'payroll';
    case Corporate = 'corporate';
    case Advisory = 'advisory';
    case Financial = 'financial';

    public function label(): string
    {
        return match ($this) {
            self::Accounting => 'Bookkeeping and financial statements',
            self::Tax => 'Tax preparation and compliance',
            self::Payroll => 'Folha de pagamento e rotinas trabalhistas',
            self::Corporate => 'Recurring entity compliance',
            self::Advisory => 'Consultoria e acompanhamento gerencial',
            self::Financial => 'Bookkeeping operations and reconciliations',
        };
    }
}
