<?php

$accounting = [
    'page_title' => 'Prazzu Tools — Ferramentas para contabilidade',
    'meta_description' => 'Ferramentas contábeis gratuitas e profissionais para facilitar a rotina de contadores e empresas.',
    'tools_section_title' => 'Ferramentas mais recentes',

    'hero' => [
        'title_before' => 'Ferramentas que facilitam',
        'title_line' => 'o seu',
        'title_highlight' => 'dia a dia',
        'description' => 'Tudo que você precisa em um só lugar. Agilidade, precisão e praticidade para o seu dia a dia contábil.',
        'search_placeholder' => 'Buscar ferramentas...',
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
    'tools_section_title' => 'Ferramentas de RH',

    'hero' => [
        'title_before' => 'Ferramentas que apoiam',
        'title_line' => 'a sua',
        'title_highlight' => 'gestão de pessoas',
        'description' => 'Indicadores e utilitários para apoiar decisões de Recursos Humanos em uma experiência dedicada, sobre a mesma plataforma Prazzu.',
        'search_placeholder' => 'Buscar ferramentas de RH...',
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
    'tools_section_title' => 'Ferramentas em destaque',

    'hero' => [
        'title_before' => 'Ferramentas que facilitam',
        'title_line' => 'o seu',
        'title_highlight' => 'dia a dia',
        'description' => 'Encontre ferramentas práticas para diferentes áreas de negócio em uma única plataforma.',
        'search_placeholder' => 'Buscar ferramentas...',
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
