<?php

declare(strict_types=1);

namespace App\Core\Feedback\Enums;

enum ToolFeedbackStatus: string
{
    case New = 'new';
    case InReview = 'in_review';
    case Planned = 'planned';
    case Implemented = 'implemented';
    case Rejected = 'rejected';
    case NotReproduced = 'not_reproduced';
    case Duplicate = 'duplicate';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Novo',
            self::InReview => 'Em análise',
            self::Planned => 'Planejado',
            self::Implemented => 'Implementado',
            self::Rejected => 'Não será implementado',
            self::NotReproduced => 'Não reproduzido',
            self::Duplicate => 'Duplicado',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this, [
            self::Implemented,
            self::Rejected,
            self::NotReproduced,
            self::Duplicate,
        ], true);
    }
}
