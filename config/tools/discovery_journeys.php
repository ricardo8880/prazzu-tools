<?php

return [
    'contabilidade' => [
        [
            'key' => 'funcionarios-folha',
            'title' => 'Funcionários e folha',
            'description' => 'Comece pela admissão e avance pelos custos e cálculos mais comuns da rotina trabalhista.',
            'icon' => 'bi-people',
            'start_slug' => 'simulador-admissao',
        ],
        [
            'key' => 'simples-nacional',
            'title' => 'Simples Nacional',
            'description' => 'Calcule o regime e siga para Fator R, regularização e cenários relacionados quando precisar.',
            'icon' => 'bi-calculator',
            'start_slug' => 'calculadora-simples-nacional',
        ],
        [
            'key' => 'socios-retiradas',
            'title' => 'Sócios e retiradas',
            'description' => 'Parta do pró-labore e encontre os próximos cálculos para retiradas e distribuição de lucros.',
            'icon' => 'bi-person-badge',
            'start_slug' => 'simulador-pro-labore-ideal',
        ],
        [
            'key' => 'financeiro-empresa',
            'title' => 'Financeiro da empresa',
            'description' => 'Organize uma sequência prática entre capital de giro, caixa, ponto de equilíbrio e margem.',
            'icon' => 'bi-graph-up-arrow',
            'start_slug' => 'capital-de-giro',
        ],
    ],

    // RH possui somente uma ferramenta oficial neste momento. Uma sequência de
    // uma única etapa seria artificial e não melhora descoberta; publicar uma
    // jornada aqui apenas quando existir continuidade real dentro da vertical.
    'rh' => [],
];
