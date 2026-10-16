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
            self::Payroll => 'Payroll processing and payroll compliance',
            self::Corporate => 'Recurring entity compliance',
            self::Advisory => 'Advisory and management reporting',
            self::Financial => 'Bookkeeping operations and reconciliations',
        };
    }
}
