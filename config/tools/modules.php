<?php

use App\Tools\MarginMarkupCalculator\Tool;

return [
    'general' => [
        Tool::class,
        App\Tools\BusinessDocumentValidator\Tool::class,
        // <tools:general>
    ],
    'fiscal' => [
        App\Tools\SimplesNacionalCalculator\Tool::class,
        App\Tools\TaxRegimeComparator\Tool::class,
        App\Tools\ProLaboreProfitDistributionCalculator\Tool::class,
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
