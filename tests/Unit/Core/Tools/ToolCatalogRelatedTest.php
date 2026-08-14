<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools;

use App\Core\Tools\ToolCatalog;
use Tests\TestCase;

final class ToolCatalogRelatedTest extends TestCase
{
    public function test_related_tools_come_from_registry_and_never_include_the_current_tool(): void
    {
        $catalog = $this->app->make(ToolCatalog::class);
        $related = $catalog->related('simulador-fator-r');

        self::assertNotEmpty($related);
        self::assertNotContains('simulador-fator-r', $related->pluck('slug')->all());
        self::assertLessThanOrEqual(4, $related->count());

        foreach ($related as $tool) {
            self::assertNotNull($catalog->find($tool['slug']));
        }
    }

    public function test_unknown_tool_has_no_related_results(): void
    {
        self::assertTrue(
            $this->app->make(ToolCatalog::class)->related('ferramenta-inexistente')->isEmpty(),
        );
    }
    public function test_curated_journey_is_used_before_the_heuristic_fallback(): void
    {
        $related = $this->app->make(ToolCatalog::class)
            ->related('calculadora-salario-liquido')
            ->pluck('slug')
            ->all();

        self::assertSame([
            'custo-funcionario-clt',
            'calculadora-hora-extra',
            'calculadora-ferias',
            'calculadora-de-rescisao',
        ], $related);
    }


    public function test_next_steps_use_only_the_editorial_journey_and_mark_the_primary_action(): void
    {
        $steps = $this->app->make(ToolCatalog::class)
            ->nextSteps('calculadora-salario-liquido');

        self::assertSame([
            'custo-funcionario-clt',
            'calculadora-hora-extra',
            'calculadora-ferias',
            'calculadora-de-rescisao',
        ], $steps->pluck('slug')->all());
        self::assertTrue((bool) $steps->first()['is_primary_next_step']);
        self::assertSame([1, 2, 3, 4], $steps->pluck('journey_position')->all());
        self::assertSame(1, $steps->where('is_primary_next_step', true)->count());
    }

    public function test_next_steps_do_not_invent_cross_vertical_fallbacks(): void
    {
        self::assertTrue(
            $this->app->make(ToolCatalog::class)->nextSteps('calculadora-turnover')->isEmpty(),
        );
    }

    public function test_every_official_tool_has_an_editorial_journey_entry_and_only_points_to_official_tools(): void
    {
        $official = collect(config('product_tools.official', []))->pluck('slug')->all();
        $journeys = config('tools.journeys', []);

        self::assertSame([], array_values(array_diff($official, array_keys($journeys))));
        self::assertSame([], array_values(array_diff(array_keys($journeys), $official)));

        foreach ($journeys as $slug => $relatedSlugs) {
            self::assertNotContains($slug, $relatedSlugs);
            self::assertSame([], array_values(array_diff($relatedSlugs, $official)));
            self::assertSame($relatedSlugs, array_values(array_unique($relatedSlugs)));
        }
    }

}
