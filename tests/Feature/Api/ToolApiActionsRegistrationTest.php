<?php

namespace Tests\Feature\Api;

use App\Core\Tools\Api\Services\ToolApiActionRegistry;
use Tests\TestCase;

final class ToolApiActionsRegistrationTest extends TestCase
{
    public function test_all_registered_tools_publish_a_primary_api_action(): void
    {
        $actions = app(ToolApiActionRegistry::class)->all();

        $this->assertCount(10, $actions);
        $slugs = array_keys($actions);
        sort($slugs);

        $this->assertSame([
            'calculadora-de-honorarios-contabeis',
            'calculadora-de-rescisao',
            'calculadora-ferias',
            'calculadora-margem-markup',
            'calculadora-pro-labore-distribuicao-lucros',
            'calculadora-simples-nacional',
            'comparador-tributario',
            'conversor-fiscal-xml',
            'gerador-darf-gps',
            'validador-de-cnpj',
        ], $slugs);
    }
}
