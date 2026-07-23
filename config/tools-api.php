<?php

$clients = json_decode((string) env('TOOLS_API_CLIENTS', '[]'), true);

return [
    'name' => env('TOOLS_API_NAME', 'Prazzu Tools API'),
    'version' => 'v1',

    /*
    | Cada cliente deve possuir id, token e abilities. Exemplo:
    | [{"id":"prazzu-core","name":"Prazzu Core","token":"segredo","abilities":["tools:read","tools:execute"]}]
    */
    'clients' => is_array($clients) ? $clients : [],

    'rate_limit' => (int) env('TOOLS_API_RATE_LIMIT', 120),
];
