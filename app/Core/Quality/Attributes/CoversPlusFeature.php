<?php

declare(strict_types=1);

namespace App\Core\Quality\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class CoversPlusFeature
{
    public function __construct(
        public string $toolSlug,
        public string $featureKey,
    ) {
        if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->toolSlug) !== 1) {
            throw new InvalidArgumentException('O slug do contrato funcional Plus é inválido.');
        }

        if (preg_match('/^[a-z][a-z0-9_]*$/', $this->featureKey) !== 1) {
            throw new InvalidArgumentException('A chave do contrato funcional Plus é inválida.');
        }
    }

    public function contractKey(): string
    {
        return $this->toolSlug.':'.$this->featureKey;
    }
}
