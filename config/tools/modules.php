<?php

return [
    'general' => [
        App\Tools\MarginMarkupCalculator\Tool::class,
        App\Tools\BusinessDocumentValidator\Tool::class,
        // <tools:general>
    ],
    'fiscal' => [
        App\Tools\SimplesNacionalCalculator\Tool::class,
        // <tools:fiscal>
    ],
    'labor' => [
        App\Tools\LaborTerminationCalculator\Tool::class,
        // <tools:labor>
    ],
    'corporate' => [
        App\Tools\AccountingFeesCalculator\Tool::class,
        // <tools:corporate>
    ],
    'documents' => [
        // <tools:documents>
    ],
];
