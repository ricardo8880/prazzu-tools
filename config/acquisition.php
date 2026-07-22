<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache dos contextos de aquisição
    |--------------------------------------------------------------------------
    |
    | Tempo, em segundos, durante o qual um contexto resolvido permanece em
    | cache. Alterações administrativas invalidam o contexto imediatamente.
    |
    */
    'cache_ttl' => (int) env('ACQUISITION_CONTEXT_CACHE_TTL', 3600),
];
