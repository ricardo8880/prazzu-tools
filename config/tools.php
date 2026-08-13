<?php

return [
    /*
    | O registro é dividido por grupos para reduzir conflitos quando novas
    | ferramentas forem adicionadas. Os grupos não alteram a categoria do módulo.
    */
    'modules' => require __DIR__.'/tools/modules.php',

    'categories' => require __DIR__.'/tools/categories.php',

    // Próximos passos editoriais entre ferramentas. O catálogo usa esta ordem
    // primeiro e completa eventuais lacunas com a heurística histórica.
    'journeys' => require __DIR__.'/tools/journeys.php',
];
