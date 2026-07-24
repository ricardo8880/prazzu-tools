<?php

declare(strict_types=1);

namespace App\Core\Feedback\Enums;

enum ToolFeedbackType: string
{
    case Problem = 'problem';
    case MissingFeature = 'missing_feature';
    case Suggestion = 'suggestion';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Problem => 'Encontrei um problema',
            self::MissingFeature => 'Senti falta de uma funcionalidade',
            self::Suggestion => 'Tenho uma sugestão',
            self::Other => 'Outro',
        };
    }
}
