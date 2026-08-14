<?php

$accounting = [
    'page_title' => 'Prazzu Tools — Ferramentas para contabilidade',
    'meta_description' => 'Ferramentas contábeis gratuitas e profissionais para facilitar a rotina de contadores e empresas.',
    'tools_section_title' => 'Novas ferramentas para resolver sua rotina',

    'hero' => [
        'title_before' => 'O que você precisa',
        'title_line' => 'resolver',
        'title_highlight' => 'hoje?',
        'description' => 'Descreva sua necessidade e encontre a ferramenta certa para calcular, validar, simular ou gerar o que você precisa.',
        'search_label' => 'Descreva o que você precisa resolver',
        'search_placeholder' => 'Ex.: calcular rescisão, validar CNPJ, comparar impostos...',
        'problem_shortcuts' => [
            ['label' => 'Calcular rescisão', 'query' => 'rescisão'],
            ['label' => 'Validar CNPJ ou CPF', 'query' => 'CNPJ CPF'],
            ['label' => 'Calcular impostos', 'query' => 'impostos'],
            ['label' => 'Custo de funcionário', 'query' => 'custo funcionário'],
        ],
        'benefits' => [
            ['label' => '100% Gratuito em muitos', 'icon' => 'bi-check-circle-fill'],
            ['label' => 'Sem cadastro para usar', 'icon' => 'bi-check-circle-fill'],
            ['label' => 'Seguro e confiável', 'icon' => 'bi-check-circle-fill'],
            ['label' => 'Sempre atualizado', 'icon' => 'bi-check-circle-fill'],
        ],
    ],

    'cta' => [
        'title' => 'Não encontrou o que precisa?',
        'description' => 'Temos novas ferramentas sendo adicionadas toda semana!',
        'label' => 'Ver todas as ferramentas',
        'url' => '/ferramentas',
    ],
];

$hr = [
    'page_title' => 'Prazzu Tools — Ferramentas para Recursos Humanos',
    'meta_description' => 'Ferramentas práticas para apoiar indicadores, rotinas e decisões de Recursos Humanos.',
    'tools_section_title' => 'Ferramentas para resolver sua rotina de RH',

    'hero' => [
        'title_before' => 'O que você precisa',
        'title_line' => 'resolver em',
        'title_highlight' => 'RH hoje?',
        'description' => 'Descreva sua necessidade de Recursos Humanos e encontre a ferramenta certa para chegar à resposta com rapidez.',
        'search_label' => 'Descreva o que você precisa resolver em RH',
        'search_placeholder' => 'Ex.: calcular turnover...',
        'problem_shortcuts' => [
            ['label' => 'Calcular turnover', 'query' => 'turnover'],
        ],
        'benefits' => $accounting['hero']['benefits'],
    ],

    'cta' => [
        'title' => 'Explore as ferramentas de RH',
        'description' => 'A vertical de Recursos Humanos começa pequena para validar a arquitetura multi-nicho e poderá crescer sem duplicar a plataforma.',
        'label' => 'Ver ferramentas de RH',
        'url' => '/ferramentas',
    ],
];

$global = [
    'page_title' => 'Prazzu Tools — Ferramentas para o seu dia a dia',
    'meta_description' => 'Ferramentas práticas para diferentes áreas de negócio em uma única plataforma.',
    'tools_section_title' => 'Ferramentas para resolver agora',

    'hero' => [
        'title_before' => 'O que você precisa',
        'title_line' => 'resolver',
        'title_highlight' => 'hoje?',
        'description' => 'Descreva sua necessidade e encontre a ferramenta certa entre as áreas disponíveis no Prazzu.',
        'search_label' => 'Descreva o que você precisa resolver',
        'search_placeholder' => 'Ex.: calcular, validar, simular ou gerar...',
        'problem_shortcuts' => [],
        'benefits' => $accounting['hero']['benefits'],
    ],

    'cta' => $accounting['cta'],
];

return [
    // Compatibilidade: o contrato histórico permanece apontando para a experiência
    // atual de Contabilidade enquanto ela continuar sendo a vertical padrão.
    ...$accounting,

    // Fallback da plataforma quando VerticalContext = null.
    'global' => $global,

    // Conteúdo específico é configuração de negócio, não uma Home paralela.
    'verticals' => [
        'contabilidade' => $accounting,
        'rh' => $hr,
    ],
];
