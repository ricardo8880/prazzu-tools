<?php

declare(strict_types=1);

namespace App\Core\Tools\Calculation\Data;

use App\Core\ToolIntegration\Data\IntegrationPayload;
use InvalidArgumentException;

final readonly class ToolCalculationResult
{
    /**
     * @param list<ToolCalculationSummaryItem> $summary
     * @param array<string, mixed> $details
     * @param list<ToolCalculationWarning> $warnings
     * @param list<ToolCalculationAction> $nextActions
     */
    public function __construct(
        public string $toolSlug,
        public string $schemaVersion,
        public array $summary,
        public array $details = [],
        public array $warnings = [],
        public array $nextActions = [],
        public ?IntegrationPayload $integrationPayload = null,
        public ?CalculationMemory $calculationMemory = null,
    ) {
        if (trim($this->toolSlug) === '') {
            throw new InvalidArgumentException('O slug da ferramenta não pode ser vazio.');
        }

        if (! preg_match('/^\d+\.\d+\.\d+$/', $this->schemaVersion)) {
            throw new InvalidArgumentException('A versão do schema do resultado deve seguir o formato semântico x.y.z.');
        }

        $this->assertListOf($this->summary, ToolCalculationSummaryItem::class, 'resumo');
        $this->assertListOf($this->warnings, ToolCalculationWarning::class, 'alertas');
        $this->assertListOf($this->nextActions, ToolCalculationAction::class, 'ações posteriores');
        $this->assertUniqueSummaryKeys();
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'tool_slug' => $this->toolSlug,
            'schema_version' => $this->schemaVersion,
            'summary' => array_map(
                static fn (ToolCalculationSummaryItem $item): array => $item->toArray(),
                $this->summary,
            ),
            'details' => $this->details,
            'warnings' => array_map(
                static fn (ToolCalculationWarning $warning): array => $warning->toArray(),
                $this->warnings,
            ),
            'next_actions' => array_map(
                static fn (ToolCalculationAction $action): array => $action->toArray(),
                $this->nextActions,
            ),
            'calculation_memory' => $this->calculationMemory?->toArray(),
            'integration_payload' => $this->integrationPayload === null ? null : [
                'source_tool' => $this->integrationPayload->sourceTool,
                'contract' => $this->integrationPayload->contractKey(),
                'data' => $this->integrationPayload->data,
                'created_at' => $this->integrationPayload->createdAt->format('Y-m-d\TH:i:s.uP'),
            ],
        ];
    }

    /**
     * Normaliza o resultado para persistência sem obrigar cada ferramenta a
     * reconstruir o resumo. As chaves do resumo permanecem no nível raiz para
     * compatibilidade com as políticas de histórico existentes, enquanto o
     * envelope completo preserva rótulos, memória, alertas e ações.
     *
     * @return array<string, mixed>
     */
    public function toPersistenceArray(): array
    {
        $payload = [];

        foreach ($this->summary as $item) {
            $payload[$item->key] = $item->value;
        }

        return [
            ...$payload,
            'summary' => array_map(
                static fn (ToolCalculationSummaryItem $item): array => $item->toArray(),
                $this->summary,
            ),
            'details' => $this->details,
            'warnings' => array_map(
                static fn (ToolCalculationWarning $warning): array => $warning->toArray(),
                $this->warnings,
            ),
            'next_actions' => array_map(
                static fn (ToolCalculationAction $action): array => $action->toArray(),
                $this->nextActions,
            ),
            'calculation_memory' => $this->calculationMemory?->toArray(),
        ];
    }

    /** @param array<mixed> $items */
    private function assertListOf(array $items, string $expectedClass, string $label): void
    {
        if (! array_is_list($items)) {
            throw new InvalidArgumentException("A coleção de {$label} deve ser uma lista.");
        }

        foreach ($items as $item) {
            if (! $item instanceof $expectedClass) {
                throw new InvalidArgumentException("A coleção de {$label} contém um item inválido.");
            }
        }
    }

    private function assertUniqueSummaryKeys(): void
    {
        $keys = array_map(
            static fn (ToolCalculationSummaryItem $item): string => $item->key,
            $this->summary,
        );

        if (count($keys) !== count(array_unique($keys))) {
            throw new InvalidArgumentException('As chaves do resumo do cálculo devem ser únicas.');
        }
    }
}
