<?php

declare(strict_types=1);

namespace App\Core\Feedback\Data;

use App\Core\Feedback\Enums\ToolResolution;
use App\Core\Feedback\Enums\ToolResolutionReason;
use InvalidArgumentException;

final readonly class ToolResolutionSubmission
{
    public function __construct(
        public string $toolSlug,
        public ToolResolution $resolution,
        public ?ToolResolutionReason $reason,
        public ?string $comment,
        public string $path,
        public string $url,
        public ?int $userId = null,
        public ?string $sessionId = null,
        public ?string $userAgent = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->toolSlug)) {
            throw new InvalidArgumentException('O slug da ferramenta é inválido.');
        }

        if (in_array($this->resolution, [ToolResolution::Partially, ToolResolution::No], true) && $this->reason === null) {
            throw new InvalidArgumentException('O motivo é obrigatório quando a necessidade não foi resolvida completamente.');
        }

        if ($this->resolution === ToolResolution::Yes && $this->reason !== null) {
            throw new InvalidArgumentException('Uma resposta positiva não deve registrar motivo de resolução incompleta.');
        }

        if ($this->comment !== null && mb_strlen(trim($this->comment)) > 1000) {
            throw new InvalidArgumentException('O comentário não pode ultrapassar 1000 caracteres.');
        }

        if (! str_starts_with($this->path, '/') || mb_strlen($this->path) > 512) {
            throw new InvalidArgumentException('O caminho da página é inválido.');
        }

        if (mb_strlen($this->url) > 4096 || filter_var($this->url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('A URL da página é inválida.');
        }

        if ($this->userId !== null && $this->userId < 1) {
            throw new InvalidArgumentException('O usuário do feedback é inválido.');
        }

        if ($this->sessionId !== null && mb_strlen($this->sessionId) > 255) {
            throw new InvalidArgumentException('O identificador da sessão é inválido.');
        }

        if ($this->userAgent !== null && mb_strlen($this->userAgent) > 1024) {
            throw new InvalidArgumentException('O user agent é inválido.');
        }
    }

    public function normalizedComment(): ?string
    {
        $value = trim((string) $this->comment);

        return $value === '' ? null : $value;
    }
}
