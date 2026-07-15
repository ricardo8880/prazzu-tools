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
            self::Accounting => 'Escrituração contábil e demonstrações',
            self::Tax => 'Apuração fiscal e obrigações acessórias',
            self::Payroll => 'Folha de pagamento e rotinas trabalhistas',
            self::Corporate => 'Rotinas societárias recorrentes',
            self::Advisory => 'Consultoria e acompanhamento gerencial',
            self::Financial => 'BPO financeiro e conciliações',
        };
    }
}
