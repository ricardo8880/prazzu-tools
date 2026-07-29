<?php

declare(strict_types=1);

namespace App\Core\Tools\Analytics\Data;

use InvalidArgumentException;

final readonly class ToolAnalyticsJourney
{
    /** @param list<ToolAnalyticsForm> $forms */
    public function __construct(
        public string $toolSlug,
        public array $forms,
        public int $schemaVersion = 1,
    ) {
        if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $toolSlug) !== 1 || strlen($toolSlug) > 120) {
            throw new InvalidArgumentException("Slug de ferramenta inválido: {$toolSlug}");
        }
        if ($schemaVersion < 1) {
            throw new InvalidArgumentException('A versão da jornada deve ser maior que zero.');
        }

        $keys = [];
        foreach ($forms as $form) {
            if (! $form instanceof ToolAnalyticsForm) {
                throw new InvalidArgumentException("Todos os formulários de [{$toolSlug}] devem ser ToolAnalyticsForm.");
            }
            if (isset($keys[$form->key])) {
                throw new InvalidArgumentException("O formulário [{$form->key}] foi declarado mais de uma vez em [{$toolSlug}].");
            }
            $keys[$form->key] = true;
        }
    }

    /** @return array{tool: string, schema_version: int, forms: list<array<string, mixed>>} */
    public function toFrontendArray(): array
    {
        return [
            'tool' => $this->toolSlug,
            'schema_version' => $this->schemaVersion,
            'forms' => array_map(
                static fn (ToolAnalyticsForm $form): array => $form->toFrontendArray(),
                $this->forms,
            ),
        ];
    }
}
