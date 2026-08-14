<?php

declare(strict_types=1);

namespace App\Tools\EcadRoyaltySimulator\Tests\Feature;

use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\EcadRoyaltySimulator\Tool;
use Tests\TestCase;

final class ToolPageTest extends TestCase
{
    public function test_page_is_public(): void
    {
        $this->get('/ferramentas/simulador-ecad-direitos-autorais')->assertOk()->assertSee('ECAD');
    }

    #[CoversPlusFeature('simulador-ecad-direitos-autorais', 'period_projection')]
    public function test_period_projection_is_a_concrete_plus_feature(): void
    {
        $keys = array_map(static fn ($feature): string => $feature->key, (new Tool)->manifest()->features);
        self::assertContains('period_projection', $keys);
    }
}
