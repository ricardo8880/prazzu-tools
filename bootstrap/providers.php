<?php

use App\Providers\AppServiceProvider;
use App\Providers\CoreInfrastructureServiceProvider;
use App\Providers\ToolServiceProvider;

return [
    App\Core\Quality\E2E\Providers\E2EObservabilityServiceProvider::class,
    AppServiceProvider::class,
    CoreInfrastructureServiceProvider::class,
    ToolServiceProvider::class,
];
