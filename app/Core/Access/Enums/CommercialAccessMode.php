<?php

namespace App\Core\Access\Enums;

use InvalidArgumentException;

/**
 * Define somente a política comercial da plataforma.
 *
 * Restrições operacionais, ferramentas desativadas e acesso interno
 * continuam sendo avaliados independentemente deste modo.
 */
enum CommercialAccessMode: string
{
    case LaunchFree = 'launch_free';
    case Monetized = 'monetized';

    public static function fromConfiguration(mixed $value): self
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException('O modo comercial do Prazzu Tools deve ser um texto válido.');
        }

        return self::tryFrom($value)
            ?? throw new InvalidArgumentException("Modo comercial [{$value}] não reconhecido.");
    }

    public function grantsPublicCapabilitiesWithoutAuthentication(): bool
    {
        return $this === self::LaunchFree;
    }

    public function enforcesUsageLimits(): bool
    {
        return $this === self::Monetized;
    }
}
