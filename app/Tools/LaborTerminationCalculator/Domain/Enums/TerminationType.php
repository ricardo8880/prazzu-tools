<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Domain\Enums;

enum TerminationType: string
{
    case DismissalWithoutCause = 'dismissal_without_cause';
    case Resignation = 'resignation';
    case DismissalWithCause = 'dismissal_with_cause';
    case MutualAgreement = 'mutual_agreement';
    case IndirectTermination = 'indirect_termination';
    case ContractEnd = 'contract_end';
    case EarlyContractEnd = 'early_contract_end';

    public function label(): string
    {
        return match ($this) {
            self::DismissalWithoutCause => 'Dispensa sem justa causa',
            self::Resignation => 'Pedido de demissão',
            self::DismissalWithCause => 'Dispensa por justa causa',
            self::MutualAgreement => 'Acordo entre empregado e empregador',
            self::IndirectTermination => 'Rescisão indireta',
            self::ContractEnd => 'Término normal do contrato',
            self::EarlyContractEnd => 'Encerramento antecipado do contrato',
        };
    }

    public function grantsProportionalVacation(): bool
    {
        return $this !== self::DismissalWithCause;
    }

    public function grantsProportionalThirteenth(): bool
    {
        return $this !== self::DismissalWithCause;
    }

    public function isEmployerInitiatedWithoutCause(): bool
    {
        return in_array($this, [self::DismissalWithoutCause, self::IndirectTermination], true);
    }
}
