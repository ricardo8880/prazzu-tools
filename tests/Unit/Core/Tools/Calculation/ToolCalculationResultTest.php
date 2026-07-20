<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools\Calculation;

use App\Core\Tools\Calculation\Data\ToolCalculationAction;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToolCalculationResultTest extends TestCase
{
    public function test_it_serializes_the_shared_calculation_contract(): void
    {
        $result = new ToolCalculationResult(
            toolSlug: 'example-tool',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('total', 'Total', 'R$ 100,00'),
            ],
            details: ['raw_total' => 10000],
            warnings: [
                new ToolCalculationWarning(
                    code: 'review-value',
                    message: 'Revise o valor antes de continuar.',
                    level: ToolCalculationWarningLevel::Info,
                ),
            ],
            nextActions: [
                new ToolCalculationAction('review', 'Revisar', 'review'),
            ],
        );

        self::assertSame('example-tool', $result->toArray()['tool_slug']);
        self::assertSame('R$ 100,00', $result->toArray()['summary'][0]['value']);
        self::assertSame('info', $result->toArray()['warnings'][0]['level']);
        self::assertSame('review', $result->toArray()['next_actions'][0]['type']);
    }

    public function test_it_rejects_duplicate_summary_keys(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('As chaves do resumo do cálculo devem ser únicas.');

        new ToolCalculationResult(
            toolSlug: 'example-tool',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('total', 'Total', '100'),
                new ToolCalculationSummaryItem('total', 'Total novamente', '100'),
            ],
        );
    }

    public function test_it_requires_a_semantic_schema_version(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ToolCalculationResult(
            toolSlug: 'example-tool',
            schemaVersion: 'v1',
            summary: [],
        );
    }
}
