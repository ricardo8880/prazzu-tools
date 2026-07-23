<?php

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;

return [
    'enabled' => env('ANALYTICS_ENABLED', true),
    'async' => env('ANALYTICS_ASYNC', false),
    'visitor_cookie' => env('ANALYTICS_VISITOR_COOKIE', 'prazzu_visitor'),
    'visitor_cookie_days' => (int) env('ANALYTICS_VISITOR_COOKIE_DAYS', 730),
    'session_timeout_minutes' => (int) env('ANALYTICS_SESSION_TIMEOUT_MINUTES', 30),
    'capture_page_views' => env('ANALYTICS_CAPTURE_PAGE_VIEWS', true),
    'excluded_paths' => [
        'admin/*',
        'up',
    ],
    'retention_days' => (int) env('ANALYTICS_RETENTION_DAYS', 730),

    'deduplication' => [
        'enabled' => env('ANALYTICS_DEDUPLICATION_ENABLED', true),
        'default_window_seconds' => (int) env('ANALYTICS_DEDUPLICATION_WINDOW_SECONDS', 5),
        'event_windows' => [
            AnalyticsEventName::PageViewed->value => 10,
            AnalyticsEventName::BlogPostViewed->value => 10,
            AnalyticsEventName::BlogReadingStarted->value => 30,
            AnalyticsEventName::BlogReadingCompleted->value => 30,
            AnalyticsEventName::BlogReadingAbandoned->value => 30,
            AnalyticsEventName::BlogScrollMeasured->value => 30,
            AnalyticsEventName::ToolOpened->value => 10,
            AnalyticsEventName::ToolCalculationStarted->value => 5,
            AnalyticsEventName::ToolCalculationCompleted->value => 5,
            AnalyticsEventName::ToolResultExported->value => 5,
            'audience.context_captured' => 300,
        ],
    ],

    'history_repair' => [
        // Deliberately conservative: only events that should be unique inside
        // the same collection window are eligible for historical removal.
        'event_windows' => [
            AnalyticsEventName::PageViewed->value => 10,
            AnalyticsEventName::BlogPostViewed->value => 10,
            AnalyticsEventName::BlogReadingStarted->value => 30,
            AnalyticsEventName::BlogReadingCompleted->value => 30,
            AnalyticsEventName::BlogReadingAbandoned->value => 30,
            AnalyticsEventName::BlogScrollMeasured->value => 30,
            AnalyticsEventName::ToolOpened->value => 10,
            AnalyticsEventName::ToolCalculationStarted->value => 5,
            AnalyticsEventName::ToolCalculationCompleted->value => 5,
            AnalyticsEventName::ToolResultExported->value => 5,
            AnalyticsEventName::AccountCreated->value => 60,
            AnalyticsEventName::SubscriptionCreated->value => 60,
            AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value => 5,
            AnalyticsEventName::BusinessDocumentValidatorBatchExported->value => 5,
        ],
        'identity_metadata_keys' => [
            'percentage', 'tool_slug', 'placement', 'position', 'destination',
            'method', 'file', 'calculation_id', 'result_id', 'batch_id',
            'subscription_id', 'account_id',
        ],
    ],

    'performance' => [
        'dashboard_cache_seconds' => (int) env('ANALYTICS_DASHBOARD_CACHE_SECONDS', 60),
        'prune_chunk_size' => (int) env('ANALYTICS_PRUNE_CHUNK_SIZE', 1000),
    ],

    'insights' => [
        'minimum_baseline' => (int) env('ANALYTICS_INSIGHTS_MINIMUM_BASELINE', 10),
        'change_threshold_percent' => (float) env('ANALYTICS_INSIGHTS_CHANGE_THRESHOLD', 20),
    ],

    'reports' => [
        'export_limit' => (int) env('ANALYTICS_REPORT_EXPORT_LIMIT', 10000),
    ],

    'dashboard' => [
        'page_view_events' => [AnalyticsEventName::PageViewed->value],
        'conversion_events' => [
            AnalyticsEventName::AccountCreated->value,
            AnalyticsEventName::SubscriptionCreated->value,
            AnalyticsEventName::ToolCalculationCompleted->value,
            AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value,
        ],
        'registration_events' => [AnalyticsEventName::AccountCreated->value],
        'subscription_events' => [AnalyticsEventName::SubscriptionCreated->value],
        'export_events' => [AnalyticsEventName::ToolResultExported->value, AnalyticsEventName::BusinessDocumentValidatorBatchExported->value],
        'revenue_metadata_keys' => ['revenue_cents', 'amount_cents', 'value_cents'],
    ],

    'acquisition' => [
        'sources' => [
            'google' => ['medium' => 'organic', 'hosts' => ['google.com', 'google.com.br']],
            'bing' => ['medium' => 'organic', 'hosts' => ['bing.com']],
            'yahoo' => ['medium' => 'organic', 'hosts' => ['search.yahoo.com', 'yahoo.com']],
            'duckduckgo' => ['medium' => 'organic', 'hosts' => ['duckduckgo.com']],
            'brave' => ['medium' => 'organic', 'hosts' => ['search.brave.com']],
            'ecosia' => ['medium' => 'organic', 'hosts' => ['ecosia.org']],
            'startpage' => ['medium' => 'organic', 'hosts' => ['startpage.com']],
            'qwant' => ['medium' => 'organic', 'hosts' => ['qwant.com']],
            'chatgpt' => ['medium' => 'ai', 'hosts' => ['chatgpt.com', 'chat.openai.com']],
            'gemini' => ['medium' => 'ai', 'hosts' => ['gemini.google.com']],
            'claude' => ['medium' => 'ai', 'hosts' => ['claude.ai']],
            'perplexity' => ['medium' => 'ai', 'hosts' => ['perplexity.ai']],
            'copilot' => ['medium' => 'ai', 'hosts' => ['copilot.microsoft.com']],
            'grok' => ['medium' => 'ai', 'hosts' => ['grok.com']],
            'deepseek' => ['medium' => 'ai', 'hosts' => ['chat.deepseek.com', 'deepseek.com']],
            'mistral' => ['medium' => 'ai', 'hosts' => ['chat.mistral.ai', 'mistral.ai']],
            'facebook' => ['medium' => 'social', 'hosts' => ['facebook.com', 'fb.com']],
            'instagram' => ['medium' => 'social', 'hosts' => ['instagram.com']],
            'linkedin' => ['medium' => 'social', 'hosts' => ['linkedin.com']],
            'pinterest' => ['medium' => 'social', 'hosts' => ['pinterest.com']],
            'tiktok' => ['medium' => 'social', 'hosts' => ['tiktok.com']],
            'threads' => ['medium' => 'social', 'hosts' => ['threads.net']],
            'x' => ['medium' => 'social', 'hosts' => ['x.com', 'twitter.com']],
            'reddit' => ['medium' => 'social', 'hosts' => ['reddit.com']],
            'discord' => ['medium' => 'social', 'hosts' => ['discord.com', 'discord.gg']],
            'whatsapp' => ['medium' => 'social', 'hosts' => ['whatsapp.com', 'wa.me']],
            'telegram' => ['medium' => 'social', 'hosts' => ['telegram.org', 't.me']],
        ],
        'funnel_steps' => [
            'landing' => ['label' => 'Acessou', 'events' => [AnalyticsEventName::PageViewed->value, AnalyticsEventName::BlogPostViewed->value]],
            'content' => ['label' => 'Abriu conteúdo', 'events' => [AnalyticsEventName::BlogPostViewed->value, AnalyticsEventName::BlogReadingStarted->value]],
            'tool' => ['label' => 'Abriu ferramenta', 'events' => [AnalyticsEventName::ToolOpened->value, AnalyticsEventName::BlogToolClicked->value]],
            'result' => ['label' => 'Concluiu resultado', 'events' => [AnalyticsEventName::ToolCalculationCompleted->value, AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value]],
            'registration' => ['label' => 'Criou conta', 'events' => [AnalyticsEventName::AccountCreated->value]],
            'subscription' => ['label' => 'Assinou Plus', 'events' => [AnalyticsEventName::SubscriptionCreated->value]],
        ],
    ],

    'funnels' => [
        'standard' => [
            'full_journey' => [
                'name' => 'Jornada completa até o Plus',
                'description' => 'Da primeira página acessada até a assinatura do Prazzu Plus.',
                'identity_type' => 'visitor',
                'steps' => [
                    ['name' => 'Visitou a plataforma', 'events' => [AnalyticsEventName::PageViewed->value, AnalyticsEventName::BlogPostViewed->value]],
                    ['name' => 'Consumiu conteúdo', 'events' => [AnalyticsEventName::BlogReadingStarted->value, AnalyticsEventName::BlogReadingCompleted->value, AnalyticsEventName::BlogPostViewed->value]],
                    ['name' => 'Abriu ferramenta', 'events' => [AnalyticsEventName::ToolOpened->value, AnalyticsEventName::BlogToolClicked->value]],
                    ['name' => 'Concluiu resultado', 'events' => [AnalyticsEventName::ToolCalculationCompleted->value, AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value]],
                    ['name' => 'Criou conta', 'events' => [AnalyticsEventName::AccountCreated->value]],
                    ['name' => 'Assinou Plus', 'events' => [AnalyticsEventName::SubscriptionCreated->value]],
                ],
            ],
            'tool_conversion' => [
                'name' => 'Conversão das ferramentas',
                'description' => 'Da abertura de uma ferramenta até a assinatura.',
                'identity_type' => 'visitor',
                'steps' => [
                    ['name' => 'Abriu ferramenta', 'events' => [AnalyticsEventName::ToolOpened->value, AnalyticsEventName::BlogToolClicked->value]],
                    ['name' => 'Iniciou cálculo', 'events' => [AnalyticsEventName::ToolCalculationStarted->value]],
                    ['name' => 'Concluiu cálculo', 'events' => [AnalyticsEventName::ToolCalculationCompleted->value, AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value]],
                    ['name' => 'Exportou resultado', 'events' => [AnalyticsEventName::ToolResultExported->value, AnalyticsEventName::BusinessDocumentValidatorBatchExported->value]],
                    ['name' => 'Assinou Plus', 'events' => [AnalyticsEventName::SubscriptionCreated->value]],
                ],
            ],
            'blog_to_tool' => [
                'name' => 'Blog para ferramenta',
                'description' => 'Capacidade do conteúdo de conduzir o visitante até uma ferramenta e um resultado.',
                'identity_type' => 'visitor',
                'steps' => [
                    ['name' => 'Abriu artigo', 'events' => [AnalyticsEventName::BlogPostViewed->value]],
                    ['name' => 'Iniciou leitura', 'events' => [AnalyticsEventName::BlogReadingStarted->value]],
                    ['name' => 'Clicou em ferramenta', 'events' => [AnalyticsEventName::BlogToolClicked->value]],
                    ['name' => 'Concluiu resultado', 'events' => [AnalyticsEventName::ToolCalculationCompleted->value, AnalyticsEventName::BusinessDocumentValidatorBatchProcessed->value]],
                ],
            ],
        ],
    ],

];
