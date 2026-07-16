<?php

use App\Core\Access\Enums\CommercialAccessMode;

return [
    /*
    |--------------------------------------------------------------------------
    | Política comercial central
    |--------------------------------------------------------------------------
    |
    | launch_free libera gratuitamente todas as capacidades públicas e ignora
    | limites comerciais. monetized restaura a avaliação normal de plano,
    | autenticação e consumo, sem exigir mudanças dentro das ferramentas.
    |
    */
    'commercial_mode' => env('PRAZZU_COMMERCIAL_MODE', CommercialAccessMode::LaunchFree->value),
];
