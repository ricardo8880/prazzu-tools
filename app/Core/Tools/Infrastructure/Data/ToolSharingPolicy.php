<?php

namespace App\Core\Tools\Infrastructure\Data;

use InvalidArgumentException;

final readonly class ToolSharingPolicy
{
    public function __construct(
        public bool $enabled = false,
        public int $expiresAfterMinutes = 60,
        public bool $requiresAuthentication = false,
        public bool $allowSensitivePayload = false,
    ) {
        if ($this->enabled && ($this->expiresAfterMinutes < 5 || $this->expiresAfterMinutes > 10080)) {
            throw new InvalidArgumentException('O compartilhamento deve expirar entre 5 minutos e 7 dias.');
        }

        if (! $this->enabled && ($this->requiresAuthentication || $this->allowSensitivePayload)) {
            throw new InvalidArgumentException('Compartilhamento desabilitado não pode declarar regras adicionais.');
        }
    }

    public static function disabled(): self
    {
        return new self();
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            enabled: (bool) ($data['enabled'] ?? false),
            expiresAfterMinutes: (int) ($data['expires_after_minutes'] ?? 60),
            requiresAuthentication: (bool) ($data['requires_authentication'] ?? false),
            allowSensitivePayload: (bool) ($data['allow_sensitive_payload'] ?? false),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'expires_after_minutes' => $this->expiresAfterMinutes,
            'requires_authentication' => $this->requiresAuthentication,
            'allow_sensitive_payload' => $this->allowSensitivePayload,
        ];
    }
}
