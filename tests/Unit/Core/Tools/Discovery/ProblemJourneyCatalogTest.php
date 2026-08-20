<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Tools\Discovery;

use App\Core\Tools\Discovery\Application\ProblemJourneyCatalog;
use App\Core\Verticals\Application\VerticalContext;
use App\Core\Verticals\Contracts\VerticalRegistry;
use Tests\TestCase;

final class ProblemJourneyCatalogTest extends TestCase
{
    public function test_accounting_journeys_reuse_the_existing_editorial_next_steps(): void
    {
        $journeys = $this->app->make(ProblemJourneyCatalog::class)->forActiveVertical();

        self::assertSame([
            'funcionarios-folha',
            'simples-nacional',
            'socios-retiradas',
            'financeiro-empresa',
        ], $journeys->pluck('key')->all());

        $employees = $journeys->firstWhere('key', 'funcionarios-folha');
        self::assertSame('simulador-admissao', $employees['start_slug']);
        self::assertSame([
            'simulador-admissao',
            'custo-funcionario-clt',
            'calculadora-salario-liquido',
            'encargos-trabalhistas',
        ], $employees['steps']->pluck('slug')->all());
    }

    public function test_vertical_without_real_sequence_does_not_receive_an_artificial_journey(): void
    {
        $vertical = $this->app->make(VerticalRegistry::class)->find('rh');
        self::assertNotNull($vertical);
        $this->app->make(VerticalContext::class)->activate($vertical);

        self::assertTrue(
            $this->app->make(ProblemJourneyCatalog::class)->forActiveVertical()->isEmpty(),
        );
    }

    public function test_unknown_journey_cannot_be_resolved(): void
    {
        self::assertNull(
            $this->app->make(ProblemJourneyCatalog::class)->findForActiveVertical('inexistente'),
        );
    }
}
