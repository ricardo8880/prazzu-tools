<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools\Analytics;

use App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ToolAnalyticsPilotJourneyTest extends TestCase
{
    #[DataProvider('pilots')]
    public function test_pilot_declares_a_valid_non_empty_journey(string $toolClass, string $slug, array $forms): void
    {
        $tool = new $toolClass;

        self::assertInstanceOf(HasAnalyticsJourney::class, $tool);
        $journey = $tool->analyticsJourney();
        self::assertSame($slug, $journey->toolSlug);
        self::assertSame($forms, array_map(static fn ($form): string => $form->key, $journey->forms));

        foreach ($journey->forms as $form) {
            self::assertNotEmpty($form->steps);
            self::assertNotEmpty($form->fields);
        }
    }

    public static function pilots(): iterable
    {
        yield 'capital de giro simples' => [\App\Tools\WorkingCapitalCalculator\Tool::class, 'capital-de-giro', ['main']];
        yield 'comparador tributário extenso' => [\App\Tools\TaxRegimeComparator\Tool::class, 'comparador-tributario', ['comparison']];
        yield 'declaração com impressão' => [\App\Tools\IncomeStatementGenerator\Tool::class, 'declaracao-rendimentos', ['statement']];
        yield 'contrato em duas fases' => [\App\Tools\ContractGenerator\Tool::class, 'gerador-de-contratos', ['draft', 'editor']];
        yield 'xml individual e lote' => [\App\Tools\FiscalXmlConverter\Tool::class, 'conversor-fiscal-xml', ['single', 'batch']];
    }
}
