<?php

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
        'page_view_events' => ['page.viewed', 'blog_post_view'],
        'conversion_events' => [
            'account.created',
            'subscription.started',
            'tool.calculation_completed',
            'business_document_validator.batch_processed',
        ],
        'registration_events' => ['account.created', 'user.registered'],
        'subscription_events' => ['subscription.started', 'subscription.created', 'plus.subscribed'],
        'export_events' => ['result.exported', 'tool.exported', 'business_document_validator.batch_exported'],
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
            'landing' => ['label' => 'Acessou', 'events' => ['page.viewed', 'blog_post_view']],
            'content' => ['label' => 'Abriu conteúdo', 'events' => ['blog_post_view', 'blog.reading.started']],
            'tool' => ['label' => 'Abriu ferramenta', 'events' => ['tool.opened', 'blog_tool_click']],
            'result' => ['label' => 'Concluiu resultado', 'events' => ['tool.calculation_completed', 'business_document_validator.batch_processed']],
            'registration' => ['label' => 'Criou conta', 'events' => ['account.created', 'user.registered']],
            'subscription' => ['label' => 'Assinou Plus', 'events' => ['subscription.started', 'subscription.created', 'plus.subscribed']],
        ],
    ],


    'funnels' => [
        'standard' => [
            'full_journey' => [
                'name' => 'Jornada completa até o Plus',
                'description' => 'Da primeira página acessada até a assinatura do Prazzu Plus.',
                'identity_type' => 'visitor',
                'steps' => [
                    ['name' => 'Visitou a plataforma', 'events' => ['page.viewed', 'blog_post_view']],
                    ['name' => 'Consumiu conteúdo', 'events' => ['blog.reading.started', 'blog.reading.completed', 'blog_post_view']],
                    ['name' => 'Abriu ferramenta', 'events' => ['tool.opened', 'blog_tool_click']],
                    ['name' => 'Concluiu resultado', 'events' => ['tool.calculation_completed', 'business_document_validator.batch_processed']],
                    ['name' => 'Criou conta', 'events' => ['account.created', 'user.registered']],
                    ['name' => 'Assinou Plus', 'events' => ['subscription.started', 'subscription.created', 'plus.subscribed']],
                ],
            ],
            'tool_conversion' => [
                'name' => 'Conversão das ferramentas',
                'description' => 'Da abertura de uma ferramenta até a assinatura.',
                'identity_type' => 'visitor',
                'steps' => [
                    ['name' => 'Abriu ferramenta', 'events' => ['tool.opened', 'blog_tool_click']],
                    ['name' => 'Iniciou cálculo', 'events' => ['tool.calculation_started']],
                    ['name' => 'Concluiu cálculo', 'events' => ['tool.calculation_completed', 'business_document_validator.batch_processed']],
                    ['name' => 'Exportou resultado', 'events' => ['result.exported', 'tool.exported', 'business_document_validator.batch_exported']],
                    ['name' => 'Assinou Plus', 'events' => ['subscription.started', 'subscription.created', 'plus.subscribed']],
                ],
            ],
            'blog_to_tool' => [
                'name' => 'Blog para ferramenta',
                'description' => 'Capacidade do conteúdo de conduzir o visitante até uma ferramenta e um resultado.',
                'identity_type' => 'visitor',
                'steps' => [
                    ['name' => 'Abriu artigo', 'events' => ['blog_post_view']],
                    ['name' => 'Iniciou leitura', 'events' => ['blog.reading.started']],
                    ['name' => 'Clicou em ferramenta', 'events' => ['blog_tool_click']],
                    ['name' => 'Concluiu resultado', 'events' => ['tool.calculation_completed', 'business_document_validator.batch_processed']],
                ],
            ],
        ],
    ],

];
