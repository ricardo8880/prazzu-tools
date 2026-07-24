<?php

declare(strict_types=1);

namespace App\Core\Feedback\Data;

use App\Core\Feedback\Enums\ToolFeedbackType;
use InvalidArgumentException;

final readonly class ToolFeedbackSubmission
{
    /** @param array<string, scalar|null> $context */
    public function __construct(
        public string $toolSlug,
        public ToolFeedbackType $type,
        public string $message,
        public ?string $attemptedAction,
        public string $path,
        public string $url,
        public ?int $userId = null,
        public ?string $sessionId = null,
        public ?string $userAgent = null,
        public array $context = [],
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $this->toolSlug)) {
            throw new InvalidArgumentException('O slug da ferramenta é inválido.');
        }

        $message = trim($this->message);

        if ($message === '') {
            throw new InvalidArgumentException('A mensagem do feedback é obrigatória.');
        }

        if (mb_strlen($message) > 5000) {
            throw new InvalidArgumentException('A mensagem do feedback não pode ultrapassar 5000 caracteres.');
        }

        if ($this->attemptedAction !== null && mb_strlen(trim($this->attemptedAction)) > 2000) {
            throw new InvalidArgumentException('A descrição do que estava sendo feito não pode ultrapassar 2000 caracteres.');
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

        foreach ($this->context as $key => $value) {
            if (! is_string($key) || $key === '' || mb_strlen($key) > 100) {
                throw new InvalidArgumentException('O contexto técnico contém uma chave inválida.');
            }

            if (is_string($value) && mb_strlen($value) > 1000) {
                throw new InvalidArgumentException("O contexto técnico [{$key}] ultrapassa o limite permitido.");
            }
        }
    }

    public function normalizedMessage(): string
    {
        return trim($this->message);
    }

    public function normalizedAttemptedAction(): ?string
    {
        $value = trim((string) $this->attemptedAction);

        return $value === '' ? null : $value;
    }
}
