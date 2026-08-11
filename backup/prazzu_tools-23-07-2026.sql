-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23-Jul-2026 às 22:52
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
('4147178c-65e3-4ad4-a8e4-cd58294c58e6', '96ada320-03e3-4118-9d86-ecd5426f4a81', 1, '2026-07-23 13:45:21', '2026-07-23 14:56:57', NULL, 'http://localhost:8000/ferramentas/calculadora-ferias', '/ferramentas/calculadora-ferias', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', 'America/Sao_Paulo', '1920x1080', NULL, NULL, NULL, NULL, '2026-07-23 16:45:21', '2026-07-23 17:56:57'),
('f0ad4cdf-991a-4a07-8e48-893d53cac53e', '96ada320-03e3-4118-9d86-ecd5426f4a81', 1, '2026-07-23 16:25:26', '2026-07-23 18:46:54', NULL, 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-23 19:25:26', '2026-07-23 21:46:54'),
('fb782b61-b303-454c-875b-bb468b4bff5f', '96ada320-03e3-4118-9d86-ecd5426f4a81', 1, '2026-07-23 19:53:10', '2026-07-23 20:52:33', NULL, 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', 'America/Sao_Paulo', '1920x1080', NULL, NULL, NULL, NULL, '2026-07-23 22:53:10', '2026-07-23 23:52:33');

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

--
-- Extraindo dados da tabela `analytics_tool_presences`
--

INSERT INTO `analytics_tool_presences` (`id`, `tool_slug`, `visitor_id`, `analytics_session_id`, `user_id`, `path`, `source`, `country_code`, `region`, `city`, `last_seen_at`, `created_at`, `updated_at`) VALUES
('d2857271-1c09-4c0d-a606-94814a94fd35', 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, '/analytics/tools/presence', 'direct', NULL, NULL, NULL, '2026-07-23 23:11:04', '2026-07-23 23:08:19', '2026-07-23 23:11:04');

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
('96ada320-03e3-4118-9d86-ecd5426f4a81', 1, '2026-07-23 13:45:21', '2026-07-23 20:52:33', 'direct', 'none', NULL, '{\"source\":null,\"medium\":null,\"campaign\":null,\"term\":null,\"content\":null}', 'direct', 'none', NULL, '{\"source\":null,\"medium\":null,\"campaign\":null,\"term\":null,\"content\":null}', 'http://localhost:8000/', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', NULL, '2026-07-23 16:45:21', '2026-07-23 23:52:33');

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
(1, NULL, 'Como calcular honorários contábeis sem perder rentabilidade', 'como-calcular-honorarios-contabeis', 'Aprenda a formar honorários contábeis considerando regime tributário, volume de trabalho, complexidade, risco e margem desejada.', '<p>Aprenda a formar honorários contábeis considerando regime tributário, volume de trabalho, complexidade, risco e margem desejada.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Por que copiar a mensalidade do concorrente é arriscado</h2><p>O preço de um serviço contábil precisa remunerar horas técnicas, estrutura, tecnologia, responsabilidade profissional e risco. Quando o escritório replica valores de mercado sem conhecer sua própria operação, pode conquistar clientes que aumentam o faturamento e, ao mesmo tempo, reduzem a margem. A precificação sustentável começa pela realidade do escritório e depois é confrontada com o posicionamento desejado.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Mapeie o custo mensal de atendimento</h2><p>Liste o tempo previsto para escrituração, fiscal, folha, obrigações acessórias, atendimento, revisão e gestão. Some custos diretos, rateio de equipe, sistemas, certificados, armazenamento, estrutura e tributos do próprio escritório. Transforme esse total em custo por hora produtiva e considere que nem todas as horas contratadas são faturáveis.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Ajuste pelo perfil e pela complexidade do cliente</h2><p>Regime tributário, quantidade de funcionários, número de documentos, diversidade de operações, filiais, comércio exterior, retenções, substituição tributária e qualidade das informações mudam o esforço necessário. Clientes com atraso recorrente, documentos desorganizados ou elevado risco fiscal exigem uma reserva maior de capacidade.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Inclua margem, risco e escopo</h2><p>Depois de estimar o custo, aplique a margem necessária para financiar crescimento, treinamento, suporte e imprevistos. Registre claramente o que está incluído, limites de movimentação, serviços extraordinários e critérios de reajuste. Um preço bem explicado tende a ser melhor aceito do que um número apresentado sem contexto.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Revise periodicamente</h2><p>Reavalie honorários quando houver mudança de regime, aumento de faturamento, contratação de funcionários, abertura de filial ou crescimento do volume documental. A revisão periódica evita que o contrato permaneça defasado enquanto o trabalho cresce silenciosamente.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-de-honorarios-contabeis\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Existe uma tabela única de honorários contábeis?</h3><p>Não existe um valor nacional que represente todos os escritórios e clientes. Referências regionais podem ajudar, mas o preço final precisa refletir custo, escopo, risco e posicionamento.</p><h3>Devo cobrar por faturamento ou por volume?</h3><p>Os dois indicadores podem ser usados, mas nenhum deve ser isolado. O ideal é combinar porte, volume operacional, complexidade e esforço estimado.</p><h3>Quando reajustar o contrato?</h3><p>Além do reajuste periódico previsto em contrato, revise quando houver alteração relevante no perfil operacional do cliente.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 1, 'Gestão Contábil', 'blog/covers/como-calcular-honorarios-contabeis.png', 'Ilustração do guia: Como calcular honorários contábeis sem perder rentabilidade', 'published', 1, '2026-07-13 12:00:00', '2026-07-13 12:00:00', 'como calcular honorários contábeis', '[\"precificação contábil\", \"tabela de honorários contábeis\", \"valor mensalidade contabilidade\", \"custos do escritório contábil\"]', 'Como calcular honorários contábeis sem perder rentabilidade', 'Como calcular honorários contábeis com critérios objetivos, margem sustentável e ajustes por porte, regime e complexidade do cliente.', NULL, 'blog/social/como-calcular-honorarios-contabeis.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(2, NULL, 'Como validar CNPJ, CPF e inscrição estadual antes do cadastro', 'como-validar-cnpj-cpf-inscricao-estadual', 'Veja como identificar erros de digitação, dígitos verificadores inválidos e inconsistências cadastrais antes de importar ou registrar dados.', '<p>Veja como identificar erros de digitação, dígitos verificadores inválidos e inconsistências cadastrais antes de importar ou registrar dados.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Validação sintática não é consulta cadastral</h2><p>A validação do número confirma formato e dígitos verificadores. Ela elimina muitos erros de digitação, mas não prova que o cadastro está ativo, pertence à pessoa informada ou está regular perante órgãos públicos. Para decisões de risco, combine a validação matemática com fontes oficiais e documentos comprobatórios.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Por que validar antes de importar</h2><p>Um documento incorreto pode contaminar cadastros, notas, obrigações acessórias, relatórios e integrações. Corrigir o erro na entrada custa menos do que localizar o mesmo problema depois que ele se espalhou por diferentes sistemas.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Como tratar arquivos em lote</h2><p>Ao validar planilhas ou arquivos tabulares, preserve a linha original, normalize pontuação apenas para o cálculo e produza um relatório com valor informado, valor normalizado e motivo da rejeição. Não altere automaticamente números duvidosos; encaminhe-os para revisão.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Cuidados com inscrição estadual</h2><p>As regras da inscrição estadual variam por unidade federativa e podem ter tamanhos e algoritmos diferentes. Por isso, a UF é parte essencial da validação. Um número matematicamente válido para um estado não deve ser aceito como se fosse de outro.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Proteção de dados</h2><p>CPF e outros identificadores pessoais exigem finalidade, controle de acesso e retenção adequada. Evite incluir documentos completos em logs, URLs, capturas de tela ou arquivos de teste sem necessidade.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/validador-de-cnpj\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Um CNPJ válido está necessariamente ativo?</h3><p>Não. A validação dos dígitos não substitui a consulta da situação cadastral em fonte oficial.</p><h3>É seguro remover pontos e traços?</h3><p>Sim para normalização técnica, desde que o valor original seja preservado quando necessário para auditoria.</p><h3>Toda inscrição estadual tem a mesma regra?</h3><p>Não. Cada UF pode adotar formato e algoritmo próprios.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 2, 'Cadastros e Validações', 'blog/covers/como-validar-cnpj-cpf-inscricao-estadual.png', 'Ilustração do guia: Como validar CNPJ, CPF e inscrição estadual antes do cadastro', 'published', 1, '2026-07-14 12:00:00', '2026-07-14 12:00:00', 'validar CNPJ CPF inscrição estadual', '[\"validador de CNPJ\", \"consulta CPF\", \"validação inscrição estadual\", \"documentos cadastrais\"]', 'Como validar CNPJ, CPF e inscrição estadual antes do cadastro', 'Valide CNPJ, CPF e inscrição estadual antes do cadastro e reduza erros em documentos fiscais, integrações e rotinas contábeis.', NULL, 'blog/social/como-validar-cnpj-cpf-inscricao-estadual.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(3, NULL, 'DARF e GPS: como conferir código, vencimento, juros e multa', 'como-gerar-darf-gps-codigo-vencimento-acrescimos', 'Entenda os dados que precisam ser revisados antes de emitir DARF ou GPS e como documentar a memória de cálculo dos acréscimos.', '<p>Entenda os dados que precisam ser revisados antes de emitir DARF ou GPS e como documentar a memória de cálculo dos acréscimos.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Comece pela obrigação correta</h2><p>A emissão da guia depende da natureza do débito, período de apuração, contribuinte e código de receita. Um código incorreto pode direcionar o pagamento para obrigação diferente e gerar pendência mesmo quando o valor foi recolhido.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Confirme período e vencimento</h2><p>Competência, período de apuração e data de vencimento não são campos equivalentes. Feriados e regras específicas também podem alterar a data efetiva. Registre a base normativa e a data de referência usada na geração.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Acréscimos por atraso</h2><p>Quando houver atraso, a memória deve separar principal, multa e juros. O cálculo precisa considerar a regra aplicável ao tributo e a data prevista de pagamento. Evite lançar apenas o total: a decomposição facilita revisão e conciliação.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Dados do contribuinte</h2><p>Revise CPF ou CNPJ, nome, identificação do débito, código, referência e valor. Em rotinas com muitos clientes, use dupla conferência ou validações automatizadas antes da emissão definitiva.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Guarde evidências</h2><p>Salve a memória de cálculo, parâmetros, data de geração e responsável pela revisão. Isso reduz retrabalho quando o cliente pergunta como o valor foi formado ou quando ocorre divergência na baixa do pagamento.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/gerador-darf-gps\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>DARF e GPS são a mesma guia?</h3><p>Não. São documentos distintos e atendem obrigações específicas.</p><h3>Posso usar qualquer código de receita parecido?</h3><p>Não. O código precisa corresponder exatamente à obrigação e ao enquadramento do débito.</p><h3>Como evitar divergência de juros?</h3><p>Use a data real de pagamento, a regra aplicável e preserve a memória de cálculo para conferência.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/como-gerar-darf-gps-codigo-vencimento-acrescimos.png', 'Ilustração do guia: DARF e GPS: como conferir código, vencimento, juros e multa', 'published', 1, '2026-07-15 12:00:00', '2026-07-15 12:00:00', 'como gerar DARF e GPS', '[\"código DARF\", \"vencimento DARF\", \"juros e multa DARF\", \"guia GPS\"]', 'DARF e GPS: como conferir código, vencimento, juros e multa', 'Confira código, período, vencimento, multa e juros antes de gerar DARF ou GPS e mantenha uma memória de cálculo auditável.', NULL, 'blog/social/como-gerar-darf-gps-codigo-vencimento-acrescimos.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(4, NULL, 'Como converter XML de NF-e e NFC-e em planilha com segurança', 'como-converter-xml-nfe-nfce-planilha', 'Aprenda a extrair produtos, NCM, CFOP, impostos e totais de arquivos XML sem perder rastreabilidade nem misturar documentos inválidos.', '<p>Aprenda a extrair produtos, NCM, CFOP, impostos e totais de arquivos XML sem perder rastreabilidade nem misturar documentos inválidos.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>O XML é a fonte estruturada</h2><p>O PDF auxiliar facilita a leitura humana, mas o XML contém os campos estruturados usados em validações e integrações. Para análise em lote, prefira o XML autorizado e preserve o arquivo original junto ao resultado.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Defina a granularidade da planilha</h2><p>Uma nota pode gerar uma linha de cabeçalho e várias linhas de itens. Antes de exportar, decida se a análise será por documento, produto ou tributo. Misturar granularidades em uma única tabela costuma causar duplicidade de totais.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Campos importantes</h2><p>Chave de acesso, emitente, destinatário, data, modelo, série, número, CFOP, NCM, CST ou CSOSN, valores de produtos, descontos, frete e tributos devem ser tratados com tipos adequados. Identificadores longos devem ser exportados como texto para não perder zeros ou precisão.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Validações de consistência</h2><p>Compare soma dos itens com totais, identifique XML duplicado, documento cancelado, campos ausentes e diferenças de arredondamento. Alertas não devem apagar dados; devem acompanhar a linha para revisão.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Segurança e retenção</h2><p>Arquivos fiscais podem conter dados comerciais e pessoais. Restrinja acesso, evite compartilhamentos públicos e defina política de retenção para originais e exportações.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/conversor-fiscal-xml\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Posso converter o DANFE em vez do XML?</h3><p>O DANFE não substitui o XML e pode omitir estrutura necessária para uma extração confiável.</p><h3>Por que a chave aparece em notação científica no Excel?</h3><p>Porque planilhas podem interpretar identificadores longos como números. Exporte a chave como texto.</p><h3>Como tratar XML duplicado?</h3><p>Use a chave de acesso como referência e sinalize duplicidades sem descartar silenciosamente os arquivos.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/como-converter-xml-nfe-nfce-planilha.png', 'Ilustração do guia: Como converter XML de NF-e e NFC-e em planilha com segurança', 'published', 0, '2026-07-16 12:00:00', '2026-07-16 12:00:00', 'converter XML NF-e para planilha', '[\"XML para Excel\", \"extrair dados XML NF-e\", \"converter NFC-e\", \"planilha de notas fiscais\"]', 'Como converter XML de NF-e e NFC-e em planilha com segurança', 'Converta XML de NF-e e NFC-e em planilha preservando produtos, NCM, CFOP, impostos, totais e alertas de consistência.', NULL, 'blog/social/como-converter-xml-nfe-nfce-planilha.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(5, NULL, 'Como calcular rescisão trabalhista passo a passo', 'como-calcular-rescisao-trabalhista', 'Confira as principais verbas da rescisão, os dados de entrada e os pontos que mudam conforme motivo do desligamento e datas do contrato.', '<p>Confira as principais verbas da rescisão, os dados de entrada e os pontos que mudam conforme motivo do desligamento e datas do contrato.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>O motivo do desligamento muda o cálculo</h2><p>Pedido de demissão, dispensa sem justa causa, justa causa, acordo e término de contrato produzem direitos diferentes. A primeira etapa é classificar corretamente o desligamento e registrar datas de admissão, comunicação e término.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Saldo de salário</h2><p>Calcule os dias trabalhados no mês da saída com base na regra aplicável ao salário mensal e considere adicionais habituais quando integrarem a remuneração. Faltas, adiantamentos e outras ocorrências precisam de documentação.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Férias e décimo terceiro</h2><p>Verifique períodos vencidos, proporcionais, faltas que afetam o direito, terço constitucional e avos do décimo terceiro. O tratamento de médias variáveis exige atenção às verbas e ao período de apuração.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Aviso-prévio e FGTS</h2><p>Defina se o aviso é trabalhado ou indenizado e considere a projeção quando aplicável. Depósitos de FGTS, multa rescisória e movimentação da conta dependem do tipo de desligamento.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Conferência final</h2><p>Separe proventos e descontos, registre as bases de INSS e IRRF e confronte o resultado com documentos do contrato. Uma memória detalhada facilita a revisão antes do pagamento e da transmissão dos eventos.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-de-rescisao\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Toda rescisão tem multa de 40% do FGTS?</h3><p>Não. A incidência depende do motivo do desligamento.</p><h3>Aviso indenizado projeta o contrato?</h3><p>Em situações aplicáveis, a projeção repercute em datas e verbas; o caso deve ser conferido conforme a regra vigente.</p><h3>Férias vencidas e proporcionais são iguais?</h3><p>Não. Elas se referem a períodos diferentes e podem ter tratamentos distintos.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 4, 'Trabalhista', 'blog/covers/como-calcular-rescisao-trabalhista.png', 'Ilustração do guia: Como calcular rescisão trabalhista passo a passo', 'published', 0, '2026-07-17 12:00:00', '2026-07-17 12:00:00', 'como calcular rescisão trabalhista', '[\"cálculo de rescisão\", \"aviso prévio\", \"férias proporcionais\", \"multa FGTS\"]', 'Como calcular rescisão trabalhista passo a passo', 'Aprenda a calcular rescisão trabalhista com saldo salarial, férias, 13º, aviso-prévio, FGTS e descontos aplicáveis.', NULL, 'blog/social/como-calcular-rescisao-trabalhista.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(6, NULL, 'Margem e markup: como calcular o preço de venda corretamente', 'margem-markup-como-calcular-preco-venda', 'Entenda a diferença entre margem e markup e monte um preço de venda que cubra custos, despesas, tributos e lucro desejado.', '<p>Entenda a diferença entre margem e markup e monte um preço de venda que cubra custos, despesas, tributos e lucro desejado.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Margem e markup não são sinônimos</h2><p>A margem mede o lucro em relação ao preço de venda. O markup é um multiplicador aplicado sobre uma base de custo. Usar o mesmo percentual nas duas abordagens gera resultados diferentes e pode deixar o preço abaixo do necessário.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Mapeie todos os componentes</h2><p>Considere custo de aquisição ou produção, frete, embalagem, comissões, taxas de meios de pagamento, tributos sobre venda, perdas e despesas variáveis. Custos fixos podem ser incorporados por rateio ou tratados na análise de ponto de equilíbrio.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Defina o lucro desejado</h2><p>O lucro deve ser definido sobre uma base clara. Para trabalhar com margem sobre venda, despesas percentuais e lucro disputam o mesmo preço final. Por isso, somar percentuais diretamente ao custo nem sempre alcança a margem pretendida.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Teste cenários</h2><p>Compare preço atual, preço calculado, volume esperado e sensibilidade a descontos. Um desconto aparentemente pequeno pode consumir grande parte do lucro quando a margem é estreita.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Revise com dados reais</h2><p>Depois de aplicar o preço, acompanhe custo efetivo, impostos, devoluções e comissões. A formação de preço é um processo contínuo, não um cálculo feito uma única vez.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-margem-markup\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Markup de 50% significa margem de 50%?</h3><p>Não. Um custo de 100 com markup de 50% gera preço de 150 e margem bruta de aproximadamente 33,3%.</p><h3>Tributos entram no custo?</h3><p>Tributos incidentes sobre a venda normalmente precisam ser considerados na formação do preço, respeitando o regime e a operação.</p><h3>Posso usar a mesma margem para todos os produtos?</h3><p>Pode ser inadequado. Giro, risco, concorrência, perdas e capital empregado variam entre produtos e serviços.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 1, 'Gestão Contábil', 'blog/covers/margem-markup-como-calcular-preco-venda.png', 'Ilustração do guia: Margem e markup: como calcular o preço de venda corretamente', 'published', 0, '2026-07-18 12:00:00', '2026-07-18 12:00:00', 'como calcular margem e markup', '[\"calculadora de markup\", \"margem de lucro\", \"formação de preço\", \"preço de venda\"]', 'Margem e markup: como calcular o preço de venda corretamente', 'Entenda margem e markup e calcule um preço de venda que cubra custos, despesas, tributos e lucro sem confundir percentuais.', NULL, 'blog/social/margem-markup-como-calcular-preco-venda.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(7, NULL, 'Pró-labore e distribuição de lucros: como definir valores', 'pro-labore-distribuicao-lucros-como-definir', 'Veja como separar remuneração pelo trabalho e retorno do capital, estimar encargos e documentar a distribuição de lucros aos sócios.', '<p>Veja como separar remuneração pelo trabalho e retorno do capital, estimar encargos e documentar a distribuição de lucros aos sócios.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>São naturezas diferentes</h2><p>Pró-labore remunera o trabalho do sócio na empresa. Distribuição de lucros representa o retorno do resultado empresarial. Tratar toda retirada como lucro sem avaliar atividade, escrituração e resultado pode criar risco fiscal e previdenciário.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Definição do pró-labore</h2><p>A empresa deve observar função exercida, capacidade financeira, referências de mercado e regras previdenciárias. O valor precisa ser formalizado e processado com os encargos correspondentes, incluindo retenções quando aplicáveis.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Apuração dos lucros</h2><p>A distribuição depende de lucro efetivamente apurado e de suporte contábil. Balancetes, demonstrações, registros societários e separação entre patrimônio pessoal e empresarial fortalecem a evidência da operação.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Vários sócios</h2><p>Critérios de participação podem seguir quotas ou disposições societárias válidas. Quando as retiradas não acompanham a participação, documente a justificativa e verifique as condições legais e contratuais.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Simule antes de decidir</h2><p>Compare diferentes valores de pró-labore, encargos, caixa disponível e resultado acumulado. A simulação não substitui a análise jurídica e contábil, mas ajuda a identificar cenários inviáveis e dados ausentes.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-pro-labore-distribuicao-lucros\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Sócio que trabalha precisa receber pró-labore?</h3><p>A situação deve ser analisada conforme atuação e regras aplicáveis; simplesmente classificar toda retirada como lucro pode ser inadequado.</p><h3>Lucro distribuído é sempre isento?</h3><p>A tributação depende do atendimento das condições legais e da comprovação do lucro.</p><h3>Posso distribuir lucro todo mês?</h3><p>É possível haver antecipações ou distribuições periódicas quando suportadas por apuração e documentação adequadas.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/pro-labore-distribuicao-lucros-como-definir.png', 'Ilustração do guia: Pró-labore e distribuição de lucros: como definir valores', 'published', 0, '2026-07-19 12:00:00', '2026-07-19 12:00:00', 'pró-labore e distribuição de lucros', '[\"cálculo pró-labore\", \"INSS pró-labore\", \"IRRF pró-labore\", \"lucros isentos\"]', 'Pró-labore e distribuição de lucros: como definir valores', 'Entenda como definir pró-labore e distribuição de lucros, estimar INSS e IRRF e manter documentação contábil adequada.', NULL, 'blog/social/pro-labore-distribuicao-lucros-como-definir.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(8, NULL, 'Simples Nacional: como calcular DAS, alíquota efetiva e Fator R', 'simples-nacional-como-calcular-das-fator-r', 'Aprenda a usar receita acumulada, faixa, parcela a deduzir e Fator R para estimar o DAS com memória de cálculo.', '<p>Aprenda a usar receita acumulada, faixa, parcela a deduzir e Fator R para estimar o DAS com memória de cálculo.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Identifique atividade e anexo</h2><p>O cálculo começa pelo enquadramento da receita no anexo adequado. Uma mesma empresa pode ter receitas sujeitas a tratamentos diferentes, por isso a segregação correta é tão importante quanto a fórmula.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Use a receita acumulada de doze meses</h2><p>A faixa é determinada pela receita bruta acumulada nos doze meses anteriores ao período de apuração. Empresas em início de atividade podem seguir regras de proporcionalização. Não confunda receita do mês com receita acumulada.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Alíquota efetiva</h2><p>A alíquota nominal da tabela não é aplicada isoladamente. A fórmula considera receita acumulada, alíquota nominal e parcela a deduzir. O resultado é a alíquota efetiva usada sobre a receita segregada do período.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Fator R</h2><p>Para determinadas atividades, a relação entre folha e receita acumuladas pode direcionar a tributação entre anexos. Folha, pró-labore e encargos devem ser apurados de forma consistente no período correspondente.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Memória e conferência</h2><p>Guarde anexo, faixa, receitas, folha, parcela a deduzir e resultado da alíquota. Compare a estimativa com o sistema oficial e investigue divergências de segregação, período ou classificação.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-simples-nacional\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>A alíquota da primeira faixa é sempre o percentual pago?</h3><p>A carga efetiva depende da fórmula e da composição da receita, embora na primeira faixa possa coincidir em situações simples.</p><h3>O que entra no Fator R?</h3><p>A composição deve seguir as regras vigentes para folha e receita no período considerado.</p><h3>Posso somar todas as receitas e aplicar uma única alíquota?</h3><p>Nem sempre. Receitas podem exigir segregações por atividade, anexo ou tratamento tributário.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/simples-nacional-como-calcular-das-fator-r.png', 'Ilustração do guia: Simples Nacional: como calcular DAS, alíquota efetiva e Fator R', 'published', 0, '2026-07-20 12:00:00', '2026-07-20 12:00:00', 'como calcular Simples Nacional', '[\"calculadora Simples Nacional\", \"alíquota efetiva\", \"Fator R\", \"anexos Simples Nacional\"]', 'Simples Nacional: como calcular DAS, alíquota efetiva e Fator R', 'Calcule Simples Nacional com receita acumulada, faixa, alíquota efetiva, anexo e Fator R, entendendo cada etapa do DAS.', NULL, 'blog/social/simples-nacional-como-calcular-das-fator-r.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20'),
(9, NULL, 'Simples, Lucro Presumido ou Lucro Real: como comparar regimes', 'simples-lucro-presumido-lucro-real-comparacao', 'Veja quais premissas usar para comparar regimes tributários e por que faturamento isolado não é suficiente para decidir.', '<p>Veja quais premissas usar para comparar regimes tributários e por que faturamento isolado não é suficiente para decidir.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Comparação exige premissas equivalentes</h2><p>Os regimes usam bases, períodos e tributos diferentes. Para comparar, aplique o mesmo horizonte de receita, atividade, folha, margem, despesas e localização. Resultados construídos com premissas diferentes não são comparáveis.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Simples Nacional</h2><p>Avalie anexos, segregações, Fator R, sublimites, substituição tributária e tributos recolhidos fora do documento único. A simplicidade operacional tem valor, mas não elimina a necessidade de conferir a carga total.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Lucro Presumido</h2><p>Considere percentuais de presunção por atividade, IRPJ, adicional, CSLL, PIS, Cofins, ISS ou ICMS e encargos sobre folha. Margens reais muito diferentes da presunção podem alterar a atratividade.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Lucro Real</h2><p>A análise depende do lucro tributável, adições, exclusões, compensações e créditos permitidos. Empresas com margem baixa ou determinadas estruturas de custos podem encontrar vantagens, mas a conformidade tende a ser mais exigente.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Decisão e sensibilidade</h2><p>Não escolha apenas pelo menor valor de um mês. Simule crescimento, queda de margem, contratação, mudança de atividade e sazonalidade. Inclua custo operacional, risco e capacidade de manter controles compatíveis com o regime.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/comparador-tributario\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>O regime com menor imposto sempre é o melhor?</h3><p>Não. Custos de conformidade, fluxo de caixa, riscos e limitações operacionais também fazem parte da decisão.</p><h3>Uma simulação substitui planejamento tributário?</h3><p>Não. Ela organiza premissas e cenários, mas a decisão requer validação técnica conforme a empresa.</p><h3>Quando revisar o regime?</h3><p>Revise antes do período de opção e sempre que houver mudança relevante em receita, margem, atividade ou estrutura.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 3, 'Fiscal e Tributário', 'blog/covers/simples-lucro-presumido-lucro-real-comparacao.png', 'Ilustração do guia: Simples, Lucro Presumido ou Lucro Real: como comparar regimes', 'published', 0, '2026-07-21 12:00:00', '2026-07-21 12:00:00', 'comparar regimes tributários', '[\"Simples ou Lucro Presumido\", \"Lucro Real ou Presumido\", \"planejamento tributário\", \"comparador tributário\"]', 'Simples, Lucro Presumido ou Lucro Real: como comparar regimes', 'Compare Simples Nacional, Lucro Presumido e Lucro Real usando receita, margem, folha, créditos e custos de conformidade.', NULL, 'blog/social/simples-lucro-presumido-lucro-real-comparacao.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(10, NULL, 'Como calcular férias, terço constitucional, abono e prazos', 'como-calcular-ferias-dias-abono-prazos', 'Entenda período aquisitivo, dias de direito, remuneração, médias, terço constitucional, venda de dias e prazos de concessão.', '<p>Entenda período aquisitivo, dias de direito, remuneração, médias, terço constitucional, venda de dias e prazos de concessão.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> revise os dados de entrada, preserve a memória de cálculo e valide o resultado antes de tomar decisões ou transmitir informações oficiais.</div><h2>Período aquisitivo e concessivo</h2><p>O direito é formado ao longo do período aquisitivo e deve ser concedido dentro do período correspondente. Datas de admissão, afastamentos e faltas relevantes precisam ser consideradas antes de calcular valores.</p><p>Na prática, vale criar um checklist padronizado para que a análise não dependa apenas da memória de quem executa a rotina. Campos obrigatórios, fonte de cada dado e data de referência devem ficar visíveis para revisão.</p><h2>Dias de direito</h2><p>A quantidade de dias pode ser afetada por ocorrências previstas em lei. Não reduza férias apenas com base em faltas sem conferir a natureza e a faixa aplicável.</p><p>Quando houver mais de um cenário possível, não esconda as premissas. Mostre o que muda entre eles e quais dados precisam ser confirmados. Isso transforma o cálculo em uma decisão auditável, e não apenas em um número final.</p><h2>Remuneração e médias</h2><p>Parta da remuneração devida na época da concessão e avalie médias de horas extras, adicionais e outras parcelas variáveis quando integrarem a base. Documente o período e o critério usado nas médias.</p><p>Automação ajuda a reduzir digitação e repetir fórmulas, mas não substitui o enquadramento técnico. Use alertas para destacar inconsistências e mantenha os dados originais disponíveis para conferência.</p><h2>Terço e abono pecuniário</h2><p>O terço constitucional incide sobre a remuneração de férias conforme a regra aplicável. O abono converte parte dos dias em valor, mas depende de requisitos e prazo de solicitação. Diferencie abono pecuniário de adiantamento salarial.</p><p>Também é recomendável registrar quem realizou a análise, quando ela foi feita e qual versão das regras foi considerada. Essa trilha reduz retrabalho em revisões futuras.</p><h2>Planejamento e pagamento</h2><p>Além do cálculo, verifique comunicação, início do descanso, feriados, parcelamento permitido e prazo de pagamento. Planejar férias evita concentração de ausências e reduz risco de concessão fora do prazo.</p><p>Antes de concluir, confronte o resultado com documentos de suporte e com a regra vigente na data de referência. Em caso de divergência, investigue a origem em vez de ajustar o valor manualmente sem justificativa.</p><h2>Passo a passo recomendado</h2><ol><li>Reúna documentos e dados do período correto.</li><li>Normalize formatos sem apagar o valor original.</li><li>Informe premissas e datas de referência.</li><li>Execute o cálculo ou validação.</li><li>Revise alertas, bases e memória detalhada.</li><li>Salve a evidência necessária e só então use o resultado.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada a este guia para organizar os dados, visualizar a memória do resultado e identificar pontos que precisam de conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-ferias\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>Férias são sempre de 30 dias?</h3><p>O direito pode variar conforme ocorrências e regras aplicáveis ao período.</p><h3>O empregado pode vender todas as férias?</h3><p>Não. O abono pecuniário é limitado e depende de solicitação no prazo adequado.</p><h3>Horas extras entram nas férias?</h3><p>Quando habituais e conforme as regras aplicáveis, podem compor médias da remuneração.</p><h2>Conclusão</h2><p>Um bom resultado depende tanto da fórmula quanto da qualidade das informações utilizadas. Padronize a coleta, documente premissas, revise alertas e mantenha a memória de cálculo. Assim, a ferramenta acelera a rotina sem retirar do profissional a decisão técnica.</p>', 4, 'Trabalhista', 'blog/covers/como-calcular-ferias-dias-abono-prazos.png', 'Ilustração do guia: Como calcular férias, terço constitucional, abono e prazos', 'published', 0, '2026-07-22 12:00:00', '2026-07-22 12:00:00', 'como calcular férias', '[\"calculadora de férias\", \"terço constitucional\", \"abono pecuniário\", \"prazo pagamento férias\"]', 'Como calcular férias, terço constitucional, abono e prazos', 'Calcule férias com período aquisitivo, dias de direito, médias, terço constitucional, abono pecuniário e prazos principais.', NULL, 'blog/social/como-calcular-ferias-dias-abono-prazos.png', 1, '2026-07-23 11:45:13', '2026-07-23 11:51:20');

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
(41, '2026_07_22_001000_create_page_feedback_table', 1),
(42, '2026_07_23_000500_create_receipt_party_profiles_table', 2);

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

--
-- Extraindo dados da tabela `page_feedback`
--

INSERT INTO `page_feedback` (`id`, `user_id`, `session_id`, `path`, `url`, `page_title`, `rating`, `comment`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', '/', 'http://localhost:8000', 'Prazzu Tools — Ferramentas para contabilidade', 5, 'essa pagina é facil de entender e facil e encontrar as coisas que eu procuro fora que é muito bonita também', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 15:58:15', '2026-07-23 15:58:15');

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
(205, '7100896d-086e-4ad7-a5b7-00f7bdf2e466', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas/calculadora-ferias', '/ferramentas/calculadora-ferias', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 16:45:21'),
(206, '236d76d1-6962-46cc-bc72-1df8fe9c4fe0', 'tool.opened', 1, 'tool', 'tool', NULL, 'calculadora-ferias', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas/calculadora-ferias', '/ferramentas/calculadora-ferias', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.calculadora-ferias.index\",\"method\":\"GET\"}', '2026-07-23 16:45:21'),
(207, 'db207164-39e4-46a5-8c41-430854520406', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/calculadora-ferias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 16:45:42'),
(208, '07cafca1-b6b3-4d82-aa5b-49e6979bce4c', 'audience.context_captured', 1, 'audience', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/analytics/audience', '/analytics/audience', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt-PT', 'America/Sao_Paulo', '1920x1080', NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"timezone\":\"America\\/Sao_Paulo\",\"screen_resolution\":\"1920x1080\",\"language\":\"pt-PT\"}', '2026-07-23 16:45:44'),
(209, 'f45468e4-80cd-4a93-964c-616b1fd136cf', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 16:46:04'),
(210, 'aa18914e-6417-47ea-9985-d7486c0e54d5', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 16:46:39'),
(211, '9739b332-b5b8-478b-8c4d-17902d09b222', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas/calculadoras', '/ferramentas/calculadoras', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 16:46:52'),
(212, 'f0358fff-e6a2-4648-a8a0-c88cdf5f9aa4', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas/calculadora-margem-markup', '/ferramentas/calculadora-margem-markup', 'http://localhost:8000/ferramentas/calculadoras', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 16:47:28'),
(213, 'eb6addab-7426-4873-a65e-b7a83e4c7032', 'tool.opened', 1, 'tool', 'tool', NULL, 'calculadora-margem-markup', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas/calculadora-margem-markup', '/ferramentas/calculadora-margem-markup', 'http://localhost:8000/ferramentas/calculadoras', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.calculadora-margem-markup.index\",\"method\":\"GET\"}', '2026-07-23 16:47:28'),
(214, '9b3c5117-45c7-4d85-b334-3fb517d691bd', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/calculadora-margem-markup', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 16:47:57'),
(215, '4a514fd4-2063-4ac8-b5ab-f02b01c8f576', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'calculadora-margem-markup', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/calculadora-margem-markup', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":29}', '2026-07-23 16:47:58'),
(216, '569f2bfe-6ddb-4766-b91a-2ae99a810937', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'calculadora-ferias', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/calculadora-ferias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":1143}', '2026-07-23 17:04:24'),
(217, '18ada461-53d9-48ba-bb28-96c98eb60027', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', 'http://localhost:8000/admin/analytics', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:04:47'),
(218, '70632c9b-5868-4391-b5ef-89573b77a150', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 17:05:13'),
(219, '2bb29886-5356-431e-8872-fc5be33a83be', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:05:13'),
(220, 'e93e0216-90d3-4080-928d-73328f3fc779', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 17:05:13'),
(221, 'b9b1d633-77de-40b2-8c84-941ed3121f55', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":50}', '2026-07-23 17:05:14'),
(222, 'be96af7b-fa19-4cc2-aba9-daf8ecb3e90d', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":75}', '2026-07-23 17:05:15'),
(223, 'b89ef345-26fe-4a1e-857b-43f5ea14808a', 'blog.reading.completed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 17:05:15'),
(224, '67bf40d9-5193-493a-9962-d61a922948b7', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":100}', '2026-07-23 17:05:16'),
(225, 'd15fd123-b28b-4af9-839e-2f459770d891', 'blog.reading.started', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 17:05:17'),
(226, 'b9ff305e-bd7b-441d-861d-9bc8c9162876', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 17:08:20'),
(227, '421cebbd-ad89-4451-b649-09b90efff3b4', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:08:21'),
(228, 'a0c98bc3-f497-467c-8c3f-8bae9fda5b8c', 'blog.time.spent', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"seconds\":188}', '2026-07-23 17:08:21'),
(229, '63a96b3d-3491-41eb-8646-2f5a4457c150', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 17:08:21'),
(230, '5a3f49a7-6f0b-4d2e-8dd5-fb8e23222e4e', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":50}', '2026-07-23 17:08:22'),
(231, '5b929967-0855-4dc8-b572-70ba74158394', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":75}', '2026-07-23 17:08:22'),
(232, '7b1a9793-cc7d-4db0-aa36-102bd2e6d2f3', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":100}', '2026-07-23 17:08:22'),
(233, 'a3e38ace-188c-4b76-99a0-a4ec3aa212af', 'blog.reading.completed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 17:08:22'),
(234, 'a3306ebc-c020-4ce7-b9b9-72cae49f2578', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000', '//', 'http://localhost:8000/admin/analytics/reports', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:16:50'),
(235, '78192d49-f08f-42a3-b360-2c9510a5c6f6', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/criar-conta', '/criar-conta', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:16:52'),
(236, 'b8a53fba-c1ea-4feb-8009-7c9b85b2d2b6', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/ferramentas/geradores', '/ferramentas/geradores', 'http://localhost:8000/criar-conta', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:16:58'),
(237, 'ca831b96-119c-4330-9ac6-0b46a5a9c50f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/entrar', '/entrar', 'http://localhost:8000/ferramentas/geradores', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:17:03'),
(238, '613f39d9-7fda-4c88-b6d3-217ce0c56d85', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', 'http://localhost:8000/criar-conta', '/criar-conta', 'http://localhost:8000/entrar', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:17:05'),
(239, '7502fbb3-be01-4682-8b0d-4620346e5a60', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/minha-conta', '/minha-conta', 'http://localhost:8000/criar-conta', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:17:40'),
(240, 'adeee28b-f829-4072-a0f1-1b3605183ac8', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/outros', '/ferramentas/outros', 'http://localhost:8000/minha-conta', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:30'),
(241, 'faee82ae-03d5-4f91-a401-6a92b56c4961', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/geradores', '/ferramentas/geradores', 'http://localhost:8000/ferramentas/outros', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:32'),
(242, 'fc43338c-d7af-4975-b6ab-07bd67c55a9e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/ferramentas/geradores', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:35'),
(243, '06705e21-98dc-402e-9b47-34d709490df8', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:40'),
(244, '61ab293f-960b-4f4f-850b-aa26b97373a5', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:45'),
(245, '39be197c-98b8-46a1-ba06-207ce579c6c4', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:47'),
(246, '0faccd5a-74eb-4776-af65-1251e35806fa', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:52'),
(247, '1e0d5ae9-a75f-4fba-83d1-84f69f6c6b6b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:18:55'),
(248, '98dc106c-7028-42ec-928c-3602e52a1dff', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/novidades', '/recursos/novidades', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:19:00'),
(249, '29af0ff4-372b-4b2e-bd58-ac16aa032022', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos/novidades', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:19:05'),
(250, 'fa3ad8be-5d77-4ef5-b423-268ddf9dd8a3', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:19:10'),
(251, '08bb0c0e-fa0f-4a4c-9d6a-6ee537396b75', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:19:15'),
(252, '329e7bb9-44f0-46b0-b8cc-3e1120b04f69', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:19:55'),
(253, 'ea5f6e81-a732-498d-a2ef-aa5dc034b66a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:19:59'),
(254, '98918219-4d96-421c-942a-cc70682a313e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:21:41'),
(255, '66097786-8dad-4f98-b7ff-0dd541d7cfa0', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/outros', '/ferramentas/outros', 'http://localhost:8000/admin/analytics/reports', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:34:58'),
(256, 'bfa68b5a-66c5-42bc-9814-bb08e3b5215f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/outros', '/ferramentas/outros', 'http://localhost:8000/admin/analytics/reports', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:55:51'),
(257, '4f7236d2-af14-4be6-a6fb-ed58418983b9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/ferramentas/outros', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:55:54'),
(258, 'd60ff561-97b3-43a6-9e84-7cf3b33f0d90', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:55:58'),
(259, '51701923-985f-4115-856b-176ffd21065c', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:56:49'),
(260, 'f02e3211-fe8e-46ef-97bd-287b6f02008c', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/calculadora-de-honorarios-contabeis', '/ferramentas/calculadora-de-honorarios-contabeis', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 17:56:55'),
(261, '0853dae3-391a-485c-b190-083e2ff60174', 'tool.opened', 1, 'tool', 'tool', NULL, 'calculadora-de-honorarios-contabeis', '96ada320-03e3-4118-9d86-ecd5426f4a81', '4147178c-65e3-4ad4-a8e4-cd58294c58e6', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/calculadora-de-honorarios-contabeis', '/ferramentas/calculadora-de-honorarios-contabeis', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.calculadora-de-honorarios-contabeis.index\",\"method\":\"GET\"}', '2026-07-23 17:56:55'),
(262, '4c82c45a-646c-4fb2-a4b5-1813e0d41c3b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:25:26'),
(263, 'd0bac419-7bc1-4144-8eeb-22cefe0c2cdd', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', '/recursos/guias/precificacao-de-honorarios-contabeis', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:25:28'),
(264, '99bed748-5266-488b-8baf-798f2da2f0ae', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:26:17'),
(265, 'e8e2927a-fde0-4f19-b320-31dde91b089e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', '/recursos/guias/precificacao-de-honorarios-contabeis', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:26:33'),
(266, '716a7ee7-05f5-4bdf-98a9-77fd62ed1964', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', '/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:27:08'),
(267, '4aadf546-25e5-40bf-8a3b-0f80c7ab72c0', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:27:23'),
(268, 'af3b7a51-22c3-444b-8137-0b184f032e62', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:27:26'),
(269, '42368e00-bc32-4d5f-9a52-a7e3a1dfd7a3', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', '/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:27:28'),
(270, '20b29292-7e27-43cb-84be-4ababbf7f754', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/calculadora-de-honorarios-contabeis', '/ferramentas/calculadora-de-honorarios-contabeis', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:27:37');
INSERT INTO `platform_analytics_events` (`id`, `event_id`, `event_name`, `schema_version`, `channel`, `subject_type`, `subject_id`, `subject_slug`, `visitor_id`, `analytics_session_id`, `user_id`, `session_id`, `url`, `path`, `referrer`, `source`, `medium`, `campaign`, `acquisition_context_id`, `acquisition_keyword`, `acquisition_campaign_identifier`, `acquisition_primary_tool_slug`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `device_type`, `browser`, `operating_system`, `language`, `timezone`, `screen_resolution`, `country_code`, `region`, `city`, `ip_hash`, `user_agent`, `metadata`, `occurred_at`) VALUES
(271, '364a6bf7-d927-46fa-ae52-749f934f4f3a', 'tool.opened', 1, 'tool', 'tool', NULL, 'calculadora-de-honorarios-contabeis', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/calculadora-de-honorarios-contabeis', '/ferramentas/calculadora-de-honorarios-contabeis', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.calculadora-de-honorarios-contabeis.index\",\"method\":\"GET\"}', '2026-07-23 19:27:37'),
(272, 'f01aa751-cfde-4633-b8e6-47b1683c3aac', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', '/recursos/guias/precificacao-de-honorarios-contabeis', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:41:14'),
(273, '4bc92ddf-21b0-4d6d-bb2b-6e9af4167ade', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', '/recursos/guias/precificacao-de-honorarios-contabeis', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:46:57'),
(274, 'f5112d1b-d5f7-4977-969e-bd3ecff5003c', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:00'),
(275, '85e304af-d925-4f8d-a6bb-45d08bfe8c7b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:08'),
(276, '62fe3243-90fb-4f59-b1b2-83cda2e70278', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:10'),
(277, '1b6694e7-2d11-4af1-957d-79f5da823323', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:15'),
(278, '75aa30c3-4f2b-4519-a844-5c2bc073e206', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:32'),
(279, '3cbab2e4-f683-47af-9eb8-4e0ca8c2929c', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', '/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:34'),
(280, 'ac454bcb-5998-4c86-8d4f-e9b784f86156', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:42'),
(281, '9cf08474-13c8-44b8-a43c-63b05f009e11', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:43'),
(282, '414e265b-08b1-429e-afda-b0d0a513e0c9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:47:59'),
(283, 'd4e3fd10-6f87-439e-a300-4ec42fcf03fa', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 19:48:01'),
(284, 'ae9d4d12-a2d3-4d26-be5b-bbc7e25de902', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:01:03'),
(285, '74ed4d37-5508-4c64-a40d-88453f48b331', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', '/recursos/guias/precificacao-de-honorarios-contabeis', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:01:10'),
(286, '43ae0329-62e8-4f8e-a2cc-840e920e83ad', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/calculadora-de-honorarios-contabeis', '/ferramentas/calculadora-de-honorarios-contabeis', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:01:18'),
(287, '262f4b14-5ace-4e41-8fe3-9e6cdd3fa267', 'tool.opened', 1, 'tool', 'tool', NULL, 'calculadora-de-honorarios-contabeis', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/calculadora-de-honorarios-contabeis', '/ferramentas/calculadora-de-honorarios-contabeis', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.calculadora-de-honorarios-contabeis.index\",\"method\":\"GET\"}', '2026-07-23 20:01:18'),
(288, 'afe152de-9dac-4ac0-87a0-e42287abb074', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', '/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:01:23'),
(289, '1f26d68b-d12f-4857-820e-27460a01e87a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:02:09'),
(290, '6949c753-b7e2-44ff-94c6-b2fb95f63851', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:04:03'),
(291, 'f80c1d4b-56dc-4596-b810-1fb9e333d755', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:04:04'),
(292, 'ace8971a-4ed2-4169-8ff8-eaffe1aa4424', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:12:46'),
(293, 'c875d0c8-9ad2-44ed-b8d7-9e1ed0cc78ed', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:13:17'),
(294, '7fa7985a-069f-4850-9e26-659adae9c7b7', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:14:23'),
(295, 'bfce0f94-9e84-4814-888f-63a0664e9610', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:14:58'),
(296, '4b1c1692-a634-4bf6-9103-3eaab1a2cf06', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:15:26'),
(297, '7da38be8-3f6e-46cc-aa34-4ba6d551403c', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:16:07'),
(298, 'b5836ca4-e6d7-43c9-bdf1-d1f3d0d83388', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:17:23'),
(299, '80b56da2-c835-4c27-9e9b-703a8351ca43', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:17:54'),
(300, '2b1df5f0-13a3-4cfe-b12a-648a6ca0cb45', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:18:20'),
(301, '01f6d5ba-f680-4eef-abe1-3641edf61e1f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:21:11'),
(302, '901060fc-01bc-4b4b-bea7-36e16ec04985', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:21:18'),
(303, '154d4dd0-b6d9-418f-a5ac-322ed6ed056e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:21:23'),
(304, '558e24c8-8118-4516-9a6b-f89ef1aede5d', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:21:45'),
(305, '93ab195b-d68c-4c97-919f-716034019388', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:26:10'),
(306, '282e637c-d072-44c4-876f-57abf75a4f36', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:26:42'),
(307, 'b749942e-76d9-494d-8f2c-dfbb3a0fb228', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:26:46'),
(308, '33ad2865-0516-418c-8dc1-392d057a6f0b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:26:47'),
(309, 'fd6d3712-7c6e-4153-aaef-80bab97ed0a9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:26:59'),
(310, '1b935ab9-7a5b-4a44-9a71-bb6d7336dfb7', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:27:02'),
(311, 'c0a431f1-c55e-4973-a0fa-e5e12d84c79b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:27:04'),
(312, '43f1de63-9861-4596-9908-8a6412590652', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:27:06'),
(313, '79999f94-5d8d-4e8a-b440-a9d2669c5be8', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:39:21'),
(314, '1596bc75-6b24-461e-9162-3fa5bfed5bb7', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:50:36'),
(315, '0ac24760-f6a2-4716-aa1f-39cd52d42c0d', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:50:40'),
(316, 'd907d460-058e-44f9-8451-a65fe57afb03', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:50:42'),
(317, '5f071680-81c9-4e5b-ad3c-f65c7f08f5f9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:50:50'),
(318, '30364810-77b2-4600-a043-17c8667f606f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:50:57'),
(319, '7b8ccb50-eaab-4ab9-9157-9edf6452a504', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:51:02'),
(320, 'b002de70-8c61-4670-9173-7cb017ba6a63', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 20:57:49'),
(321, '52dae246-1540-4f86-9263-26407e45bcbe', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:11:55'),
(322, 'f6197cce-4973-4711-9fc0-b1d6ca3e3f81', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:11:57'),
(323, 'a1564839-00d1-4dea-abc7-fea4e8c2f3bb', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:31:24'),
(324, '07b9e41b-4acb-46ce-8da6-82b64d142beb', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:31:28'),
(325, 'f564c38e-b831-4993-9d82-7c8ef62ee98c', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:31:33'),
(326, '609edc10-1f8c-455e-ab98-6bb0ec019f24', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:31:36'),
(327, '8153128a-faa3-433b-bcb6-1ab85dcc22ec', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:31:41'),
(328, '0cc25959-046f-4658-9528-56cc81f3bfc0', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:31:51'),
(329, '97388906-8c22-4cf6-9f46-fd6230f1fdd2', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:35:02'),
(330, '8900e058-4e18-4102-b2f4-bf3ab6ff8c7f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:39:06'),
(331, '5f5e3486-569e-49db-9c2b-87d8343d9c1e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:39:15'),
(332, 'af14696e-ae87-4b37-8090-99027aae7d59', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'f0ad4cdf-991a-4a07-8e48-893d53cac53e', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 21:46:53'),
(333, '7d33f1cc-1b70-48ad-9b2b-8a84fa03b7a2', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 22:53:11'),
(334, 'ed1c23c3-75ee-4504-be0e-103aad292365', 'audience.context_captured', 1, 'audience', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/audience', '/analytics/audience', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt-PT', 'America/Sao_Paulo', '1920x1080', NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"timezone\":\"America\\/Sao_Paulo\",\"screen_resolution\":\"1920x1080\",\"language\":\"pt-PT\"}', '2026-07-23 22:53:11'),
(335, '32324f4f-bc1a-4879-86a6-a52584623648', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 22:53:18'),
(336, '070505e7-7b31-4fd1-acda-b6c08076226a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:01:29'),
(337, '03a2e142-e2d5-4d9c-ad34-39149798e6a6', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:01:35'),
(338, 'aacf7e72-deb2-48fa-87f0-4e31a14236cb', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:01:41'),
(339, '6e6e71ef-fcd0-4661-a68f-a17694b5d139', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:01:53'),
(340, '4d850b22-5de1-4235-aec6-0ad5809fc884', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/documentos', '/ferramentas/documentos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:02:01'),
(341, '345d5cfe-a2be-45d2-b671-d4cdf151463d', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:04:32'),
(342, 'f16cef6a-b58a-4e80-af2d-5ef7a74e3aba', 'tool.opened', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.emissor-de-recibos.index\",\"method\":\"GET\"}', '2026-07-23 23:04:32');
INSERT INTO `platform_analytics_events` (`id`, `event_id`, `event_name`, `schema_version`, `channel`, `subject_type`, `subject_id`, `subject_slug`, `visitor_id`, `analytics_session_id`, `user_id`, `session_id`, `url`, `path`, `referrer`, `source`, `medium`, `campaign`, `acquisition_context_id`, `acquisition_keyword`, `acquisition_campaign_identifier`, `acquisition_primary_tool_slug`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `device_type`, `browser`, `operating_system`, `language`, `timezone`, `screen_resolution`, `country_code`, `region`, `city`, `ip_hash`, `user_agent`, `metadata`, `occurred_at`) VALUES
(343, '0f61a3d9-94c4-4492-acbb-58f08196496b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos/perfis', '/ferramentas/emissor-de-recibos/perfis', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:08:13'),
(344, '1c81ff49-31dc-4478-9bb1-02ee98d6bd0e', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":221}', '2026-07-23 23:08:13'),
(345, 'bf8ada94-5447-4b9e-a638-6b505b9c6513', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/emissor-de-recibos/perfis', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":6}', '2026-07-23 23:08:19'),
(346, '08ec9366-3461-4ee3-abe0-06eed7c602b4', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos/historico', '/ferramentas/emissor-de-recibos/historico', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:10:46'),
(347, '7448c9ca-0f86-4626-a07d-f7b7cca50979', 'tool.history.viewed', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos/historico', '/ferramentas/emissor-de-recibos/historico', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.emissor-de-recibos.history.index\",\"method\":\"GET\"}', '2026-07-23 23:10:46'),
(348, '711acc2b-907f-4950-9f82-9265294a025e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos/lote', '/ferramentas/emissor-de-recibos/lote', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:10:49'),
(349, 'b937c2e7-7354-441e-870b-ea9c6eea1586', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/emissor-de-recibos/lote', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":14}', '2026-07-23 23:11:04'),
(350, '829dbc6a-3c1c-4e40-a51f-f90b278c76e8', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:11:32'),
(351, '44a79550-bfb6-4461-88c5-ad5183640d6a', 'tool.opened', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.emissor-de-recibos.index\",\"method\":\"GET\"}', '2026-07-23 23:11:32'),
(352, '409ee4bc-3b6e-4250-a4fd-846bd8c647dc', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:11:52'),
(353, '471472a3-f0fb-4a84-b817-f9f3a3955a58', 'tool.opened', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.emissor-de-recibos.index\",\"method\":\"GET\"}', '2026-07-23 23:11:52'),
(354, 'bfa46e2f-b313-4225-af87-5e00f07dbc74', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":20}', '2026-07-23 23:11:52'),
(355, '7d0f76aa-7415-4742-86ee-7aa307d3fd59', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:12:28'),
(356, 'e0d06ad2-136b-4c5a-8063-460f9f78eca4', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":35}', '2026-07-23 23:12:28'),
(357, '72321c7c-34b7-4d00-b75c-4083d69c1c9b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/validadores', '/ferramentas/validadores', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:13:37'),
(358, '54a77fc2-283c-4066-815e-1d8d3f36cf76', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/validador-de-cnpj', '/ferramentas/validador-de-cnpj', 'http://localhost:8000/ferramentas/validadores', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:13:38'),
(359, 'ec52aea9-b53a-426e-abad-162fa015d125', 'tool.opened', 1, 'tool', 'tool', NULL, 'validador-de-cnpj', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/validador-de-cnpj', '/ferramentas/validador-de-cnpj', 'http://localhost:8000/ferramentas/validadores', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.validador-de-cnpj.index\",\"method\":\"GET\"}', '2026-07-23 23:13:38'),
(360, '6efac278-7ad4-4684-923c-d75e427f8545', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/validador-de-cnpj', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:13:41'),
(361, '6c582510-6195-4307-8122-fe5d7e94c3da', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/documentos', '/ferramentas/documentos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:13:43'),
(362, 'a93ab5be-df70-4175-bb05-64f66e9b94b2', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:13:44'),
(363, '1160e388-f27b-4b46-820d-4fb9a3ae3fbb', 'tool.opened', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/ferramentas/documentos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.emissor-de-recibos.index\",\"method\":\"GET\"}', '2026-07-23 23:13:44'),
(364, '2b596ac1-3f2f-4625-9263-bb5f9961695e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:14:18'),
(365, '401dbd3e-fea9-41b5-804c-57d3e6a56394', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":33}', '2026-07-23 23:14:18'),
(366, 'd5076e59-8536-42e7-986c-206dcf2e6b8a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:14:21'),
(367, '3229c478-9589-40ac-8bdd-bc809366c587', 'tool.opened', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas/emissor-de-recibos', '/ferramentas/emissor-de-recibos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"route\":\"tools.emissor-de-recibos.index\",\"method\":\"GET\"}', '2026-07-23 23:14:21'),
(368, '0cd84c4d-1446-4f91-b9e1-56c13392cc1b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:16:01'),
(369, '44a8522c-ab0d-4792-9ada-8024b260adeb', 'tool.time.spent', 1, 'tool', 'tool', NULL, 'emissor-de-recibos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/analytics/tools', '/analytics/tools', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"seconds\":99}', '2026-07-23 23:16:02'),
(370, 'abb2d992-197b-4515-839a-fdd2e34f6469', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:16:52'),
(371, 'b716dc89-a49c-4fd5-957a-6782239b73af', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/ferramentas/emissor-de-recibos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:19:38'),
(372, '391d9c9b-4983-402a-ac6f-85676dcfd947', 'blog.post.viewed', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 23:19:43'),
(373, '199b1864-a748-403b-8bf0-76cd867a6591', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', '/blog/como-calcular-ferias-dias-abono-prazos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:19:43'),
(374, '7e5cae8f-6653-4503-bf93-e0d75f2d0d03', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":25}', '2026-07-23 23:19:44'),
(375, 'a7b4d0c5-4558-41b3-8dfa-17236bf143f0', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":50}', '2026-07-23 23:19:45'),
(376, '324969ef-e457-42fb-879d-da0e8f307b38', 'blog.scroll.measured', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"percentage\":75}', '2026-07-23 23:19:46'),
(377, 'a48280b2-cc1c-468f-a30d-12b5ad9c6fba', 'blog.reading.started', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 23:19:48'),
(378, '09500ebe-6b76-45d1-b39e-d090dbf7f2c8', 'blog.time.spent', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\",\"seconds\":4}', '2026-07-23 23:19:48'),
(379, '88fc3b04-c835-4d52-93c1-3ad558601513', 'blog.reading.abandoned', 1, 'blog', 'blog_post', 10, 'como-calcular-ferias-dias-abono-prazos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":10,\"subject_slug\":\"como-calcular-ferias-dias-abono-prazos\"}', '2026-07-23 23:19:48'),
(380, '5f563b62-8c54-47c0-9566-e0b80d1d663a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/blog/como-calcular-ferias-dias-abono-prazos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:19:51'),
(381, '727fb726-1059-4ccb-b09c-ad128b8d6877', 'blog.post.viewed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:20:14'),
(382, 'a85053db-8612-4375-9788-2f4b902d70c1', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:20:14'),
(383, '511c9125-660f-457a-8ab1-c40218e2e965', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":25}', '2026-07-23 23:20:15'),
(384, '7bb13819-dc34-44a4-a903-9c8334144a38', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":50}', '2026-07-23 23:20:16'),
(385, 'ae1aeb2b-8984-4817-bd25-7330cb8c687c', 'blog.reading.started', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:20:18'),
(386, 'd7eb41ef-d257-4e51-9fd0-f5bc444a591f', 'blog.post.viewed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:21:14'),
(387, '1db812a7-7ed9-4945-8940-e9916bd2ac23', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:21:14'),
(388, 'e5840f09-c7a4-487b-afa4-cd2d30ba1a82', 'blog.time.spent', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"seconds\":60}', '2026-07-23 23:21:14'),
(389, 'e47f07e2-a78a-4d55-990b-a9c2e02ca88f', 'blog.reading.abandoned', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:21:15'),
(390, 'a874802f-cdaa-4595-ad82-9888072f307f', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":25}', '2026-07-23 23:21:15'),
(391, '346e2c14-bfe6-4e7f-a7c0-5519a1e45c8d', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":50}', '2026-07-23 23:21:15'),
(392, 'a4e309ad-dd83-483f-9172-db9e8fdb19a1', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:21:17'),
(393, '7c865597-c0f9-49f9-bc31-23d8d02a375e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', NULL, 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:22:23'),
(394, 'b5253cdb-ac2b-481c-bc4d-ce64856746d3', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:22:24'),
(395, '46dc125f-9e2c-4e40-be9f-679b129915b8', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:22:38'),
(396, '88e582c8-4605-4e34-a1e2-25fa404c37f9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:23:13'),
(397, '8e8f4ce3-a7ac-4300-9390-803b3ee43888', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:23:25'),
(398, 'c4c6eda7-ece4-4b32-b45a-86ca204b331b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:23:46'),
(399, '49e398b2-be17-46b1-b608-15410e88afc9', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:23:48'),
(400, '2977f416-8832-48a1-a66f-adc2653ac5f3', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:24:42'),
(401, '95bb2f8b-7fcb-41eb-a5c4-c2ae3ef896e6', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:28:22'),
(402, '04b91707-c9a1-422c-93e4-6a8709a7c420', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:28:34'),
(403, '1e53b6bd-a95d-42b0-bbfa-4199ed41a834', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:29:04'),
(404, 'b1f19ee7-1f71-4a07-bd4e-aef328ee249c', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:29:38'),
(405, '26ae2bf1-bfaa-452a-8b99-41ee1d59097d', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:29:51'),
(406, '296176be-34c1-4615-9964-3a1cce471c89', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:30:47'),
(407, '9dec9bd3-0a80-406d-a62d-136a6cd7ff92', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:32:00'),
(408, '25231115-9968-4398-857a-2f9da1dc8af7', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:32:16');
INSERT INTO `platform_analytics_events` (`id`, `event_id`, `event_name`, `schema_version`, `channel`, `subject_type`, `subject_id`, `subject_slug`, `visitor_id`, `analytics_session_id`, `user_id`, `session_id`, `url`, `path`, `referrer`, `source`, `medium`, `campaign`, `acquisition_context_id`, `acquisition_keyword`, `acquisition_campaign_identifier`, `acquisition_primary_tool_slug`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `device_type`, `browser`, `operating_system`, `language`, `timezone`, `screen_resolution`, `country_code`, `region`, `city`, `ip_hash`, `user_agent`, `metadata`, `occurred_at`) VALUES
(409, '82921248-0cfa-4337-82ea-4bb05d9e8599', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:32:59'),
(410, 'beea579a-ac3c-401e-a906-9ad696176517', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:33:00'),
(411, '91d8f2af-c318-4141-9a6c-a36be9ef8f8e', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/admin', '/admin', 'http://localhost:8000/admin/blog/analytics', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:35:34'),
(412, 'b044c633-ec8f-4e59-b2ff-121d59c0599d', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/admin', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:38:03'),
(413, '44376262-5236-4b4b-990a-96b56ee623bf', 'blog.post.viewed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:38:42'),
(414, '067742f0-9847-42ea-adff-9bf5c55106d5', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:38:42'),
(415, '39b11b7b-fd8e-47e8-9f3d-9f9fdb9af0da', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":25}', '2026-07-23 23:38:43'),
(416, 'b0e11be7-dfd8-452c-be25-afb3f5954d11', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":50}', '2026-07-23 23:38:45'),
(417, '2157b0e5-0412-4fe8-84d6-87080717436a', 'blog.reading.started', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:38:46'),
(418, 'a94e4b03-4d4c-44ff-85c7-242bbd2a2ca6', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":75}', '2026-07-23 23:38:47'),
(419, '2ae0d2ce-8c10-490b-bca8-a485fa930c2a', 'blog.reading.completed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:38:47'),
(420, '00c9ea4a-fc1e-4039-b61b-749855e5c2da', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":100}', '2026-07-23 23:38:47'),
(421, '3657dfa7-0fd8-4f76-bad9-0013b60e4ece', 'blog.post.viewed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:42:16'),
(422, 'e5e203d0-7330-4e15-8ecf-ca680790b8c4', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:42:16'),
(423, 'e8baaae8-9fe3-48f6-87bf-4cd0e9e92851', 'blog.time.spent', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"seconds\":214}', '2026-07-23 23:42:17'),
(424, '655e5522-87db-42f6-b680-3a8f97ba1039', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":25}', '2026-07-23 23:42:17'),
(425, 'b11a765f-f9c2-4b08-922c-b3effaff9f70', 'blog.reading.started', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:42:20'),
(426, 'aa7f4fc1-f382-4974-a8d4-3dff2610a149', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":50}', '2026-07-23 23:42:20'),
(427, '66e6837f-185f-4dfb-8250-4cd740e52018', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":75}', '2026-07-23 23:42:21'),
(428, '496d70f0-6a30-4f68-8b95-a0ad5d053e40', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":100}', '2026-07-23 23:42:21'),
(429, '58e0edfe-608b-45a8-aea8-9e6bb701af3f', 'blog.reading.completed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:42:22'),
(430, '24307059-ab12-44c6-89a7-e5f2ab52964a', 'blog.post.viewed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:43:16'),
(431, 'f485e550-1ae5-420f-a382-f1bf18e703ce', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', '/blog/simples-lucro-presumido-lucro-real-comparacao', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:43:16'),
(432, '1541e32d-41dd-49c5-864c-0b431ef21bfe', 'blog.time.spent', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"seconds\":60}', '2026-07-23 23:43:17'),
(433, 'd70921dc-df5c-4dea-9f5b-b4866a7a49f1', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":25}', '2026-07-23 23:43:17'),
(434, 'ead43cfb-9854-40f1-a1e8-821868fa20ac', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":50}', '2026-07-23 23:43:18'),
(435, '3024889c-54ef-4179-a425-aa5869197c87', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":75}', '2026-07-23 23:43:18'),
(436, 'ceafba6a-f8cd-47eb-b073-b769d9c2e93e', 'blog.scroll.measured', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"percentage\":100}', '2026-07-23 23:43:18'),
(437, '34422b30-282f-4e28-8f42-d1df85af15a7', 'blog.reading.completed', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:43:18'),
(438, '621eac38-724b-42d5-a75e-7cf8511ff194', 'blog.reading.started', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\"}', '2026-07-23 23:43:20'),
(439, '139fba93-865f-4df2-bebf-b59a2b4322c6', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/ferramentas', '/ferramentas', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:43:28'),
(440, 'f982c761-972e-4baa-bca5-002ec8f49b48', 'blog.time.spent', 1, 'blog', 'blog_post', 9, 'simples-lucro-presumido-lucro-real-comparacao', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/simples-lucro-presumido-lucro-real-comparacao', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":9,\"subject_slug\":\"simples-lucro-presumido-lucro-real-comparacao\",\"seconds\":11}', '2026-07-23 23:43:28'),
(441, 'a74abe1f-4581-45fe-98f8-90ba95bf1204', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/ferramentas', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:43:30'),
(442, '581f4833-253e-44cc-a9e9-fabfd18316df', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:43:36'),
(443, '50770b3d-2369-43e4-a8c6-c571713aa52b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:43:48'),
(444, '5792617f-0f7c-494e-9c4e-c3678850865f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:43:51'),
(445, '56898ebe-2888-4b0e-99bd-28f7520b6b97', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:43:53'),
(446, '6993b798-02b4-41a2-a228-95467cfc8e6a', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos', '/recursos/modelos', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:44:15'),
(447, '479a7fa0-c8f5-4eb4-af07-689dcc8f1c34', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias', '/recursos/guias', 'http://localhost:8000/recursos/modelos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:44:17'),
(448, '5d55d2cc-1bc5-4372-a156-90ae2d079c63', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/recursos/guias', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:44:18'),
(449, '7d0a51ff-405a-488f-8ff8-429b7aaf57e8', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/guias/precificacao-de-honorarios-contabeis', '/recursos/guias/precificacao-de-honorarios-contabeis', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:44:28'),
(450, '44610566-3066-4d8b-a571-7036aa3a955f', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', '/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:44:41'),
(451, 'f2214a31-8d1a-4d58-97bd-128f04aed746', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', '/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:46:51'),
(452, '23f3a642-7624-4638-a053-aadbfad374b3', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/sobre', '/sobre', 'http://localhost:8000/recursos/modelos/levantamento-para-precificacao-de-honorarios', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:46:59'),
(453, '54edafe0-a2c5-40d6-ae03-6ac5b2ee24d6', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000', '//', 'http://localhost:8000/sobre', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:47:08'),
(454, 'fa30de6a-321e-4ed7-a5ff-2c4c86722bb6', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/recursos', '/recursos', 'http://localhost:8000/', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:47:12'),
(455, '62908aa4-9bc3-4096-be32-26e72c8c16f7', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/planos', '/planos', 'http://localhost:8000/recursos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:47:15'),
(456, 'a5cc6779-087f-45cf-be06-93ba3cfc3a57', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog', '/blog', 'http://localhost:8000/planos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:47:16'),
(457, '86c48276-6473-4532-9ea7-28d6fcdb9a63', 'blog.post.viewed', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', '/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 23:47:18'),
(458, '5a44d364-a28b-4fb8-801f-a927e4355bb7', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', '/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:47:18'),
(459, '096d65d5-36d0-45d2-8f6c-5ee73e7f923f', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":25}', '2026-07-23 23:47:18'),
(460, 'cd3f7e89-4cb1-4236-ba1d-d27d192f5aab', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":50}', '2026-07-23 23:47:19'),
(461, '932427da-50cc-40f0-ad56-6b89d76ee747', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":75}', '2026-07-23 23:47:20'),
(462, 'aba7d102-2e2c-4894-9342-e3c7346db41e', 'blog.reading.completed', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 23:47:20'),
(463, '17583ad0-46bf-48c9-af5c-cc21f996e383', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":100}', '2026-07-23 23:47:21'),
(464, '2f698902-9200-4b8c-ab0a-10d248becdd1', 'blog.reading.started', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 23:47:22'),
(465, '018ea40c-6699-47fd-8448-3fd1f27a8e9e', 'blog.post.viewed', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', '/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 23:52:06'),
(466, 'b2cef36f-e3a2-4a09-ae28-535d8a99232b', 'page.viewed', 1, 'platform', NULL, NULL, NULL, '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', '/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'http://localhost:8000/blog', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '[]', '2026-07-23 23:52:06'),
(467, '217b78ed-3e33-4d50-a8ac-07d5fb56ca12', 'blog.time.spent', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"seconds\":288}', '2026-07-23 23:52:06'),
(468, '48ae64dc-984c-4310-948a-323b588a395b', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":25}', '2026-07-23 23:52:07');
INSERT INTO `platform_analytics_events` (`id`, `event_id`, `event_name`, `schema_version`, `channel`, `subject_type`, `subject_id`, `subject_slug`, `visitor_id`, `analytics_session_id`, `user_id`, `session_id`, `url`, `path`, `referrer`, `source`, `medium`, `campaign`, `acquisition_context_id`, `acquisition_keyword`, `acquisition_campaign_identifier`, `acquisition_primary_tool_slug`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `device_type`, `browser`, `operating_system`, `language`, `timezone`, `screen_resolution`, `country_code`, `region`, `city`, `ip_hash`, `user_agent`, `metadata`, `occurred_at`) VALUES
(469, 'd57f7028-5782-4909-a7f1-d9847a26549e', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":50}', '2026-07-23 23:52:07'),
(470, '10ea9855-5908-42a0-987e-01a25ebb539c', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":75}', '2026-07-23 23:52:08'),
(471, '34875b78-3b98-4653-8e03-4c362897cdf5', 'blog.scroll.measured', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"percentage\":100}', '2026-07-23 23:52:08'),
(472, '3bfb4d4a-951e-4ed0-9804-dd0a06e678d6', 'blog.reading.completed', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 23:52:08'),
(473, '2539b8c5-507c-412d-ac78-cb714f4aa2fa', 'blog.reading.started', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\"}', '2026-07-23 23:52:10'),
(474, '54e00246-7516-482d-8660-9aa9a1552787', 'blog.time.spent', 1, 'blog', 'blog_post', 3, 'como-gerar-darf-gps-codigo-vencimento-acrescimos', '96ada320-03e3-4118-9d86-ecd5426f4a81', 'fb782b61-b303-454c-875b-bb468b4bff5f', 1, 'IHvzt6gaTN2Zx1Yj9yPvGaWGAqGQDqJqPjsCNOaZ', 'http://localhost:8000/blog/analytics', '/blog/analytics', 'http://localhost:8000/blog/como-gerar-darf-gps-codigo-vencimento-acrescimos', 'direct', 'none', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'desktop', 'Chrome', 'Windows', 'pt_PT', NULL, NULL, NULL, NULL, NULL, '2cc57dc18b3493d40c518b2b435ff43646bcfc79afc8a9ac1c11a47adf5bc956', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '{\"subject_type\":\"blog_post\",\"subject_id\":3,\"subject_slug\":\"como-gerar-darf-gps-codigo-vencimento-acrescimos\",\"seconds\":26}', '2026-07-23 23:52:33');

-- --------------------------------------------------------

--
-- Estrutura da tabela `receipt_party_profiles`
--

CREATE TABLE `receipt_party_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `party_type` varchar(10) NOT NULL,
  `label` varchar(80) NOT NULL,
  `name` varchar(160) NOT NULL,
  `document_type` varchar(4) DEFAULT NULL,
  `document` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `prazzu_account_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`, `subscription_plan`) VALUES
(1, NULL, 'ricardo', 'ricardo-s-a@hotmail.com', NULL, '$2y$12$BcodyoOBgqx5suwbiwEsAuaHR8iL0JfFoCP6ok.jct2jJHSKQmBua', NULL, '2026-07-23 17:17:39', '2026-07-23 17:17:39', 'user', 'free');

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
-- Índices para tabela `receipt_party_profiles`
--
ALTER TABLE `receipt_party_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_party_profiles_user_id_party_type_label_unique` (`user_id`,`party_type`,`label`),
  ADD KEY `receipt_party_profiles_user_id_party_type_updated_at_index` (`user_id`,`party_type`,`updated_at`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `platform_analytics_events`
--
ALTER TABLE `platform_analytics_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=475;

--
-- AUTO_INCREMENT de tabela `receipt_party_profiles`
--
ALTER TABLE `receipt_party_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tool_usage_events`
--
ALTER TABLE `tool_usage_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Limitadores para a tabela `receipt_party_profiles`
--
ALTER TABLE `receipt_party_profiles`
  ADD CONSTRAINT `receipt_party_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
