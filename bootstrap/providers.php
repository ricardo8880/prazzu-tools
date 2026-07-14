<?php

use App\Providers\AppServiceProvider;
use App\Providers\CoreInfrastructureServiceProvider;
use App\Providers\ToolServiceProvider;
use App\Tools\BusinessDocumentValidator\Providers\BusinessDocumentValidatorServiceProvider;

return [
    AppServiceProvider::class,
    CoreInfrastructureServiceProvider::class,
    ToolServiceProvider::class,
    BusinessDocumentValidatorServiceProvider::class,
];
