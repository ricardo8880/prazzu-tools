<?php

return [
    /*
    | O registro é dividido por grupos para reduzir conflitos quando novas
    | ferramentas forem adicionadas. Os grupos não alteram a categoria do módulo.
    */
    'modules' => require __DIR__.'/tools/modules.php',

    'categories' => require __DIR__.'/tools/categories.php',

    /* Metadados estáticos dos placeholders. Módulos reais substituem o mesmo slug. */
    'catalog' => require __DIR__.'/tools/catalog.php',

    /* Dados demonstrativos/dinâmicos mantidos fora dos manifestos. */
    'metrics' => require __DIR__.'/tools/metrics.php',
];
