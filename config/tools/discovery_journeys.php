<?php

return [
    'contabilidade' => [
        [
            'key' => 'funcionarios-folha',
            'title' => 'Funcionários e folha',
            'description' => 'Comece pela admissão e avance pelos custos e cálculos mais comuns da rotina trabalhista.',
            'icon' => 'bi-people',
            'start_slug' => 'new-hire-cost-calculator',
        ],
        [
            'key' => 'financeiro-empresa',
            'title' => 'Financeiro da empresa',
            'description' => 'Organize uma sequência prática entre capital de giro, caixa, ponto de equilíbrio e margem.',
            'icon' => 'bi-graph-up-arrow',
            'start_slug' => 'working-capital-calculator',
        ],
    ],

    // RH possui somente uma ferramenta oficial neste momento. Uma sequência de
    // uma única etapa seria artificial e não melhora descoberta; publicar uma
    // jornada aqui apenas quando existir continuidade real dentro da vertical.
    'rh' => [],
];
