<?php

declare(strict_types=1);

namespace App\Core\Feedback\Enums;

enum ToolResolutionReason: string
{
    case ResultUnclear = 'result_unclear';
    case MissingOption = 'missing_option';
    case TrustConcern = 'trust_concern';
    case CaseNotCovered = 'case_not_covered';
    case FoundError = 'found_error';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ResultUnclear => 'Não entendi o resultado',
            self::MissingOption => 'Faltou um campo ou opção',
            self::TrustConcern => 'Não consegui confiar no cálculo',
            self::CaseNotCovered => 'Meu caso não é atendido',
            self::FoundError => 'Encontrei um erro',
            self::Other => 'Outro motivo',
        };
    }
}
