<?php

declare(strict_types=1);

namespace App\Core\Tools\Analytics\Data;

use InvalidArgumentException;

final readonly class ToolAnalyticsField
{
    public function __construct(
        public string $key,
        public string $step,
        public bool $required = false,
        public ?string $selector = null,
    ) {
        self::assertIdentifier($key, 'campo');
        self::assertIdentifier($step, 'etapa');
        self::assertSelector($selector, 'campo');
    }

    /** @return array{key: string, step: string, required: bool, selector?: string} */
    public function toFrontendArray(): array
    {
        $data = [
            'key' => $this->key,
            'step' => $this->step,
            'required' => $this->required,
        ];

        if ($this->selector !== null) {
            $data['selector'] = $this->selector;
        }

        return $data;
    }

    private static function assertIdentifier(string $value, string $label): void
    {
        if (preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $value) !== 1 || strlen($value) > 80) {
            throw new InvalidArgumentException("Identificador de {$label} inválido: {$value}");
        }
    }

    private static function assertSelector(?string $selector, string $label): void
    {
        if ($selector === null) {
            return;
        }

        if ($selector === '' || strlen($selector) > 200 || preg_match('/[\x00-\x1F\x7F]/', $selector) === 1) {
            throw new InvalidArgumentException("Seletor de {$label} inválido.");
        }
    }
}
