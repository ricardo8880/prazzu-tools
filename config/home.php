<?php

$accounting = [
    'page_title' => 'Prazzu Tools — Free Business Calculators and Generators',
    'meta_description' => 'Free online business, payroll, tax-planning, accounting, and document tools for U.S. users.',
    'tools_section_title' => 'Tools for the task in front of you',
    'hero' => [
        'title_before' => 'What do you need', 'title_line' => 'to solve', 'title_highlight' => 'today?',
        'description' => 'Describe the task and find a calculator, validator, simulator, or generator built for it.',
        'search_label' => 'Describe what you need to solve',
        'search_placeholder' => 'e.g. calculate overtime, validate an EIN, estimate sales tax...',
        'problem_shortcuts' => [
            ['label'=>'Estimate final pay','query'=>'final pay'], ['label'=>'Validate an EIN','query'=>'EIN'], ['label'=>'Estimate sales tax','query'=>'sales tax'], ['label'=>'Estimate employee cost','query'=>'employee cost'],
        ],
        'benefits' => [
            ['label'=>'Many tools are free','icon'=>'bi-check-circle-fill'], ['label'=>'No account required for basic use','icon'=>'bi-check-circle-fill'], ['label'=>'Privacy-conscious workflows','icon'=>'bi-check-circle-fill'], ['label'=>'Versioned calculation assumptions','icon'=>'bi-check-circle-fill'],
        ],
    ],
    'cta' => ['title'=>'Looking for another tool?','description'=>'Browse the full catalog.','label'=>'View all tools','url'=>'/tools'],
];

$hr = [
    'page_title'=>'Prazzu Tools — HR Calculators','meta_description'=>'Practical calculators for HR metrics, workforce planning, and payroll-related decisions.','tools_section_title'=>'HR tools',
    'hero'=>['title_before'=>'What do you need','title_line'=>'to solve in','title_highlight'=>'HR today?','description'=>'Find practical HR calculators and workforce planning tools.','search_label'=>'Describe your HR task','search_placeholder'=>'e.g. calculate turnover...','problem_shortcuts'=>[['label'=>'Calculate turnover','query'=>'turnover']],'benefits'=>$accounting['hero']['benefits']],
    'cta'=>['title'=>'Explore HR tools','description'=>'Browse the available HR calculators.','label'=>'View HR tools','url'=>'/tools'],
];

$global = [
    'page_title'=>'Prazzu Tools — Free Online Business Tools','meta_description'=>'Practical online tools for business, payroll, finance, accounting, and HR.','tools_section_title'=>'Tools to solve it now',
    'hero'=>['title_before'=>'What do you need','title_line'=>'to solve','title_highlight'=>'today?','description'=>'Search the Prazzu catalog for a calculator, validator, simulator, or generator.','search_label'=>'Describe what you need to solve','search_placeholder'=>'e.g. calculate, validate, simulate, or generate...','problem_shortcuts'=>[],'benefits'=>$accounting['hero']['benefits']],
    'cta'=>$accounting['cta'],
];

return [...$accounting, 'global'=>$global, 'verticals'=>['contabilidade'=>$accounting,'rh'=>$hr]];
