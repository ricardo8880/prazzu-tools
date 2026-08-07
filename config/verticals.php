<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vertical padrão
    |--------------------------------------------------------------------------
    |
    | Mantém a experiência pública atual explicitamente associada à primeira
    | vertical oficial. Use null quando a experiência geral do Prazzu precisar
    | ser o fallback padrão.
    |
    */
    'default' => 'contabilidade',

    /* Contexto explicitamente escolhido e persistido na sessão do visitante. */
    'session_key' => 'vertical.context',

    /*
    |--------------------------------------------------------------------------
    | Verticais registradas
    |--------------------------------------------------------------------------
    |
    | Verticais são dados de negócio. O Core não possui enum nem lista fechada
    | de nichos; novas entradas devem poder ser registradas sem alterar código
    | compartilhado.
    |
    */
    'registered' => [
        'contabilidade' => [
            'name' => 'Contabilidade',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sinais de aquisição
    |--------------------------------------------------------------------------
    |
    | AcquisitionContext permanece independente. Quando uma campanha ou keyword
    | conhecer explicitamente sua vertical, esse mapeamento poderá contribuir
    | para a resolução sem transferir responsabilidades entre os contextos.
    |
    */
    'acquisition' => [
        'keywords' => [],
        'campaigns' => [],
    ],
];
