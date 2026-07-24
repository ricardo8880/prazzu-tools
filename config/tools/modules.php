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
        \App\Tools\FiscalXmlConverter\Tool::class,
        \App\Tools\FederalPaymentGuideGenerator\Tool::class,
        // <tools:fiscal>
    ],
    'labor' => [
        App\Tools\LaborTerminationCalculator\Tool::class,
        \App\Tools\VacationCalculator\Tool::class,
        // <tools:labor>
    ],
    'corporate' => [
        App\Tools\AccountingFeesCalculator\Tool::class,
        // <tools:corporate>
    ],
    'documents' => [
        \App\Tools\ReceiptIssuer\Tool::class,
        \App\Tools\ContractGenerator\Tool::class,
        // <tools:documents>
    ],
];
