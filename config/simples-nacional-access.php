<?php

return [
    /* Libera a ferramenta inteira para desenvolvimento e lançamento inicial. */
    'enabled' => env('SIMPLES_NACIONAL_ENABLED', true),

    /* Ao implementar a assinatura, altere para false. */
    'unlock_plus' => env('SIMPLES_NACIONAL_UNLOCK_PLUS', true),

    /* Campo do usuário que armazenará o plano contratado. */
    'user_plan_attribute' => env('SIMPLES_NACIONAL_PLAN_ATTRIBUTE', 'plan'),

    /* Valores que concedem acesso aos recursos Plus. */
    'plus_plans' => ['plus', 'premium'],
];
