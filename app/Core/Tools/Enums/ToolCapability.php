<?php

namespace App\Core\Tools\Enums;

enum ToolCapability: string
{
    case History = 'history';
    case PublishesIntegrations = 'publishes-integrations';
    case AcceptsIntegrations = 'accepts-integrations';
    case SensitiveData = 'sensitive-data';
    case VersionedPersistence = 'versioned-persistence';
    case Export = 'export';
    case Sharing = 'sharing';

    public function label(): string
    {
        return match ($this) {
            self::History => 'Histórico',
            self::PublishesIntegrations => 'Publica integrações',
            self::AcceptsIntegrations => 'Aceita integrações',
            self::SensitiveData => 'Dados sensíveis',
            self::VersionedPersistence => 'Persistência versionada',
            self::Export => 'Exportação',
            self::Sharing => 'Compartilhamento',
        };
    }
}
