<?php

use App\Providers\AppServiceProvider;
use App\Providers\CoreInfrastructureServiceProvider;
use App\Providers\ToolServiceProvider;

return [
    AppServiceProvider::class,
    CoreInfrastructureServiceProvider::class,
    ToolServiceProvider::class,
];
