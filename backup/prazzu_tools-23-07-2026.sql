-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23-Jul-2026 às 14:18
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `prazzu_tools`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `accounting_fee_adjustments`
--

CREATE TABLE `accounting_fee_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_key` char(36) DEFAULT NULL,
  `scenario_label` varchar(150) NOT NULL,
  `index_type` varchar(20) NOT NULL,
  `reference_period` varchar(7) NOT NULL,
  `percentage` decimal(8,4) NOT NULL,
  `current_value_cents` bigint(20) UNSIGNED NOT NULL,
  `difference_cents` bigint(20) NOT NULL,
  `adjusted_value_cents` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `accounting_fee_calculations`
--

CREATE TABLE `accounting_fee_calculations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_key` char(36) DEFAULT NULL,
  `input` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`input`)),
  `result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`result`)),
  `is_favorite` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `acquisition_contexts`
--

CREATE TABLE `acquisition_contexts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `campaign_identifier` varchar(255) DEFAULT NULL,
  `campaign_source` varchar(120) DEFAULT NULL,
  `campaign_medium` varchar(120) DEFAULT NULL,
  `content_identifier` varchar(255) DEFAULT NULL,
  `video_identifier` varchar(255) DEFAULT NULL,
  `banner_identifier` varchar(255) DEFAULT NULL,
  `cta_identifier` varchar(255) DEFAULT NULL,
  `monthly_investment_cents` bigint(20) UNSIGNED DEFAULT NULL,
  `investment_currency` char(3) NOT NULL DEFAULT 'BRL',
  `status` varchar(20) NOT NULL DEFAULT 'inactive',
  `hero_title_before` varchar(255) DEFAULT NULL,
  `hero_title_line` varchar(255) DEFAULT NULL,
  `hero_title_highlight` varchar(255) DEFAULT NULL,
  `hero_description` text DEFAULT NULL,
  `hero_search_placeholder` varchar(255) DEFAULT NULL,
  `tools_section_title` varchar(255) DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_description` text DEFAULT NULL,
  `cta_label` varchar(255) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `cta_tool_slug` varchar(255) DEFAULT NULL,
  `contextual_message` varchar(255) DEFAULT NULL,
  `contextual_continue_label` varchar(80) DEFAULT NULL,
  `contextual_continue_url` varchar(2048) DEFAULT NULL,
  `contextual_continue_tool_slug` varchar(255) DEFAULT NULL,
  `primary_tool_slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `acquisition_context_articles`
--

CREATE TABLE `acquisition_context_articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acquisition_context_id` bigint(20) UNSIGNED NOT NULL,
  `article_slug` varchar(255) NOT NULL,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `acquisition_context_tools`
--

CREATE TABLE `acquisition_context_tools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acquisition_context_id` bigint(20) UNSIGNED NOT NULL,
  `tool_slug` varchar(255) NOT NULL,
  `placement` varchar(30) NOT NULL,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_funnels`
--

CREATE TABLE `analytics_funnels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `identity_type` varchar(20) NOT NULL DEFAULT 'visitor',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_funnel_steps`
--

CREATE TABLE `analytics_funnel_steps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `funnel_id` bigint(20) UNSIGNED NOT NULL,
  `position` smallint(5) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `event_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`event_names`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_insights`
--

CREATE TABLE `analytics_insights` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fingerprint` varchar(64) NOT NULL,
  `type` varchar(30) NOT NULL,
  `severity` varchar(20) NOT NULL,
  `title` varchar(180) NOT NULL,
  `message` text NOT NULL,
  `recommendation` text DEFAULT NULL,
  `subject_type` varchar(50) DEFAULT NULL,
  `subject_slug` varchar(180) DEFAULT NULL,
  `metric_name` varchar(80) DEFAULT NULL,
  `current_value` decimal(18,4) DEFAULT NULL,
  `previous_value` decimal(18,4) DEFAULT NULL,
  `change_percent` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_report_schedules`
--

CREATE TABLE `analytics_report_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `frequency` varchar(20) NOT NULL,
  `format` varchar(20) NOT NULL,
  `filters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`filters`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `next_run_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_run_at` timestamp NULL DEFAULT NULL,
  `last_file_path` varchar(500) DEFAULT NULL,
  `last_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_seo_metric_snapshots`
--

CREATE TABLE `analytics_seo_metric_snapshots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blog_post_id` bigint(20) UNSIGNED NOT NULL,
  `metric_date` date NOT NULL,
  `source` varchar(40) NOT NULL DEFAULT 'google_search_console',
  `search_type` varchar(30) NOT NULL DEFAULT 'web',
  `device` varchar(30) NOT NULL DEFAULT 'all',
  `country_code` varchar(2) DEFAULT NULL,
  `clicks` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `impressions` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `average_position` decimal(8,2) DEFAULT NULL,
  `discover_clicks` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `discover_impressions` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `news_clicks` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `news_impressions` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `rich_result_clicks` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `rich_result_impressions` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_sessions`
--

CREATE TABLE `analytics_sessions` (
  `id` char(36) NOT NULL,
  `visitor_id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `started_at` datetime NOT NULL,
  `last_activity_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `landing_url` varchar(2048) DEFAULT NULL,
  `landing_path` varchar(2048) DEFAULT NULL,
  `referrer` varchar(2048) DEFAULT NULL,
  `source` varchar(120) DEFAULT NULL,
  `medium` varchar(120) DEFAULT NULL,
  `campaign` varchar(255) DEFAULT NULL,
  `acquisition_context_id` bigint(20) UNSIGNED DEFAULT NULL,
  `acquisition_keyword` varchar(255) DEFAULT NULL,
  `acquisition_campaign_identifier` varchar(255) DEFAULT NULL,
  `acquisition_primary_tool_slug` varchar(255) DEFAULT NULL,
  `utm_source` varchar(120) DEFAULT NULL,
  `utm_medium` varchar(120) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `utm_content` varchar(255) DEFAULT NULL,
  `device_type` varchar(30) DEFAULT NULL,
  `browser` varchar(80) DEFAULT NULL,
  `operating_system` varchar(80) DEFAULT NULL,
  `language` varchar(20) DEFAULT NULL,
  `timezone` varchar(80) DEFAULT NULL,
  `screen_resolution` varchar(20) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `region` varchar(120) DEFAULT NULL,
  `city` varchar(160) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `analytics_sessions`
--

INSERT INTO `analytics_sessions` (`id`, `visitor_id`, `user_id`, `started_at`, `last_activity_at`, `ended_at`, `landing_url`, `landing_path`, `referrer`, `source`, `medium`, `campaign`, `acquisition_context_id`, `acquisition_keyword`, `acquisition_campaign_identifier`, `acquisition_primary_tool_slug`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `device_type`, `browser`, `operating_system`, `language`, `timezone`, `screen_resolution`, `country_code`, `region`, `city`, `properties`, `created_at`, `updated_at`) VALUES
('5b58b327-6abe-4113-a5b8-a08132d89b34', '17b71e79-b7b2-440d-abae-83a76642fc50', NULL, '2026-07-23 11:47:08', '2026-07-23 12:17:29', NULL, 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', 'America/Sao_Paulo', '1920x1080', NULL, NULL, NULL, NULL, '2026-07-23 14:47:08', '2026-07-23 15:17:29');

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_tool_presences`
--

CREATE TABLE `analytics_tool_presences` (
  `id` char(36) NOT NULL,
  `tool_slug` varchar(120) NOT NULL,
  `visitor_id` char(36) DEFAULT NULL,
  `analytics_session_id` char(36) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `path` varchar(500) DEFAULT NULL,
  `source` varchar(120) DEFAULT NULL,
  `country_code` varchar(8) DEFAULT NULL,
  `region` varchar(120) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `last_seen_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `analytics_visitors`
--

CREATE TABLE `analytics_visitors` (
  `id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_seen_at` datetime NOT NULL,
  `last_seen_at` datetime NOT NULL,
  `first_source` varchar(120) DEFAULT NULL,
  `first_medium` varchar(120) DEFAULT NULL,
  `first_campaign` varchar(255) DEFAULT NULL,
  `first_utm` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`first_utm`)),
  `last_source` varchar(120) DEFAULT NULL,
  `last_medium` varchar(120) DEFAULT NULL,
  `last_campaign` varchar(255) DEFAULT NULL,
  `last_utm` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`last_utm`)),
  `first_referrer` varchar(2048) DEFAULT NULL,
  `last_referrer` varchar(2048) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `analytics_visitors`
--

INSERT INTO `analytics_visitors` (`id`, `user_id`, `first_seen_at`, `last_seen_at`, `first_source`, `first_medium`, `first_campaign`, `first_utm`, `last_source`, `last_medium`, `last_campaign`, `last_utm`, `first_referrer`, `last_referrer`, `properties`, `created_at`, `updated_at`) VALUES
('17b71e79-b7b2-440d-abae-83a76642fc50', NULL, '2026-07-23 11:47:08', '2026-07-23 12:17:29', 'direct', 'none', NULL, '{\"source\":null,\"medium\":null,\"campaign\":null,\"term\":null,\"content\":null}', 'direct', 'none', NULL, '{\"source\":null,\"medium\":null,\"campaign\":null,\"term\":null,\"content\":null}', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', NULL, '2026-07-23 14:47:08', '2026-07-23 15:17:29');

-- --------------------------------------------------------

--
-- Estrutura da tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` char(36) NOT NULL,
  `actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `auditable_type` varchar(180) DEFAULT NULL,
  `auditable_id` varchar(180) DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Gestão Contábil', 'gestao-contabil', 'Precificação, rentabilidade e decisões de gestão para escritórios e empresas.', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(2, 'Cadastros e Validações', 'cadastros-e-validacoes', 'Validação de documentos e qualidade de dados cadastrais.', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(3, 'Fiscal e Tributário', 'fiscal-e-tributario', 'Guias, cálculos, documentos fiscais e planejamento tributário.', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(4, 'Trabalhista', 'trabalhista', 'Cálculos e orientações para rotinas trabalhistas.', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `content` longtext NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `cover_image_path` varchar(255) DEFAULT NULL,
  `cover_image_alt` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `content_updated_at` timestamp NULL DEFAULT NULL,
  `primary_keyword` varchar(255) DEFAULT NULL,
  `related_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_keywords`)),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(320) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `social_image_path` varchar(255) DEFAULT NULL,
  `should_index` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Como calcular honorários contábeis sem perder rentabilidade', 'como-calcular-honorarios-contabeis', 'Aprenda a formar honorários contábeis considerando regime tributário, volume de trabalho, complexidade, risco e margem desejada.', '<p>Aprenda a formar honorários contábeis considerando regime tributário, volume de trabalho, complexidade, risco e margem desejada.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Por que copiar a mensalidade do concorrente é arriscado</h2><p>O preço de um serviço contábil precisa remunerar horas técnicas, estrutura, tecnologia, responsabilidade profissional e risco. Quando o escritório replica valores de mercado sem conhecer sua própria operação, pode conquistar clientes que aumentam o faturamento e, ao mesmo tempo, reduzem a margem. A precificação sustentável começa pela realidade do escritório e depois é confrontada com o posicionamento desejado.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Mapeie o custo mensal de atendimento</h2><p>Liste o tempo previsto para escrituração, fiscal, folha, obrigações acessórias, atendimento, revisão e gestão. Some custos diretos, rateio de equipe, sistemas, certificados, armazenamento, estrutura e tributos do próprio escritório. Transforme esse total em custo por hora produtiva e considere que nem todas as horas contratadas são faturáveis.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Ajuste pelo perfil e pela complexidade do cliente</h2><p>Regime tributário, quantidade de funcionários, número de documentos, diversidade de operações, filiais, comércio exterior, retenções, substituição tributária e qualidade das informações mudam o esforço necessário. Clientes com atraso recorrente, documentos desorganizados ou elevado risco fiscal exigem uma reserva maior de capacidade.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Inclua margem, risco e escopo</h2><p>Depois de estimar o custo, aplique a margem necessária para financiar crescimento, treinamento, suporte e imprevistos. Registre claramente o que está incluído, limites de movimentação, serviços extraordinários e critérios de reajuste. Um preço bem explicado tende a ser melhor aceito do que um número apresentado sem contexto.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Revise periodicamente</h2><p>Reavalie honorários quando houver mudança de regime, aumento de faturamento, contratação de funcionários, abertura de filial ou crescimento do volume documental. A revisão periódica evita que o contrato permaneça defasado enquanto o trabalho cresce silenciosamente.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/calculadora-de-honorarios-contabeis\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Existe uma tabela única de honorários contábeis?</h3><p>Não existe um valor nacional que represente todos os escritórios e clientes. Referências regionais podem ajudar, mas o preço final precisa refletir custo, escopo, risco e posicionamento.</p><h3>Devo cobrar por faturamento ou por volume?</h3><p>Os dois indicadores podem ser usados, mas nenhum deve ser isolado. O ideal é combinar porte, volume operacional, complexidade e esforço estimado.</p><h3>Quando reajustar o contrato?</h3><p>Além do reajuste periódico previsto em contrato, revise quando houver alteração relevante no perfil operacional do cliente.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 1, 'Gestão Contábil', 'blog/covers/como-calcular-honorarios-contabeis.png', 'Ilustração do guia: Como calcular honorários contábeis sem perder rentabilidade', 'published', 1, '2026-07-13 12:00:00', '2026-07-13 12:00:00', 'como calcular honorários contábeis', '[\"precificação contábil\", \"tabela de honorários contábeis\", \"valor mensalidade contabilidade\", \"custos do escritório contábil\"]', 'Como calcular honorários contábeis sem perder rentabilidade', 'Como calcular honorários contábeis com critérios objetivos, margem sustentável e ajustes por porte, regime e complexidade do cliente.', NULL, 'blog/social/como-calcular-honorarios-contabeis.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(2, NULL, 'Como validar CNPJ, CPF e inscrição estadual antes do cadastro', 'como-validar-cnpj-cpf-inscricao-estadual', 'Veja como identificar erros de digitação, dígitos verificadores inválidos e inconsistências cadastrais antes de importar ou registrar dados.', '<p>Veja como identificar erros de digitação, dígitos verificadores inválidos e inconsistências cadastrais antes de importar ou registrar dados.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Validação sintática não é consulta cadastral</h2><p>A validação do número confirma formato e dígitos verificadores. Ela elimina muitos erros de digitação, mas não prova que o cadastro está ativo, pertence à pessoa informada ou está regular perante órgãos públicos. Para decisões de risco, combine a validação matemática com fontes oficiais e documentos comprobatórios.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Por que validar antes de importar</h2><p>Um documento incorreto pode contaminar cadastros, notas, obrigações acessórias, relatórios e integrações. Corrigir o erro na entrada custa menos do que localizar o mesmo problema depois que ele se espalhou por diferentes sistemas.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Como tratar arquivos em lote</h2><p>Ao validar planilhas ou arquivos tabulares, preserve a linha original, normalize pontuação apenas para o cálculo e produza um relatório com valor informado, valor normalizado e motivo da rejeição. Não altere automaticamente números duvidosos; encaminhe-os para revisão.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Cuidados com inscrição estadual</h2><p>As regras da inscrição estadual variam por unidade federativa e podem ter tamanhos e algoritmos diferentes. Por isso, a UF é parte essencial da validação. Um número matematicamente válido para um estado não deve ser aceito como se fosse de outro.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Proteção de dados</h2><p>CPF e outros identificadores pessoais exigem finalidade, controle de acesso e retenção adequada. Evite incluir documentos completos em logs, URLs, capturas de tela ou arquivos de teste sem necessidade.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/validador-de-cnpj\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Um CNPJ válido está necessariamente ativo?</h3><p>Não. A validação dos dígitos não substitui a consulta da situação cadastral em fonte oficial.</p><h3>É seguro remover pontos e traços?</h3><p>Sim para normalização técnica, desde que o valor original seja preservado quando necessário para auditoria.</p><h3>Toda inscrição estadual tem a mesma regra?</h3><p>Não. Cada UF pode adotar formato e algoritmo próprios.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 2, 'Cadastros e Validações', 'blog/covers/como-validar-cnpj-cpf-inscricao-estadual.png', 'Ilustração do guia: Como validar CNPJ, CPF e inscrição estadual antes do cadastro', 'published', 1, '2026-07-14 12:00:00', '2026-07-14 12:00:00', 'validar CNPJ CPF inscrição estadual', '[\"validador de CNPJ\", \"consulta CPF\", \"validação inscrição estadual\", \"documentos cadastrais\"]', 'Como validar CNPJ, CPF e inscrição estadual antes do cadastro', 'Valide CNPJ, CPF e inscrição estadual antes do cadastro e reduza erros em documentos fiscais, integrações e rotinas contábeis.', NULL, 'blog/social/como-validar-cnpj-cpf-inscricao-estadual.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(3, NULL, 'DARF e GPS: como conferir código, vencimento, juros e multa', 'como-gerar-darf-gps-codigo-vencimento-acrescimos', 'Entenda os dados que precisam ser revisados antes de emitir DARF ou GPS e como documentar a memória de cálculo dos acréscimos.', '<p>Entenda os dados que precisam ser revisados antes de emitir DARF ou GPS e como documentar a memória de cálculo dos acréscimos.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Comece pela obrigação correta</h2><p>A emissão da guia depende da natureza do débito, período de apuração, contribuinte e código de receita. Um código incorreto pode direcionar o pagamento para obrigação diferente e gerar pendência mesmo quando o valor foi recolhido.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Confirme período e vencimento</h2><p>Competência, período de apuração e data de vencimento não são campos equivalentes. Feriados e regras específicas também podem alterar a data efetiva. Registre a base normativa e a data de referência usada na geração.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Acréscimos por atraso</h2><p>Quando houver atraso, a memória deve separar principal, multa e juros. O cálculo precisa considerar a regra aplicável ao tributo e a data prevista de pagamento. Evite lançar apenas o total: a decomposição facilita revisão e conciliação.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Dados do contribuinte</h2><p>Revise CPF ou CNPJ, nome, identificação do débito, código, referência e valor. Em rotinas com muitos clientes, use dupla conferência ou validações automatizadas antes da emissão definitiva.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Guarde evidências</h2><p>Salve a memória de cálculo, parâmetros, data de geração e responsável pela revisão. Isso reduz retrabalho quando o cliente pergunta como o valor foi formado ou quando ocorre divergência na baixa do pagamento.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/gerador-darf-gps\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>DARF e GPS são a mesma guia?</h3><p>Não. São documentos distintos e atendem obrigações específicas.</p><h3>Posso usar qualquer código de receita parecido?</h3><p>Não. O código precisa corresponder exatamente à obrigação e ao enquadramento do débito.</p><h3>Como evitar divergência de juros?</h3><p>Use a data real de pagamento, a regra aplicável e preserve a memória de cálculo para conferência.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/como-gerar-darf-gps-codigo-vencimento-acrescimos.png', 'Ilustração do guia: DARF e GPS: como conferir código, vencimento, juros e multa', 'published', 1, '2026-07-15 12:00:00', '2026-07-15 12:00:00', 'como gerar DARF e GPS', '[\"código DARF\", \"vencimento DARF\", \"juros e multa DARF\", \"guia GPS\"]', 'DARF e GPS: como conferir código, vencimento, juros e multa', 'Confira código, período, vencimento, multa e juros antes de gerar DARF ou GPS e mantenha uma memória de cálculo auditável.', NULL, 'blog/social/como-gerar-darf-gps-codigo-vencimento-acrescimos.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(4, NULL, 'Como converter XML de NF-e e NFC-e em planilha com segurança', 'como-converter-xml-nfe-nfce-planilha', 'Aprenda a extrair produtos, NCM, CFOP, impostos e totais de arquivos XML sem perder rastreabilidade nem misturar documentos inválidos.', '<p>Aprenda a extrair produtos, NCM, CFOP, impostos e totais de arquivos XML sem perder rastreabilidade nem misturar documentos inválidos.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>O XML é a fonte estruturada</h2><p>O PDF auxiliar facilita a leitura humana, mas o XML contém os campos estruturados usados em validações e integrações. Para análise em lote, prefira o XML autorizado e preserve o arquivo original junto ao resultado.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Defina a granularidade da planilha</h2><p>Uma nota pode gerar uma linha de cabeçalho e várias linhas de itens. Antes de exportar, decida se a análise será por documento, produto ou tributo. Misturar granularidades em uma única tabela costuma causar duplicidade de totais.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Campos importantes</h2><p>Chave de acesso, emitente, destinatário, data, modelo, série, número, CFOP, NCM, CST ou CSOSN, valores de produtos, descontos, frete e tributos devem ser tratados com tipos adequados. Identificadores longos devem ser exportados como texto para não perder zeros ou precisão.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Validações de consistência</h2><p>Compare soma dos itens com totais, identifique XML duplicado, documento cancelado, campos ausentes e diferenças de arredondamento. Alertas não devem apagar dados; devem acompanhar a linha para revisão.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Segurança e retenção</h2><p>Arquivos fiscais podem conter dados comerciais e pessoais. Restrinja acesso, evite compartilhamentos públicos e defina política de retenção para originais e exportações.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/conversor-fiscal-xml\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Posso converter o DANFE em vez do XML?</h3><p>O DANFE não substitui o XML e pode omitir estrutura necessária para uma extração confiável.</p><h3>Por que a chave aparece em notação científica no Excel?</h3><p>Porque planilhas podem interpretar identificadores longos como números. Exporte a chave como texto.</p><h3>Como tratar XML duplicado?</h3><p>Use a chave de acesso como referência e sinalize duplicidades sem descartar silenciosamente os arquivos.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/como-converter-xml-nfe-nfce-planilha.png', 'Ilustração do guia: Como converter XML de NF-e e NFC-e em planilha com segurança', 'published', 0, '2026-07-16 12:00:00', '2026-07-16 12:00:00', 'converter XML NF-e para planilha', '[\"XML para Excel\", \"extrair dados XML NF-e\", \"converter NFC-e\", \"planilha de notas fiscais\"]', 'Como converter XML de NF-e e NFC-e em planilha com segurança', 'Converta XML de NF-e e NFC-e em planilha preservando produtos, NCM, CFOP, impostos, totais e alertas de consistência.', NULL, 'blog/social/como-converter-xml-nfe-nfce-planilha.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(5, NULL, 'Como calcular rescisão trabalhista passo a passo', 'como-calcular-rescisao-trabalhista', 'Confira as principais verbas da rescisão, os dados de entrada e os pontos que mudam conforme motivo do desligamento e datas do contrato.', '<p>Confira as principais verbas da rescisão, os dados de entrada e os pontos que mudam conforme motivo do desligamento e datas do contrato.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>O motivo do desligamento muda o cálculo</h2><p>Pedido de demissão, dispensa sem justa causa, justa causa, acordo e término de contrato produzem direitos diferentes. A primeira etapa é classificar corretamente o desligamento e registrar datas de admissão, comunicação e término.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Saldo de salário</h2><p>Calcule os dias trabalhados no mês da saída com base na regra aplicável ao salário mensal e considere adicionais habituais quando integrarem a remuneração. Faltas, adiantamentos e outras ocorrências precisam de documentação.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Férias e décimo terceiro</h2><p>Verifique períodos vencidos, proporcionais, faltas que afetam o direito, terço constitucional e avos do décimo terceiro. O tratamento de médias variáveis exige atenção às verbas e ao período de apuração.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Aviso-prévio e FGTS</h2><p>Defina se o aviso é trabalhado ou indenizado e considere a projeção quando aplicável. Depósitos de FGTS, multa rescisória e movimentação da conta dependem do tipo de desligamento.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Conferência final</h2><p>Separe proventos e descontos, registre as bases de INSS e IRRF e confronte o resultado com documentos do contrato. Uma memória detalhada facilita a revisão antes do pagamento e da transmissão dos eventos.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/calculadora-de-rescisao\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Toda rescisão tem multa de 40% do FGTS?</h3><p>Não. A incidência depende do motivo do desligamento.</p><h3>Aviso indenizado projeta o contrato?</h3><p>Em situações aplicáveis, a projeção repercute em datas e verbas; o caso deve ser conferido conforme a regra vigente.</p><h3>Férias vencidas e proporcionais são iguais?</h3><p>Não. Elas se referem a períodos diferentes e podem ter tratamentos distintos.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 4, 'Trabalhista', 'blog/covers/como-calcular-rescisao-trabalhista.png', 'Ilustração do guia: Como calcular rescisão trabalhista passo a passo', 'published', 0, '2026-07-17 12:00:00', '2026-07-17 12:00:00', 'como calcular rescisão trabalhista', '[\"cálculo de rescisão\", \"aviso prévio\", \"férias proporcionais\", \"multa FGTS\"]', 'Como calcular rescisão trabalhista passo a passo', 'Aprenda a calcular rescisão trabalhista com saldo salarial, férias, 13º, aviso-prévio, FGTS e descontos aplicáveis.', NULL, 'blog/social/como-calcular-rescisao-trabalhista.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(6, NULL, 'Margem e markup: como calcular o preço de venda corretamente', 'margem-markup-como-calcular-preco-venda', 'Entenda a diferença entre margem e markup e monte um preço de venda que cubra custos, despesas, tributos e lucro desejado.', '<p>Entenda a diferença entre margem e markup e monte um preço de venda que cubra custos, despesas, tributos e lucro desejado.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Margem e markup não são sinônimos</h2><p>A margem mede o lucro em relação ao preço de venda. O markup é um multiplicador aplicado sobre uma base de custo. Usar o mesmo percentual nas duas abordagens gera resultados diferentes e pode deixar o preço abaixo do necessário.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Mapeie todos os componentes</h2><p>Considere custo de aquisição ou produção, frete, embalagem, comissões, taxas de meios de pagamento, tributos sobre venda, perdas e despesas variáveis. Custos fixos podem ser incorporados por rateio ou tratados na análise de ponto de equilíbrio.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Defina o lucro desejado</h2><p>O lucro deve ser definido sobre uma base clara. Para trabalhar com margem sobre venda, despesas percentuais e lucro disputam o mesmo preço final. Por isso, somar percentuais diretamente ao custo nem sempre alcança a margem pretendida.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Teste cenários</h2><p>Compare preço atual, preço calculado, volume esperado e sensibilidade a descontos. Um desconto aparentemente pequeno pode consumir grande parte do lucro quando a margem é estreita.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Revise com dados reais</h2><p>Depois de aplicar o preço, acompanhe custo efetivo, impostos, devoluções e comissões. A formação de preço é um processo contínuo, não um cálculo feito uma única vez.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/calculadora-margem-markup\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Markup de 50% significa margem de 50%?</h3><p>Não. Um custo de 100 com markup de 50% gera preço de 150 e margem bruta de aproximadamente 33,3%.</p><h3>Tributos entram no custo?</h3><p>Tributos incidentes sobre a venda normalmente precisam ser considerados na formação do preço, respeitando o regime e a operação.</p><h3>Posso usar a mesma margem para todos os produtos?</h3><p>Pode ser inadequado. Giro, risco, concorrência, perdas e capital empregado variam entre produtos e serviços.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 1, 'Gestão Contábil', 'blog/covers/margem-markup-como-calcular-preco-venda.png', 'Ilustração do guia: Margem e markup: como calcular o preço de venda corretamente', 'published', 0, '2026-07-18 12:00:00', '2026-07-18 12:00:00', 'como calcular margem e markup', '[\"calculadora de markup\", \"margem de lucro\", \"formação de preço\", \"preço de venda\"]', 'Margem e markup: como calcular o preço de venda corretamente', 'Entenda margem e markup e calcule um preço de venda que cubra custos, despesas, tributos e lucro sem confundir percentuais.', NULL, 'blog/social/margem-markup-como-calcular-preco-venda.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(7, NULL, 'Pró-labore e distribuição de lucros: como definir valores', 'pro-labore-distribuicao-lucros-como-definir', 'Veja como separar remuneração pelo trabalho e retorno do capital, estimar encargos e documentar a distribuição de lucros aos sócios.', '<p>Veja como separar remuneração pelo trabalho e retorno do capital, estimar encargos e documentar a distribuição de lucros aos sócios.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>São naturezas diferentes</h2><p>Pró-labore remunera o trabalho do sócio na empresa. Distribuição de lucros representa o retorno do resultado empresarial. Tratar toda retirada como lucro sem avaliar atividade, escrituração e resultado pode criar risco fiscal e previdenciário.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Definição do pró-labore</h2><p>A empresa deve observar função exercida, capacidade financeira, referências de mercado e regras previdenciárias. O valor precisa ser formalizado e processado com os encargos correspondentes, incluindo retenções quando aplicáveis.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Apuração dos lucros</h2><p>A distribuição depende de lucro efetivamente apurado e de suporte contábil. Balancetes, demonstrações, registros societários e separação entre patrimônio pessoal e empresarial fortalecem a evidência da operação.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Vários sócios</h2><p>Critérios de participação podem seguir quotas ou disposições societárias válidas. Quando as retiradas não acompanham a participação, documente a justificativa e verifique as condições legais e contratuais.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Simule antes de decidir</h2><p>Compare diferentes valores de pró-labore, encargos, caixa disponível e resultado acumulado. A simulação não substitui a análise jurídica e contábil, mas ajuda a identificar cenários inviáveis e dados ausentes.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/calculadora-pro-labore-distribuicao-lucros\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Sócio que trabalha precisa receber pró-labore?</h3><p>A situação deve ser analisada conforme atuação e regras aplicáveis; simplesmente classificar toda retirada como lucro pode ser inadequado.</p><h3>Lucro distribuído é sempre isento?</h3><p>A tributação depende do atendimento das condições legais e da comprovação do lucro.</p><h3>Posso distribuir lucro todo mês?</h3><p>É possível haver antecipações ou distribuições periódicas quando suportadas por apuração e documentação adequadas.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/pro-labore-distribuicao-lucros-como-definir.png', 'Ilustração do guia: Pró-labore e distribuição de lucros: como definir valores', 'published', 0, '2026-07-19 12:00:00', '2026-07-19 12:00:00', 'pró-labore e distribuição de lucros', '[\"cálculo pró-labore\", \"INSS pró-labore\", \"IRRF pró-labore\", \"lucros isentos\"]', 'Pró-labore e distribuição de lucros: como definir valores', 'Entenda como definir pró-labore e distribuição de lucros, estimar INSS e IRRF e manter documentação contábil adequada.', NULL, 'blog/social/pro-labore-distribuicao-lucros-como-definir.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(8, NULL, 'Simples Nacional: como calcular DAS, alíquota efetiva e Fator R', 'simples-nacional-como-calcular-das-fator-r', 'Aprenda a usar receita acumulada, faixa, parcela a deduzir e Fator R para estimar o DAS com memória de cálculo.', '<p>Aprenda a usar receita acumulada, faixa, parcela a deduzir e Fator R para estimar o DAS com memória de cálculo.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Identifique atividade e anexo</h2><p>O cálculo começa pelo enquadramento da receita no anexo adequado. Uma mesma empresa pode ter receitas sujeitas a tratamentos diferentes, por isso a segregação correta é tão importante quanto a fórmula.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Use a receita acumulada de doze meses</h2><p>A faixa é determinada pela receita bruta acumulada nos doze meses anteriores ao período de apuração. Empresas em início de atividade podem seguir regras de proporcionalização. Não confunda receita do mês com receita acumulada.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Alíquota efetiva</h2><p>A alíquota nominal da tabela não é aplicada isoladamente. A fórmula considera receita acumulada, alíquota nominal e parcela a deduzir. O resultado é a alíquota efetiva usada sobre a receita segregada do período.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Fator R</h2><p>Para determinadas atividades, a relação entre folha e receita acumuladas pode direcionar a tributação entre anexos. Folha, pró-labore e encargos devem ser apurados de forma consistente no período correspondente.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Memória e conferência</h2><p>Guarde anexo, faixa, receitas, folha, parcela a deduzir e resultado da alíquota. Compare a estimativa com o sistema oficial e investigue divergências de segregação, período ou classificação.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/calculadora-simples-nacional\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>A alíquota da primeira faixa é sempre o percentual pago?</h3><p>A carga efetiva depende da fórmula e da composição da receita, embora na primeira faixa possa coincidir em situações simples.</p><h3>O que entra no Fator R?</h3><p>A composição deve seguir as regras vigentes para folha e receita no período considerado.</p><h3>Posso somar todas as receitas e aplicar uma única alíquota?</h3><p>Nem sempre. Receitas podem exigir segregações por atividade, anexo ou tratamento tributário.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/simples-nacional-como-calcular-das-fator-r.png', 'Ilustração do guia: Simples Nacional: como calcular DAS, alíquota efetiva e Fator R', 'published', 0, '2026-07-20 12:00:00', '2026-07-20 12:00:00', 'como calcular Simples Nacional', '[\"calculadora Simples Nacional\", \"alíquota efetiva\", \"Fator R\", \"anexos Simples Nacional\"]', 'Simples Nacional: como calcular DAS, alíquota efetiva e Fator R', 'Calcule Simples Nacional com receita acumulada, faixa, alíquota efetiva, anexo e Fator R, entendendo cada etapa do DAS.', NULL, 'blog/social/simples-nacional-como-calcular-das-fator-r.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(9, NULL, 'Simples, Lucro Presumido ou Lucro Real: como comparar regimes', 'simples-lucro-presumido-lucro-real-comparacao', 'Veja quais premissas usar para comparar regimes tributários e por que faturamento isolado não é suficiente para decidir.', '<p>Veja quais premissas usar para comparar regimes tributários e por que faturamento isolado não é suficiente para decidir.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Comparação exige premissas equivalentes</h2><p>Os regimes usam bases, períodos e tributos diferentes. Para comparar, aplique o mesmo horizonte de receita, atividade, folha, margem, despesas e localização. Resultados construídos com premissas diferentes não são comparáveis.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Simples Nacional</h2><p>Avalie anexos, segregações, Fator R, sublimites, substituição tributária e tributos recolhidos fora do documento único. A simplicidade operacional tem valor, mas não elimina a necessidade de conferir a carga total.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Lucro Presumido</h2><p>Considere percentuais de presunção por atividade, IRPJ, adicional, CSLL, PIS, Cofins, ISS ou ICMS e encargos sobre folha. Margens reais muito diferentes da presunção podem alterar a atratividade.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Lucro Real</h2><p>A análise depende do lucro tributável, adições, exclusões, compensações e créditos permitidos. Empresas com margem baixa ou determinadas estruturas de custos podem encontrar vantagens, mas a conformidade tende a ser mais exigente.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Decisão e sensibilidade</h2><p>Não escolha apenas pelo menor valor de um mês. Simule crescimento, queda de margem, contratação, mudança de atividade e sazonalidade. Inclua custo operacional, risco e capacidade de manter controles compatíveis com o regime.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/comparador-tributario\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>O regime com menor imposto sempre é o melhor?</h3><p>Não. Custos de conformidade, fluxo de caixa, riscos e limitações operacionais também fazem parte da decisão.</p><h3>Uma simulação substitui planejamento tributário?</h3><p>Não. Ela organiza premissas e cenários, mas a decisão requer validação técnica conforme a empresa.</p><h3>Quando revisar o regime?</h3><p>Revise antes do período de opção e sempre que houver mudança relevante em receita, margem, atividade ou estrutura.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/simples-lucro-presumido-lucro-real-comparacao.png', 'Ilustração do guia: Simples, Lucro Presumido ou Lucro Real: como comparar regimes', 'published', 0, '2026-07-21 12:00:00', '2026-07-21 12:00:00', 'comparar regimes tributários', '[\"Simples ou Lucro Presumido\", \"Lucro Real ou Presumido\", \"planejamento tributário\", \"comparador tributário\"]', 'Simples, Lucro Presumido ou Lucro Real: como comparar regimes', 'Compare Simples Nacional, Lucro Presumido e Lucro Real usando receita, margem, folha, créditos e custos de conformidade.', NULL, 'blog/social/simples-lucro-presumido-lucro-real-comparacao.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(10, NULL, 'Como calcular férias, terço constitucional, abono e prazos', 'como-calcular-ferias-dias-abono-prazos', 'Entenda período aquisitivo, dias de direito, remuneração, médias, terço constitucional, venda de dias e prazos de concessão.', '<p>Entenda período aquisitivo, dias de direito, remuneração, médias, terço constitucional, venda de dias e prazos de concessão.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Período aquisitivo e concessivo</h2><p>O direito é formado ao longo do período aquisitivo e deve ser concedido dentro do período correspondente. Datas de admissão, afastamentos e faltas relevantes precisam ser consideradas antes de calcular valores.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Dias de direito</h2><p>A quantidade de dias pode ser afetada por ocorrências previstas em lei. Não reduza férias apenas com base em faltas sem conferir a natureza e a faixa aplicável.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Remuneração e médias</h2><p>Parta da remuneração devida na época da concessão e avalie médias de horas extras, adicionais e outras parcelas variáveis quando integrarem a base. Documente o período e o critério usado nas médias.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Terço e abono pecuniário</h2><p>O terço constitucional incide sobre a remuneração de férias conforme a regra aplicável. O abono converte parte dos dias em valor, mas depende de requisitos e prazo de solicitação. Diferencie abono pecuniário de adiantamento salarial.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Planejamento e pagamento</h2><p>Além do cálculo, verifique comunicação, início do descanso, feriados, parcelamento permitido e prazo de pagamento. Planejar férias evita concentração de ausências e reduz risco de concessão fora do prazo.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary\" href=\"/ferramentas/calculadora-ferias\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Férias são sempre de 30 dias?</h3><p>O direito pode variar conforme ocorrências e regras aplicáveis ao período.</p><h3>O empregado pode vender todas as férias?</h3><p>Não. O abono pecuniário é limitado e depende de solicitação no prazo adequado.</p><h3>Horas extras entram nas férias?</h3><p>Quando habituais e conforme as regras aplicáveis, podem compor médias da remuneração.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 4, 'Trabalhista', 'blog/covers/como-calcular-ferias-dias-abono-prazos.png', 'Ilustração do guia: Como calcular férias, terço constitucional, abono e prazos', 'published', 0, '2026-07-22 12:00:00', '2026-07-22 12:00:00', 'como calcular férias', '[\"calculadora de férias\", \"terço constitucional\", \"abono pecuniário\", \"prazo pagamento férias\"]', 'Como calcular férias, terço constitucional, abono e prazos', 'Calcule férias com período aquisitivo, dias de direito, médias, terço constitucional, abono pecuniário e prazos principais.', NULL, 'blog/social/como-calcular-ferias-dias-abono-prazos.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20');

-- --------------------------------------------------------

--
-- Estrutura da tabela `blog_post_tool`
--

CREATE TABLE `blog_post_tool` (
  `blog_post_id` bigint(20) UNSIGNED NOT NULL,
  `tool_slug` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `blog_post_tool`
--

INSERT INTO `blog_post_tool` (`blog_post_id`, `tool_slug`, `created_at`, `updated_at`) VALUES
(1, 'calculadora-de-honorarios-contabeis', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(2, 'validador-de-cnpj', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(3, 'gerador-darf-gps', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(4, 'conversor-fiscal-xml', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(5, 'calculadora-de-rescisao', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(6, 'calculadora-margem-markup', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(7, 'calculadora-pro-labore-distribuicao-lucros', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(8, 'calculadora-simples-nacional', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(9, 'comparador-tributario', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(10, 'calculadora-ferias', '2026-07-23 11:45:13', '2026-07-23 11:45:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_13_000100_create_tool_runs_table', 1),
(5, '2026_07_13_000200_create_audit_logs_table', 1),
(6, '2026_07_13_000300_create_organizations_and_access_fields', 1),
(7, '2026_07_13_000400_create_tool_usage_events_table', 1),
(8, '2026_07_14_000500_create_blog_posts_table', 1),
(9, '2026_07_14_000500_create_simples_nacional_calculations_table', 1),
(10, '2026_07_14_000700_create_platform_analytics_events_table', 1),
(11, '2026_07_15_000100_upgrade_platform_analytics_to_v2', 1),
(12, '2026_07_15_000200_create_margin_markup_shares_table', 1),
(13, '2026_07_15_000400_add_acquisition_attribution_to_analytics', 1),
(14, '2026_07_15_000500_create_analytics_seo_metric_snapshots', 1),
(15, '2026_07_15_000600_create_accounting_fee_clients_table', 1),
(16, '2026_07_15_000700_create_accounting_fee_adjustments_table', 1),
(17, '2026_07_15_000800_create_accounting_fee_calculations_table', 1),
(18, '2026_07_15_000900_add_audience_context_to_analytics', 1),
(19, '2026_07_15_001000_create_analytics_funnels', 1),
(20, '2026_07_15_001000_create_analytics_report_schedules_table', 1),
(21, '2026_07_15_001100_create_analytics_insights_table', 1),
(22, '2026_07_15_001200_optimize_analytics_queries', 1),
(23, '2026_07_15_001300_normalize_analytics_event_names', 1),
(24, '2026_07_15_010000_create_blog_categories_table', 1),
(25, '2026_07_16_000100_add_prazzu_account_id_to_users_table', 1),
(26, '2026_07_16_000400_create_organization_subscriptions_and_seats', 1),
(27, '2026_07_17_000100_allow_link_only_organization_invitations', 1),
(28, '2026_07_18_000100_drop_margin_markup_shares_table', 1),
(29, '2026_07_18_000100_remove_accounting_crm_and_sharing', 1),
(30, '2026_07_18_000200_create_tool_run_favorites_table', 1),
(31, '2026_07_18_000200_migrate_simples_nacional_history_to_core', 1),
(32, '2026_07_18_000300_rename_premium_subscription_plan_to_plus', 1),
(33, '2026_07_20_000100_add_schema_version_to_tool_runs_table', 1),
(34, '2026_07_21_000100_create_analytics_tool_presences_table', 1),
(35, '2026_07_22_000100_create_acquisition_contexts_table', 1),
(36, '2026_07_22_000200_add_tools_section_title_to_acquisition_contexts_table', 1),
(37, '2026_07_22_000300_add_acquisition_context_to_analytics', 1),
(38, '2026_07_22_000700_add_campaign_creative_metadata_to_acquisition_contexts', 1),
(39, '2026_07_22_000800_add_campaign_investment_to_acquisition_contexts', 1),
(40, '2026_07_22_000900_add_contextual_bar_to_acquisition_contexts', 1),
(41, '2026_07_22_001000_create_page_feedback_table', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `organizations`
--

CREATE TABLE `organizations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `owner_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `organization_invitations`
--

CREATE TABLE `organization_invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'member',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `token` varchar(64) NOT NULL,
  `invited_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accepted_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `organization_members`
--

CREATE TABLE `organization_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'member',
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `joined_at` timestamp NULL DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `organization_seats`
--

CREATE TABLE `organization_seats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_subscription_id` bigint(20) UNSIGNED NOT NULL,
  `organization_member_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `released_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `organization_subscriptions`
--

CREATE TABLE `organization_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `seat_limit` int(10) UNSIGNED NOT NULL,
  `billing_provider` varchar(255) DEFAULT NULL,
  `billing_reference` varchar(255) DEFAULT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `page_feedback`
--

CREATE TABLE `page_feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `path` varchar(512) NOT NULL,
  `url` text NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `user_agent` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `platform_analytics_events`
--

CREATE TABLE `platform_analytics_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` char(36) DEFAULT NULL,
  `event_name` varchar(80) NOT NULL,
  `schema_version` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `channel` varchar(40) NOT NULL,
  `subject_type` varchar(80) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_slug` varchar(255) DEFAULT NULL,
  `visitor_id` char(36) DEFAULT NULL,
  `analytics_session_id` char(36) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `path` varchar(2048) DEFAULT NULL,
  `referrer` varchar(2048) DEFAULT NULL,
  `source` varchar(120) DEFAULT NULL,
  `medium` varchar(120) DEFAULT NULL,
  `campaign` varchar(255) DEFAULT NULL,
  `acquisition_context_id` bigint(20) UNSIGNED DEFAULT NULL,
  `acquisition_keyword` varchar(255) DEFAULT NULL,
  `acquisition_campaign_identifier` varchar(255) DEFAULT NULL,
  `acquisition_primary_tool_slug` varchar(255) DEFAULT NULL,
  `utm_source` varchar(120) DEFAULT NULL,
  `utm_medium` varchar(120) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `utm_content` varchar(255) DEFAULT NULL,
  `device_type` varchar(30) DEFAULT NULL,
  `browser` varchar(80) DEFAULT NULL,
  `operating_system` varchar(80) DEFAULT NULL,
  `language` varchar(20) DEFAULT NULL,
  `timezone` varchar(80) DEFAULT NULL,
  `screen_resolution` varchar(20) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `region` varchar(120) DEFAULT NULL,
  `city` varchar(160) DEFAULT NULL,
  `ip_hash` varchar(64) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `platform_analytics_events`
--

INSERT INTO `platform_analytics_events` (`id`, `event_id`, `event_name`, `schema_version`, `channel`, `subject_type`, `subject_id`, `subject_slug`, `visitor_id`, `analytics_session_id`, `user_id`, `session_id`, `url`, `path`, `referrer`, `source`, `medium`, `campaign`, `acquisition_context_id`, `acquisition_keyword`, `acquisition_campaign_identifier`, `acquisition_primary_tool_slug`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `device_type`, `browser`, `operating_system`, `language`, `timezone`, `screen_resolution`, `country_code`, `region`, `city`, `ip_hash`, `user_agent`, `metadata`, `occurred_at`) VALUES
(1, 'caba8a6b-15fa-41b7-8bdb-e3df307898fe', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:47:08'),
(2, 'e54be9e2-ab21-4b16-b9dd-a2e93744064d', 'blog.time.spent', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"seconds\":299}', '2026-07-23 14:47:09'),
(3, '6f6cdb3d-72d1-43ba-871c-95c9a1a6d4f2', 'blog.reading.abandoned', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 14:47:09'),
(4, '08d0c774-1a35-4442-afb2-a5d0ca30e4d1', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:47:13'),
(5, '4601aafb-7a41-426f-9c5e-89c1e3e1c07c', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:47:24'),
(6, '8e5c6354-2837-4aaf-87fe-d28606659835', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:47:24'),
(7, '51c18152-3093-4266-a018-6ca7bef0a23b', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 14:47:25'),
(8, 'ba1b8192-480f-4733-a32b-c5f9e01da98b', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":50}', '2026-07-23 14:47:26'),
(9, 'e43dfdb3-19c6-41a8-8df1-af37412d1160', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":75}', '2026-07-23 14:47:27'),
(10, 'a3711643-2bd9-4469-9234-b9ed473f28d4', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":100}', '2026-07-23 14:47:27'),
(11, '93c47a28-52c4-427a-9f92-52f2bfa08139', 'blog.reading.completed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:47:27'),
(12, '1b187f1c-779a-4a5d-80ee-bb0ace386c47', 'blog.reading.started', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:47:28'),
(13, '866fe2e2-4530-40e1-807d-bc47dc52f03c', 'blog.time.spent', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"seconds\":5}', '2026-07-23 14:47:30'),
(14, 'd6a71d56-6a19-4b83-a302-d05fb8b2e96a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:52:23'),
(15, '14749256-5ee0-4d61-9ab2-d5d8043fc0e2', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:52:30'),
(16, 'e1a9c53b-99d4-4b00-b832-27a60a8aa825', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:52:30'),
(17, '9707073f-9ac9-4287-8f9d-5ac21242bcd3', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 14:52:31'),
(18, '24fd4736-7535-4f19-9fd1-5bcf327ddd7f', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":50}', '2026-07-23 14:52:32'),
(19, '87d4f263-b3a6-470c-90d1-ab75ebdcfab6', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":75}', '2026-07-23 14:52:33'),
(20, '8e68a995-73fd-4a8d-beee-22faf3ccb5b4', 'blog.reading.completed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:52:34'),
(21, '78866816-be12-48e7-85f2-06da60485e46', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":100}', '2026-07-23 14:52:34'),
(22, 'd271e701-1f58-40c4-9751-d5d7ae68a4ac', 'blog.reading.started', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:52:35'),
(23, '83e65b79-cf15-4964-b261-6a5baaea46b2', 'blog.time.spent', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"seconds\":5}', '2026-07-23 14:52:36'),
(24, 'e54cd8f0-5fe3-4c41-83ca-1d5cbcee3174', 'blog.post.viewed', 1, 'blog', 'blog_post', 8, 'simples-nacional-como-calcular-das-fator-r', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/simples-nacional-como-calcular-das-fator-r', '/blog/simples-nacional-como-calcular-das-fator-r', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":8,\"subject_slug\":\"simples-nacional-como-calcular-das-fator-r\"}', '2026-07-23 14:52:43'),
(25, 'd3248729-6db4-418e-965a-b1ae04d311ee', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/simples-nacional-como-calcular-das-fator-r', '/blog/simples-nacional-como-calcular-das-fator-r', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:52:43'),
(26, 'cb1be9c8-940e-4ae5-877e-2f35fa853763', 'blog.scroll.measured', 1, 'blog', 'blog_post', 8, 'simples-nacional-como-calcular-das-fator-r', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-nacional-como-calcular-das-fator-r', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":8,\"subject_slug\":\"simples-nacional-como-calcular-das-fator-r\",\"percentage\":25}', '2026-07-23 14:52:44'),
(27, '4832ad5c-798c-4b3b-8a82-14b2c6ff63f0', 'blog.time.spent', 1, 'blog', 'blog_post', 8, 'simples-nacional-como-calcular-das-fator-r', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-nacional-como-calcular-das-fator-r', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":8,\"subject_slug\":\"simples-nacional-como-calcular-das-fator-r\",\"seconds\":1}', '2026-07-23 14:52:45'),
(28, 'a828f8a9-fa9f-44d9-8693-c7f40cb0630b', 'blog.reading.abandoned', 1, 'blog', 'blog_post', 8, 'simples-nacional-como-calcular-das-fator-r', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-nacional-como-calcular-das-fator-r', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":8,\"subject_slug\":\"simples-nacional-como-calcular-das-fator-r\"}', '2026-07-23 14:52:45'),
(29, 'b27c8084-4d99-4073-9346-ad8feeb71974', 'blog.post.viewed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 14:52:46'),
(30, '23ba8719-e05a-44e2-b9db-14be97784b30', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:52:46'),
(31, '65248acb-5224-4e71-bd8d-cf3f6caa4423', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":25}', '2026-07-23 14:52:47'),
(32, 'ab75fdca-abec-4ff1-93be-544f913ff972', 'blog.time.spent', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"seconds\":1}', '2026-07-23 14:52:48'),
(33, '6fe95e56-aa64-490a-87c3-46bb15a5d20f', 'blog.reading.abandoned', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 14:52:49'),
(34, '52af6747-4dea-4b26-ac30-d3a5daef2244', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:52:49'),
(35, '5a0324bb-76b1-4589-8ee9-534b61147788', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:52:50'),
(36, '67bf5403-1d5b-4b85-8cb2-27d1299e67cb', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 14:52:51'),
(37, 'c4d0ab91-3bbb-4993-a359-692e2606607d', 'blog.time.spent', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"seconds\":0}', '2026-07-23 14:52:52'),
(38, '236cc3d3-2d7a-4196-9555-f42304a296d7', 'blog.reading.abandoned', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:52:52'),
(39, '68ef4d39-c6ff-4944-a901-14f23f803ca8', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:53:34'),
(40, '3461dfcc-30c5-4596-a3d0-69794c98db75', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:53:34'),
(41, '8ed228b2-eeca-4957-82cf-5c52ad203328', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 14:53:35'),
(42, 'a292e40a-47bf-45bb-ae5a-34b9cdc4f31f', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":50}', '2026-07-23 14:53:35'),
(43, '6c5f86bd-f071-43d7-9823-24b2cfd99daa', 'blog.reading.started', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:53:38'),
(44, '7bfac877-2d70-477c-b7f8-0c39609bd5e0', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":75}', '2026-07-23 14:53:43'),
(45, '9f7c5b16-f345-49bf-a900-77ce18197fc2', 'blog.reading.completed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 14:53:43'),
(46, 'a328cf39-82ff-4214-9007-e1e7ced3a296', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":100}', '2026-07-23 14:53:44'),
(47, '664bda40-2760-48d6-916a-fe3c36c65b29', 'blog.time.spent', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"seconds\":29}', '2026-07-23 14:54:04'),
(48, '1cae76df-efe9-4b5e-a1ee-97209e2bbc7a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:55:32'),
(49, '4172d2e4-2edd-42ea-bca5-36e2dfef353f', 'audience.context_captured', 1, 'audience', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/analytics/audience', '/analytics/audience', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt-PT', 'America/Sao_Paulo', '1920x1080', NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"timezone\":\"America\\/Sao_Paulo\",\"screen_resolution\":\"1920x1080\",\"language\":\"pt-PT\"}', '2026-07-23 14:55:32'),
(50, '10564390-cc9f-436b-9e8b-0b2b7d410be9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:55:34'),
(51, 'ffb28f69-1724-4c02-a3ba-8bd1667e0933', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:59:30'),
(52, 'aec1ea11-5b30-4d84-a403-e0ab103694b0', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:59:34'),
(53, 'd9d23f98-e613-418e-89c4-c035f48b185f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:59:40'),
(54, 'ff94ed72-a727-4e6a-8908-364e0c767ff9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:59:41'),
(55, 'f82dc1d9-325b-4f6b-9b8e-c07ac3a2938b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 14:59:43'),
(56, '82791f3b-5539-40e8-a4f5-e035ed67b191', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog', '/blog', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 15:04:56'),
(57, 'b81921ab-5923-48b5-b239-3e69af850167', 'blog.post.viewed', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', '/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'http://localhost:8000/index.php/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 15:06:09'),
(58, '7bbf7cfd-5a3c-40a4-a504-2a15f93d5c0e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', '/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'http://localhost:8000/index.php/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 15:06:09'),
(59, 'a71c7ffc-1614-4929-bb94-de3d03d2ca4b', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/analytics', '/blog/analytics', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":25}', '2026-07-23 15:06:10');
INSERT INTO `platform_analytics_events` (`id`, `event_id`, `event_name`, `schema_version`, `channel`, `subject_type`, `subject_id`, `subject_slug`, `visitor_id`, `analytics_session_id`, `user_id`, `session_id`, `url`, `path`, `referrer`, `source`, `medium`, `campaign`, `acquisition_context_id`, `acquisition_keyword`, `acquisition_campaign_identifier`, `acquisition_primary_tool_slug`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `device_type`, `browser`, `operating_system`, `language`, `timezone`, `screen_resolution`, `country_code`, `region`, `city`, `ip_hash`, `user_agent`, `metadata`, `occurred_at`) VALUES
(60, 'd85c307f-e522-49ca-b82b-c344f1355741', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/analytics', '/blog/analytics', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":50}', '2026-07-23 15:06:11'),
(61, '4499cdea-7ea3-41e7-baad-f2829d1db0fe', 'blog.reading.started', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/analytics', '/blog/analytics', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 15:06:12'),
(62, '31562028-9a30-4bda-94b2-c3b51a2d87b9', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/analytics', '/blog/analytics', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":75}', '2026-07-23 15:06:16'),
(63, '408df0fd-f08f-4668-82ef-ebb14ae1909d', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/analytics', '/blog/analytics', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":100}', '2026-07-23 15:06:16'),
(64, 'dca65d46-194d-4d78-9bcd-858d50219b28', 'blog.reading.completed', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/analytics', '/blog/analytics', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 15:06:16'),
(65, '6d828080-cce6-4d60-9524-8c1ef28f293c', 'blog.post.viewed', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/index.php/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', '/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'http://localhost:8000/index.php/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 15:15:00'),
(66, '94698ae5-9d1c-4055-97ee-c9df9b849441', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 15:16:23'),
(67, '37bd43e3-2842-4ed4-a09c-ecb822609493', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 15:16:28'),
(68, '91d39195-ebbe-4a52-b333-d42550101ad4', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 15:16:30'),
(69, '807fff83-f1c8-480f-bea2-c00c504f307b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 15:16:30'),
(70, '92c13653-b22b-4cf9-ac43-7ed90336ca40', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 15:16:31'),
(71, 'b3c3f093-61db-40cd-a3e0-7aab92d39cba', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":50}', '2026-07-23 15:16:32'),
(72, '4d9c3b7e-23bf-42ce-ab96-6368e60bd7df', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":75}', '2026-07-23 15:16:32'),
(73, '2eba4efd-45f1-41a1-9810-8f3fc1b4598a', 'blog.reading.completed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 15:16:33'),
(74, '6ace6c0c-95ed-40e7-841b-bfeb3331b894', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":100}', '2026-07-23 15:16:33'),
(75, '298133ac-c9a1-4309-9900-f4e48d13b3cc', 'blog.reading.started', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 15:16:34'),
(76, '35909935-4952-494c-bd1f-7b910ff1b3e8', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 15:17:25'),
(77, '4f0cd28c-b542-4dab-816d-5c5872733c95', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 15:17:25'),
(78, 'b7829364-985e-4054-995e-75f0728a140e', 'blog.time.spent', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"seconds\":55}', '2026-07-23 15:17:26'),
(79, 'ece3c2ef-2300-4b92-be21-a9c60046cbb1', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 15:17:27'),
(80, 'c6867dde-9e01-46c8-8af4-1ad150929631', 'blog.reading.started', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '17b71e79-b7b2-440d-abae-83a76642fc50', '5b58b327-6abe-4113-a5b8-a08132d89b34', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 15:17:29');

-- --------------------------------------------------------

--
-- Estrutura da tabela `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tool_runs`
--

CREATE TABLE `tool_runs` (
  `id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tool_slug` varchar(120) NOT NULL,
  `tool_version` varchar(50) NOT NULL,
  `schema_version` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `rule_version` varchar(50) NOT NULL,
  `reference_date` date NOT NULL,
  `status` varchar(30) NOT NULL,
  `input_payload` text DEFAULT NULL,
  `result_payload` text DEFAULT NULL,
  `normative_references` text DEFAULT NULL,
  `error_code` varchar(100) DEFAULT NULL,
  `started_at` datetime NOT NULL,
  `finished_at` datetime DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tool_run_favorites`
--

CREATE TABLE `tool_run_favorites` (
  `id` char(36) NOT NULL,
  `tool_run_id` char(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tool_usage_events`
--

CREATE TABLE `tool_usage_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tool_slug` varchar(120) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `organization_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event` varchar(60) NOT NULL,
  `duration_ms` int(10) UNSIGNED DEFAULT NULL,
  `occurred_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prazzu_account_id` varchar(191) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'user',
  `subscription_plan` varchar(30) NOT NULL DEFAULT 'free'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `accounting_fee_adjustments`
--
ALTER TABLE `accounting_fee_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounting_fee_adjustments_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `accounting_fee_adjustments_session_key_index` (`session_key`);

--
-- Índices para tabela `accounting_fee_calculations`
--
ALTER TABLE `accounting_fee_calculations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounting_fee_calculations_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `accounting_fee_calculations_session_key_created_at_index` (`session_key`,`created_at`),
  ADD KEY `accounting_fee_calculations_user_id_index` (`user_id`),
  ADD KEY `accounting_fee_calculations_session_key_index` (`session_key`),
  ADD KEY `accounting_fee_calculations_is_favorite_index` (`is_favorite`);

--
-- Índices para tabela `acquisition_contexts`
--
ALTER TABLE `acquisition_contexts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acquisition_contexts_keyword_unique` (`keyword`),
  ADD KEY `acquisition_contexts_campaign_identifier_index` (`campaign_identifier`),
  ADD KEY `acquisition_contexts_status_index` (`status`),
  ADD KEY `acquisition_contexts_cta_tool_slug_index` (`cta_tool_slug`),
  ADD KEY `acquisition_contexts_primary_tool_slug_index` (`primary_tool_slug`),
  ADD KEY `acquisition_contexts_source_medium` (`campaign_source`,`campaign_medium`),
  ADD KEY `acquisition_contexts_content_identifier_index` (`content_identifier`),
  ADD KEY `acquisition_contexts_video_identifier_index` (`video_identifier`);

--
-- Índices para tabela `acquisition_context_articles`
--
ALTER TABLE `acquisition_context_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acquisition_context_articles_unique` (`acquisition_context_id`,`article_slug`),
  ADD KEY `acquisition_context_articles_order` (`acquisition_context_id`,`position`);

--
-- Índices para tabela `acquisition_context_tools`
--
ALTER TABLE `acquisition_context_tools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acquisition_context_tools_unique` (`acquisition_context_id`,`placement`,`tool_slug`),
  ADD KEY `acquisition_context_tools_order` (`acquisition_context_id`,`placement`,`position`);

--
-- Índices para tabela `analytics_funnels`
--
ALTER TABLE `analytics_funnels`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `analytics_funnel_steps`
--
ALTER TABLE `analytics_funnel_steps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `analytics_funnel_steps_funnel_id_position_unique` (`funnel_id`,`position`);

--
-- Índices para tabela `analytics_insights`
--
ALTER TABLE `analytics_insights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `analytics_insights_fingerprint_unique` (`fingerprint`),
  ADD KEY `analytics_insights_type_index` (`type`),
  ADD KEY `analytics_insights_severity_index` (`severity`),
  ADD KEY `analytics_insights_subject_type_index` (`subject_type`),
  ADD KEY `analytics_insights_subject_slug_index` (`subject_slug`),
  ADD KEY `analytics_insights_status_index` (`status`),
  ADD KEY `analytics_insights_period_start_index` (`period_start`),
  ADD KEY `analytics_insights_period_end_index` (`period_end`),
  ADD KEY `analytics_insights_generated_at_index` (`generated_at`);

--
-- Índices para tabela `analytics_report_schedules`
--
ALTER TABLE `analytics_report_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_report_schedules_is_active_index` (`is_active`),
  ADD KEY `analytics_report_schedules_next_run_at_index` (`next_run_at`);

--
-- Índices para tabela `analytics_seo_metric_snapshots`
--
ALTER TABLE `analytics_seo_metric_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seo_metric_snapshot_dimension_unique` (`blog_post_id`,`metric_date`,`source`,`search_type`,`device`,`country_code`),
  ADD KEY `analytics_seo_metric_snapshots_metric_date_source_index` (`metric_date`,`source`),
  ADD KEY `analytics_seo_metric_snapshots_blog_post_id_metric_date_index` (`blog_post_id`,`metric_date`);

--
-- Índices para tabela `analytics_sessions`
--
ALTER TABLE `analytics_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_sessions_visitor_id_started_at_index` (`visitor_id`,`started_at`),
  ADD KEY `analytics_sessions_user_id_started_at_index` (`user_id`,`started_at`),
  ADD KEY `analytics_sessions_last_activity_at_index` (`last_activity_at`),
  ADD KEY `analytics_sessions_source_started_at_index` (`source`,`started_at`),
  ADD KEY `analytics_sessions_medium_started_at_index` (`medium`,`started_at`),
  ADD KEY `analytics_sessions_campaign_started_at_index` (`campaign`,`started_at`),
  ADD KEY `analytics_sessions_utm_source_started_at_index` (`utm_source`,`started_at`),
  ADD KEY `analytics_sessions_location_idx` (`country_code`,`region`,`started_at`),
  ADD KEY `analytics_sessions_device_idx` (`device_type`,`started_at`),
  ADD KEY `analytics_sessions_activity_visitor_idx` (`last_activity_at`,`visitor_id`),
  ADD KEY `analytics_sessions_acquisition_context_id_foreign` (`acquisition_context_id`),
  ADD KEY `analytics_sessions_acquisition_keyword_started` (`acquisition_keyword`,`started_at`),
  ADD KEY `analytics_sessions_acquisition_campaign_started` (`acquisition_campaign_identifier`,`started_at`);

--
-- Índices para tabela `analytics_tool_presences`
--
ALTER TABLE `analytics_tool_presences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_tool_presences_last_seen_at_tool_slug_index` (`last_seen_at`,`tool_slug`),
  ADD KEY `analytics_tool_presences_tool_slug_index` (`tool_slug`),
  ADD KEY `analytics_tool_presences_visitor_id_index` (`visitor_id`),
  ADD KEY `analytics_tool_presences_analytics_session_id_index` (`analytics_session_id`),
  ADD KEY `analytics_tool_presences_user_id_index` (`user_id`),
  ADD KEY `analytics_tool_presences_source_index` (`source`),
  ADD KEY `analytics_tool_presences_last_seen_at_index` (`last_seen_at`);

--
-- Índices para tabela `analytics_visitors`
--
ALTER TABLE `analytics_visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_visitors_user_id_index` (`user_id`),
  ADD KEY `analytics_visitors_first_seen_at_index` (`first_seen_at`),
  ADD KEY `analytics_visitors_last_seen_at_index` (`last_seen_at`),
  ADD KEY `analytics_visitors_first_medium_first_seen_at_index` (`first_medium`,`first_seen_at`),
  ADD KEY `analytics_visitors_last_medium_last_seen_at_index` (`last_medium`,`last_seen_at`);

--
-- Índices para tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_actor_id_foreign` (`actor_id`),
  ADD KEY `audit_logs_auditable_lookup` (`auditable_type`,`auditable_id`),
  ADD KEY `audit_logs_action_index` (`action`),
  ADD KEY `audit_logs_occurred_at_index` (`occurred_at`);

--
-- Índices para tabela `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_categories_name_unique` (`name`),
  ADD UNIQUE KEY `blog_categories_slug_unique` (`slug`),
  ADD KEY `blog_categories_is_active_index` (`is_active`);

--
-- Índices para tabela `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  ADD KEY `blog_posts_author_id_foreign` (`author_id`),
  ADD KEY `blog_posts_status_published_at_index` (`status`,`published_at`),
  ADD KEY `blog_posts_category_status_published_at_index` (`category`,`status`,`published_at`),
  ADD KEY `blog_posts_category_index` (`category`),
  ADD KEY `blog_posts_status_index` (`status`),
  ADD KEY `blog_posts_is_featured_index` (`is_featured`),
  ADD KEY `blog_posts_published_at_index` (`published_at`),
  ADD KEY `blog_posts_should_index_index` (`should_index`),
  ADD KEY `blog_posts_category_id_foreign` (`category_id`);

--
-- Índices para tabela `blog_post_tool`
--
ALTER TABLE `blog_post_tool`
  ADD PRIMARY KEY (`blog_post_id`,`tool_slug`),
  ADD KEY `blog_post_tool_tool_slug_index` (`tool_slug`);

--
-- Índices para tabela `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Índices para tabela `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Índices para tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices para tabela `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Índices para tabela `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organizations_slug_unique` (`slug`),
  ADD KEY `organizations_owner_user_id_foreign` (`owner_user_id`);

--
-- Índices para tabela `organization_invitations`
--
ALTER TABLE `organization_invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organization_invitations_token_unique` (`token`),
  ADD KEY `organization_invitations_invited_by_user_id_foreign` (`invited_by_user_id`),
  ADD KEY `organization_invitations_accepted_by_user_id_foreign` (`accepted_by_user_id`),
  ADD KEY `organization_invitations_organization_id_status_index` (`organization_id`,`status`),
  ADD KEY `organization_invitations_email_status_index` (`email`,`status`);

--
-- Índices para tabela `organization_members`
--
ALTER TABLE `organization_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organization_members_organization_id_user_id_unique` (`organization_id`,`user_id`),
  ADD KEY `organization_members_organization_id_status_index` (`organization_id`,`status`),
  ADD KEY `organization_members_user_id_status_index` (`user_id`,`status`);

--
-- Índices para tabela `organization_seats`
--
ALTER TABLE `organization_seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_seats_subscription_active_index` (`organization_subscription_id`,`released_at`),
  ADD KEY `org_seats_member_active_index` (`organization_member_id`,`released_at`);

--
-- Índices para tabela `organization_subscriptions`
--
ALTER TABLE `organization_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organization_subscriptions_billing_reference_unique` (`billing_reference`),
  ADD KEY `organization_subscriptions_organization_id_status_index` (`organization_id`,`status`),
  ADD KEY `organization_subscriptions_status_index` (`status`);

--
-- Índices para tabela `page_feedback`
--
ALTER TABLE `page_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_feedback_user_id_foreign` (`user_id`),
  ADD KEY `page_feedback_path_created_at_index` (`path`,`created_at`),
  ADD KEY `page_feedback_session_id_index` (`session_id`),
  ADD KEY `page_feedback_rating_index` (`rating`);

--
-- Índices para tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Índices para tabela `platform_analytics_events`
--
ALTER TABLE `platform_analytics_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `platform_analytics_events_event_id_unique` (`event_id`),
  ADD KEY `platform_analytics_events_user_id_foreign` (`user_id`),
  ADD KEY `platform_analytics_events_channel_event_name_occurred_at_index` (`channel`,`event_name`,`occurred_at`),
  ADD KEY `platform_analytics_events_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  ADD KEY `platform_analytics_events_subject_slug_index` (`subject_slug`),
  ADD KEY `analytics_events_period_event_idx` (`occurred_at`,`event_name`),
  ADD KEY `analytics_events_period_channel_idx` (`occurred_at`,`channel`),
  ADD KEY `analytics_events_period_source_idx` (`occurred_at`,`source`),
  ADD KEY `analytics_events_period_subject_idx` (`occurred_at`,`subject_slug`),
  ADD KEY `analytics_events_visitor_period_idx` (`visitor_id`,`occurred_at`),
  ADD KEY `analytics_events_session_period_idx` (`analytics_session_id`,`occurred_at`),
  ADD KEY `analytics_events_acquisition_context_occurred` (`acquisition_context_id`,`occurred_at`),
  ADD KEY `analytics_events_acquisition_keyword_occurred` (`acquisition_keyword`,`occurred_at`),
  ADD KEY `analytics_events_acquisition_campaign_occurred` (`acquisition_campaign_identifier`,`occurred_at`);

--
-- Índices para tabela `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Índices para tabela `tool_runs`
--
ALTER TABLE `tool_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tool_runs_user_id_foreign` (`user_id`),
  ADD KEY `tool_runs_version_lookup` (`tool_slug`,`tool_version`,`rule_version`),
  ADD KEY `tool_runs_tool_slug_index` (`tool_slug`),
  ADD KEY `tool_runs_reference_date_index` (`reference_date`),
  ADD KEY `tool_runs_status_index` (`status`),
  ADD KEY `tool_runs_tool_slug_schema_version_index` (`tool_slug`,`schema_version`);

--
-- Índices para tabela `tool_run_favorites`
--
ALTER TABLE `tool_run_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tool_run_favorites_owner_unique` (`tool_run_id`,`user_id`),
  ADD KEY `tool_run_favorites_owner_lookup` (`user_id`,`created_at`);

--
-- Índices para tabela `tool_usage_events`
--
ALTER TABLE `tool_usage_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tool_usage_events_user_id_foreign` (`user_id`),
  ADD KEY `tool_usage_events_organization_id_foreign` (`organization_id`),
  ADD KEY `tool_usage_lookup` (`tool_slug`,`event`,`occurred_at`),
  ADD KEY `tool_usage_events_tool_slug_index` (`tool_slug`),
  ADD KEY `tool_usage_events_event_index` (`event`),
  ADD KEY `tool_usage_events_occurred_at_index` (`occurred_at`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_prazzu_account_id_unique` (`prazzu_account_id`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_subscription_plan_index` (`subscription_plan`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `accounting_fee_adjustments`
--
ALTER TABLE `accounting_fee_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `accounting_fee_calculations`
--
ALTER TABLE `accounting_fee_calculations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `acquisition_contexts`
--
ALTER TABLE `acquisition_contexts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `acquisition_context_articles`
--
ALTER TABLE `acquisition_context_articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `acquisition_context_tools`
--
ALTER TABLE `acquisition_context_tools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `analytics_funnels`
--
ALTER TABLE `analytics_funnels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `analytics_funnel_steps`
--
ALTER TABLE `analytics_funnel_steps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `analytics_insights`
--
ALTER TABLE `analytics_insights`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `analytics_report_schedules`
--
ALTER TABLE `analytics_report_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `analytics_seo_metric_snapshots`
--
ALTER TABLE `analytics_seo_metric_snapshots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `organization_invitations`
--
ALTER TABLE `organization_invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `organization_members`
--
ALTER TABLE `organization_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `organization_seats`
--
ALTER TABLE `organization_seats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `organization_subscriptions`
--
ALTER TABLE `organization_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `page_feedback`
--
ALTER TABLE `page_feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `platform_analytics_events`
--
ALTER TABLE `platform_analytics_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de tabela `tool_usage_events`
--
ALTER TABLE `tool_usage_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `accounting_fee_adjustments`
--
ALTER TABLE `accounting_fee_adjustments`
  ADD CONSTRAINT `accounting_fee_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `acquisition_context_articles`
--
ALTER TABLE `acquisition_context_articles`
  ADD CONSTRAINT `acquisition_context_articles_acquisition_context_id_foreign` FOREIGN KEY (`acquisition_context_id`) REFERENCES `acquisition_contexts` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `acquisition_context_tools`
--
ALTER TABLE `acquisition_context_tools`
  ADD CONSTRAINT `acquisition_context_tools_acquisition_context_id_foreign` FOREIGN KEY (`acquisition_context_id`) REFERENCES `acquisition_contexts` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `analytics_funnel_steps`
--
ALTER TABLE `analytics_funnel_steps`
  ADD CONSTRAINT `analytics_funnel_steps_funnel_id_foreign` FOREIGN KEY (`funnel_id`) REFERENCES `analytics_funnels` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `analytics_seo_metric_snapshots`
--
ALTER TABLE `analytics_seo_metric_snapshots`
  ADD CONSTRAINT `analytics_seo_metric_snapshots_blog_post_id_foreign` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `analytics_sessions`
--
ALTER TABLE `analytics_sessions`
  ADD CONSTRAINT `analytics_sessions_acquisition_context_id_foreign` FOREIGN KEY (`acquisition_context_id`) REFERENCES `acquisition_contexts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `analytics_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `analytics_sessions_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `analytics_visitors` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `analytics_visitors`
--
ALTER TABLE `analytics_visitors`
  ADD CONSTRAINT `analytics_visitors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `blog_posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`);

--
-- Limitadores para a tabela `blog_post_tool`
--
ALTER TABLE `blog_post_tool`
  ADD CONSTRAINT `blog_post_tool_blog_post_id_foreign` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `organizations_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `organization_invitations`
--
ALTER TABLE `organization_invitations`
  ADD CONSTRAINT `organization_invitations_accepted_by_user_id_foreign` FOREIGN KEY (`accepted_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `organization_invitations_invited_by_user_id_foreign` FOREIGN KEY (`invited_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `organization_invitations_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `organization_members`
--
ALTER TABLE `organization_members`
  ADD CONSTRAINT `organization_members_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `organization_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `organization_seats`
--
ALTER TABLE `organization_seats`
  ADD CONSTRAINT `organization_seats_organization_member_id_foreign` FOREIGN KEY (`organization_member_id`) REFERENCES `organization_members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `organization_seats_organization_subscription_id_foreign` FOREIGN KEY (`organization_subscription_id`) REFERENCES `organization_subscriptions` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `organization_subscriptions`
--
ALTER TABLE `organization_subscriptions`
  ADD CONSTRAINT `organization_subscriptions_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `page_feedback`
--
ALTER TABLE `page_feedback`
  ADD CONSTRAINT `page_feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `platform_analytics_events`
--
ALTER TABLE `platform_analytics_events`
  ADD CONSTRAINT `platform_analytics_events_acquisition_context_id_foreign` FOREIGN KEY (`acquisition_context_id`) REFERENCES `acquisition_contexts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `platform_analytics_events_analytics_session_id_foreign` FOREIGN KEY (`analytics_session_id`) REFERENCES `analytics_sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `platform_analytics_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `platform_analytics_events_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `analytics_visitors` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `tool_runs`
--
ALTER TABLE `tool_runs`
  ADD CONSTRAINT `tool_runs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `tool_run_favorites`
--
ALTER TABLE `tool_run_favorites`
  ADD CONSTRAINT `tool_run_favorites_tool_run_id_foreign` FOREIGN KEY (`tool_run_id`) REFERENCES `tool_runs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tool_run_favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tool_usage_events`
--
ALTER TABLE `tool_usage_events`
  ADD CONSTRAINT `tool_usage_events_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tool_usage_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
