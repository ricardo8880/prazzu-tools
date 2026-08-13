<?php

use App\Core\Quality\E2E\Providers\E2EObservabilityServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\CoreInfrastructureServiceProvider;
use App\Providers\ToolServiceProvider;

return [
    E2EObservabilityServiceProvider::class,
    AppServiceProvider::class,
    CoreInfrastructureServiceProvider::class,
    ToolServiceProvider::class,
];
