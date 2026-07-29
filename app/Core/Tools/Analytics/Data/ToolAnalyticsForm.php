<?php

declare(strict_types=1);

namespace App\Core\Tools\Analytics\Data;

use InvalidArgumentException;

final readonly class ToolAnalyticsForm
{
    /**
     * @param list<string> $steps
     * @param list<ToolAnalyticsField> $fields
     * @param list<string> $actions
     */
    public function __construct(
        public string $key,
        public array $steps,
        public array $fields,
        public array $actions = ['calculate'],
        public ?string $selector = null,
        public ?string $resultSelector = null,
    ) {
        self::assertIdentifier($key, 'formulário');
        self::assertSelector($selector, 'formulário');
        self::assertSelector($resultSelector, 'resultado');

        if ($steps === []) {
            throw new InvalidArgumentException("A jornada do formulário [{$key}] deve declarar ao menos uma etapa.");
        }

        $normalizedSteps = [];
        foreach ($steps as $step) {
            self::assertIdentifier($step, 'etapa');
            if (isset($normalizedSteps[$step])) {
                throw new InvalidArgumentException("A etapa [{$step}] foi declarada mais de uma vez em [{$key}].");
            }
            $normalizedSteps[$step] = true;
        }

        $normalizedFields = [];
        foreach ($fields as $field) {
            if (! $field instanceof ToolAnalyticsField) {
                throw new InvalidArgumentException("Todos os campos de [{$key}] devem ser ToolAnalyticsField.");
            }
            if (! isset($normalizedSteps[$field->step])) {
                throw new InvalidArgumentException("O campo [{$field->key}] aponta para a etapa inexistente [{$field->step}].");
            }
            if (isset($normalizedFields[$field->key])) {
                throw new InvalidArgumentException("O campo [{$field->key}] foi declarado mais de uma vez em [{$key}].");
            }
            $normalizedFields[$field->key] = true;
        }

        $normalizedActions = [];
        foreach ($actions as $action) {
            self::assertIdentifier($action, 'ação');
            if (isset($normalizedActions[$action])) {
                throw new InvalidArgumentException("A ação [{$action}] foi declarada mais de uma vez em [{$key}].");
            }
            $normalizedActions[$action] = true;
        }
    }

    /** @return array<string, mixed> */
    public function toFrontendArray(): array
    {
        $data = [
            'key' => $this->key,
            'steps' => $this->steps,
            'fields' => array_map(
                static fn (ToolAnalyticsField $field): array => $field->toFrontendArray(),
                $this->fields,
            ),
            'actions' => $this->actions,
        ];

        if ($this->selector !== null) {
            $data['selector'] = $this->selector;
        }
        if ($this->resultSelector !== null) {
            $data['result_selector'] = $this->resultSelector;
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
