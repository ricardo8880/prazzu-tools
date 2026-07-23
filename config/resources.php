<?php

return [
    'principles' => [
        'practical' => 'Cada recurso deve ajudar o profissional a executar, conferir ou decidir algo concreto.',
        'complementary' => 'Guias e modelos complementam as ferramentas; não substituem cálculos, validações ou decisões profissionais.',
        'focused' => 'A biblioteca cresce por relevância, não por volume. Conteúdo superficial não deve ser publicado.',
        'reviewed' => 'Todo recurso informa seu estado editorial e a data da última revisão antes da publicação.',
    ],

    'sections' => [
        'guias' => [
            'title' => 'Guias práticos',
            'singular' => 'Guia',
            'eyebrow' => 'Recursos para aplicar',
            'description' => 'Conteúdos aprofundados para executar tarefas, interpretar resultados e evitar erros na rotina contábil.',
            'icon' => 'bi-journal-check',
            'route' => 'resources.show',
            'empty_message' => 'Os primeiros guias estão sendo preparados com revisão técnica e aplicação prática.',
        ],
        'modelos' => [
            'title' => 'Modelos profissionais',
            'singular' => 'Modelo',
            'eyebrow' => 'Recursos para usar',
            'description' => 'Materiais reutilizáveis para levantar dados, conferir informações e documentar premissas com mais segurança.',
            'icon' => 'bi-file-earmark-check',
            'route' => 'resources.show',
            'empty_message' => 'Os primeiros modelos estão sendo preparados para uso direto e responsável.',
        ],
    ],

    'items' => [
        [
            'type' => 'guias',
            'slug' => 'precificacao-de-honorarios-contabeis',
            'title' => 'Guia profissional para precificação de honorários contábeis',
            'summary' => 'Método prático para levantar esforço, complexidade, risco e escopo antes de definir ou reajustar honorários.',
            'category' => 'Honorários contábeis',
            'icon' => 'bi-calculator',
            'status' => 'published',
            'status_label' => 'Publicado',
            'reading_time' => '18 min de leitura',
            'reviewed_at' => '23/07/2026',
            'seo' => [
                'title' => 'Como precificar honorários contábeis com método',
                'description' => 'Guia prático para definir e revisar honorários considerando escopo, volume, complexidade, risco, capacidade e margem.',
                'schema_type' => 'Article',
            ],
            'related_slugs' => ['levantamento-para-precificacao-de-honorarios'],
            'route' => 'resources.item',
            'view' => 'pages.resources.guides.accounting-fees-pricing',
            'tool' => [
                'name' => 'Calculadora de Honorários Contábeis',
                'route' => 'tools.calculadora-de-honorarios-contabeis.index',
            ],
        ],
        [
            'type' => 'modelos',
            'slug' => 'levantamento-para-precificacao-de-honorarios',
            'title' => 'Modelo de levantamento para precificação de honorários',
            'summary' => 'Estrutura para reunir dados da empresa, volume operacional, serviços contratados e fatores de complexidade.',
            'category' => 'Honorários contábeis',
            'icon' => 'bi-clipboard-data',
            'status' => 'published',
            'status_label' => 'Publicado',
            'format' => 'Planilha e checklist',
            'reviewed_at' => '23/07/2026',
            'seo' => [
                'title' => 'Modelo para levantamento de honorários contábeis',
                'description' => 'Baixe uma planilha profissional para levantar volumes, escopo, complexidade e premissas antes de precificar honorários.',
                'schema_type' => 'DigitalDocument',
            ],
            'related_slugs' => ['precificacao-de-honorarios-contabeis'],
            'route' => 'resources.item',
            'view' => 'pages.resources.models.accounting-fees-survey',
            'download' => 'downloads/resources/modelo-levantamento-honorarios-contabeis.xlsx',
            'tool' => [
                'name' => 'Calculadora de Honorários Contábeis',
                'route' => 'tools.calculadora-de-honorarios-contabeis.index',
            ],
        ],
    ],
];
