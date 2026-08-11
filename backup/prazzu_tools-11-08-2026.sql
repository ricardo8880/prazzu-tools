-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11-Ago-2026 às 15:39
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
  `vertical_slug` varchar(255) DEFAULT NULL,
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
  `vertical_slug` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `description`, `vertical_slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Gestão Contábil', 'gestao-contabil', 'Precificação, rentabilidade e decisões de gestão para escritórios e empresas.', 'contabilidade', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(2, 'Cadastros e Validações', 'cadastros-e-validacoes', 'Validação de documentos e qualidade de dados cadastrais.', 'contabilidade', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(3, 'Fiscal e Tributário', 'fiscal-e-tributario', 'Guias, cálculos, documentos fiscais e planejamento tributário.', 'contabilidade', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(4, 'Trabalhista', 'trabalhista', 'Cálculos e orientações para rotinas trabalhistas.', 'contabilidade', 1, '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(5, 'Gestão de Pessoas', 'gestao-de-pessoas', 'Indicadores, processos e práticas de Recursos Humanos.', 'rh', 1, '2026-08-11 15:01:34', '2026-08-11 15:01:34');

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
  `vertical_slug` varchar(120) DEFAULT NULL,
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

INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `vertical_slug`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Como calcular honorários contábeis sem perder rentabilidade', 'como-calcular-honorarios-contabeis', 'Aprenda como calcular honorários contábeis considerando custos, volume de trabalho, complexidade, risco, margem e reajustes para proteger a rentabilidade.', '<p><strong>Como calcular honorários contábeis</strong> sem comprometer a rentabilidade? A resposta começa pelo custo real de atender cada cliente, e não pela mensalidade praticada pelo concorrente. O honorário precisa remunerar equipe, sistemas, estrutura, responsabilidade técnica, retrabalho e risco, além de deixar margem para crescimento.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> estime o esforço mensal do cliente, converta a estrutura do escritório em custo produtivo, acrescente custos específicos, ajuste pela complexidade e aplique a margem desejada. Depois, documente o escopo e revise o preço quando o volume de trabalho mudar.</div>\r\n\r\n<h2>Por que copiar a mensalidade do mercado pode gerar prejuízo</h2>\r\n<p>Tabelas e referências de mercado ajudam a entender posicionamento, mas não revelam quanto custa executar o serviço dentro do seu escritório. Dois clientes com o mesmo faturamento podem exigir esforços muito diferentes. Um pode enviar documentos organizados e ter poucas movimentações; outro pode concentrar filiais, folha, retenções, operações interestaduais, retrabalho e atendimento frequente.</p>\r\n<p>Por isso, o preço deve nascer de uma análise interna e depois ser comparado com o mercado. Se o valor calculado ficar muito acima ou abaixo dos concorrentes, investigue a causa em vez de simplesmente substituir o número.</p>\r\n\r\n<h2>1. Levante os custos mensais do escritório</h2>\r\n<p>Comece pelos custos necessários para manter a operação funcionando. Inclua salários e encargos, sistemas contábeis, certificados, armazenamento, infraestrutura, aluguel, energia, internet, telefonia, treinamento, suporte, serviços terceirizados e tributos do próprio escritório. Separe custos diretamente atribuíveis a um cliente daqueles que precisam ser rateados.</p>\r\n<p>O objetivo não é criar uma contabilidade de custos excessivamente complexa, mas chegar a uma base consistente para comparar clientes e contratos.</p>\r\n\r\n<h2>2. Descubra o custo por hora produtiva</h2>\r\n<p>Nem toda hora contratada da equipe está disponível para execução de serviços faturáveis. Reuniões internas, administração, treinamento, férias, afastamentos e períodos ociosos reduzem a capacidade produtiva.</p>\r\n<div class=\"card border-secondary-subtle my-3\"><div class=\"card-body\"><strong>Fórmula gerencial:</strong> custo por hora produtiva = custos mensais da operação ÷ horas produtivas disponíveis.</div></div>\r\n<p>Exemplo: se a operação custa R$ 60.000 por mês e possui 1.000 horas realmente produtivas, o custo médio é de R$ 60 por hora. Esse valor ainda não é o honorário: ele é uma referência para medir o custo do atendimento.</p>\r\n\r\n<h2>3. Estime o esforço mensal de cada cliente</h2>\r\n<p>Liste as rotinas que consomem tempo e atribua uma estimativa de horas. Vale separar fiscal, contábil, folha, societário, atendimento e revisão técnica.</p>\r\n<ul>\r\n<li><strong>Fiscal:</strong> documentos, apuração, retenções, conferências e obrigações.</li>\r\n<li><strong>Contábil:</strong> conciliações, classificações, fechamento e revisão.</li>\r\n<li><strong>Folha:</strong> admissões, férias, rescisões, eventos e encargos.</li>\r\n<li><strong>Societário e cadastral:</strong> alterações, cadastros e certificados.</li>\r\n<li><strong>Atendimento:</strong> reuniões, dúvidas, solicitações e acompanhamento.</li>\r\n<li><strong>Retrabalho:</strong> correções provocadas por atraso, inconsistência ou falta de documentos.</li>\r\n</ul>\r\n<p>Se o escritório possui histórico, compare a estimativa com o tempo realmente gasto nos últimos meses. Essa diferença revela contratos subprecificados e também oportunidades de automação.</p>\r\n\r\n<h2>4. Considere volume, regime e complexidade</h2>\r\n<p>Faturamento isoladamente não mede o esforço contábil. Para <strong>como calcular honorários contábeis</strong> de forma mais justa, considere quantidade de documentos, funcionários, contas bancárias, filiais, volume de notas, regime tributário, atividade, retenções, comércio exterior, substituição tributária, qualidade dos dados e frequência de suporte.</p>\r\n<p>Uma forma simples é transformar esses fatores em níveis de complexidade. O importante é usar o mesmo critério para clientes semelhantes e registrar por que um contrato recebeu acréscimo.</p>\r\n\r\n<h2>5. Acrescente risco e margem</h2>\r\n<p>Depois de estimar o custo do atendimento, adicione uma margem compatível com a estratégia do escritório. A margem financia lucro, investimentos, treinamento, tecnologia e imprevistos. Também pode ser necessário um fator de risco quando a operação exige maior responsabilidade, revisão ou disponibilidade.</p>\r\n<p>Evite confundir margem com simples acréscimo sobre custos. Acompanhe a rentabilidade efetiva após o início do contrato para confirmar se a premissa se realizou.</p>\r\n\r\n<h2>Exemplo de precificação</h2>\r\n<p>Imagine um cliente que consuma 8 horas produtivas por mês e que o custo médio seja R$ 60 por hora. O custo operacional estimado seria R$ 480. Se houver R$ 120 de custos específicos, a base passa para R$ 600. A partir daí, o escritório aplica o critério interno de complexidade e a margem desejada.</p>\r\n<p>O exemplo serve para mostrar a lógica, não para definir uma tabela universal. Cada escritório deve usar seus próprios custos e sua própria capacidade.</p>\r\n\r\n<h2>Defina claramente o que está incluído</h2>\r\n<p>Preço sem escopo cria conflito. Registre quantidade de empresas, filiais, funcionários, movimentações ou documentos considerados, canais de atendimento, periodicidade de reuniões e serviços extraordinários. Alterações contratuais, regularizações, parcelamentos, trabalhos retroativos e demandas fora da rotina podem ter cobrança específica.</p>\r\n\r\n<h2>Quando reajustar os honorários</h2>\r\n<p>Além do reajuste periódico previsto em contrato, faça uma revisão quando houver mudança relevante no cliente: aumento expressivo de documentos, contratação de funcionários, nova filial, mudança de regime tributário, novas obrigações, expansão de atividade ou aumento persistente do suporte.</p>\r\n<p>Uma boa prática é comparar trimestral ou semestralmente a receita do contrato com as horas e custos que ele consome. Se a operação mudou, a conversa sobre reajuste passa a ser baseada em evidências.</p>\r\n\r\n<h2>Checklist antes de apresentar o preço</h2>\r\n<ol>\r\n<li>Mapeie os custos mensais do escritório.</li>\r\n<li>Calcule a capacidade e o custo por hora produtiva.</li>\r\n<li>Estime as horas do cliente por área.</li>\r\n<li>Considere volume, regime, complexidade e qualidade das informações.</li>\r\n<li>Inclua custos específicos e o fator de risco adotado.</li>\r\n<li>Aplique a margem definida pelo escritório.</li>\r\n<li>Descreva o escopo e os limites do contrato.</li>\r\n<li>Defina critérios de reajuste e revisão extraordinária.</li>\r\n<li>Acompanhe a rentabilidade real depois da contratação.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule os honorários no Prazzu Tools</h2><p>A Calculadora de Honorários Contábeis do projeto considera porte, regime tributário, complexidade e outros dados da operação, além de permitir trabalhar com reajustes e memória do cálculo.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-de-honorarios-contabeis\">Abrir Calculadora de Honorários Contábeis</a></div></div>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Existe uma tabela única de honorários contábeis?</h3>\r\n<p>Não existe uma única tabela capaz de representar os custos e o escopo de todos os escritórios. Referências profissionais e regionais podem ajudar no posicionamento, mas a formação do preço deve considerar a realidade da operação e o contrato oferecido.</p>\r\n<h3>É melhor cobrar pelo faturamento ou pelo volume?</h3>\r\n<p>Os dois podem ser indicadores, mas nenhum deveria ser usado isoladamente. Volume documental, folha, complexidade tributária, suporte e qualidade dos dados costumam explicar melhor o esforço necessário.</p>\r\n<h3>Cliente que automatiza processos deveria pagar menos?</h3>\r\n<p>A automação pode reduzir o custo de execução, mas o preço também remunera responsabilidade técnica, revisão, tecnologia e disponibilidade. O ganho de produtividade pode aumentar margem, permitir reposicionamento ou justificar condições comerciais, conforme a estratégia.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Aprender <strong>como calcular honorários contábeis</strong> é transformar custo, esforço, risco e escopo em uma regra de precificação repetível. Quando o escritório acompanha o custo real por cliente e revisa contratos conforme a operação cresce, fica mais fácil proteger margem sem depender de reajustes improvisados.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/como-calcular-honorarios-contabeis.png', 'Cálculo de honorários contábeis com custos, margem e rentabilidade', 'published', 1, '2026-07-13 12:00:00', '2026-07-27 20:04:50', 'como calcular honorários contábeis', '[\"precificação contábil\", \"honorários contábeis\", \"mensalidade contábil\", \"custo por cliente\", \"rentabilidade do escritório contábil\", \"reajuste de honorários\"]', 'Como calcular honorários contábeis com rentabilidade', 'Veja como calcular honorários contábeis usando custos, volume, complexidade, risco e margem para formar preços sustentáveis e revisar contratos.', NULL, 'blog/social/como-calcular-honorarios-contabeis.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:04:50'),
(2, NULL, 'Como validar CNPJ, CPF e inscrição estadual antes do cadastro', 'como-validar-cnpj-cpf-inscricao-estadual', 'Veja como validar CNPJ, CPF e inscrição estadual, separar dígito verificador de situação cadastral e preparar sistemas para o novo CNPJ alfanumérico.', '<p><strong>Como validar CNPJ</strong>, CPF e inscrição estadual antes de gravar um cadastro? O processo ideal tem mais de uma camada. Primeiro, valide formato e dígitos verificadores. Depois, quando a decisão exigir, consulte a situação cadastral em fonte oficial e confira se os dados correspondem à pessoa ou empresa informada.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> um número matematicamente válido não prova que o documento existe, está ativo ou pertence ao titular informado. Use a validação local para bloquear erros de digitação e a consulta cadastral para confirmar dados oficiais.</div>\r\n\r\n<h2>Validação matemática e consulta cadastral são coisas diferentes</h2>\r\n<p>CPF, CNPJ e muitas inscrições estaduais possuem regras de formação e dígitos verificadores. A validação matemática identifica números incompatíveis com essas regras e é útil antes de salvar, importar ou processar dados.</p>\r\n<p>Porém, um número pode passar no algoritmo e ainda assim não representar um cadastro ativo ou a empresa esperada. Para verificar situação cadastral do CNPJ, utilize os serviços oficiais da Receita Federal e da Redesim quando necessário.</p>\r\n\r\n<h2>Como validar CNPJ na entrada de dados</h2>\r\n<ol>\r\n<li>Remova apenas caracteres de formatação quando o formato recebido permitir.</li>\r\n<li>Verifique se o tamanho e os caracteres são aceitos pela regra atual.</li>\r\n<li>Calcule e confira os dígitos verificadores.</li>\r\n<li>Rejeite valores obviamente artificiais quando a regra do seu sistema exigir.</li>\r\n<li>Preserve o valor original para auditoria quando o dado vier de importação.</li>\r\n<li>Se a operação depender da existência ou situação da empresa, faça consulta cadastral em fonte confiável.</li>\r\n</ol>\r\n\r\n<h2>Atenção ao CNPJ alfanumérico em 2026</h2>\r\n<p>Esse ponto passou a ser essencial para sistemas de cadastro. A Receita Federal programou a implementação do primeiro <strong>CNPJ alfanumérico</strong> para <strong>31 de julho de 2026</strong>, destinado a novas inscrições. Os CNPJs já existentes continuam válidos e não precisam ser alterados.</p>\r\n<p>No novo formato, o CNPJ continua com 14 posições, mas as 12 primeiras podem combinar letras e números; os dois últimos caracteres permanecem como dígitos verificadores numéricos. Por isso, validações antigas baseadas em “somente números” podem rejeitar novas inscrições válidas.</p>\r\n<div class=\"alert alert-warning\"><strong>Para equipes de tecnologia:</strong> revise máscaras, banco de dados, expressões regulares, integrações, importadores, exportadores e APIs. O sistema deve conviver com CNPJs numéricos existentes e com o formato alfanumérico adotado para novas inscrições.</div>\r\n\r\n<h2>Como validar CPF</h2>\r\n<p>No CPF, a validação local normalmente confere quantidade de dígitos e os dois dígitos verificadores. Ela ajuda a eliminar erros de digitação antes do cadastro, mas não substitui uma consulta oficial quando for necessário confirmar situação do CPF ou identidade do titular.</p>\r\n<p>Evite tratar a simples passagem pelo algoritmo como prova de identidade. Em processos sensíveis, combine validação do número com os controles de autenticação e documentação apropriados ao contexto.</p>\r\n\r\n<h2>Como validar inscrição estadual</h2>\r\n<p>A inscrição estadual exige atenção extra porque as regras variam entre unidades da federação. Tamanho, posição do dígito verificador e algoritmo podem mudar conforme o estado e, em alguns casos, conforme o tipo de inscrição.</p>\r\n<p>O cadastro deve solicitar a UF ou inferi-la de uma fonte confiável antes de aplicar a regra. Para confirmar situação e dados cadastrais, utilize a consulta disponibilizada pela Secretaria da Fazenda correspondente ou serviços oficiais integrados.</p>\r\n\r\n<h2>Erros comuns em cadastros fiscais</h2>\r\n<ul>\r\n<li>aceitar qualquer sequência que tenha a quantidade correta de caracteres;</li>\r\n<li>confundir número válido com cadastro ativo;</li>\r\n<li>remover letras de um CNPJ alfanumérico para “normalizar” o valor;</li>\r\n<li>aplicar uma única regra de inscrição estadual a todas as UFs;</li>\r\n<li>alterar automaticamente zeros à esquerda;</li>\r\n<li>não registrar a origem e a data de uma consulta cadastral;</li>\r\n<li>bloquear o cadastro apenas porque um serviço externo está temporariamente indisponível, sem política definida de contingência.</li>\r\n</ul>\r\n\r\n<h2>Validação em lote: como reduzir retrabalho</h2>\r\n<p>Ao importar planilhas ou arquivos, valide antes de inserir definitivamente os registros. Separe as linhas em grupos: válidas, inválidas, duplicadas, inconsistentes e pendentes de consulta. Isso evita descobrir problemas somente depois que os dados já alimentaram emissão, cobrança ou obrigações.</p>\r\n<p>Também vale guardar uma mensagem de erro útil. Em vez de “documento inválido”, informe se o problema é formato, dígito verificador, UF ausente, duplicidade ou indisponibilidade da consulta.</p>\r\n\r\n<h2>Checklist para um cadastro mais confiável</h2>\r\n<ol>\r\n<li>Não destrua o valor original recebido.</li>\r\n<li>Normalize somente a formatação permitida.</li>\r\n<li>Valide o algoritmo adequado ao documento.</li>\r\n<li>Considere o CNPJ alfanumérico nas aplicações atualizadas.</li>\r\n<li>Para IE, selecione a regra pela UF.</li>\r\n<li>Quando necessário, consulte a situação cadastral em fonte oficial.</li>\r\n<li>Compare razão social e demais dados antes de confirmar o registro.</li>\r\n<li>Registre data, origem e resultado da validação quando houver necessidade de auditoria.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Valide documentos no Prazzu Tools</h2><p>O Validador Inteligente de CNPJ, CPF e IE do projeto oferece validação individual, consulta cadastral de CNPJ, análise de inconsistências e validação de inscrição estadual, além de recursos em lote conforme o nível de acesso.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/validador-de-cnpj\">Abrir Validador de CNPJ, CPF e IE</a></div></div>\r\n\r\n<h2>Fontes oficiais úteis</h2>\r\n<ul>\r\n<li><a href=\"https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/cnpj\" rel=\"nofollow noopener\" target=\"_blank\">Receita Federal — Cadastro Nacional da Pessoa Jurídica</a></li>\r\n<li><a href=\"https://www.gov.br/receitafederal/pt-br/acesso-a-informacao/acoes-e-programas/programas-e-atividades/cnpj-alfanumerico\" rel=\"nofollow noopener\" target=\"_blank\">Receita Federal — CNPJ Alfanumérico</a></li>\r\n</ul>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Um CNPJ com dígitos verificadores corretos está ativo?</h3>\r\n<p>Não necessariamente. A validação matemática verifica a formação do número. A situação cadastral precisa ser confirmada em consulta apropriada.</p>\r\n<h3>Os CNPJs antigos vão mudar para letras?</h3>\r\n<p>Não. Segundo a Receita Federal, os números já existentes permanecem válidos. O formato alfanumérico será usado em novas inscrições conforme a implantação oficial.</p>\r\n<h3>Posso validar inscrição estadual sem saber a UF?</h3>\r\n<p>Não é recomendável, porque as regras variam por estado. Identifique a UF antes de aplicar o algoritmo correspondente.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Saber <strong>como validar CNPJ</strong>, CPF e inscrição estadual evita erros simples antes que eles se espalhem por faturamento, fiscal e financeiro. A melhor rotina separa validação de formato, conferência de dígito, consulta oficial e análise de consistência — e, em 2026, também precisa estar preparada para o CNPJ alfanumérico.</p>', 2, 'Cadastros e Validações', 'contabilidade', 'blog/covers/como-validar-cnpj-cpf-inscricao-estadual.png', 'Validação de CNPJ, CPF e inscrição estadual em cadastro', 'published', 1, '2026-07-14 12:00:00', '2026-07-27 20:04:50', 'como validar CNPJ', '[\"validar CPF\", \"validar inscrição estadual\", \"CNPJ alfanumérico\", \"dígito verificador CNPJ\", \"consulta CNPJ Receita Federal\", \"validação cadastral\"]', 'Como validar CNPJ, CPF e inscrição estadual', 'Aprenda como validar CNPJ, CPF e inscrição estadual, evitar cadastros incorretos e preparar sistemas para o CNPJ alfanumérico em 2026.', NULL, 'blog/social/como-validar-cnpj-cpf-inscricao-estadual.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:04:50'),
(3, NULL, 'DARF e GPS: como conferir código, vencimento, juros e multa', 'como-gerar-darf-gps-codigo-vencimento-acrescimos', 'Entenda como conferir DARF e GPS, identificar a guia correta, validar código e vencimento e revisar multa e juros antes de efetuar o pagamento.', '<p><strong>DARF e GPS</strong> são documentos de arrecadação usados em contextos diferentes, e escolher a guia errada pode gerar retrabalho mesmo quando o valor pago está correto. Antes de emitir ou recalcular um pagamento, confira o tipo de obrigação, o código de receita, o período de apuração, o vencimento, o valor principal e a data prevista para pagamento.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> identifique primeiro qual documento deve ser usado. Depois confirme código, competência/período, vencimento e principal. Se houver atraso, calcule os acréscimos com a regra aplicável e compare o resultado com o canal oficial de emissão.</div>\r\n\r\n<h2>Qual é a diferença entre DARF e GPS?</h2>\r\n<p>O DARF é utilizado para pagamento de diversos tributos, taxas e contribuições administrados pela Receita Federal. A GPS permanece aplicável a determinadas contribuições previdenciárias, como as de contribuinte individual, segurado especial, segurado facultativo e situações vinculadas a GFIP/SEFIP.</p>\r\n<p>Há uma distinção importante: entidades obrigadas à DCTFWeb devem recolher as contribuições previdenciárias abrangidas pela declaração por meio do DARF gerado na própria DCTFWeb. Portanto, não escolha GPS apenas porque a obrigação é previdenciária.</p>\r\n\r\n<h2>1. Confirme a origem do débito</h2>\r\n<p>Antes de procurar um código, identifique de onde surgiu a obrigação: folha, retenção, imposto federal, declaração, parcelamento, regularização ou contribuição de pessoa física. A origem normalmente determina o documento e o canal correto de emissão.</p>\r\n<p>Se o débito já estiver constituído em declaração ou cobrança, prefira o canal indicado para aquela situação. A Receita Federal oferece, entre outros, SicalcWeb para diversos DARFs e DCTFWeb para débitos declarados nesse sistema.</p>\r\n\r\n<h2>2. Confira o código de receita</h2>\r\n<p>O código direciona o pagamento para a receita correspondente. Um código incorreto pode exigir retificação e impedir a baixa automática da obrigação esperada. Não escolha o código apenas por descrição parecida: confirme-o na tabela, declaração ou orientação oficial aplicável ao tributo.</p>\r\n<p>Registre junto ao cálculo o código utilizado e a fonte consultada. Isso facilita a revisão por outra pessoa antes do pagamento.</p>\r\n\r\n<h2>3. Revise período de apuração e vencimento</h2>\r\n<p>Período de apuração, competência e vencimento não são sinônimos. O período identifica quando o fato gerador ou obrigação ocorreu; o vencimento define até quando o pagamento pode ser feito sem os acréscimos legais previstos para atraso.</p>\r\n<p>Feriados, regras específicas e alterações normativas podem afetar datas. Quando o vencimento tiver impacto material, confirme-o no calendário ou orientação oficial daquela obrigação, em vez de reutilizar automaticamente a data do mês anterior.</p>\r\n\r\n<h2>Como funciona a multa de mora do DARF em atraso</h2>\r\n<p>Nas orientações do Sicalc para os débitos aos quais essa regra se aplica, a multa de mora é calculada à razão de <strong>0,33% por dia de atraso</strong>, limitada a <strong>20%</strong>. A contagem indicada pela Receita começa no primeiro dia útil seguinte ao vencimento e termina no dia do pagamento.</p>\r\n<div class=\"card border-secondary-subtle my-3\"><div class=\"card-body\"><strong>Exemplo didático:</strong> em uma situação sujeita à regra de 0,33% ao dia, 10 dias de atraso representam 3,30% de multa sobre o valor principal. Sempre valide se o débito específico segue essa sistemática.</div></div>\r\n\r\n<h2>Como funcionam os juros de mora</h2>\r\n<p>Para os débitos abrangidos pela regra geral apresentada pelo Sicalc, os juros acumulam a taxa Selic a partir do mês seguinte ao vencimento até o mês anterior ao pagamento, com acréscimo de 1% no mês do pagamento. O próprio SicalcWeb calcula os acréscimos para as receitas suportadas pelo sistema.</p>\r\n<p>Evite manter uma taxa Selic fixa em planilhas. A taxa muda com o período, e o cálculo deve respeitar o intervalo do débito. Se uma ferramenta solicitar a Selic acumulada, confirme o percentual na fonte utilizada para a competência.</p>\r\n\r\n<h2>GPS em atraso exige a mesma atenção</h2>\r\n<p>Para contribuições que ainda são recolhidas por GPS, utilize o serviço e as regras correspondentes à situação do segurado ou da entidade. Não transfira automaticamente regras de um DARF para uma GPS apenas porque ambos estão atrasados.</p>\r\n<p>A página oficial de emissão de GPS também alerta que contribuições abrangidas pela DCTFWeb devem ser pagas por DARF e que o empregador doméstico utiliza DAE do eSocial. Essa checagem evita gerar uma guia formalmente inadequada.</p>\r\n\r\n<h2>Checklist antes de pagar</h2>\r\n<ol>\r\n<li>Identifique o tributo ou contribuição.</li>\r\n<li>Confirme se o documento correto é DARF, GPS, DAE ou outro.</li>\r\n<li>Valide CPF/CNPJ e demais dados do contribuinte.</li>\r\n<li>Confira o código de receita.</li>\r\n<li>Confira período de apuração ou competência.</li>\r\n<li>Confirme o vencimento original.</li>\r\n<li>Informe o valor principal sem misturar multa e juros antigos.</li>\r\n<li>Calcule os acréscimos até a data real de pagamento.</li>\r\n<li>Compare a memória de cálculo com o sistema oficial aplicável.</li>\r\n<li>Guarde guia e comprovante após o pagamento.</li>\r\n</ol>\r\n\r\n<h2>Erros comuns que merecem revisão</h2>\r\n<ul>\r\n<li>usar GPS para contribuição que deveria sair da DCTFWeb;</li>\r\n<li>reaproveitar código de receita de outro tributo;</li>\r\n<li>informar competência correta com vencimento incorreto;</li>\r\n<li>calcular multa sobre um valor que já continha acréscimos;</li>\r\n<li>usar Selic de outro período;</li>\r\n<li>emitir a guia para uma data e pagar em data posterior sem recalcular;</li>\r\n<li>considerar uma simulação como substituta do documento oficial de arrecadação.</li>\r\n</ul>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Monte a memória no Prazzu Tools</h2><p>O Gerador Inteligente de DARF/GPS do projeto auxilia na identificação da guia, código, vencimento e acréscimos informados, mantendo uma memória de cálculo para conferência. Use o resultado como apoio e valide a emissão no canal oficial aplicável.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/gerador-darf-gps\">Abrir Gerador de DARF/GPS</a></div></div>\r\n\r\n<h2>Fontes oficiais para conferência</h2>\r\n<ul>\r\n<li><a href=\"https://www.gov.br/pt-br/servicos/emitir-darf-para-pagamento-de-tributos-federais\" rel=\"nofollow noopener\" target=\"_blank\">Gov.br — Emitir DARF para pagamento de tributos federais</a></li>\r\n<li><a href=\"https://sicalc.receita.fazenda.gov.br/sicalc/\" rel=\"nofollow noopener\" target=\"_blank\">Receita Federal — SicalcWeb</a></li>\r\n<li><a href=\"https://www.gov.br/pt-br/servicos/emitir-gps-para-pagamento-de-contribuicoes-previdenciarias\" rel=\"nofollow noopener\" target=\"_blank\">Gov.br — Emitir GPS para contribuições previdenciárias</a></li>\r\n</ul>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>GPS acabou?</h3>\r\n<p>Não. Ela continua sendo usada em situações específicas. Porém, débitos previdenciários de entidades obrigadas à DCTFWeb são pagos com DARF gerado pela declaração.</p>\r\n<h3>Posso pagar um DARF atrasado sem recalcular?</h3>\r\n<p>Se o débito estiver sujeito a acréscimos, a guia deve refletir a data efetiva do pagamento. Recalcule ou utilize o canal oficial para obter os valores atualizados.</p>\r\n<h3>A multa de mora é sempre 20%?</h3>\r\n<p>Não. Na regra geral descrita pelo Sicalc, ela cresce 0,33% por dia de atraso até atingir o limite de 20%. O percentual efetivo depende do número de dias e da regra aplicável ao débito.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Conferir <strong>DARF e GPS</strong> corretamente exige mais do que calcular juros. A sequência segura é identificar o documento adequado, validar código e período, confirmar vencimento, calcular acréscimos quando necessários e comparar com o sistema oficial antes do pagamento.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/como-gerar-darf-gps-codigo-vencimento-acrescimos.png', 'Conferência de DARF e GPS com vencimento, juros e multa', 'published', 1, '2026-07-15 12:00:00', '2026-07-27 20:04:50', 'DARF e GPS', '[\"calcular DARF em atraso\", \"GPS em atraso\", \"multa de mora DARF\", \"juros Selic DARF\", \"SicalcWeb\", \"DCTFWeb DARF\", \"código de receita\"]', 'DARF e GPS: código, vencimento, juros e multa', 'Confira DARF e GPS: quando usar cada guia, como revisar código e vencimento e como funcionam multa de mora e juros em pagamentos atrasados.', NULL, 'blog/social/como-gerar-darf-gps-codigo-vencimento-acrescimos.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:04:50'),
(4, NULL, 'Como converter XML de NF-e e NFC-e em planilha com segurança', 'como-converter-xml-nfe-nfce-planilha', 'Aprenda como converter XML de NF-e e NFC-e em planilha, quais campos extrair, como tratar lotes e quais conferências fazer antes de usar os dados no fiscal.', '<p><strong>Converter XML de NF-e</strong> e NFC-e em planilha pode economizar horas de digitação, mas a conversão precisa preservar a estrutura fiscal do documento. O objetivo não é apenas transformar XML em colunas: é identificar corretamente emitente, destinatário, itens, NCM, CFOP, tributos, totais e possíveis inconsistências.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> trabalhe com o XML fiscal original, valide o modelo e a estrutura, separe dados do cabeçalho e dos itens, normalize sem perder o valor de origem e confira totais antes de usar a planilha em conciliações ou análises.</div>\r\n\r\n<h2>Por que usar o XML em vez de copiar dados do DANFE</h2>\r\n<p>O DANFE é uma representação auxiliar da NF-e e não contém a mesma riqueza estruturada do XML. Para tratamento de dados, o XML permite ler campos de forma automatizada, reduzir digitação e preservar informações por item.</p>\r\n<p>Em rotinas com dezenas ou milhares de documentos, essa diferença é importante. Copiar manualmente número, chave, NCM, CFOP, quantidade, valores e tributos aumenta o risco de erro e dificulta a repetição do processo.</p>\r\n\r\n<h2>Quais dados normalmente devem ir para a planilha</h2>\r\n<p>A estrutura ideal depende do objetivo, mas uma exportação fiscal útil costuma separar informações do documento e dos produtos.</p>\r\n<ul>\r\n<li><strong>Identificação:</strong> modelo, chave de acesso, número, série e data de emissão.</li>\r\n<li><strong>Emitente:</strong> CNPJ/CPF, nome e UF.</li>\r\n<li><strong>Destinatário:</strong> CNPJ/CPF, nome e UF quando informados.</li>\r\n<li><strong>Produtos:</strong> código, descrição, NCM, CFOP, unidade, quantidade e valores.</li>\r\n<li><strong>Tributos:</strong> campos disponíveis de ICMS, IPI, PIS e Cofins conforme a estrutura do documento.</li>\r\n<li><strong>Totais:</strong> produtos, descontos, frete, impostos e valor total da nota.</li>\r\n</ul>\r\n<p>Quando houver mais de um item por nota, evite colocar todos os produtos em uma única célula. Para análise, o formato mais prático costuma ser uma linha por item, repetindo os identificadores principais do documento.</p>\r\n\r\n<h2>Passo a passo para converter XML de NF-e</h2>\r\n<ol>\r\n<li>Selecione os XMLs originais que serão processados.</li>\r\n<li>Identifique o modelo do documento e a versão do layout.</li>\r\n<li>Faça a leitura dos campos sem alterar o arquivo de origem.</li>\r\n<li>Separe cabeçalho, participantes, itens, tributos e totais.</li>\r\n<li>Normalize datas e números apenas na camada de exportação.</li>\r\n<li>Registre arquivos rejeitados e o motivo do erro.</li>\r\n<li>Compare somatórios da planilha com os totais dos documentos.</li>\r\n<li>Exporte no formato apropriado para a etapa seguinte.</li>\r\n</ol>\r\n\r\n<h2>NF-e e NFC-e: trate o modelo corretamente</h2>\r\n<p>NF-e e NFC-e compartilham conceitos, mas possuem usos e preenchimentos diferentes. O conversor deve identificar o modelo do documento antes de assumir que todos os campos estarão presentes. Em NFC-e, por exemplo, informações do destinatário podem não estar preenchidas em todas as operações.</p>\r\n<p>Uma boa exportação deixa campos ausentes como ausentes, em vez de inventar valores para completar a planilha.</p>\r\n\r\n<h2>NCM e CFOP merecem colunas próprias</h2>\r\n<p>NCM e CFOP são essenciais para análises fiscais e de cadastro de produtos. Ao converter, preserve os valores exatamente como encontrados no XML e faça qualquer classificação adicional em colunas separadas.</p>\r\n<p>Isso permite identificar produtos sem NCM esperado, CFOP divergente entre operações semelhantes e mudanças de classificação ao longo do tempo sem apagar a evidência original.</p>\r\n\r\n<h2>Como tratar impostos no XML</h2>\r\n<p>Os grupos tributários podem variar conforme regime, operação e item. Em vez de criar uma única coluna chamada “imposto”, extraia os campos necessários de forma organizada. O mesmo documento pode conter itens com tratamentos diferentes.</p>\r\n<p>Para conciliações, mantenha valores de base, alíquota e imposto em campos separados sempre que disponíveis. Isso facilita comparar a origem do valor e evita cálculos reversos desnecessários.</p>\r\n\r\n<h2>Conversão em lote: controle erros por arquivo</h2>\r\n<p>Em lote, não interrompa todo o processamento por causa de um único XML problemático. O fluxo ideal separa documentos processados de arquivos com erro, registrando nome, motivo e etapa da falha. Assim, o usuário pode corrigir somente as exceções.</p>\r\n<p>Também é útil detectar duplicidades pela chave de acesso antes de consolidar períodos. Duplicar o mesmo XML na planilha pode distorcer faturamento, quantidade de itens e totais fiscais.</p>\r\n\r\n<h2>Conferências antes de usar a planilha</h2>\r\n<ul>\r\n<li>quantidade de XMLs recebidos versus processados;</li>\r\n<li>chaves de acesso duplicadas;</li>\r\n<li>modelo e período dos documentos;</li>\r\n<li>soma dos itens comparada ao total esperado;</li>\r\n<li>documentos cancelados ou eventos, quando fizerem parte do fluxo analisado;</li>\r\n<li>campos obrigatórios ausentes ou estruturalmente inválidos;</li>\r\n<li>diferenças de arredondamento e descontos;</li>\r\n<li>arquivos que não são XML fiscal compatível.</li>\r\n</ul>\r\n\r\n<h2>Segurança e privacidade dos arquivos</h2>\r\n<p>XML fiscal pode conter dados empresariais e pessoais. Defina quem pode acessar os arquivos, por quanto tempo serão armazenados e onde os resultados serão salvos. Evite enviar documentos reais para serviços desconhecidos sem avaliar política de privacidade e necessidade do tratamento.</p>\r\n<p>Quando houver histórico ou armazenamento, proteja os dados sensíveis e permita exclusão conforme a política do sistema. Para testes, prefira documentos fictícios ou devidamente anonimizados quando possível.</p>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Converta XML fiscal no Prazzu Tools</h2><p>O Conversor Fiscal de XML do projeto lê NF-e e NFC-e e extrai emitente, destinatário, produtos, NCM, CFOP, impostos e totais, além de alertas de consistência. O módulo também prevê processamento em lote e exportações em CSV, JSON e XLSX conforme o nível de acesso.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/conversor-fiscal-xml\">Abrir Conversor Fiscal de XML</a></div></div>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Posso converter XML para Excel?</h3>\r\n<p>Sim. Os dados estruturados podem ser exportados para XLSX ou CSV. Para análise fiscal, organize as colunas de forma que seja possível rastrear cada linha até o documento e item de origem.</p>\r\n<h3>O DANFE substitui o XML?</h3>\r\n<p>Não. O DANFE é um documento auxiliar para representação da nota. O XML é o arquivo eletrônico estruturado utilizado como fonte de dados da NF-e/NFC-e.</p>\r\n<h3>Posso juntar várias notas em uma única planilha?</h3>\r\n<p>Sim, desde que a exportação mantenha chave, número, série e identificação do item. Também é importante bloquear ou sinalizar chaves duplicadas.</p>\r\n<h3>Converter o XML valida a tributação da nota?</h3>\r\n<p>Não necessariamente. A extração pode apontar inconsistências estruturais e facilitar a revisão, mas a correção do enquadramento tributário depende da operação, legislação e dados do contribuinte.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p><strong>Converter XML de NF-e</strong> em planilha é mais útil quando a exportação mantém rastreabilidade, separa itens, preserva NCM e CFOP e registra erros do lote. Com essas conferências, a planilha deixa de ser apenas uma cópia do XML e passa a ser uma base confiável para análises e conciliações.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/como-converter-xml-nfe-nfce-planilha.png', 'Conversão de XML de NF-e e NFC-e para planilha fiscal', 'published', 0, '2026-07-16 12:00:00', '2026-07-27 20:04:50', 'converter XML de NF-e', '[\"XML para Excel\", \"converter XML para planilha\", \"NF-e para Excel\", \"NFC-e XML\", \"extrair NCM CFOP do XML\", \"XML fiscal em lote\"]', 'Como converter XML de NF-e e NFC-e em planilha', 'Veja como converter XML de NF-e e NFC-e em planilha, extrair produtos, NCM, CFOP, impostos e totais e validar os dados antes de usar no fiscal.', NULL, 'blog/social/como-converter-xml-nfe-nfce-planilha.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:04:50'),
(5, NULL, 'Como calcular rescisão trabalhista passo a passo', 'como-calcular-rescisao-trabalhista', 'Aprenda como calcular rescisão trabalhista e confira saldo de salário, aviso-prévio, 13º, férias, FGTS e descontos conforme o motivo do desligamento.', '<p><strong>Como calcular rescisão trabalhista</strong> corretamente depende primeiro do motivo do desligamento. Pedido de demissão, dispensa sem justa causa, justa causa, acordo e término de contrato podem gerar verbas diferentes. Por isso, antes de aplicar fórmulas, confirme datas, remuneração, férias, aviso-prévio e ocorrências do contrato.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> classifique o desligamento, confira admissão e término, apure saldo de salário, 13º e férias, trate o aviso-prévio conforme o caso, revise FGTS e descontos e preserve uma memória de cálculo antes de fechar a rescisão.</div>\r\n<h2>1. Identifique o motivo do desligamento</h2><p>Essa é a etapa que define quais parcelas entram no cálculo. No pedido de demissão, por exemplo, não há a mesma combinação de direitos existente na dispensa sem justa causa. No acordo entre empregado e empregador também existem regras próprias. Não use uma rescisão anterior como modelo sem confirmar a modalidade.</p>\r\n<h2>2. Confira as datas do contrato</h2><p>Registre admissão, comunicação do desligamento, último dia trabalhado e eventual projeção do aviso. Datas erradas alteram avos de 13º, férias e outras parcelas. Se houver afastamentos ou mudanças contratuais relevantes, mantenha esses eventos disponíveis para revisão.</p>\r\n<h2>3. Calcule o saldo de salário</h2><p>O saldo corresponde à remuneração dos dias trabalhados no mês da saída, observando a forma de remuneração e as verbas que integram a base no caso concreto. Antes de calcular, confira faltas, adicionais, horas extras, comissões e outros eventos que possam repercutir no fechamento.</p>\r\n<h2>4. Revise o aviso-prévio</h2><p>O aviso pode ser trabalhado ou indenizado conforme a modalidade de desligamento. Na dispensa sem justa causa, a duração também pode ser influenciada pelo tempo de serviço. Já no pedido de demissão, o tratamento é diferente e pode haver desconto quando o empregado não cumpre o aviso devido, ressalvadas as situações aplicáveis.</p>\r\n<p>Não trate o aviso como um campo isolado: sua projeção pode repercutir em datas e parcelas da rescisão.</p>\r\n<h2>5. Calcule o 13º salário proporcional</h2><p>Confira os avos adquiridos no ano da rescisão e a remuneração que deve compor a base. Remuneração variável exige atenção às médias e às regras aplicáveis. Registre a quantidade de avos utilizada para que outra pessoa consiga reproduzir o resultado.</p>\r\n<h2>6. Confira férias vencidas e proporcionais</h2><p>Separe períodos já adquiridos de férias proporcionais do período em curso. Quando devidas, as férias são acrescidas do terço constitucional. Faltas e situações específicas do contrato podem alterar o tratamento, portanto a memória deve mostrar quais períodos foram considerados.</p>\r\n<h2>7. FGTS e multa rescisória</h2><p>O tratamento do FGTS varia conforme o desligamento. O Ministério do Trabalho informa, por exemplo, que no pedido de demissão o trabalhador não recebe a multa rescisória nem realiza o saque nas mesmas condições da dispensa sem justa causa. No acordo previsto na legislação, há regras próprias para multa e saque. Confirme sempre a modalidade antes de lançar percentuais.</p>\r\n<h2>8. Descontos e bases de INSS e IRRF</h2><p>Separe proventos de descontos e identifique quais verbas integram cada base. Adiantamentos, faltas, benefícios e outros descontos precisam ter origem documentada. Evite aplicar um percentual genérico sobre o total da rescisão.</p>\r\n<h2>Exemplo de conferência</h2><p>Em vez de começar pelo valor líquido, monte uma tabela com cada verba: saldo de salário, aviso, 13º, férias, terço constitucional e demais parcelas. Em seguida, apresente os descontos separadamente. Essa estrutura torna mais fácil encontrar um avo incorreto, uma base duplicada ou um desconto sem justificativa.</p>\r\n<h2>Checklist antes de fechar a rescisão</h2><ol><li>Confirme a modalidade do desligamento.</li><li>Revise admissão, aviso e término.</li><li>Confira salário e remunerações variáveis.</li><li>Apure saldo de salário.</li><li>Revise 13º e quantidade de avos.</li><li>Separe férias vencidas e proporcionais.</li><li>Confira aviso-prévio e seus reflexos.</li><li>Revise FGTS conforme o desligamento.</li><li>Valide bases e descontos.</li><li>Compare o resultado com os documentos e eventos enviados.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule a rescisão no Prazzu Tools</h2><p>Use a Calculadora de Rescisão para organizar os dados e obter uma memória de cálculo que facilite a conferência das verbas antes do fechamento.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-de-rescisao\">Abrir Calculadora de Rescisão</a></div></div>\r\n<h2>Perguntas frequentes</h2><h3>Toda rescisão tem multa de 40% do FGTS?</h3><p>Não. O tratamento depende do motivo do desligamento. Pedido de demissão, acordo e dispensa sem justa causa não devem ser calculados como se fossem a mesma situação.</p><h3>Férias proporcionais recebem adicional de um terço?</h3><p>Quando devidas na rescisão, as férias proporcionais são pagas com o terço constitucional, observadas as regras aplicáveis ao caso.</p><h3>Posso usar apenas o valor líquido para conferir?</h3><p>Não é o ideal. Confira verba por verba e depois os descontos. O líquido sozinho pode esconder erros que se compensam entre si.</p>\r\n<h2>Conclusão</h2><p>Saber <strong>como calcular rescisão trabalhista</strong> é principalmente saber organizar as premissas. Quando motivo, datas, avos, aviso, FGTS e descontos ficam explícitos, a conferência se torna mais segura e o cálculo pode ser reproduzido antes de qualquer pagamento ou transmissão oficial.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/como-calcular-rescisao-trabalhista.png', 'Cálculo de rescisão trabalhista com verbas, aviso-prévio, férias e FGTS', 'published', 0, '2026-07-17 12:00:00', '2026-07-27 20:09:51', 'como calcular rescisão trabalhista', '[\"cálculo de rescisão\", \"verbas rescisórias\", \"aviso-prévio\", \"férias proporcionais\", \"13º proporcional\", \"FGTS rescisão\"]', 'Como calcular rescisão trabalhista passo a passo', 'Veja como calcular rescisão trabalhista, quais verbas conferir em cada desligamento e como revisar aviso, férias, 13º, FGTS e descontos.', NULL, 'blog/social/como-calcular-rescisao-trabalhista.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:09:51'),
(6, NULL, 'Margem e markup: como calcular o preço de venda corretamente', 'margem-markup-como-calcular-preco-venda', 'Entenda margem e markup, aprenda a formar o preço de venda com custos, despesas, impostos, comissões e lucro e descubra por que os percentuais não são iguais.', '<p><strong>Margem e markup</strong> ajudam a formar e analisar preços, mas não significam a mesma coisa. A margem mede quanto do preço de venda permanece depois dos custos considerados; o markup é um fator aplicado sobre uma base de custo para chegar ao preço. Confundir os dois pode fazer uma empresa acreditar que possui uma rentabilidade que o preço real não entrega.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> primeiro mapeie custos e percentuais que incidem sobre a venda. Depois defina a margem desejada e calcule o preço. Use o markup como mecanismo de formação e a margem como indicador para conferir o resultado.</div>\r\n<h2>Qual é a diferença entre margem e markup?</h2><p>Se um produto custa R$ 100 e é vendido por R$ 150, houve um acréscimo de 50% sobre o custo. Isso não significa margem de 50% sobre a venda. Antes de falar em lucro, ainda podem existir impostos, comissão, taxa do cartão, frete subsidiado e outras despesas.</p><p>A diferença de base é o ponto central: percentuais sobre custo e percentuais sobre preço de venda produzem resultados diferentes.</p>\r\n<h2>Comece pelo custo correto</h2><p>Use o custo que realmente representa colocar o produto ou serviço em condição de venda. Conforme o negócio, isso pode envolver aquisição, matéria-prima, frete de entrada, embalagem, perdas e custos diretamente atribuíveis. Não misture custos recuperáveis e não recuperáveis sem critério.</p>\r\n<h2>Mapeie despesas percentuais da venda</h2><p>Liste tributos sobre faturamento, comissões, taxas de marketplace, meios de pagamento, royalties e outras despesas que variam com o preço. Se uma despesa corresponde a 5% da venda, ela consome 5 pontos percentuais do preço final e deve entrar na formação.</p>\r\n<h2>Defina a margem desejada</h2><p>A margem desejada precisa coexistir com os demais percentuais. Por isso, simplesmente somar 20% ao custo não garante margem de 20% sobre o preço. O preço deve ser suficiente para pagar o custo, as despesas variáveis e ainda preservar o resultado pretendido.</p>\r\n<h2>Como funciona o markup divisor</h2><p>Uma abordagem prática é somar os percentuais incidentes sobre a venda e a margem desejada, transformar o restante em um divisor e dividir o custo por esse fator. Se impostos, comissões, despesas e margem consumirem 40% do preço, restam 60% para cobrir a base de custo; o custo é então dividido por 0,60.</p><p>Esse exemplo é didático. A classificação de cada componente deve refletir a realidade do negócio.</p>\r\n<h2>Exemplo completo</h2><p>Considere custo de R$ 120. Suponha que os percentuais sobre a venda totalizem 25% e que a empresa deseje 15% de margem após esses componentes. O conjunto consome 40% do preço, deixando 60% para o custo. O preço de referência seria R$ 120 ÷ 0,60 = R$ 200.</p><p>No preço de R$ 200, R$ 50 correspondem aos 25% de despesas percentuais e R$ 30 aos 15% de margem considerada no exemplo, restando R$ 120 para o custo.</p>\r\n<h2>Desconto pode destruir a margem</h2><p>Se o preço calculado for reduzido sem recalcular os componentes, o desconto não sai apenas do lucro: impostos e comissões podem continuar incidindo sobre a venda. Simule o preço promocional antes de autorizar descontos recorrentes.</p>\r\n<h2>E os custos fixos?</h2><p>Aluguel, equipe administrativa, sistemas e outras despesas fixas precisam ser cobertos pelo conjunto das vendas. A empresa pode incorporá-los à formação por um critério de rateio ou acompanhá-los por margem de contribuição e ponto de equilíbrio. O importante é não esquecê-los ao avaliar se o preço sustenta a operação.</p>\r\n<h2>Checklist de formação de preço</h2><ol><li>Defina o custo-base.</li><li>Liste despesas variáveis.</li><li>Confirme tributos incidentes.</li><li>Inclua comissões e taxas.</li><li>Defina a margem pretendida.</li><li>Calcule o preço de referência.</li><li>Teste descontos e cenários.</li><li>Compare com mercado e posicionamento.</li><li>Revise o preço quando custos mudarem.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule margem e markup no Prazzu Tools</h2><p>Use a calculadora para testar custo, percentuais e preço de venda sem confundir acréscimo sobre custo com margem sobre a receita.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-margem-markup\">Abrir Calculadora de Margem e Markup</a></div></div>\r\n<h2>Perguntas frequentes</h2><h3>Markup de 50% significa margem de 50%?</h3><p>Não. As bases são diferentes. Um acréscimo de 50% sobre o custo não equivale automaticamente a uma margem de 50% sobre o preço de venda.</p><h3>Posso usar o mesmo markup para todos os produtos?</h3><p>Somente se custos, impostos, comissões, riscos e estratégia forem equivalentes. Categorias diferentes frequentemente exigem parâmetros diferentes.</p><h3>Preço calculado precisa ser o preço final?</h3><p>Não. Ele é uma referência econômica. Posicionamento, concorrência e percepção de valor também importam, mas qualquer alteração deve ser testada contra a margem mínima aceitável.</p><h2>Conclusão</h2><p>Usar <strong>margem e markup</strong> corretamente torna a formação de preço verificável. Em vez de escolher um percentual arbitrário, mostre quais parcelas o preço precisa cobrir e simule o efeito de descontos, impostos e mudanças de custo antes de vender.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/margem-markup-como-calcular-preco-venda.png', 'Cálculo de margem e markup para formação do preço de venda', 'published', 0, '2026-07-18 12:00:00', '2026-07-27 20:09:51', 'margem e markup', '[\"como calcular markup\", \"como calcular margem\", \"preço de venda\", \"formação de preço\", \"margem de lucro\", \"markup divisor\"]', 'Margem e markup: como calcular o preço de venda', 'Entenda margem e markup, veja a diferença entre os conceitos e aprenda a calcular um preço de venda que cubra custos, despesas, tributos e lucro.', NULL, 'blog/social/margem-markup-como-calcular-preco-venda.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:09:51');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `vertical_slug`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(7, NULL, 'Pró-labore e distribuição de lucros: como calcular', 'pro-labore-distribuicao-lucros-como-definir', 'Entenda a diferença entre pró-labore e distribuição de lucros, como organizar a remuneração dos sócios e quais dados contábeis devem ser conferidos antes da retirada.', '<p><strong>Pró-labore e distribuição de lucros</strong> são formas diferentes de remunerar sócios e não devem ser tratadas como se fossem a mesma retirada. O pró-labore está ligado ao trabalho do sócio na empresa; a distribuição depende da existência de resultado e da documentação contábil e societária adequada.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> identifique quem trabalha na operação, defina e documente o pró-labore, apure corretamente encargos e retenções e só trate uma retirada como distribuição de lucros quando houver base para demonstrar o resultado disponível.</div>\r\n<h2>O que é pró-labore?</h2><p>Pró-labore é a remuneração atribuída ao sócio pelo trabalho prestado à empresa. Ele não deve ser confundido com salário de empregado nem com lucro do investimento. A definição do valor precisa considerar a função exercida, a capacidade financeira da empresa e as regras previdenciárias e fiscais vigentes.</p>\r\n<h2>O que é distribuição de lucros?</h2><p>Distribuição de lucros corresponde à destinação de resultado da empresa aos sócios. Para saber quanto pode ser distribuído, é necessário conhecer o lucro efetivamente apurado e observar a escrituração, o contrato social e as demais regras aplicáveis.</p><p>Saldo em conta bancária não é sinônimo de lucro. Parte do caixa pode estar comprometida com fornecedores, impostos, salários, empréstimos ou capital de giro.</p>\r\n<h2>Por que não substituir todo pró-labore por lucro?</h2><p>Quando o sócio trabalha efetivamente na empresa, simplesmente chamar todas as retiradas de lucro não altera a natureza econômica dos fatos. A rotina deve separar remuneração pelo trabalho de remuneração do capital e manter documentação coerente com o que realmente acontece.</p>\r\n<h2>Como definir um pró-labore de referência</h2><p>Mapeie a função do sócio, responsabilidade, dedicação e capacidade de pagamento da empresa. Depois simule os efeitos previdenciários e tributários do valor escolhido. O objetivo não é encontrar um número mágico, mas adotar um critério defensável e sustentável.</p>\r\n<h2>Como saber quanto lucro pode ser distribuído</h2><p>Parta da apuração contábil e verifique resultados acumulados, prejuízos, reservas ou restrições aplicáveis. Em seguida, confira a participação de cada sócio e eventuais regras específicas do contrato social ou deliberação dos sócios.</p><p>Se a empresa não possui contabilidade capaz de demonstrar o lucro, podem existir limitações e critérios fiscais específicos. Nesses casos, a análise deve ser feita com base na legislação vigente e na situação concreta.</p>\r\n<h2>Exemplo de organização</h2><p>Imagine dois sócios que trabalham na empresa e também participam do capital. A rotina pode registrar mensalmente o pró-labore de cada um e, após a apuração de resultado disponível, documentar separadamente eventual distribuição. Assim, extratos e contabilidade mostram claramente a natureza de cada transferência.</p>\r\n<h2>Preserve o capital de giro</h2><p>Mesmo quando existe lucro contábil, distribuir todo o valor pode enfraquecer o caixa. Antes da retirada, projete impostos, folha, fornecedores, parcelas de empréstimos e investimentos próximos. Uma política de distribuição pode reservar parte do resultado para financiar a operação.</p>\r\n<h2>Checklist antes da retirada</h2><ol><li>Identifique os sócios que trabalham na empresa.</li><li>Documente o critério do pró-labore.</li><li>Calcule encargos e retenções aplicáveis.</li><li>Apure o resultado contábil.</li><li>Confira prejuízos e saldos anteriores.</li><li>Revise participação societária e contrato social.</li><li>Projete necessidade de caixa.</li><li>Registre separadamente pró-labore e lucro.</li><li>Guarde a documentação da deliberação quando aplicável.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Compare pró-labore e lucros no Prazzu Tools</h2><p>Use a calculadora do projeto para organizar cenários de remuneração e visualizar separadamente os componentes da retirada dos sócios.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-pro-labore-distribuicao-lucros\">Abrir Calculadora de Pró-labore e Lucros</a></div></div>\r\n<h2>Perguntas frequentes</h2><h3>Pró-labore e distribuição de lucros são a mesma coisa?</h3><p>Não. O pró-labore remunera o trabalho do sócio; a distribuição decorre do resultado disponível da empresa.</p><h3>Dinheiro no banco significa lucro disponível?</h3><p>Não. Caixa e lucro são grandezas diferentes. O saldo bancário pode incluir recursos necessários para obrigações e capital de giro.</p><h3>Posso definir o pró-labore apenas pelo valor que quero retirar?</h3><p>É melhor usar critérios ligados à atuação do sócio, capacidade da empresa e regras aplicáveis, mantendo coerência entre documentação, contabilidade e movimentação financeira.</p><h2>Conclusão</h2><p>Organizar <strong>pró-labore e distribuição de lucros</strong> exige separar trabalho, resultado e caixa. Essa distinção melhora a contabilidade, facilita a conferência das retiradas e evita decisões baseadas apenas no saldo disponível na conta da empresa.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/pro-labore-distribuicao-lucros-como-definir.png', 'Pró-labore e distribuição de lucros para remuneração dos sócios', 'published', 0, '2026-07-19 12:00:00', '2026-07-27 20:09:51', 'pró-labore e distribuição de lucros', '[\"como calcular pró-labore\", \"distribuição de lucros\", \"remuneração dos sócios\", \"INSS pró-labore\", \"lucro contábil\", \"retirada de sócios\"]', 'Pró-labore e distribuição de lucros: como calcular', 'Veja a diferença entre pró-labore e distribuição de lucros, como organizar a remuneração dos sócios e quais bases conferir antes de fazer retiradas.', NULL, 'blog/social/pro-labore-distribuicao-lucros-como-definir.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:09:51'),
(8, NULL, 'Simples Nacional: como calcular a alíquota efetiva', 'simples-nacional-como-calcular-das-fator-r', 'Aprenda como calcular o Simples Nacional usando RBT12, anexo, alíquota nominal e parcela a deduzir e veja quais pontos precisam ser conferidos antes do DAS.', '<p><strong>Como calcular o Simples Nacional</strong> sem confundir a alíquota da tabela com a alíquota realmente aplicada? Para empresas já em atividade, a apuração normalmente exige identificar a receita bruta acumulada em 12 meses (RBT12), localizar o anexo e a faixa corretos e calcular a alíquota efetiva com a parcela a deduzir.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme atividade e anexo, apure a RBT12 correta, localize alíquota nominal e parcela a deduzir, calcule a alíquota efetiva e só então aplique o percentual à receita tributável do período, observando segregações e regras específicas.</div>\r\n<h2>Alíquota nominal não é necessariamente a alíquota do DAS</h2><p>As tabelas dos anexos apresentam faixas de receita, alíquota nominal e parcela a deduzir. A alíquota nominal serve de componente para chegar à alíquota efetiva. Por isso, pegar o percentual exibido na faixa e multiplicá-lo diretamente pelo faturamento pode produzir um resultado incorreto.</p>\r\n<h2>1. Calcule a RBT12</h2><p>RBT12 é a receita bruta acumulada nos 12 meses anteriores ao período de apuração, observadas as regras do regime. Ela é usada para localizar a faixa correspondente. Empresas em início de atividade possuem regras próprias de proporcionalização, então não improvise uma RBT12 quando ainda não existem 12 meses completos.</p>\r\n<h2>2. Identifique o anexo correto</h2><p>Comércio, indústria e diferentes serviços podem estar em anexos distintos. Além disso, determinadas atividades de serviços podem depender do Fator R para definição entre anexos. O CNAE isolado nem sempre resolve toda a classificação: confira a receita e a atividade efetivamente exercida.</p>\r\n<h2>3. Localize alíquota nominal e parcela a deduzir</h2><p>Depois de identificar RBT12 e anexo, encontre a faixa. A tabela fornece a alíquota nominal e o valor a deduzir. Esses parâmetros devem corresponder ao período da apuração, pois alterações legais podem modificar tabelas futuras.</p>\r\n<h2>4. Calcule a alíquota efetiva</h2><p>A fórmula tradicional é: <strong>[(RBT12 × alíquota nominal) − parcela a deduzir] ÷ RBT12</strong>. O resultado é a alíquota efetiva usada como referência na apuração, sujeita às particularidades e segregações de cada receita.</p>\r\n<h2>Exemplo didático</h2><p>Suponha uma empresa comercial com RBT12 de R$ 300.000. Na tabela aplicável ao exemplo, a segunda faixa do Anexo I apresenta alíquota nominal de 7,30% e parcela a deduzir de R$ 5.940. A conta é: [(300.000 × 7,30%) − 5.940] ÷ 300.000. O resultado é 5,32% de alíquota efetiva de referência.</p><p>Se a receita tributável do mês fosse R$ 25.000 e não houvesse outra particularidade no exemplo, 5,32% corresponderiam a R$ 1.330. Use o exemplo para entender a fórmula, não como substituto da apuração oficial.</p>\r\n<h2>Fator R pode mudar o anexo de serviços</h2><p>Para atividades sujeitas ao Fator R, a relação entre folha/remunerações consideradas e receita influencia o enquadramento entre anexos previstos na legislação. Antes de calcular, confira se a atividade está sujeita a essa regra e use os períodos corretos.</p>\r\n<h2>Segregação de receitas é essencial</h2><p>Uma empresa pode possuir receitas com tratamentos diferentes. Substituição tributária, tributação monofásica, exportação, retenções e outras situações podem afetar a apuração. Não aplique uma única alíquota sobre todo o faturamento sem classificar as receitas.</p>\r\n<h2>Reforma tributária e datas de vigência</h2><p>As regras do Simples Nacional passam por adaptações relacionadas à reforma tributária. Tabelas e repartições podem ter vigências específicas nos próximos anos. Para cálculos de 2026, utilize parâmetros vigentes em 2026; para períodos futuros, confirme a legislação correspondente à data de apuração.</p>\r\n<h2>Checklist antes de conferir o DAS</h2><ol><li>Confirme se a empresa está no Simples no período.</li><li>Revise atividades e anexos.</li><li>Calcule a RBT12 correta.</li><li>Verifique Fator R quando aplicável.</li><li>Localize faixa, alíquota nominal e dedução.</li><li>Calcule a alíquota efetiva.</li><li>Segregue as receitas do mês.</li><li>Confira particularidades de ICMS e ISS quando aplicáveis.</li><li>Compare a simulação com o PGDAS-D antes de concluir a obrigação.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule o Simples Nacional no Prazzu Tools</h2><p>Use a calculadora para organizar RBT12, anexo e parâmetros do cenário e entender a formação da alíquota antes de conferir a apuração oficial.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-simples-nacional\">Abrir Calculadora do Simples Nacional</a></div></div>\r\n<h2>Perguntas frequentes</h2><h3>A alíquota da faixa é a alíquota efetiva?</h3><p>Não necessariamente. A alíquota nominal e a parcela a deduzir entram na fórmula que determina a alíquota efetiva.</p><h3>RBT12 é o faturamento do mês?</h3><p>Não. Ela representa a receita bruta acumulada no período de 12 meses utilizado pela regra, enquanto a receita do período de apuração é a base mensal analisada para o DAS.</p><h3>Toda empresa de serviços usa o mesmo anexo?</h3><p>Não. A atividade e, em determinados casos, o Fator R influenciam o enquadramento. É necessário classificar a receita antes de calcular.</p><h2>Conclusão</h2><p>Entender <strong>como calcular o Simples Nacional</strong> evita o erro comum de aplicar diretamente a alíquota nominal. RBT12, anexo, parcela a deduzir, alíquota efetiva e segregação de receitas precisam estar coerentes antes da conferência no sistema oficial.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/simples-nacional-como-calcular-das-fator-r.png', 'Cálculo do Simples Nacional com RBT12, faixa e alíquota efetiva', 'published', 0, '2026-07-20 12:00:00', '2026-07-27 20:09:51', 'Simples Nacional', '[\"como calcular o Simples Nacional\", \"alíquota efetiva Simples Nacional\", \"RBT12\", \"anexos Simples Nacional\", \"cálculo DAS\", \"fator R\"]', 'Como calcular o Simples Nacional e a alíquota efetiva', 'Aprenda como calcular o Simples Nacional com RBT12, anexo, alíquota nominal e parcela a deduzir e saiba o que conferir antes de apurar o DAS.', NULL, 'blog/social/simples-nacional-como-calcular-das-fator-r.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:09:51'),
(9, NULL, 'Simples, Lucro Presumido ou Lucro Real: como comparar', 'simples-lucro-presumido-lucro-real-comparacao', 'Compare regimes tributários considerando receita, margem, folha, créditos, atividade, custo de conformidade e fluxo de caixa antes de escolher.', '<p><strong>regimes tributários</strong> exige conferir premissas antes de chegar ao número final. Este guia organiza a rotina em etapas práticas, com foco em reduzir erros, deixar a memória de cálculo compreensível e facilitar a revisão por outra pessoa.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme dados, período e regra aplicável; calcule cada componente separadamente; registre a memória e valide o resultado antes de usar a informação em uma obrigação oficial.</div><h2>O que reunir antes da comparação</h2><p>Use dados do mesmo período: receita por atividade, folha, pró-labore, margem, compras, despesas, retenções e localização. Trabalhar com bases de períodos diferentes pode produzir uma comparação matematicamente correta, mas economicamente inútil. Registre também a fonte e a data de cada premissa.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Simples Nacional</h2><p>Confira elegibilidade, atividade, anexo, RBT12, segregações e Fator R quando aplicável. O DAS simplifica o recolhimento, mas isso não significa que toda receita receba o mesmo tratamento. Crescimento de faturamento também pode alterar a alíquota efetiva durante o ano.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Lucro Presumido</h2><p>Considere os percentuais de presunção aplicáveis à atividade, IRPJ, adicional quando cabível, CSLL, PIS, Cofins, ISS ou ICMS e encargos relacionados à folha. Compare a margem econômica real com a presunção e não olhe apenas para um tributo isolado.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Lucro Real</h2><p>A análise parte do resultado contábil ajustado pelas regras fiscais. Adições, exclusões, compensações e créditos admitidos exigem documentação e controles consistentes. Uma eventual economia precisa ser comparada com a maior exigência operacional e de conformidade.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Compare também o custo de conformidade</h2><p>Inclua contabilidade, sistemas, equipe, controles, obrigações acessórias e impacto no capital de giro. Dois regimes com carga tributária parecida podem exigir estruturas muito diferentes para funcionar com segurança.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Faça análise de sensibilidade</h2><p>Simule cenário base, crescimento de receita, queda de margem e mudança de folha. Uma vantagem que desaparece com pequena alteração de premissa merece cautela. A escolha deve continuar adequada quando a empresa sair do cenário mais otimista.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Use a regra vigente na data analisada</h2><p>A legislação tributária está em transição. Para períodos de 2026 e anos seguintes, identifique a data de referência e confirme os parâmetros vigentes naquele período. Não reutilize tabelas antigas automaticamente.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Checklist de conferência</h2><ol><li>Defina a data e o período de referência.</li><li>Reúna os documentos e dados de origem.</li><li>Separe bases, percentuais e valores calculados.</li><li>Registre premissas e exceções.</li><li>Revise o resultado com a regra vigente.</li><li>Guarde a memória necessária para futura conferência.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada para organizar as premissas e visualizar o resultado de forma estruturada. A simulação apoia a conferência e não substitui a validação técnica quando necessária.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/comparador-tributario\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>A calculadora substitui a conferência profissional?</h3><p>Não. Ela reduz trabalho operacional e organiza a memória, mas o enquadramento e os dados continuam precisando de validação.</p><h3>Posso reutilizar um cálculo antigo?</h3><p>Use-o como referência, não como resposta automática. Datas, bases, valores e regras podem ter mudado.</p><h3>Qual é o erro mais comum?</h3><p>Usar uma premissa incorreta e confiar no resultado porque a fórmula foi executada sem erro. A qualidade da entrada é tão importante quanto a conta.</p><h2>Conclusão</h2><p>Uma boa análise de <strong>regimes tributários</strong> combina cálculo, documentação e revisão. Quando as premissas ficam explícitas, o resultado se torna mais útil para decidir e mais fácil de auditar.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/simples-lucro-presumido-lucro-real-comparacao.png', 'Simples, Lucro Presumido ou Lucro Real: como comparar', 'published', 0, '2026-07-21 12:00:00', '2026-07-27 20:14:39', 'regimes tributários', '[\"Simples ou Lucro Presumido\", \"Lucro Presumido ou Lucro Real\", \"planejamento tributário\", \"comparador tributário\"]', 'Simples, Presumido ou Real: compare os regimes', 'Veja como comparar Simples Nacional, Lucro Presumido e Lucro Real usando receita, margem, folha, créditos e custos para escolher com mais segurança.', NULL, 'blog/social/simples-lucro-presumido-lucro-real-comparacao.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:14:39'),
(10, NULL, 'Como calcular férias, terço constitucional e abono', 'como-calcular-ferias-dias-abono-prazos', 'Aprenda como calcular férias, terço constitucional e abono pecuniário, conferindo dias de direito, médias, remuneração e prazos.', '<p><strong>como calcular férias</strong> exige conferir premissas antes de chegar ao número final. Este guia organiza a rotina em etapas práticas, com foco em reduzir erros, deixar a memória de cálculo compreensível e facilitar a revisão por outra pessoa.</p><div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme dados, período e regra aplicável; calcule cada componente separadamente; registre a memória e valide o resultado antes de usar a informação em uma obrigação oficial.</div><h2>Comece pelo período aquisitivo</h2><p>Confirme admissão e o período em que o empregado adquiriu o direito. Afastamentos e ocorrências relevantes devem ser analisados antes de definir quantidade de dias e prazo de concessão. Não comece pelo valor sem validar as datas.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Férias nem sempre significam automaticamente 30 dias</h2><p>A legislação prevê efeitos para determinadas faltas injustificadas e situações específicas. Não reduza férias por qualquer ausência sem classificar a ocorrência e conferir a regra aplicável ao período.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Monte a remuneração de férias</h2><p>Parta da remuneração devida na época da concessão. Horas extras, adicionais, comissões e outras parcelas variáveis podem exigir médias conforme sua natureza. Documente competências e critérios usados para que o cálculo possa ser reproduzido.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Calcule o terço constitucional</h2><p>Depois de encontrar a remuneração de férias, acrescente o adicional constitucional de um terço sobre a base correspondente. Em um exemplo simples de R$ 3.000 de remuneração de férias, o terço corresponde a R$ 1.000, antes de outros componentes e descontos.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Trate o abono pecuniário separadamente</h2><p>O abono permite converter em dinheiro até um terço do período de férias a que o empregado tiver direito, observados requisitos e prazo de solicitação. Ele não equivale a vender todas as férias e deve aparecer de forma separada na memória.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Confira fracionamento e datas</h2><p>Quando houver fracionamento, confirme concordância e limites previstos na legislação. Verifique também a data de início do descanso e restrições relacionadas a feriados e repouso semanal, evitando planejar apenas com base na conveniência operacional.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Valide o prazo de pagamento</h2><p>A data do pagamento e a data de início das férias são informações diferentes. Mantenha ambas visíveis no processo e confira a regra vigente antes de fechar o recibo.</p><p>Na conferência, preserve os dados de origem e destaque qualquer premissa estimada. Se houver divergência, investigue a causa em vez de ajustar o resultado manualmente apenas para chegar a um valor esperado.</p><h2>Checklist de conferência</h2><ol><li>Defina a data e o período de referência.</li><li>Reúna os documentos e dados de origem.</li><li>Separe bases, percentuais e valores calculados.</li><li>Registre premissas e exceções.</li><li>Revise o resultado com a regra vigente.</li><li>Guarde a memória necessária para futura conferência.</li></ol><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça a simulação no Prazzu Tools</h2><p>Use a ferramenta relacionada para organizar as premissas e visualizar o resultado de forma estruturada. A simulação apoia a conferência e não substitui a validação técnica quando necessária.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-ferias\">Abrir a ferramenta</a></div></div><h2>Perguntas frequentes</h2><h3>A calculadora substitui a conferência profissional?</h3><p>Não. Ela reduz trabalho operacional e organiza a memória, mas o enquadramento e os dados continuam precisando de validação.</p><h3>Posso reutilizar um cálculo antigo?</h3><p>Use-o como referência, não como resposta automática. Datas, bases, valores e regras podem ter mudado.</p><h3>Qual é o erro mais comum?</h3><p>Usar uma premissa incorreta e confiar no resultado porque a fórmula foi executada sem erro. A qualidade da entrada é tão importante quanto a conta.</p><h2>Conclusão</h2><p>Uma boa análise de <strong>como calcular férias</strong> combina cálculo, documentação e revisão. Quando as premissas ficam explícitas, o resultado se torna mais útil para decidir e mais fácil de auditar.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/como-calcular-ferias-dias-abono-prazos.png', 'Como calcular férias, terço constitucional e abono', 'published', 0, '2026-07-22 12:00:00', '2026-07-27 20:14:39', 'como calcular férias', '[\"calculadora de férias\", \"terço constitucional\", \"abono pecuniário\", \"férias vencidas\", \"prazo pagamento férias\"]', 'Como calcular férias, terço constitucional e abono', 'Aprenda como calcular férias, terço constitucional e abono pecuniário, conferir médias, dias de direito e prazos antes de fechar o pagamento.', NULL, 'blog/social/como-calcular-ferias-dias-abono-prazos.png', 1, '2026-07-23 11:45:13', '2026-07-27 20:14:39'),
(11, NULL, 'Custo de funcionário CLT: como calcular o custo total', 'custo-funcionario-clt-como-calcular-custo-total', 'Calcule o custo total de um funcionário CLT considerando salário, benefícios, FGTS, provisões de férias e 13º e demais encargos aplicáveis.', '<p>O salário registrado na carteira é apenas uma parte do custo de uma contratação CLT. Para saber quanto um funcionário realmente representa no orçamento, a empresa precisa separar o que é remuneração do mês, benefício, encargo sobre a folha, provisão de direitos futuros e gasto operacional ligado à vaga. Misturar tudo em um percentual único costuma produzir uma resposta rápida, mas pouco confiável.</p><p>Esse cálculo é especialmente útil antes de contratar, ao formar preço de serviços, projetar crescimento da equipe ou comparar cenários. O ponto central não é descobrir um “multiplicador mágico” do salário, e sim construir uma memória que mostre de onde veio cada parcela.</p><div class=\"alert alert-info\"><strong>Em resumo:</strong> este guia mostra como analisar custo de funcionário clt com memória de cálculo e premissas explícitas. Use os exemplos para entender a lógica e confirme regras variáveis antes de uma obrigação oficial.</div><h2>O que entra no custo de um funcionário CLT</h2><p>Comece pelo salário bruto e acrescente somente parcelas que realmente existam no cenário. Benefícios como vale-refeição, assistência médica e auxílio-alimentação dependem da política da empresa ou de norma coletiva. FGTS e contribuições patronais dependem do enquadramento. Férias, adicional de um terço e 13º são direitos periódicos que, para fins gerenciais, podem ser provisionados mês a mês.</p><p>Também podem existir custos que não aparecem na folha: exame ocupacional, uniforme, equipamentos, software, treinamento, recrutamento e estrutura física. Para decisões de contratação, vale separar esses itens dos encargos trabalhistas, pois alguns são mensais e outros acontecem apenas na entrada ou em momentos específicos.</p><h2>Como calcular as provisões mensais</h2><p>Para planejamento, o 13º pode ser representado por uma fração mensal do valor esperado ao longo do ano. Férias também podem ser provisionadas mensalmente, incluindo o terço constitucional, e os encargos incidentes precisam seguir as bases aplicáveis. O objetivo da provisão gerencial é evitar a falsa impressão de que um mês sem pagamento de férias ou 13º é mais barato do que realmente é.</p><p>A ferramenta deve servir como memória de cenário: informe salário, benefícios e parâmetros aplicáveis e observe quanto cada bloco acrescenta ao custo. Se houver dúvida sobre incidência, não esconda a incerteza dentro de um percentual aproximado.</p><h2>Exemplo prático de custo mensal</h2><p>Imagine uma contratação com salário de R$ 4.000, benefício de alimentação de R$ 600 e plano de saúde empresarial de R$ 350. Só esses três itens já somam R$ 4.950 antes de considerar FGTS, possíveis contribuições patronais, provisão de 13º, férias e terço. Isso mostra por que comparar apenas salário com orçamento disponível costuma subestimar a contratação.</p><p>O exemplo não fixa um percentual total porque a composição muda conforme regime tributário, atividade, benefícios e outras regras. O procedimento correto é montar o cenário com as parcelas reais da empresa e só então obter o total.</p><h2>Custo mensal, anual e por hora</h2><p>O custo mensal ajuda a enxergar desembolso e provisões. O anual é melhor para orçamento porque absorve pagamentos que não se repetem todos os meses. Já o custo por hora pode apoiar formação de preço, desde que a empresa diferencie horas contratuais de horas efetivamente produtivas quando isso for relevante.</p><p>Se o funcionário custa R$ 80 mil por ano, dividir simplesmente por 12 responde uma pergunta; dividir pelo número de horas produtivas responde outra. Não misture os dois indicadores.</p><h2>Erros que mais distorcem a conta</h2><p>Os erros mais comuns são usar um percentual padrão para qualquer empresa, tratar descontos do empregado como custo patronal, esquecer benefícios, ignorar provisões e comparar um custo CLT completo com uma contratação PJ usando apenas a nota fiscal mensal.</p><p>Outro erro é usar o resultado como valor oficial de folha. A calculadora é uma ferramenta de análise e conferência; a apuração efetiva deve respeitar as informações da folha, o enquadramento da empresa, normas coletivas e sistemas oficiais.</p><h2>Como usar a ferramenta do Prazzu Tools</h2><p>Abra a ferramenta, informe os dados do seu cenário e revise a memória apresentada antes de salvar o resultado. Se estiver comparando alternativas, altere uma premissa de cada vez para entender o efeito real de cada variável.</p><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça o cálculo no Prazzu Tools</h2><p>Use a ferramenta para transformar os dados deste guia em um cenário conferível, com resultado e memória organizados.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/custo-funcionario-clt\">Abrir a ferramenta</a></div></div><p><strong>Continue a análise:</strong> veja também <a href=\"/blog/como-calcular-rescisao-trabalhista\">como calcular rescisão trabalhista</a> e <a href=\"/ferramentas/encargos-trabalhistas\">calculadora de encargos trabalhistas</a>.</p><h2>Perguntas frequentes</h2><h3>O salário líquido representa o custo da empresa?</h3><p>Não. O líquido é o que sobra ao empregado após descontos. O custo da empresa inclui salário bruto e outras parcelas que podem existir, como benefícios, encargos e provisões.</p><h3>Existe um percentual padrão para transformar salário em custo CLT?</h3><p>Não existe um percentual universal confiável. A composição varia por empresa, regime, atividade, benefícios e regras aplicáveis.</p><h3>Vale calcular o custo anual?</h3><p>Sim. O anual ajuda a enxergar férias, 13º, benefícios e despesas que não aparecem de forma uniforme em todos os meses.</p><h2>Conclusão</h2><p>Custo de funcionário CLT é mais útil quando o resultado pode ser reproduzido e explicado. Em vez de depender de um percentual genérico ou de um número isolado, registre bases, datas e premissas, compare cenários e use a ferramenta como apoio à análise. Quando houver efeito fiscal, trabalhista, previdenciário ou jurídico, confirme a regra vigente e os dados oficiais antes de executar a decisão.</p><script type=\"application/ld+json\">{\"@context\":\"https://schema.org\",\"@type\":\"FAQPage\",\"mainEntity\":[{\"@type\":\"Question\",\"name\":\"O salário líquido representa o custo da empresa?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Não. O líquido é o que sobra ao empregado após descontos. O custo da empresa inclui salário bruto e outras parcelas que podem existir, como benefícios, encargos e provisões.\"}},{\"@type\":\"Question\",\"name\":\"Existe um percentual padrão para transformar salário em custo CLT?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Não existe um percentual universal confiável. A composição varia por empresa, regime, atividade, benefícios e regras aplicáveis.\"}},{\"@type\":\"Question\",\"name\":\"Vale calcular o custo anual?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Sim. O anual ajuda a enxergar férias, 13º, benefícios e despesas que não aparecem de forma uniforme em todos os meses.\"}}]}</script>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/custo-funcionario-clt-como-calcular-custo-total.png', 'Custo de funcionário CLT: como calcular o custo total', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 14:06:29', 'custo de funcionário CLT', '[\"custo funcionário empresa\", \"custo empregado CLT\", \"encargos funcionário CLT\", \"custo mensal funcionário\", \"custo anual funcionário CLT\"]', 'Custo de funcionário CLT: cálculo completo', 'Entenda o custo real de um funcionário CLT com salário, benefícios, FGTS, provisões de férias e 13º, encargos e uma memória de cálculo completa.', NULL, 'blog/covers/custo-funcionario-clt-como-calcular-custo-total.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:14:39'),
(12, NULL, 'Fator R: como calcular e entender os Anexos III e V', 'fator-r-como-calcular-anexo-iii-v', 'Aprenda a calcular o Fator R, organizar folha e receita dos 12 meses, interpretar o limite de 28% e entender quando o resultado influencia os Anexos III e V.', '<p>O Fator R é relevante para determinadas atividades de serviços do Simples Nacional porque relaciona a folha de salários com a receita bruta acumulada. Para as atividades sujeitas à regra, o resultado pode influenciar a tributação entre os Anexos III e V. A conta aritmética é simples; o trabalho importante está em usar a janela correta e saber quais valores pertencem a cada lado da razão.</p><p>Na prática, um Fator R calculado com receita de 12 meses e folha de outro período pode parecer plausível e ainda assim estar errado. Por isso, o melhor uso de uma calculadora é mostrar a memória do cálculo e a distância em relação ao limite aplicável, não apenas exibir um percentual final.</p><div class=\"alert alert-info\"><strong>Em resumo:</strong> este guia mostra como analisar fator r com memória de cálculo e premissas explícitas. Use os exemplos para entender a lógica e confirme regras variáveis antes de uma obrigação oficial.</div><h2>Fórmula do Fator R e o limite de 28%</h2><p>A lógica é dividir a folha considerada para o Fator R pela receita bruta acumulada correspondente e converter o resultado em percentual. Para as atividades alcançadas pelo mecanismo, 28% é o ponto de referência atualmente utilizado para a comparação entre Anexo III e Anexo V. Antes de concluir qualquer enquadramento, confirme se a atividade realmente está sujeita ao Fator R.</p><p>Exemplo didático: se a folha considerada no período for R$ 168.000 e a receita bruta acumulada for R$ 600.000, a razão será 0,28, ou 28%. Se a folha fosse R$ 150.000 com a mesma receita, o percentual seria 25%. O exemplo serve para entender a matemática; a composição da folha e o enquadramento devem seguir a regra vigente.</p><h2>RBT12 e folha precisam conversar</h2><p>A Receita Bruta Total acumulada e a folha usada no numerador precisam respeitar a janela prevista para a competência analisada. Não some 12 meses de receita e apenas os últimos seis meses de folha. Também tenha cuidado com empresas em início de atividade, meses sem movimento e alterações de cadastro.</p><p>Monte uma tabela mensal antes de calcular. Ela facilita encontrar competência duplicada, mês ausente ou valor lançado na coluna errada.</p><h2>O que deve ser conferido na folha</h2><p>O conceito de folha para Fator R não deve ser substituído por um simples “salário dos funcionários”. O cálculo pode envolver remunerações e outros componentes definidos pelas regras do Simples. Use os dados efetivos da empresa e preserve a origem dos valores para permitir conferência posterior.</p><p>Pró-labore merece atenção porque decisões artificiais tomadas apenas para cruzar o limite podem gerar outros efeitos previdenciários, tributários e societários. Planejamento precisa continuar coerente com a realidade econômica.</p><h2>Quanto falta para chegar a 28%</h2><p>Um uso interessante do simulador é responder “qual folha corresponderia a 28% desta receita?”. Com RBT12 de R$ 800.000, por exemplo, 28% representa R$ 224.000. Se a folha considerada estiver em R$ 200.000, a diferença matemática é R$ 24.000. Isso não significa que a empresa deva criar remuneração artificialmente; apenas mostra a distância do cenário atual para o limite.</p><p>A análise fica mais útil quando se projeta também a receita dos próximos meses, porque a saída de um mês antigo e a entrada de um mês novo alteram os acumulados.</p><h2>Quando o resultado não basta para escolher o anexo</h2><p>Fator R não é uma autorização para escolher livremente entre Anexos III e V. Primeiro é necessário confirmar a atividade exercida, seu tratamento no Simples e demais condições aplicáveis. Se a atividade não estiver sujeita ao mecanismo, o percentual calculado não muda o anexo por si só.</p><p>Use a ferramenta para simular e explicar o cálculo; use a apuração oficial e a legislação vigente para fechar a competência.</p><h2>Como usar a ferramenta do Prazzu Tools</h2><p>Abra a ferramenta, informe os dados do seu cenário e revise a memória apresentada antes de salvar o resultado. Se estiver comparando alternativas, altere uma premissa de cada vez para entender o efeito real de cada variável.</p><div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Faça o cálculo no Prazzu Tools</h2><p>Use a ferramenta para transformar os dados deste guia em um cenário conferível, com resultado e memória organizados.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/simulador-fator-r\">Abrir a ferramenta</a></div></div><p><strong>Continue a análise:</strong> veja também <a href=\"/blog/simples-nacional-como-calcular-das-fator-r\">guia de Simples Nacional, DAS e Fator R</a> e <a href=\"/ferramentas/simulador-pro-labore-ideal\">simulador de pró-labore</a>.</p><h2>Perguntas frequentes</h2><h3>Fator R acima de 28% sempre leva ao Anexo III?</h3><p>Não sozinho. O primeiro passo é confirmar se a atividade está sujeita ao Fator R e se as demais regras de enquadramento são atendidas.</p><h3>Posso usar receita e folha de períodos diferentes?</h3><p>Não. A comparação precisa respeitar a janela prevista para a competência analisada.</p><h3>A calculadora substitui o PGDAS-D?</h3><p>Não. Ela ajuda a simular e conferir os números, enquanto a apuração e emissão do DAS devem seguir os sistemas oficiais.</p><h2>Conclusão</h2><p>Fator R é mais útil quando o resultado pode ser reproduzido e explicado. Em vez de depender de um percentual genérico ou de um número isolado, registre bases, datas e premissas, compare cenários e use a ferramenta como apoio à análise. Quando houver efeito fiscal, trabalhista, previdenciário ou jurídico, confirme a regra vigente e os dados oficiais antes de executar a decisão.</p><script type=\"application/ld+json\">{\"@context\":\"https://schema.org\",\"@type\":\"FAQPage\",\"mainEntity\":[{\"@type\":\"Question\",\"name\":\"Fator R acima de 28% sempre leva ao Anexo III?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Não sozinho. O primeiro passo é confirmar se a atividade está sujeita ao Fator R e se as demais regras de enquadramento são atendidas.\"}},{\"@type\":\"Question\",\"name\":\"Posso usar receita e folha de períodos diferentes?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Não. A comparação precisa respeitar a janela prevista para a competência analisada.\"}},{\"@type\":\"Question\",\"name\":\"A calculadora substitui o PGDAS-D?\",\"acceptedAnswer\":{\"@type\":\"Answer\",\"text\":\"Não. Ela ajuda a simular e conferir os números, enquanto a apuração e emissão do DAS devem seguir os sistemas oficiais.\"}}]}</script>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/fator-r-como-calcular-anexo-iii-v.png', 'Fator R: como calcular e entender os Anexos III e V', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 14:06:29', 'Fator R', '[\"Fator R Simples Nacional\", \"Anexo III ou V\", \"RBT12\", \"folha de salários Fator R\", \"limite 28% Fator R\"]', 'Fator R: cálculo, 28% e Anexos III e V', 'Veja como calcular o Fator R no Simples Nacional, quais valores entram na folha e RBT12, como interpretar 28% e evitar erros de período.', NULL, 'blog/covers/fator-r-como-calcular-anexo-iii-v.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:14:39');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `vertical_slug`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(13, NULL, 'DAS em atraso: como calcular multa, juros e valor atualizado', 'das-em-atraso-como-calcular-multa-juros', 'Veja como calcular DAS em atraso, separar principal, multa de mora e juros, escolher a data de pagamento e conferir o valor atualizado antes de emitir a guia.', '<p>Quando há <strong>DAS em atraso</strong>, o valor original deixa de ser suficiente para planejar o pagamento. O débito pode receber multa de mora e juros, e o total depende da data efetiva de quitação. Uma boa conferência separa o principal dos acréscimos para que seja possível comparar a memória de cálculo com a guia oficial.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme o período e o valor principal, informe o vencimento original, escolha a data real de pagamento, calcule multa e juros separadamente e depois gere o DAS atualizado no canal oficial correspondente.</div>\r\n\r\n<h2>O que acontece quando o DAS vence?</h2>\r\n<p>O atraso gera acréscimos legais. Na regra geral de mora utilizada para débitos federais, a multa é calculada à razão de 0,33% por dia de atraso, limitada a 20%. Os juros são calculados com base na Selic acumulada no período aplicável e recebem 1% no mês do pagamento, conforme as orientações oficiais.</p>\r\n<p>Esses critérios ajudam a entender a formação do valor, mas a emissão oficial continua sendo a referência para o pagamento. Se o débito foi retificado, parcelado, inscrito ou passou para outra etapa de cobrança, o procedimento pode ser diferente.</p>\r\n\r\n<h2>1. Comece pelo valor principal</h2>\r\n<p>Use o valor originalmente devido no período de apuração. Não some manualmente uma multa antiga ao principal antes de fazer uma nova atualização, pois isso pode gerar cobrança em duplicidade. A memória deve manter as parcelas separadas: principal, multa, juros e total.</p>\r\n<p>Se houver divergência entre o valor que você possui e o sistema oficial, investigue primeiro se ocorreu retificação do PGDAS-D, pagamento parcial, compensação ou alteração do débito.</p>\r\n\r\n<h2>2. Confira o vencimento original</h2>\r\n<p>A data de vencimento é fundamental porque determina o início da mora. Um erro de um dia pode alterar a multa enquanto ela ainda não atingiu o teto. Não use automaticamente o dia 20 sem confirmar o período e a situação específica da empresa.</p>\r\n<p>Também diferencie atraso no pagamento do DAS de atraso em obrigação acessória. Em 2026, por exemplo, existem regras próprias de penalidade para atraso na entrega de informações do PGDAS-D; isso não deve ser confundido com os acréscimos do DAS já apurado.</p>\r\n\r\n<h2>3. Como calcular a multa de mora</h2>\r\n<p>Na regra geral divulgada pela Receita Federal, a multa corresponde a <strong>0,33% por dia de atraso</strong>, limitada a <strong>20%</strong>. A contagem considera o período previsto na orientação oficial até a data de pagamento.</p>\r\n<div class=\"card border-secondary-subtle my-3\"><div class=\"card-body\"><strong>Exemplo didático:</strong> em um débito de R$ 2.000 sujeito a 10 dias de multa, 10 × 0,33% = 3,30%. A multa seria R$ 66, antes dos juros. O exemplo serve apenas para demonstrar a lógica.</div></div>\r\n<p>Depois de aproximadamente 61 dias de incidência a soma matemática ultrapassaria 20%, mas o teto impede que a multa continue crescendo além desse limite.</p>\r\n\r\n<h2>4. Como calcular os juros</h2>\r\n<p>Os juros não devem ser estimados com uma taxa mensal fixa. A orientação do Sicalc utiliza a Selic acumulada a partir do mês seguinte ao vencimento até o mês anterior ao pagamento e acrescenta 1% no mês do pagamento.</p>\r\n<p>Por isso, duas dívidas com a mesma quantidade de dias de atraso podem ter juros diferentes se estiverem em meses distintos. Sempre use a Selic do período correspondente ou deixe que o sistema oficial faça a atualização.</p>\r\n\r\n<h2>5. Escolha a data real em que pretende pagar</h2>\r\n<p>Uma guia calculada hoje pode ficar incorreta se for paga em outra data. Antes de gerar o documento, confirme se existe caixa para quitar o débito na data escolhida. Se o pagamento for adiado, recalcule.</p>\r\n<p>Para planejamento financeiro, vale simular algumas datas e comparar quanto os acréscimos aumentam. Isso ajuda a decidir entre quitação, negociação e outras alternativas permitidas para o débito.</p>\r\n\r\n<h2>Exemplo completo de conferência</h2>\r\n<p>Imagine um DAS principal de R$ 5.000. O cálculo deve exibir o principal de R$ 5.000 em uma linha, a multa em outra, os juros em outra e o total na última. Se a guia oficial mostrar valor diferente, compare primeiro a quantidade de dias, a data de pagamento e a Selic aplicada antes de concluir que houve erro.</p>\r\n<p>Essa estrutura também evita uma prática ruim: alterar o principal apenas para que o total “bata” com outro sistema.</p>\r\n\r\n<h2>Quando uma calculadora não deve ser usada sozinha</h2>\r\n<ul>\r\n<li>débito parcelado ou com negociação em andamento;</li>\r\n<li>valor alterado por retificação;</li>\r\n<li>pagamento parcial já realizado;</li>\r\n<li>débito transferido para cobrança em outro ambiente;</li>\r\n<li>empresa com situação cadastral ou tributária que exija procedimento específico;</li>\r\n<li>divergência entre a memória local e o débito exibido pelo sistema oficial.</li>\r\n</ul>\r\n\r\n<h2>Checklist antes de pagar um DAS vencido</h2>\r\n<ol>\r\n<li>Confirme o período de apuração.</li>\r\n<li>Confira o valor principal.</li>\r\n<li>Valide o vencimento original.</li>\r\n<li>Defina a data efetiva de pagamento.</li>\r\n<li>Calcule a multa sem ultrapassar o limite aplicável.</li>\r\n<li>Use a Selic correspondente ao período.</li>\r\n<li>Mantenha principal, multa e juros separados.</li>\r\n<li>Gere o documento atualizado no canal oficial.</li>\r\n<li>Compare a guia com a memória de cálculo.</li>\r\n<li>Guarde o comprovante após a quitação.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule o DAS em atraso no Prazzu Tools</h2><p>A ferramenta de DAS em atraso organiza principal, vencimento, data de pagamento, multa e juros para facilitar a conferência antes da emissão oficial.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/das-em-atraso\">Abrir Calculadora de DAS em Atraso</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> entenda <a href=\"/blog/simples-nacional-como-calcular-das-fator-r\">como calcular o Simples Nacional e a alíquota efetiva</a> antes de analisar os acréscimos do pagamento.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>A multa do DAS pode passar de 20%?</h3><p>Na regra geral de mora utilizada para esse cálculo, a multa é limitada a 20% do principal.</p>\r\n<h3>Posso pagar o DAS vencido pelo valor original?</h3><p>Quando existem acréscimos, o correto é gerar o documento atualizado para a data de pagamento.</p>\r\n<h3>Se eu mudar a data de pagamento preciso recalcular?</h3><p>Sim. A mudança pode afetar multa e juros, especialmente enquanto a multa ainda não atingiu o limite.</p>\r\n<h3>Atraso no PGDAS-D é a mesma coisa que DAS vencido?</h3><p>Não. Uma situação envolve a obrigação declaratória e suas penalidades; a outra envolve o pagamento do débito apurado.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Calcular <strong>DAS em atraso</strong> com segurança significa tratar principal, multa, juros e data de pagamento como informações separadas. A simulação é ótima para planejamento e conferência, mas o valor a pagar deve ser validado na emissão oficial correspondente.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/das-em-atraso-como-calcular-multa-juros.png', 'Cálculo de DAS em atraso com multa, juros e valor atualizado', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:18:36', 'DAS em atraso', '[\"calcular DAS atrasado\", \"multa DAS atraso\", \"juros DAS Simples Nacional\", \"DAS vencido\", \"Simples Nacional em atraso\", \"atualizar DAS\"]', 'DAS em atraso: como calcular multa e juros', 'Aprenda como calcular DAS em atraso, separar principal, multa e juros e conferir o valor atualizado antes de gerar a guia oficial do Simples Nacional.', NULL, 'blog/covers/das-em-atraso-como-calcular-multa-juros.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:18:36'),
(14, NULL, 'Encargos trabalhistas: como calcular o custo da folha', 'encargos-trabalhistas-como-calcular-folha', 'Aprenda como calcular encargos trabalhistas separando remuneração, FGTS, encargos patronais, benefícios e provisões para entender o custo real da folha.', '<p><strong>Encargos trabalhistas</strong> não deveriam ser resumidos a um percentual fixo aplicado sobre o salário. O custo da folha depende das verbas pagas, das bases de incidência, do enquadramento da empresa, dos benefícios e das provisões usadas para planejamento. Separar esses componentes produz uma visão muito mais útil do que perguntar apenas “quanto custa um funcionário além do salário?”.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> monte a remuneração da competência, classifique as rubricas, aplique cada encargo apenas sobre sua base correta, separe benefícios e provisões e mantenha uma memória que explique o custo mensal e anual.</div>\r\n\r\n<h2>Encargo, benefício e provisão não são a mesma coisa</h2>\r\n<p>FGTS e contribuições patronais, quando devidas, são componentes associados às bases da folha. Férias, terço e 13º podem ser provisionados mensalmente para gestão, mas representam direitos com momentos próprios de pagamento. Vale-transporte, alimentação, plano de saúde e outros benefícios possuem natureza e tratamento específicos.</p>\r\n<p>Somar tudo em uma única “taxa de encargos” pode ser útil para uma estimativa muito inicial, mas esconde a origem dos valores e dificulta a revisão.</p>\r\n\r\n<h2>1. Comece pelas rubricas da folha</h2>\r\n<p>Liste salário, horas extras, adicionais, comissões, gratificações e demais verbas. Depois classifique quais entram na base de FGTS, contribuições previdenciárias e outros encargos aplicáveis. Não assuma que todas possuem a mesma incidência.</p>\r\n<p>Quando o sistema de folha já calcula as bases, use o relatório como ponto de partida e compare com a memória gerencial.</p>\r\n\r\n<h2>2. Separe o FGTS</h2>\r\n<p>O depósito do FGTS deve ser mostrado em linha própria, utilizando a base e a alíquota aplicáveis ao contrato e à situação. Essa separação é importante porque o FGTS não é salário líquido do empregado nem contribuição previdenciária.</p>\r\n<p>Em projeções anuais, considere também os reflexos das verbas que compõem a base quando forem devidos.</p>\r\n\r\n<h2>3. Identifique os encargos patronais aplicáveis</h2>\r\n<p>CPP, RAT e contribuições destinadas a terceiros dependem do enquadramento da empresa e não devem ser copiados de uma empresa para outra. Regime tributário, atividade e demais classificações podem alterar o resultado.</p>\r\n<p>Quando o objetivo for orçamento, registre explicitamente os percentuais utilizados. Assim, se o enquadramento mudar, é possível atualizar o cenário sem reconstruir toda a planilha.</p>\r\n\r\n<h2>4. Trate benefícios fora do “percentual mágico”</h2>\r\n<p>Vale-refeição, vale-alimentação, plano de saúde, seguro, auxílio e outros benefícios podem representar parcela relevante do custo. Lance cada um separadamente, inclusive a participação descontada do empregado quando houver.</p>\r\n<p>Isso permite responder perguntas melhores: quanto custa aumentar o benefício? Quanto do custo é folha e quanto é política de benefícios?</p>\r\n\r\n<h2>5. Faça provisões de férias e 13º</h2>\r\n<p>Para gestão, é útil distribuir mensalmente a expectativa de férias, terço constitucional e 13º. A provisão melhora a leitura econômica do custo mesmo quando o pagamento ocorrerá em outro mês.</p>\r\n<p>Uma provisão gerencial não deve ser confundida com guia ou obrigação já vencida. Mantenha a nomenclatura clara para evitar interpretar previsão de caixa como pagamento da competência.</p>\r\n\r\n<h2>Exemplo de custo estruturado</h2>\r\n<p>Imagine remuneração de R$ 4.000 e benefícios de R$ 800. Em vez de afirmar que “o custo é salário + 70%”, crie linhas para remuneração, FGTS, componentes patronais, benefícios, provisão de férias, terço e 13º. O total pode até coincidir com um percentual aproximado, mas agora é possível entender de onde veio e alterar apenas o componente que mudou.</p>\r\n\r\n<h2>Custo mensal e custo anual</h2>\r\n<p>A média mensal gerencial é útil, mas a empresa também precisa enxergar desembolsos reais ao longo do ano. Férias, 13º, reajustes salariais, dissídios, bônus e mudanças de benefícios podem concentrar caixa em determinados meses.</p>\r\n<p>Por isso, use duas visões: custo médio mensal para precificação e orçamento; fluxo de caixa mensal para saber quando o dinheiro realmente será necessário.</p>\r\n\r\n<h2>Checklist para calcular encargos trabalhistas</h2>\r\n<ol>\r\n<li>Liste todas as rubricas de remuneração.</li>\r\n<li>Confirme a base de cada encargo.</li>\r\n<li>Separe o FGTS.</li>\r\n<li>Valide o enquadramento patronal.</li>\r\n<li>Inclua benefícios por valor real.</li>\r\n<li>Calcule provisões de férias, terço e 13º.</li>\r\n<li>Registre descontos e participações do empregado quando aplicáveis.</li>\r\n<li>Compare custo mensal médio e desembolso anual.</li>\r\n<li>Atualize o cenário após reajustes ou mudanças de enquadramento.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule os encargos no Prazzu Tools</h2><p>A Calculadora de Encargos Trabalhistas separa remuneração, enquadramento e componentes do custo para produzir uma memória mais auditável do que um percentual genérico.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/encargos-trabalhistas\">Abrir Calculadora de Encargos Trabalhistas</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> aprofunde a análise com o <a href=\"/blog/custo-funcionario-clt-como-calcular\">guia de custo total de funcionário CLT</a> e com o <a href=\"/blog/inss-patronal-como-calcular-cpp-rat-terceiros\">guia de INSS patronal</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Existe um percentual padrão de encargos sobre salário?</h3><p>Não existe um único percentual que represente todas as empresas e contratos. O resultado depende das bases, do enquadramento e dos componentes incluídos na conta.</p>\r\n<h3>Férias e 13º são encargos?</h3><p>Para gestão, frequentemente aparecem como provisões do custo do empregado. É melhor mantê-los separados dos encargos incidentes na folha da competência.</p>\r\n<h3>Benefícios entram no custo do funcionário?</h3><p>Sim, quando pagos pela empresa eles fazem parte do custo econômico da contratação, ainda que tenham tratamento diferente dos encargos sobre a folha.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Calcular <strong>encargos trabalhistas</strong> de forma útil é decompor o custo. Quando remuneração, bases, FGTS, componentes patronais, benefícios e provisões ficam separados, o orçamento se torna mais confiável e a empresa consegue explicar por que o custo mudou.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/encargos-trabalhistas-como-calcular-folha.png', 'Encargos trabalhistas separados por salário, FGTS, benefícios e provisões', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:18:36', 'encargos trabalhistas', '[\"calcular encargos trabalhistas\", \"custo da folha de pagamento\", \"encargos sobre salário\", \"FGTS patronal\", \"provisão férias e 13º\", \"custo funcionário\"]', 'Encargos trabalhistas: como calcular a folha', 'Aprenda a calcular encargos trabalhistas separando salário, FGTS, encargos patronais, benefícios e provisões para entender o custo real da folha.', NULL, 'blog/covers/encargos-trabalhistas-como-calcular-folha.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:18:36'),
(15, NULL, 'CLT, PJ ou autônomo: como comparar custos e valor líquido', 'clt-pj-autonomo-comparar-custos', 'Compare CLT, PJ ou autônomo na mesma base anual, considerando custo do contratante, líquido estimado, benefícios, tributos, férias e riscos do vínculo.', '<p>Comparar <strong>CLT, PJ ou autônomo</strong> apenas pelo valor mensal recebido cria uma comparação incompleta. No emprego existem férias, 13º, benefícios e encargos. Na pessoa jurídica existem tributos, contabilidade, custos operacionais e períodos sem faturamento. No trabalho autônomo existem regras próprias de contribuição e tributação. A comparação precisa colocar tudo na mesma unidade de tempo.</p>\r\n<div class=\"alert alert-warning\"><strong>Importante:</strong> uma calculadora compara cenários econômicos; ela não define a natureza jurídica da relação. Se os fatos caracterizam vínculo de emprego, uma simulação mais barata não transforma automaticamente a relação em prestação independente.</div>\r\n\r\n<h2>Use uma base anual</h2>\r\n<p>Transforme todos os cenários em 12 meses ou em outro período comum. Somar apenas salário CLT de um lado e 12 notas PJ do outro distorce férias, 13º e períodos sem faturamento. Depois de calcular o total anual, você pode converter o resultado para média mensal equivalente.</p>\r\n\r\n<h2>O que incluir no cenário CLT</h2>\r\n<p>Do lado do contratante, considere salário, benefícios, FGTS, encargos patronais e provisões aplicáveis. Do lado do trabalhador, considere o líquido após descontos e também o valor econômico dos benefícios recebidos.</p>\r\n<p>Férias e 13º não são “extras gratuitos”: fazem parte da estrutura econômica do contrato de emprego e precisam ser representados na comparação anual.</p>\r\n\r\n<h2>O que incluir no cenário PJ</h2>\r\n<p>Comece pelo faturamento bruto esperado. Depois desconte tributos, contabilidade, taxas bancárias, softwares, seguros, equipamentos e outros custos necessários à atividade. Se o profissional pretende tirar férias sem faturar, reserve esse período na conta.</p>\r\n<p>Uma nota mensal de R$ 10.000 não representa R$ 10.000 líquidos. O valor econômico disponível só aparece depois de considerar as despesas da própria estrutura empresarial.</p>\r\n\r\n<h2>O que incluir no cenário autônomo</h2>\r\n<p>O autônomo deve considerar a forma de contratação, contribuição previdenciária, imposto de renda quando aplicável, despesas necessárias e eventual retenção na fonte. O contratante também pode ter obrigações próprias conforme a operação.</p>\r\n<p>Não trate “autônomo” como sinônimo de PJ. São formas distintas, com regras e documentos diferentes.</p>\r\n\r\n<h2>Compare custo e valor líquido separadamente</h2>\r\n<p>Existem duas perguntas diferentes: quanto custa para o contratante e quanto sobra economicamente para o profissional. Um modelo pode ser mais barato para a empresa e não ser o melhor para o contratado, ou vice-versa.</p>\r\n<p>Crie duas colunas no comparador. Isso evita usar o custo da empresa como se fosse o líquido do trabalhador.</p>\r\n\r\n<h2>Exemplo de comparação anual</h2>\r\n<p>Suponha uma proposta CLT com salário e benefícios e uma proposta PJ com valor mensal maior. Primeiro anualize a remuneração CLT, incluindo férias, 13º e benefícios. Depois anualize a receita PJ e desconte tributos, custos e um período sem faturamento. Só então compare os valores equivalentes.</p>\r\n<p>Se a diferença final for pequena, fatores como previsibilidade de renda, cobertura de benefícios, capacidade de negociação, risco empresarial e objetivos profissionais podem pesar mais do que o valor bruto anunciado.</p>\r\n\r\n<h2>Risco de vínculo não cabe em uma fórmula</h2>\r\n<p>A caracterização jurídica depende dos fatos concretos da relação. Subordinação, pessoalidade, habitualidade e outras características podem ser relevantes na análise trabalhista. Evite usar uma calculadora para “provar” que determinada contratação é válida juridicamente.</p>\r\n<p>Quando houver dúvida sobre o modelo adequado, a decisão deve considerar orientação trabalhista e contratual compatível com a situação real.</p>\r\n\r\n<h2>Checklist para comparar CLT, PJ ou autônomo</h2>\r\n<ol>\r\n<li>Escolha um período comum, preferencialmente anual.</li>\r\n<li>Some remuneração e benefícios no cenário CLT.</li>\r\n<li>Inclua encargos e provisões do contratante.</li>\r\n<li>Calcule descontos e líquido do trabalhador.</li>\r\n<li>No PJ, desconte tributos e custos da empresa.</li>\r\n<li>Reserve períodos sem faturamento quando fizer sentido.</li>\r\n<li>No autônomo, considere contribuições e retenções aplicáveis.</li>\r\n<li>Compare custo do contratante e líquido do profissional separadamente.</li>\r\n<li>Faça cenários de reajuste e férias.</li>\r\n<li>Não use a conta como definição jurídica do vínculo.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Compare os três modelos no Prazzu Tools</h2><p>O Comparador CLT, PJ e Autônomo organiza premissas mensais e transforma os cenários em uma base comparável de custo e valor líquido estimado.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/comparador-clt-pj-autonomo\">Abrir Comparador CLT, PJ e Autônomo</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> use o <a href=\"/blog/custo-funcionario-clt-como-calcular\">cálculo do custo total de funcionário CLT</a> para aprofundar o cenário de emprego.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Quanto a proposta PJ precisa ser maior que a CLT?</h3><p>Não existe um percentual universal. A diferença depende de benefícios, tributos, custos, férias desejadas, risco e estrutura necessária para prestar o serviço.</p>\r\n<h3>Ser PJ elimina risco de vínculo?</h3><p>Não. A existência de uma pessoa jurídica não resolve sozinha a natureza da relação; os fatos concretos também precisam ser considerados.</p>\r\n<h3>Benefícios devem entrar na comparação?</h3><p>Sim. Eles têm valor econômico e podem alterar significativamente a equivalência entre propostas.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>A comparação entre <strong>CLT, PJ ou autônomo</strong> fica muito mais útil quando todos os cenários são anualizados e separados em custo do contratante e líquido do profissional. A matemática organiza a decisão econômica; a natureza jurídica deve ser analisada pelos fatos da relação.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/clt-pj-autonomo-comparar-custos.png', 'Comparação anual de CLT, PJ e autônomo por custo e valor líquido', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:18:36', 'CLT, PJ ou autônomo', '[\"CLT ou PJ\", \"PJ ou autônomo\", \"comparar CLT e PJ\", \"custo contratação CLT\", \"valor líquido PJ\", \"pejotização\"]', 'CLT, PJ ou autônomo: compare custos e líquido', 'Compare CLT, PJ e autônomo em uma base anual considerando custo do contratante, líquido, benefícios, tributos, férias e riscos jurídicos do vínculo.', NULL, 'blog/covers/clt-pj-autonomo-comparar-custos.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:18:36'),
(16, NULL, 'INSS patronal: como calcular CPP, RAT e terceiros', 'inss-patronal-como-calcular-cpp-rat-terceiros', 'Entenda como calcular INSS patronal separando CPP, RAT e contribuições a terceiros, conferindo base de folha, FPAS e enquadramento antes das alíquotas.', '<p><strong>INSS patronal</strong> é uma expressão usada no dia a dia para diferentes componentes previdenciários da folha. Para calcular corretamente, não aplique um percentual único sem antes confirmar base, regime tributário, atividade, FPAS, RAT e contribuições destinadas a terceiros que efetivamente se aplicam à empresa.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> comece pela base de remunerações, confirme o enquadramento da empresa, calcule CPP, RAT e terceiros em linhas separadas e compare a memória com a apuração oficial da competência.</div>\r\n\r\n<h2>Por que não existe um percentual único para todas as empresas</h2>\r\n<p>A composição previdenciária patronal pode variar conforme o enquadramento. Algumas empresas seguem a regra geral sobre a folha; outras possuem tratamentos próprios decorrentes do regime, atividade ou legislação específica. Além disso, RAT e terceiros dependem de informações que não são iguais para todos os contribuintes.</p>\r\n<p>Uma boa calculadora deve permitir parametrização e explicar quais alíquotas foram utilizadas, em vez de esconder tudo em um número final.</p>\r\n\r\n<h2>1. Confira a base de remunerações</h2>\r\n<p>Liste as rubricas da competência que formam a base previdenciária conforme as regras aplicáveis. Salário, adicionais, comissões e outras parcelas podem ter incidências próprias. Uma base incorreta multiplicada pela alíquota correta continua produzindo um valor errado.</p>\r\n<p>Preserve o relatório da folha ou a origem das rubricas para conseguir reconciliar o cálculo posteriormente.</p>\r\n\r\n<h2>2. Entenda a CPP</h2>\r\n<p>A Contribuição Previdenciária Patronal deve ser analisada de acordo com o enquadramento da empresa. Não copie automaticamente uma alíquota utilizada por outro CNPJ ou por uma competência antiga.</p>\r\n<p>Empresas optantes por regimes e anexos específicos podem ter tratamento diferente. Por isso, confirme como a contribuição patronal está sendo recolhida na situação concreta antes de adicionar a parcela à simulação.</p>\r\n\r\n<h2>3. Entenda o RAT</h2>\r\n<p>O RAT está relacionado ao risco da atividade e ao enquadramento previdenciário da empresa. A alíquota aplicável e eventuais ajustes precisam ser conferidos com base nos dados da própria organização.</p>\r\n<p>Na memória, mostre base, percentual e valor. Isso torna visível se uma diferença veio do enquadramento ou da própria folha.</p>\r\n\r\n<h2>4. Terceiros, FPAS e outras entidades</h2>\r\n<p>Contribuições destinadas a terceiros podem depender do FPAS e do código de terceiros relacionado ao enquadramento. Esse componente não deve ser estimado apenas pelo CNAE sem revisar as informações cadastrais e previdenciárias necessárias.</p>\r\n<p>Se a empresa possui estabelecimentos com atividades distintas, confira se a configuração usada na folha corresponde ao estabelecimento analisado.</p>\r\n\r\n<h2>5. Compare com a competência correta</h2>\r\n<p>Use a mesma competência para base, parâmetros e apuração. Mudanças de atividade, estabelecimento, regime ou classificação podem tornar inadequada a simples repetição das configurações do mês anterior.</p>\r\n<p>Ao encontrar divergência, compare cada componente separadamente: base da CPP, base do RAT, alíquota do RAT, FPAS e terceiros.</p>\r\n\r\n<h2>Exemplo de memória de cálculo</h2>\r\n<p>Suponha uma base previdenciária de R$ 100.000. Em vez de multiplicar esse valor por uma “taxa de INSS patronal” genérica, a memória deve apresentar cada parcela aplicável com a respectiva alíquota. Se uma empresa não estiver sujeita a determinado componente naquele cenário, a linha deve refletir isso explicitamente.</p>\r\n<p>Esse formato também ajuda no orçamento: é possível testar aumento de folha mantendo os parâmetros ou alterar apenas um enquadramento para avaliar o impacto.</p>\r\n\r\n<h2>Relação com o custo total do funcionário</h2>\r\n<p>O INSS patronal é apenas parte do custo trabalhista. FGTS, benefícios, provisões de férias e 13º e outros custos devem ser analisados separadamente. Misturar todos em uma única “alíquota de encargos” dificulta a gestão e a auditoria.</p>\r\n\r\n<h2>Checklist para calcular INSS patronal</h2>\r\n<ol>\r\n<li>Defina a competência.</li>\r\n<li>Revise as rubricas da folha.</li>\r\n<li>Confirme a base previdenciária.</li>\r\n<li>Valide o regime e o enquadramento da empresa.</li>\r\n<li>Confira a CPP aplicável.</li>\r\n<li>Confira RAT e atividade preponderante.</li>\r\n<li>Revise FPAS e terceiros.</li>\r\n<li>Calcule cada componente separadamente.</li>\r\n<li>Compare com a apuração oficial.</li>\r\n<li>Documente alterações de parâmetro entre competências.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule o INSS patronal no Prazzu Tools</h2><p>A calculadora do projeto organiza base, CPP, RAT e terceiros em componentes separados para facilitar conferência e simulação de custo da folha.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/inss-patronal\">Abrir Calculadora de INSS Patronal</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> consulte o <a href=\"/blog/encargos-trabalhistas-como-calcular-folha\">guia de encargos trabalhistas</a> para incorporar benefícios e provisões ao custo mais amplo da folha.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>INSS patronal é sempre 20%?</h3><p>Não é seguro assumir isso para todas as empresas. O tratamento depende do enquadramento e a composição final pode envolver outros componentes.</p>\r\n<h3>RAT é igual para todas as empresas do mesmo setor?</h3><p>Não deve ser simplesmente copiado. É necessário conferir atividade, enquadramento e dados aplicáveis à própria empresa.</p>\r\n<h3>Terceiros fazem parte da mesma guia?</h3><p>A forma de recolhimento pode estar integrada ao processo previdenciário, mas para análise gerencial é útil manter o valor de terceiros separado dos demais componentes.</p>\r\n<h3>FGTS faz parte do INSS patronal?</h3><p>Não. FGTS e contribuições previdenciárias possuem naturezas diferentes e devem aparecer em linhas separadas na memória de custo.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Calcular <strong>INSS patronal</strong> corretamente é decompor a contribuição em bases e enquadramentos verificáveis. CPP, RAT e terceiros precisam ser tratados separadamente para que diferenças possam ser investigadas sem ajustar o total no escuro.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/inss-patronal-como-calcular-cpp-rat-terceiros.png', 'INSS patronal separado em CPP, RAT e contribuições a terceiros', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:18:36', 'INSS patronal', '[\"calcular INSS patronal\", \"CPP patronal\", \"RAT folha pagamento\", \"terceiros FPAS\", \"encargos previdenciários empresa\", \"custo previdenciário folha\"]', 'INSS patronal: como calcular CPP, RAT e terceiros', 'Aprenda a calcular INSS patronal separando CPP, RAT e terceiros, conferindo base da folha, FPAS, atividade e enquadramento antes de aplicar alíquotas.', NULL, 'blog/covers/inss-patronal-como-calcular-cpp-rat-terceiros.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:18:36'),
(17, NULL, 'Capital de giro: como calcular NCG e necessidade financeira', 'capital-de-giro-como-calcular-ncg', 'Aprenda como calcular capital de giro e NCG com contas a receber, estoques e fornecedores, interpretar a necessidade financeira e testar melhorias no ciclo.', '<p><strong>Capital de giro</strong> é o recurso necessário para sustentar a operação enquanto existe diferença entre pagar fornecedores, manter estoques e receber dos clientes. Uma empresa pode apresentar lucro e ainda enfrentar falta de caixa se o ciclo financeiro consumir recursos mais rápido do que eles retornam.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> escolha uma data-base, separe ativos e passivos operacionais, calcule a necessidade de capital de giro (NCG), compare com os recursos disponíveis e simule mudanças em prazo de recebimento, estoque e fornecedores.</div>\r\n<h2>O que é necessidade de capital de giro?</h2><p>Em uma análise gerencial simplificada, contas a receber e estoques representam recursos presos na operação. Fornecedores e outras obrigações operacionais financiam parte desse ciclo. A diferença mostra quanto a empresa precisa financiar com caixa próprio ou outras fontes.</p><p>O ponto mais importante é classificar as contas pela função. Empréstimo bancário, por exemplo, é uma fonte financeira e não deve ser misturado automaticamente com fornecedores apenas porque ambos aparecem no curto prazo.</p>\r\n<h2>Como calcular a NCG</h2><p>Uma forma prática é somar os ativos operacionais de curto prazo e subtrair os passivos operacionais. Se clientes somam R$ 120.000 e estoques R$ 80.000, existem R$ 200.000 aplicados no ciclo. Se fornecedores e obrigações operacionais somam R$ 130.000, a NCG do exemplo é R$ 70.000.</p><p>Esse valor não significa automaticamente que a empresa precisa contratar um empréstimo de R$ 70.000. Ele indica quanto do ciclo ainda precisa ser financiado por alguma fonte.</p>\r\n<h2>NCG positiva é sempre ruim?</h2><p>Não. Muitos negócios naturalmente precisam financiar estoque e prazo aos clientes. O problema aparece quando a necessidade cresce mais rápido do que a capacidade de geração de caixa ou quando a empresa depende de dívida cara para sustentar um ciclo mal administrado.</p>\r\n<h2>NCG negativa é sempre boa?</h2><p>Também não. Uma NCG negativa pode ocorrer quando fornecedores financiam grande parte da operação, o que pode ser saudável em alguns modelos. Mas também pode refletir atraso de pagamentos ou concentração excessiva em obrigações. O contexto precisa ser analisado.</p>\r\n<h2>Capital de giro não é saldo bancário</h2><p>Caixa mostra disponibilidade em uma data. NCG mostra a necessidade estrutural criada pelo ciclo operacional. A empresa pode ter dinheiro no banco hoje e ainda enfrentar pressão nas próximas semanas se clientes demorarem a pagar e fornecedores vencerem antes.</p>\r\n<h2>Use sempre a mesma data-base</h2><p>Não combine estoque do fechamento do mês com clientes de quinze dias depois e fornecedores de outra data. Todos os saldos devem representar o mesmo momento para que a fotografia operacional seja coerente.</p>\r\n<h2>Como reduzir a necessidade de capital de giro</h2><p>Existem três alavancas clássicas: receber mais rápido, reduzir estoque sem prejudicar a operação e negociar melhores prazos com fornecedores. Cada mudança deve ser simulada separadamente para medir seu impacto real.</p><p>Reduzir estoque de forma indiscriminada pode causar ruptura; apertar clientes pode reduzir vendas; alongar fornecedores pode elevar preço ou prejudicar relacionamento. A melhor decisão equilibra caixa e operação.</p>\r\n<h2>Exemplo de cenário melhorado</h2><p>Partindo da NCG de R$ 70.000 do exemplo, imagine que a empresa reduza contas a receber em R$ 15.000 e estoque em R$ 10.000 sem alterar fornecedores. A necessidade cairia para R$ 45.000. A simulação mostra que R$ 25.000 deixariam de ficar presos no ciclo.</p>\r\n<h2>Checklist de capital de giro</h2><ol><li>Defina uma única data-base.</li><li>Concilie contas a receber.</li><li>Revise estoques e itens sem giro.</li><li>Confirme fornecedores e obrigações operacionais.</li><li>Separe dívidas financeiras.</li><li>Calcule a NCG atual.</li><li>Compare com períodos anteriores.</li><li>Simule prazos de clientes e fornecedores.</li><li>Teste redução de estoque.</li><li>Combine a análise com fluxo de caixa.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule o capital de giro no Prazzu Tools</h2><p>Use a ferramenta para organizar clientes, estoques e fornecedores e visualizar quanto do ciclo operacional precisa ser financiado.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/capital-de-giro\">Abrir Calculadora de Capital de Giro</a></div></div>\r\n<p><strong>Continue a análise:</strong> use também o <a href=\"/ferramentas/fluxo-de-caixa\">Fluxo de Caixa</a> para descobrir quando a pressão financeira acontece e o <a href=\"/ferramentas/ponto-de-equilibrio\">Ponto de Equilíbrio</a> para relacionar estrutura de custos e vendas.</p>\r\n<h2>Perguntas frequentes</h2><h3>Capital de giro e NCG são a mesma coisa?</h3><p>Os termos aparecem juntos, mas a NCG mede especificamente a necessidade criada pelo ciclo operacional. A análise de capital de giro pode incluir também caixa e fontes financeiras.</p><h3>Estoque sempre aumenta a NCG?</h3><p>Em uma leitura operacional, estoque normalmente representa recurso aplicado no ciclo. Quanto maior o estoque, maior tende a ser a necessidade, mantendo as demais contas constantes.</p><h3>Empréstimo reduz a NCG?</h3><p>O empréstimo pode financiar a necessidade, mas não corrige a origem operacional dela. Por isso, deve ser analisado como fonte financeira separada.</p>\r\n<h2>Conclusão</h2><p>Calcular <strong>capital de giro</strong> de forma útil é entender onde o dinheiro fica preso no ciclo. Quando clientes, estoques e fornecedores são classificados na mesma data-base, a NCG deixa de ser apenas um indicador e passa a orientar ações concretas sobre prazo, estoque e financiamento.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/capital-de-giro-como-calcular-ncg.png', 'Cálculo de capital de giro e NCG com clientes, estoques e fornecedores', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:22:23', 'capital de giro', '[\"necessidade de capital de giro\", \"NCG\", \"capital de giro líquido\", \"ciclo financeiro\", \"contas a receber\", \"estoques e fornecedores\"]', 'Capital de giro: como calcular NCG e necessidade', 'Aprenda como calcular capital de giro e NCG com clientes, estoques e fornecedores, interpretar a necessidade financeira e testar melhorias no ciclo.', NULL, 'blog/covers/capital-de-giro-como-calcular-ncg.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:22:23'),
(18, NULL, 'Fluxo de caixa: como calcular entradas, saídas e saldo', 'fluxo-de-caixa-como-calcular-saldo', 'Aprenda como montar fluxo de caixa com saldo inicial, entradas e saídas por data, cenários de recebimento e projeção para antecipar falta ou sobra de recursos.', '<p><strong>Fluxo de caixa</strong> mostra quando o dinheiro realmente entra e sai. Uma venda pode aumentar a receita hoje e só virar caixa daqui a 30, 60 ou 90 dias. Da mesma forma, uma compra pode ser reconhecida economicamente antes de seu pagamento. Por isso, controlar caixa exige trabalhar com datas financeiras, não apenas com faturamento e despesas por competência.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> concilie o saldo inicial, lance recebimentos e pagamentos nas datas previstas, separe realizado de projetado, acompanhe o menor saldo do período e crie cenários para eventos incertos.</div>\r\n<h2>Comece por um saldo inicial conciliado</h2><p>Escolha a data inicial e registre apenas os recursos considerados disponíveis pelo critério da empresa. Compare o número com bancos, caixa físico e aplicações incluídas na análise. Se o saldo inicial estiver errado, toda projeção posterior também estará.</p>\r\n<h2>Entrada de caixa não é faturamento</h2><p>Uma venda de R$ 12.000 em três parcelas não deve aparecer como R$ 12.000 de entrada no dia da emissão se o dinheiro será recebido depois. Lance cada parcela na data esperada de recebimento.</p><p>Para clientes com histórico de atraso, vale criar uma data provável além da data contratual. Essa diferença torna a projeção mais realista.</p>\r\n<h2>Saída de caixa é a data do pagamento</h2><p>Folha, impostos, fornecedores, aluguel, financiamentos e investimentos devem aparecer conforme a data de desembolso. Uma compra parcelada precisa ser distribuída pelos vencimentos, em vez de concentrada no dia da aquisição.</p>\r\n<h2>Separe realizado e projetado</h2><p>O realizado já aconteceu e deve ser conciliado com extratos e comprovantes. O projetado depende de eventos futuros. Misturar os dois dificulta descobrir se uma diferença veio de erro de lançamento ou de uma previsão que não se confirmou.</p>\r\n<h2>Saldo final não conta toda a história</h2><p>Imagine saldo inicial de R$ 20.000, entradas de R$ 35.000 e saídas de R$ 42.000. O saldo final projetado seria R$ 13.000. Porém, se R$ 25.000 das saídas ocorrerem antes das principais entradas, o caixa pode ficar negativo no meio do mês e se recuperar depois.</p><p>Por isso, acompanhe também o <strong>menor saldo projetado</strong> e a data em que ele ocorre.</p>\r\n<h2>Crie um cenário conservador</h2><p>Se R$ 10.000 dos recebimentos dependem de um cliente que costuma atrasar, simule o período sem essa entrada na data original. No exemplo anterior, o saldo final poderia cair de R$ 13.000 para R$ 3.000. Essa visão ajuda a decidir antes do problema.</p>\r\n<h2>Classifique os movimentos</h2><p>Separar operação, financiamento e investimento melhora a leitura. Se o caixa aumentou apenas porque entrou um empréstimo, isso é diferente de gerar caixa com clientes. Da mesma forma, comprar um equipamento não deve ser interpretado como piora recorrente da operação.</p>\r\n<h2>Qual horizonte usar?</h2><p>Para tesouraria apertada, uma projeção diária ou semanal pode ser necessária. Para planejamento, combine uma visão curta detalhada com meses futuros mais agregados. Atualize o horizonte continuamente em vez de criar uma planilha anual e abandoná-la.</p>\r\n<h2>Checklist do fluxo de caixa</h2><ol><li>Concilie o saldo inicial.</li><li>Lance recebimentos por data provável.</li><li>Lance pagamentos por vencimento.</li><li>Separe realizado e previsto.</li><li>Classifique operação, financiamento e investimento.</li><li>Calcule o saldo após cada movimento.</li><li>Identifique o menor saldo do período.</li><li>Crie cenário conservador para entradas incertas.</li><li>Atualize previsões com o realizado.</li><li>Investigue diferenças recorrentes.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Monte o fluxo de caixa no Prazzu Tools</h2><p>Use a ferramenta para organizar saldo inicial, entradas, saídas e projeções e visualizar antecipadamente os períodos de maior pressão financeira.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/fluxo-de-caixa\">Abrir Fluxo de Caixa</a></div></div>\r\n<p><strong>Continue a análise:</strong> calcule também a <a href=\"/ferramentas/capital-de-giro\">necessidade de capital de giro</a> e o <a href=\"/ferramentas/ponto-de-equilibrio\">ponto de equilíbrio</a>.</p>\r\n<h2>Perguntas frequentes</h2><h3>Fluxo de caixa é igual a DRE?</h3><p>Não. A DRE analisa resultado por competência; o fluxo acompanha movimentação financeira conforme recebimentos e pagamentos.</p><h3>Devo lançar uma venda quando emito a nota?</h3><p>No fluxo de caixa, registre a entrada na data em que o dinheiro é recebido ou projetado para recebimento.</p><h3>Saldo final positivo significa que o mês está seguro?</h3><p>Não necessariamente. O caixa pode ficar negativo durante o período. Verifique o menor saldo e a sequência das datas.</p>\r\n<h2>Conclusão</h2><p>Um bom <strong>fluxo de caixa</strong> não é apenas uma soma mensal. Ele mostra a sequência de entradas e saídas, separa realizado de previsto e revela o momento exato em que a empresa pode precisar de recursos ou ter sobra disponível.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/fluxo-de-caixa-como-calcular-saldo.png', 'Fluxo de caixa com saldo inicial, entradas, saídas e saldo projetado', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:22:23', 'fluxo de caixa', '[\"como fazer fluxo de caixa\", \"projeção de caixa\", \"saldo de caixa\", \"entradas e saídas\", \"fluxo de caixa projetado\", \"controle financeiro\"]', 'Fluxo de caixa: como calcular entradas, saídas e saldo', 'Aprenda a montar fluxo de caixa com saldo inicial, entradas e saídas por data, cenários de recebimento e projeção para antecipar falta ou sobra de caixa.', NULL, 'blog/covers/fluxo-de-caixa-como-calcular-saldo.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:22:23');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `vertical_slug`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(19, NULL, 'Ponto de equilíbrio: como calcular quanto precisa vender', 'ponto-de-equilibrio-como-calcular', 'Aprenda como calcular ponto de equilíbrio com custos fixos e margem de contribuição, converter o resultado em faturamento ou unidades e testar preço e mix.', '<p>O <strong>ponto de equilíbrio</strong> indica quanto a empresa precisa vender para que a margem gerada pelas vendas cubra os custos fixos considerados. Ele não responde diretamente quanto é preciso faturar para ter caixa positivo ou atingir determinado lucro; primeiro mostra o nível em que a operação analisada deixa de apresentar prejuízo operacional dentro das premissas utilizadas.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> classifique custos fixos e variáveis, calcule a margem de contribuição, divida os custos fixos pela margem percentual para obter faturamento de equilíbrio e teste descontos, aumentos de custo e mudanças no mix.</div>\r\n<h2>Primeiro calcule a margem de contribuição</h2><p>A margem de contribuição é o valor que sobra da venda após custos e despesas variáveis ligados a ela. Se um produto é vendido por R$ 100 e possui R$ 55 de componentes variáveis, a margem unitária é R$ 45, equivalente a 45% do preço.</p><p>Esse percentual é a ponte entre faturamento e capacidade de pagar a estrutura fixa.</p>\r\n<h2>Como calcular o ponto de equilíbrio em faturamento</h2><p>Em uma abordagem gerencial simples, divida os custos fixos pela margem de contribuição percentual. Com R$ 30.000 de custos fixos e margem de 40%, o faturamento de equilíbrio é R$ 75.000.</p><p>Abaixo desse nível, a margem gerada não cobre integralmente os custos fixos considerados. Acima dele, começa a existir resultado operacional dentro do modelo.</p>\r\n<h2>Como calcular em unidades</h2><p>Quando existe um produto representativo, divida os custos fixos pela margem de contribuição unitária. Com R$ 30.000 de custos fixos e R$ 45 de margem por unidade, seriam necessárias aproximadamente 667 unidades para cobrir a estrutura do exemplo.</p><p>Em negócios com muitos produtos, usar apenas um item pode distorcer o resultado.</p>\r\n<h2>Como trabalhar com mix de vendas</h2><p>Se produtos possuem margens diferentes, calcule uma margem média ponderada pelo mix esperado. Se o mix mudar para itens de margem menor, o ponto de equilíbrio sobe mesmo que os custos fixos permaneçam iguais.</p><p>Atualize a ponderação com vendas reais periodicamente para evitar usar um mix que já não representa o negócio.</p>\r\n<h2>Desconto aumenta o ponto de equilíbrio</h2><p>Quando o preço cai e os custos variáveis não caem na mesma proporção, a margem diminui. Isso significa que a empresa precisa vender mais para pagar a mesma estrutura fixa. Antes de conceder desconto recorrente, simule o novo ponto de equilíbrio.</p>\r\n<h2>Aumento de custo variável também muda o resultado</h2><p>Frete, comissão, taxa de marketplace, imposto ou custo do produto podem reduzir a margem. Um reajuste pequeno em uma despesa variável de grande volume pode elevar significativamente o faturamento mínimo necessário.</p>\r\n<h2>Ponto de equilíbrio não é meta de vendas</h2><p>Vender apenas o suficiente para empatar não remunera necessariamente capital, investimentos, risco ou objetivos de lucro. Use o ponto de equilíbrio como piso analítico e depois calcule uma meta que incorpore o resultado desejado.</p>\r\n<h2>Exemplo com lucro desejado</h2><p>Se a empresa possui R$ 30.000 de custos fixos, margem de contribuição de 40% e deseja gerar R$ 10.000 de resultado operacional, pode somar o lucro desejado aos custos cobertos no cenário: R$ 40.000 ÷ 40% = R$ 100.000 de faturamento de referência.</p>\r\n<h2>Checklist do ponto de equilíbrio</h2><ol><li>Classifique custos fixos.</li><li>Mapeie custos e despesas variáveis.</li><li>Calcule margem unitária e percentual.</li><li>Valide o mix de vendas.</li><li>Calcule faturamento de equilíbrio.</li><li>Converta em unidades quando útil.</li><li>Teste descontos.</li><li>Teste aumento de custos variáveis.</li><li>Inclua lucro desejado em cenário separado.</li><li>Atualize as premissas periodicamente.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule o ponto de equilíbrio no Prazzu Tools</h2><p>Use a ferramenta para testar custos fixos, margem de contribuição e cenários de faturamento sem depender de uma conta isolada.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/ponto-de-equilibrio\">Abrir Calculadora de Ponto de Equilíbrio</a></div></div>\r\n<p><strong>Continue a análise:</strong> revise <a href=\"/blog/margem-markup-como-calcular-preco-venda\">margem e markup na formação de preço</a> e acompanhe o efeito financeiro no <a href=\"/ferramentas/fluxo-de-caixa\">fluxo de caixa</a>.</p>\r\n<h2>Perguntas frequentes</h2><h3>Ponto de equilíbrio é o mesmo que lucro zero?</h3><p>No modelo gerencial básico, ele representa o nível em que a margem cobre os custos fixos considerados, produzindo resultado operacional próximo de zero dentro dessas premissas.</p><h3>Impostos entram no cálculo?</h3><p>Tributos que variam com a venda normalmente devem ser refletidos na margem de contribuição conforme a estrutura utilizada.</p><h3>Posso usar uma margem média?</h3><p>Sim, desde que seja ponderada por um mix de vendas representativo. Uma média simples pode distorcer negócios com produtos muito diferentes.</p>\r\n<h2>Conclusão</h2><p>O <strong>ponto de equilíbrio</strong> transforma estrutura de custos e margem em uma meta mínima de atividade. Ele fica especialmente útil quando a empresa simula preço, desconto, custos variáveis e mix antes de tomar decisões comerciais.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/ponto-de-equilibrio-como-calcular.png', 'Cálculo do ponto de equilíbrio com custos fixos e margem de contribuição', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:22:23', 'ponto de equilíbrio', '[\"como calcular ponto de equilíbrio\", \"margem de contribuição\", \"ponto de equilíbrio financeiro\", \"break even\", \"custos fixos\", \"faturamento mínimo\"]', 'Ponto de equilíbrio: como calcular quanto vender', 'Aprenda como calcular ponto de equilíbrio com custos fixos e margem de contribuição, transformar em faturamento ou unidades e testar preço, desconto e mix.', NULL, 'blog/covers/ponto-de-equilibrio-como-calcular.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:22:23'),
(20, NULL, 'Pró-labore ideal: como definir e simular o valor dos sócios', 'pro-labore-ideal-como-calcular-inss-irrf', 'Entenda como definir pró-labore ideal considerando função do sócio, capacidade da empresa, INSS, IRRF, caixa e separação entre remuneração e lucros.', '<p><strong>Pró-labore ideal</strong> não é um número universal nem simplesmente o menor valor possível. O pró-labore remunera o trabalho do sócio na empresa e deve ser analisado de forma coerente com a função exercida, a realidade financeira do negócio e os efeitos previdenciários e tributários aplicáveis.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> descreva a atuação do sócio, escolha cenários de remuneração, simule encargos e líquido, verifique se o caixa suporta a retirada e mantenha pró-labore separado da distribuição de lucros.</div>\r\n<h2>Comece pela função realmente exercida</h2><p>Registre responsabilidade, dedicação, frequência e papel operacional. Um sócio que administra a empresa diariamente está em situação econômica diferente de um investidor que não trabalha na operação.</p><p>Essa descrição não define sozinha o valor, mas cria uma justificativa muito melhor do que escolher uma retirada aleatória.</p>\r\n<h2>Não confunda pró-labore com distribuição de lucros</h2><p>Pró-labore remunera trabalho. Distribuição de lucros decorre do resultado empresarial apurado e documentado. Transferir dinheiro da conta da empresa para o sócio não transforma automaticamente o valor em lucro distribuído.</p><p>Mantenha cada natureza registrada separadamente na contabilidade e na movimentação financeira.</p>\r\n<h2>Simule o valor bruto e o líquido</h2><p>Escolha alguns cenários de pró-labore e calcule os descontos e encargos aplicáveis em cada um. O valor bruto mostra a remuneração definida; o líquido mostra o que efetivamente chega ao sócio após os descontos considerados.</p><p>Como tabelas e limites podem mudar, use parâmetros vigentes na competência analisada em vez de gravar percentuais antigos como regra permanente.</p>\r\n<h2>Considere o custo para a empresa</h2><p>O pró-labore não deve ser analisado apenas pelo líquido do sócio. Dependendo do enquadramento, podem existir custos patronais relacionados. O simulador deve mostrar separadamente remuneração, descontos e custo empresarial quando aplicável.</p>\r\n<h2>Teste a capacidade de caixa</h2><p>Imagine dois sócios operacionais. Um cenário de R$ 5.000 para cada um representa R$ 10.000 de pró-labore bruto mensal antes dos demais efeitos. Um cenário de R$ 7.000 para cada um aumenta essa base para R$ 14.000. Compare os dois com o caixa e com a geração recorrente do negócio.</p><p>Uma remuneração que força a empresa a atrasar impostos, fornecedores ou folha não é sustentável apenas porque parece adequada isoladamente.</p>\r\n<h2>Relação entre pró-labore e Fator R</h2><p>Em empresas sujeitas ao Fator R, componentes da folha podem influenciar o cálculo usado no enquadramento entre anexos do Simples Nacional. Isso torna importante simular o efeito do pró-labore dentro do conjunto da folha, sem aumentar uma retirada artificialmente apenas para perseguir um resultado tributário.</p>\r\n<h2>Compare com a distribuição de lucros</h2><p>Depois de definir uma remuneração coerente pelo trabalho, analise separadamente se existe lucro disponível para distribuição. Caixa bancário não é sinônimo de lucro contábil, e distribuir tudo o que sobra na conta pode comprometer capital de giro.</p>\r\n<h2>Crie uma política de revisão</h2><p>Em vez de alterar o pró-labore todo mês sem critério, defina momentos de revisão: mudança relevante de função, crescimento da empresa, alteração societária ou revisão anual. Documente o motivo da mudança.</p>\r\n<h2>Checklist para definir pró-labore</h2><ol><li>Descreva a função de cada sócio.</li><li>Defina cenários de valor bruto.</li><li>Calcule descontos aplicáveis.</li><li>Calcule custos patronais quando cabíveis.</li><li>Compare o líquido entre cenários.</li><li>Teste a capacidade de caixa.</li><li>Analise efeitos no Fator R quando aplicável.</li><li>Separe distribuição de lucros.</li><li>Documente o critério escolhido.</li><li>Revise o valor periodicamente.</li></ol>\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule o pró-labore no Prazzu Tools</h2><p>Use o simulador para comparar diferentes valores, visualizar descontos e avaliar o impacto da remuneração dos sócios antes de definir a retirada.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/simulador-pro-labore-ideal\">Abrir Simulador de Pró-labore Ideal</a></div></div>\r\n<p><strong>Veja também:</strong> aprofunde a separação entre retiradas no guia de <a href=\"/blog/pro-labore-distribuicao-lucros-como-calcular\">pró-labore e distribuição de lucros</a> e confira o <a href=\"/blog/fator-r-como-calcular-anexo-iii-v\">Fator R</a> quando aplicável.</p>\r\n<h2>Perguntas frequentes</h2><h3>Existe um pró-labore mínimo ideal para toda empresa?</h3><p>Não existe um único valor ideal para todas as situações. A análise depende da atuação do sócio, das regras aplicáveis e da realidade financeira da empresa.</p><h3>Posso retirar apenas distribuição de lucros?</h3><p>A resposta depende da atuação do sócio e da situação concreta. Pró-labore e lucro possuem naturezas diferentes e não devem ser trocados apenas por conveniência.</p><h3>Pró-labore maior sempre melhora o Fator R?</h3><p>O Fator R considera um conjunto de valores e períodos. Aumentar uma retirada sem analisar o quadro completo pode elevar custo sem produzir o benefício esperado.</p>\r\n<h2>Conclusão</h2><p>Definir um <strong>pró-labore ideal</strong> é equilibrar coerência da remuneração, custo, líquido, caixa e organização contábil. A simulação ajuda a comparar alternativas, mas a escolha precisa continuar compatível com a atuação real dos sócios e com as regras vigentes.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/pro-labore-ideal-como-calcular-inss-irrf.png', 'Simulação de pró-labore ideal com remuneração, encargos e valor líquido', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:22:23', 'pró-labore ideal', '[\"como definir pró-labore\", \"calcular pró-labore\", \"INSS pró-labore\", \"IRRF pró-labore\", \"remuneração dos sócios\", \"distribuição de lucros\"]', 'Pró-labore ideal: como definir e simular o valor', 'Veja como definir pró-labore ideal considerando função do sócio, capacidade da empresa, INSS, IRRF, caixa e separação entre remuneração e lucros.', NULL, 'blog/covers/pro-labore-ideal-como-calcular-inss-irrf.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:22:23'),
(21, NULL, 'Comissão de vendedores: como calcular metas, percentuais e pagamento', 'comissao-vendedores-como-calcular', 'Aprenda como calcular comissão de vendedores com base líquida, metas, percentuais, bônus, estornos e regras de competência antes de fechar o pagamento.', '<p><strong>Comissão de vendedores</strong> parece simples quando existe apenas um percentual sobre as vendas, mas a rotina real costuma envolver meta, bônus, estornos, devoluções, competência e regras diferentes por produto ou canal. Para evitar conflito, a memória precisa mostrar qual foi a base comissionável e como cada parcela foi formada.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> defina a base de vendas, retire estornos e devoluções conforme a regra aplicável, calcule a comissão-base, verifique o atingimento da meta e só então some bônus ou aceleradores previstos.</div>\r\n\r\n<h2>Defina primeiro a base comissionável</h2>\r\n<p>O primeiro erro é aplicar o percentual sobre um faturamento que não corresponde à regra comercial. Dependendo do plano, a comissão pode considerar pedidos faturados, vendas recebidas, vendas líquidas de cancelamentos ou outra métrica contratada.</p>\r\n<p>Registre de forma objetiva o que entra e o que sai da base. Isso evita discussões posteriores sobre vendas canceladas, devolvidas ou não recebidas.</p>\r\n\r\n<h2>Estornos e devoluções precisam de regra clara</h2>\r\n<p>Se a política comercial prevê que devoluções reduzem a base, mostre esses valores separadamente. Por exemplo, faturamento de R$ 100.000 com R$ 8.000 de estornos gera base líquida de R$ 92.000 antes da aplicação do percentual.</p>\r\n<p>Quando a devolução ocorre em competência posterior, defina se haverá ajuste retroativo ou compensação no período seguinte. Essa regra precisa ser conhecida antes do fechamento.</p>\r\n\r\n<h2>Como calcular a comissão-base</h2>\r\n<p>Depois de chegar à base líquida, aplique o percentual previsto. Em uma base de R$ 92.000 com comissão de 2%, a comissão-base seria R$ 1.840.</p>\r\n<p>Se existirem percentuais diferentes por produto, canal ou faixa, calcule cada grupo separadamente e some no final. Usar uma média simples pode distorcer o resultado.</p>\r\n\r\n<h2>Como calcular metas e bônus</h2>\r\n<p>A meta deve usar a mesma base prevista no plano. Se o atingimento da meta considera vendas líquidas de estornos, a comparação precisa ser feita sobre esse mesmo valor.</p>\r\n<p>Exemplo: meta de R$ 100.000 e base líquida de R$ 92.000 significam 92% de atingimento. Se o bônus só começa em 100%, não deve ser pago nesse cenário.</p>\r\n\r\n<h2>Faixas progressivas e aceleradores</h2>\r\n<p>Alguns planos elevam o percentual quando a meta é superada. Nesse caso, documente se a nova alíquota incide sobre toda a base ou apenas sobre a faixa excedente. As duas regras produzem resultados diferentes.</p>\r\n<p>Evite frases genéricas como “3% acima da meta” sem definir a base exata de incidência.</p>\r\n\r\n<h2>Comissão por recebimento ou faturamento?</h2>\r\n<p>Essa escolha altera o momento do reconhecimento. Um plano por faturamento pode gerar comissão antes do caixa entrar; um plano por recebimento reduz esse descasamento, mas exige acompanhar liquidações.</p>\r\n<p>O critério deve estar alinhado ao contrato, política comercial e legislação aplicável ao vínculo.</p>\r\n\r\n<h2>Como conferir o pagamento</h2>\r\n<ol>\r\n<li>Some as vendas consideradas na competência.</li>\r\n<li>Separe cancelamentos, estornos e devoluções.</li>\r\n<li>Calcule a base líquida.</li>\r\n<li>Aplique os percentuais previstos.</li>\r\n<li>Calcule o atingimento da meta.</li>\r\n<li>Adicione bônus ou aceleradores.</li>\r\n<li>Revise ajustes de competências anteriores.</li>\r\n<li>Compare com o relatório comercial.</li>\r\n<li>Registre a memória de cálculo.</li>\r\n</ol>\r\n\r\n<h2>Erros comuns em planos de comissão</h2>\r\n<ul>\r\n<li>meta calculada sobre uma base e comissão sobre outra;</li>\r\n<li>estorno aplicado duas vezes;</li>\r\n<li>bônus pago sem verificar o gatilho;</li>\r\n<li>percentual progressivo aplicado à base inteira sem previsão;</li>\r\n<li>mudança de regra no meio da competência sem documentação;</li>\r\n<li>confundir venda realizada com venda recebida.</li>\r\n</ul>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule a comissão no Prazzu Tools</h2><p>A Calculadora de Comissão de Vendedores do projeto trabalha com faturamento, percentual, meta, bônus e estornos, permitindo visualizar a base líquida e o resultado de forma organizada.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/comissao-vendedores\">Abrir Calculadora de Comissão</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> para analisar o impacto da comissão na rentabilidade, consulte o guia de <a href=\"/blog/margem-markup-como-calcular-preco-venda\">margem e markup</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Comissão deve ser calculada sobre o faturamento bruto?</h3><p>Não necessariamente. A base depende da regra adotada. O importante é definir previamente o que integra a base e como tratar cancelamentos e devoluções.</p>\r\n<h3>Meta e comissão podem usar bases diferentes?</h3><p>Podem, se a política definir isso claramente, mas aumenta a complexidade. Para evitar erro, documente as duas bases separadamente.</p>\r\n<h3>Como tratar devolução após a comissão já paga?</h3><p>A política deve definir o tratamento da competência posterior e respeitar as regras aplicáveis à relação de trabalho ou contratação.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Calcular <strong>comissão de vendedores</strong> com segurança é transformar a política comercial em etapas verificáveis. Base líquida, percentual, meta, bônus e estornos devem aparecer separados para que vendedor, financeiro e folha consigam chegar ao mesmo resultado.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/comissao-vendedores-como-calcular.png', 'Cálculo de comissão de vendedores com meta, percentual, bônus e estornos', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:25:26', 'comissão de vendedores', '[\"como calcular comissão\", \"comissão de vendas\", \"meta de vendas\", \"bônus por meta\", \"estorno de comissão\", \"base comissionável\"]', 'Comissão de vendedores: como calcular metas e valor', 'Aprenda como calcular comissão de vendedores com base líquida, percentuais, metas, bônus e estornos e monte uma memória clara antes do pagamento.', NULL, 'blog/covers/comissao-vendedores-como-calcular.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:25:26'),
(22, NULL, 'Como preencher um holerite: proventos, descontos e conferência', 'holerite-como-calcular-proventos-descontos', 'Veja como preencher um holerite organizando salário, proventos, descontos, bases e líquido e aprenda quais pontos conferir antes de entregar o demonstrativo.', '<p><strong>Como preencher um holerite</strong> corretamente exige mais do que mostrar salário bruto e líquido. O demonstrativo precisa organizar proventos, descontos, bases de cálculo e identificação da competência de forma que o empregado consiga entender como o valor final foi formado.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme empregado e competência, lance proventos por rubrica, classifique corretamente descontos, confira bases previdenciárias e fiscais e valide se bruto menos descontos corresponde ao líquido exibido.</div>\r\n\r\n<h2>Identificação do empregado e da competência</h2>\r\n<p>Comece pelos dados básicos: nome, matrícula quando usada, função, competência e identificação do empregador. A competência deve corresponder ao período em que as verbas foram apuradas.</p>\r\n<p>Um erro de competência pode fazer o trabalhador comparar valores com o mês errado e também prejudicar conciliações internas.</p>\r\n\r\n<h2>O que são proventos</h2>\r\n<p>Proventos são valores creditados ao trabalhador no demonstrativo. Podem incluir salário, horas extras, adicionais, comissões, gratificações e outras verbas conforme o caso.</p>\r\n<p>Cada verba deve aparecer em linha própria, com descrição suficientemente clara para permitir conferência. Evite agrupar componentes diferentes em uma única rubrica sem necessidade.</p>\r\n\r\n<h2>Salário-base e remuneração não são sempre iguais</h2>\r\n<p>O salário contratual pode ser apenas um dos componentes da remuneração do mês. Horas extras, adicional noturno, insalubridade, periculosidade, comissões e outros itens podem alterar o total bruto.</p>\r\n<p>Por isso, não use o salário-base como se fosse automaticamente a base de todos os descontos.</p>\r\n\r\n<h2>O que são descontos</h2>\r\n<p>Descontos podem incluir INSS, IRRF quando devido, vale-transporte, faltas, adiantamentos, benefícios e outras retenções permitidas. Cada item precisa ter fundamento e base compatíveis com sua natureza.</p>\r\n<p>Não lance um desconto apenas para fazer o líquido coincidir com um valor esperado. Se houver diferença, investigue a rubrica de origem.</p>\r\n\r\n<h2>Confira as bases separadamente</h2>\r\n<p>Base de INSS, base de FGTS e base de IRRF podem ser diferentes. O holerite deve refletir as bases geradas pela folha e permitir que a equipe identifique por que determinada verba integrou ou não cada cálculo.</p>\r\n<p>Essa separação é especialmente útil quando existem férias, afastamentos, adicionais ou verbas com incidências distintas.</p>\r\n\r\n<h2>Como chegar ao líquido</h2>\r\n<p>Em uma conferência básica, some todos os proventos para obter o total bruto e depois some os descontos. O líquido é a diferença entre os dois grupos.</p>\r\n<p>Exemplo: proventos de R$ 4.800 e descontos de R$ 950 resultam em líquido de R$ 3.850. Se o demonstrativo mostrar outro número, existe alguma rubrica não considerada na soma ou um erro a investigar.</p>\r\n\r\n<h2>Comissões e valores variáveis</h2>\r\n<p>Quando houver comissão, horas extras ou outras parcelas variáveis, registre a memória que originou o valor. Isso facilita responder dúvidas e comparar o holerite com relatórios comerciais ou de ponto.</p>\r\n<p>Se a comissão foi ajustada por estornos, mantenha esse cálculo disponível fora do holerite para auditoria.</p>\r\n\r\n<h2>Checklist de conferência do holerite</h2>\r\n<ol>\r\n<li>Confirme empregado e competência.</li>\r\n<li>Revise salário contratual.</li>\r\n<li>Confira horas e adicionais.</li>\r\n<li>Valide comissões e variáveis.</li>\r\n<li>Some os proventos.</li>\r\n<li>Revise cada desconto.</li>\r\n<li>Confira bases de INSS, FGTS e IRRF.</li>\r\n<li>Calcule o líquido.</li>\r\n<li>Compare com a folha e o pagamento bancário.</li>\r\n<li>Guarde o demonstrativo conforme a rotina da empresa.</li>\r\n</ol>\r\n\r\n<h2>Erros comuns no holerite</h2>\r\n<ul>\r\n<li>competência incorreta;</li>\r\n<li>comissão lançada em duplicidade;</li>\r\n<li>adiantamento não descontado ou descontado duas vezes;</li>\r\n<li>base de cálculo copiada de outro empregado;</li>\r\n<li>benefício lançado como provento quando deveria ser apenas informativo;</li>\r\n<li>diferença entre líquido do demonstrativo e valor pago.</li>\r\n</ul>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Gere um holerite no Prazzu Tools</h2><p>O Gerador de Holerite organiza salário, proventos adicionais, descontos e resumo do demonstrativo, permitindo impressão ou salvamento em PDF pelo navegador.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/gerador-holerite\">Abrir Gerador de Holerite</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> confira o guia de <a href=\"/blog/comissao-vendedores-como-calcular\">comissão de vendedores</a> para entender a origem das verbas variáveis.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Holerite e folha de pagamento são a mesma coisa?</h3><p>Não exatamente. A folha reúne a apuração da empresa; o holerite é o demonstrativo individual entregue ao trabalhador.</p>\r\n<h3>Todo desconto usa a mesma base?</h3><p>Não. Cada desconto ou encargo possui regra própria. Por isso, é importante separar as bases.</p>\r\n<h3>Posso corrigir o líquido manualmente?</h3><p>Não é uma boa prática. A correção deve ocorrer na rubrica, base ou parâmetro que causou a diferença.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Entender <strong>como preencher um holerite</strong> é organizar a folha em uma sequência que possa ser auditada. Quando proventos, descontos e bases aparecem claramente, o demonstrativo deixa de ser apenas um recibo e passa a funcionar como ferramenta de conferência.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/holerite-como-calcular-proventos-descontos.png', 'Holerite com proventos, descontos, bases e valor líquido', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:25:26', 'como preencher um holerite', '[\"holerite\", \"contracheque\", \"proventos e descontos\", \"folha de pagamento\", \"salário líquido\", \"bases INSS e IRRF\"]', 'Como preencher um holerite e conferir os valores', 'Veja como preencher um holerite com salário, proventos, descontos, bases e líquido e quais conferências fazer antes de entregar o demonstrativo ao empregado.', NULL, 'blog/covers/holerite-como-calcular-proventos-descontos.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:25:26'),
(23, NULL, 'Admissão de funcionário: custos e checklist antes da contratação', 'admissao-como-calcular-custo-contratacao', 'Veja os principais custos e etapas da admissão de funcionário, desde salário e benefícios até exame, equipamentos, documentos e envio ao eSocial.', '<p>A <strong>admissão de funcionário</strong> envolve custo recorrente, desembolso inicial e obrigações cadastrais que precisam estar prontas antes do início do trabalho. Planejar apenas o salário pode fazer a empresa subestimar equipamentos, exame admissional, benefícios, recrutamento e impacto mensal da folha.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme cargo, salário e jornada, estime os custos mensais e únicos, reúna documentos, realize os procedimentos ocupacionais aplicáveis e envie a admissão ao eSocial dentro do prazo correspondente.</div>\r\n\r\n<h2>Separe custo mensal de custo de admissão</h2>\r\n<p>O salário e os encargos fazem parte do custo recorrente. Exame admissional, recrutamento, equipamento, uniforme e treinamento inicial podem ser custos concentrados no começo do vínculo.</p>\r\n<p>Separar os dois grupos ajuda a responder duas perguntas diferentes: quanto custa contratar agora e quanto essa contratação acrescentará ao orçamento mensal.</p>\r\n\r\n<h2>Salário é apenas o ponto de partida</h2>\r\n<p>Além do salário, avalie benefícios, FGTS, contribuições patronais quando aplicáveis, provisões de férias e 13º e outros componentes relacionados ao contrato.</p>\r\n<p>Use o mesmo critério dos demais cálculos de folha para evitar que o simulador de admissão apresente custo diferente do orçamento de pessoal.</p>\r\n\r\n<h2>Custos únicos da contratação</h2>\r\n<p>Dependendo da função, podem existir exame admissional, recrutamento, uniformes, equipamentos, licenças de software, treinamento e integração. Alguns itens permanecem como ativos da empresa e outros são despesas do processo.</p>\r\n<p>Registre esses valores separadamente para não transformá-los em “encargo mensal” artificial.</p>\r\n\r\n<h2>Documentos e dados cadastrais</h2>\r\n<p>Confirme os dados necessários ao registro do vínculo, função, salário, jornada, estabelecimento e demais informações exigidas no processo. Dados incorretos na admissão podem gerar retrabalho em folha e eventos posteriores.</p>\r\n<p>Evite coletar documentos e informações sem necessidade. A rotina deve seguir as exigências aplicáveis e a política de proteção de dados da empresa.</p>\r\n\r\n<h2>Prazo de admissão no eSocial</h2>\r\n<p>Para empregados, o Manual Web Geral do eSocial informa que a admissão deve ser transmitida até o dia imediatamente anterior ao início da prestação dos serviços. Existem regras específicas para situações como admissão preliminar, transferência e outras categorias.</p>\r\n<p>Por isso, não deixe a transmissão para depois que o empregado já começou a trabalhar. Organize o checklist para que a documentação esteja pronta antes da data de início.</p>\r\n\r\n<h2>Exame admissional e saúde ocupacional</h2>\r\n<p>Os procedimentos de saúde ocupacional devem ser planejados antes do início das atividades conforme as regras aplicáveis à função e ao empregador. Inclua o custo e o prazo desses procedimentos no cronograma de contratação.</p>\r\n\r\n<h2>Equipamentos e acessos</h2>\r\n<p>Liste computador, telefone, uniforme, EPI quando necessário, crachá, e-mail, sistemas e permissões. A falta de acesso no primeiro dia gera custo improdutivo e pode comprometer segurança da informação quando permissões são improvisadas.</p>\r\n<p>Defina também quem aprova e quem revoga acessos no futuro.</p>\r\n\r\n<h2>Exemplo de orçamento de admissão</h2>\r\n<p>Imagine salário de R$ 4.000, benefícios mensais de R$ 800 e custos iniciais de R$ 2.500 com recrutamento, exame, equipamento e treinamento. O planejamento deve mostrar os R$ 2.500 como desembolso inicial e calcular separadamente o custo mensal completo do vínculo.</p>\r\n<p>Essa estrutura facilita comparar contratação, orçamento anual e prazo de retorno esperado para a nova função.</p>\r\n\r\n<h2>Checklist antes do primeiro dia</h2>\r\n<ol>\r\n<li>Defina cargo, salário, jornada e local de trabalho.</li>\r\n<li>Aprove o orçamento da vaga.</li>\r\n<li>Reúna os dados cadastrais necessários.</li>\r\n<li>Realize o procedimento admissional aplicável.</li>\r\n<li>Calcule benefícios e custo mensal.</li>\r\n<li>Providencie equipamentos e acessos.</li>\r\n<li>Prepare contrato e políticas internas.</li>\r\n<li>Envie a admissão ao eSocial no prazo correto.</li>\r\n<li>Cadastre o empregado nos sistemas internos.</li>\r\n<li>Organize integração e treinamento.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule a admissão no Prazzu Tools</h2><p>O Simulador de Admissão considera salário, benefícios e custos únicos como exame, recrutamento, equipamentos e treinamento, além de apoiar um checklist da contratação.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/simulador-admissao\">Abrir Simulador de Admissão</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> aprofunde o orçamento com o guia de <a href=\"/blog/custo-funcionario-clt-como-calcular\">custo total de funcionário CLT</a> e o de <a href=\"/blog/encargos-trabalhistas-como-calcular-folha\">encargos trabalhistas</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Posso registrar a admissão depois que o empregado começou?</h3><p>A regra geral do eSocial para empregados exige transmissão até o dia imediatamente anterior ao início da prestação dos serviços, ressalvadas situações específicas previstas no sistema.</p>\r\n<h3>Equipamento entra no custo mensal?</h3><p>Para análise gerencial, pode ser mostrado como custo inicial e depois depreciado ou rateado conforme a política da empresa. Não é necessário misturá-lo aos encargos da folha.</p>\r\n<h3>Exame admissional deve ser previsto no cronograma?</h3><p>Sim. Procedimentos ocupacionais aplicáveis precisam ser considerados antes do início das atividades.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Planejar a <strong>admissão de funcionário</strong> é combinar orçamento, documentos, saúde ocupacional, acessos e prazo cadastral. Quando custos iniciais e recorrentes ficam separados, a empresa sabe quanto precisa desembolsar antes e quanto o novo vínculo representará todos os meses.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/admissao-como-calcular-custo-contratacao.png', 'Checklist de admissão de funcionário com custos, documentos e eSocial', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:25:26', 'admissão de funcionário', '[\"admissão CLT\", \"custo de admissão\", \"checklist de admissão\", \"eSocial admissão\", \"exame admissional\", \"documentos admissão funcionário\"]', 'Admissão de funcionário: custos e checklist completo', 'Veja custos e etapas da admissão de funcionário, incluindo salário, benefícios, exame, equipamentos, documentos e o envio da admissão ao eSocial.', NULL, 'blog/covers/admissao-como-calcular-custo-contratacao.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:25:26'),
(24, NULL, 'Reajuste salarial: como calcular percentual, retroativo e novo salário', 'reajuste-salarial-como-calcular-retroativo', 'Aprenda como calcular reajuste salarial, aplicar percentual, somar aumento fixo, apurar diferenças retroativas e conferir a data-base da convenção.', '<p><strong>Reajuste salarial</strong> deve ser calculado a partir da regra válida para a categoria e para o empregado, não de um índice escolhido isoladamente. Convenção ou acordo coletivo pode definir percentual, data-base, pisos, valores fixos, proporcionalidade e tratamento das diferenças retroativas.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme o instrumento coletivo e a data-base, identifique o salário de referência, aplique percentual e eventual aumento fixo, calcule o novo salário e depois apure mês a mês as diferenças retroativas quando houver.</div>\r\n\r\n<h2>Comece pela convenção ou acordo aplicável</h2>\r\n<p>Antes da conta, confirme categoria profissional, sindicato, abrangência territorial e período de vigência. Duas empresas da mesma cidade podem estar sujeitas a instrumentos diferentes conforme atividade e representação.</p>\r\n<p>Não presuma que o IPCA, INPC ou outro índice econômico é automaticamente o percentual do reajuste. O instrumento coletivo é que define a regra aplicável quando houver negociação coletiva.</p>\r\n\r\n<h2>Como aplicar um reajuste percentual</h2>\r\n<p>Se o salário-base é R$ 3.000 e o reajuste definido é 5%, o aumento é R$ 150 e o novo salário passa a R$ 3.150.</p>\r\n<p>A conta é simples, mas a base precisa estar correta. Algumas normas podem definir piso, proporcionalidade para admitidos após determinada data ou critérios específicos.</p>\r\n\r\n<h2>Como tratar aumento fixo adicional</h2>\r\n<p>Algumas convenções podem prever, além do percentual, um valor fixo ou regra complementar. Se houver um aumento fixo, aplique exatamente na ordem prevista no instrumento.</p>\r\n<p>Percentual seguido de valor fixo pode produzir resultado diferente de somar tudo como se fosse um único percentual.</p>\r\n\r\n<h2>O que é reajuste retroativo</h2>\r\n<p>Quando o instrumento é concluído depois da data-base, pode haver diferença entre o salário pago e o salário que passou a ser devido desde a data definida. Essa diferença deve ser apurada por competência.</p>\r\n<p>Exemplo: se o novo salário é R$ 3.150 e foram pagos R$ 3.000 por dois meses abrangidos pela retroatividade, a diferença salarial bruta simples seria R$ 150 por mês, totalizando R$ 300 antes de reflexos e demais efeitos aplicáveis.</p>\r\n\r\n<h2>Apure o retroativo mês a mês</h2>\r\n<p>Não multiplique automaticamente a diferença por vários meses sem revisar férias, afastamentos, admissões, alterações salariais, horas extras, adicionais e outras verbas que possam ter sido afetadas.</p>\r\n<p>Uma memória detalhada mostra salário pago, salário devido, diferença e reflexos de cada competência.</p>\r\n\r\n<h2>Piso salarial também precisa ser conferido</h2>\r\n<p>Mesmo após aplicar o percentual, verifique se o resultado respeita o piso definido para função ou categoria quando houver. Em algumas negociações, o piso recebe regra própria diferente do percentual geral.</p>\r\n\r\n<h2>Admitidos após a data-base</h2>\r\n<p>Alguns instrumentos coletivos definem reajuste proporcional ou regras específicas para empregados admitidos após determinada data. Não aplique a mesma fórmula automaticamente a todos os empregados sem revisar essas cláusulas.</p>\r\n\r\n<h2>Exemplo de memória de reajuste</h2>\r\n<p>Uma tabela útil pode ter as colunas: salário anterior, percentual, aumento percentual, aumento fixo, novo salário, competência inicial e diferença retroativa. Se houver reflexos, mantenha-os em linhas separadas.</p>\r\n<p>Isso facilita a conferência pelo RH, contabilidade e empregado e evita alterar valores manualmente para fazer o total “bater”.</p>\r\n\r\n<h2>Checklist do reajuste salarial</h2>\r\n<ol>\r\n<li>Confirme categoria e instrumento coletivo.</li>\r\n<li>Verifique data-base e vigência.</li>\r\n<li>Identifique salário de referência.</li>\r\n<li>Confira pisos aplicáveis.</li>\r\n<li>Aplique percentual previsto.</li>\r\n<li>Some valor fixo quando houver.</li>\r\n<li>Revise regras de proporcionalidade.</li>\r\n<li>Calcule retroativos por competência.</li>\r\n<li>Analise reflexos nas demais verbas.</li>\r\n<li>Atualize folha e registros correspondentes.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule o reajuste no Prazzu Tools</h2><p>A Calculadora de Reajuste Salarial permite informar salário atual, percentual e aumento fixo previsto, apoiando a conferência do novo salário e das diferenças.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/reajuste-salarial\">Abrir Calculadora de Reajuste Salarial</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> depois do reajuste, revise o impacto no <a href=\"/blog/encargos-trabalhistas-como-calcular-folha\">custo da folha e encargos trabalhistas</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Reajuste salarial é sempre igual à inflação?</h3><p>Não. O percentual pode ser definido por negociação coletiva ou outra regra aplicável e não precisa coincidir exatamente com um índice econômico.</p>\r\n<h3>Todo empregado recebe o mesmo percentual?</h3><p>Não necessariamente. Pisos, datas de admissão, faixas e cláusulas específicas podem alterar a aplicação.</p>\r\n<h3>Retroativo é só a diferença do salário-base?</h3><p>Nem sempre. Dependendo da situação, outras verbas e reflexos podem precisar ser revistos. Por isso, a apuração deve ser feita por competência.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Calcular <strong>reajuste salarial</strong> corretamente significa transformar a cláusula aplicável em uma memória verificável. Percentual, valor fixo, piso, proporcionalidade e retroativo devem ser tratados separadamente para reduzir erros na folha.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/reajuste-salarial-como-calcular-retroativo.png', 'Cálculo de reajuste salarial com percentual, retroativo e novo salário', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:25:26', 'reajuste salarial', '[\"como calcular reajuste salarial\", \"dissídio salarial\", \"retroativo salarial\", \"aumento percentual salário\", \"convenção coletiva\", \"novo salário\"]', 'Reajuste salarial: percentual, retroativo e novo salário', 'Aprenda como calcular reajuste salarial, aplicar percentual e aumento fixo, apurar retroativos e conferir data-base e regras da convenção coletiva.', NULL, 'blog/covers/reajuste-salarial-como-calcular-retroativo.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:25:26'),
(25, NULL, 'Distribuição de lucros: como calcular o valor de cada sócio', 'distribuicao-de-lucros-como-calcular-socios', 'Aprenda como calcular distribuição de lucros por sócio, definir a base disponível, preservar capital de giro e conferir a retenção de IRRF aplicável em 2026.', '<p><strong>Distribuição de lucros</strong> não deve começar pelo saldo bancário. O valor distribuível precisa partir de resultado apurado, documentação contábil e decisão societária compatível com a realidade da empresa. Em 2026, a análise também ganhou uma camada tributária importante: determinados pagamentos de lucros e dividendos a pessoa física residente no Brasil passaram a exigir retenção de IRRF.</p>\r\n<div class=\"alert alert-warning\"><strong>Atenção em 2026:</strong> a Lei nº 15.270/2025 passou a prever retenção de 10% quando uma mesma pessoa jurídica paga, credita, emprega ou entrega a uma mesma pessoa física residente no Brasil mais de R$ 50.000 em lucros e dividendos no mesmo mês, observadas as exceções e regras de transição previstas na legislação.</div>\r\n\r\n<h2>1. Comece pelo lucro efetivamente apurado</h2>\r\n<p>Faturamento não é lucro, e caixa não é lucro. A empresa pode ter dinheiro disponível porque ainda não pagou fornecedores, impostos ou folha. Da mesma forma, pode apresentar lucro contábil e ter pouco caixa porque vendeu a prazo ou aumentou estoques.</p>\r\n<p>Antes de dividir valores, confirme o resultado do período, prejuízos acumulados, reservas e outras limitações aplicáveis. A base usada na calculadora deve corresponder ao montante efetivamente aprovado para distribuição.</p>\r\n\r\n<h2>2. Defina quanto ficará na empresa</h2>\r\n<p>Mesmo quando existe lucro disponível, distribuir 100% pode enfraquecer o capital de giro. Faça uma projeção de caixa para impostos, folha, fornecedores, parcelas, investimentos e sazonalidade.</p>\r\n<p>Exemplo: lucro disponível de R$ 200.000 não significa que R$ 200.000 devam sair da empresa. Se a operação precisa manter R$ 80.000 para sustentar o ciclo financeiro, uma decisão possível seria distribuir apenas parte do saldo, conforme a deliberação dos sócios.</p>\r\n\r\n<h2>3. Calcule a participação de cada sócio</h2>\r\n<p>Se a empresa decidiu distribuir R$ 120.000 e os sócios possuem participações de 60% e 40%, a divisão proporcional seria R$ 72.000 e R$ 48.000. A memória deve mostrar base, percentual e valor individual.</p>\r\n<p>Se a distribuição não seguir exatamente a participação societária, é necessário verificar contrato social, legislação e documentação aplicável antes de executar o pagamento.</p>\r\n\r\n<h2>4. Pró-labore e lucro são naturezas diferentes</h2>\r\n<p>O pró-labore remunera o trabalho do sócio na operação. A distribuição de lucros remunera o capital e decorre do resultado empresarial. Misturar as duas naturezas em uma única transferência reduz rastreabilidade e pode criar problemas contábeis e tributários.</p>\r\n<p>Mantenha os lançamentos e documentos separados, mesmo quando os valores são pagos na mesma data.</p>\r\n\r\n<h2>5. Como funciona a retenção de IRRF em 2026</h2>\r\n<p>A partir de janeiro de 2026, pagamentos de lucros e dividendos feitos por uma mesma pessoa jurídica a uma mesma pessoa física residente no Brasil em valor superior a R$ 50.000 no mesmo mês ficam sujeitos, em regra, à retenção de 10% sobre o total pago, creditado, empregado ou entregue naquele mês.</p>\r\n<p>Se houver mais de um pagamento no mesmo mês, os valores precisam ser considerados em conjunto para verificar o limite. Também existem regras de transição para determinadas distribuições aprovadas até 31 de dezembro de 2025 e pagas nos anos seguintes, desde que atendidos os requisitos legais.</p>\r\n\r\n<h2>6. Exemplo de conferência do limite mensal</h2>\r\n<p>Imagine que uma empresa pague R$ 30.000 a um sócio no início do mês e mais R$ 25.000 no final do mesmo mês. O total mensal entregue pela mesma PJ à mesma pessoa física é R$ 55.000. Para fins da regra de 2026, os pagamentos precisam ser analisados de forma acumulada no mês.</p>\r\n<p>Não use esse exemplo como substituto da apuração fiscal. Verifique se há exceção, regra de transição ou tratamento específico aplicável ao caso.</p>\r\n\r\n<h2>7. EFD-Reinf e registro da distribuição</h2>\r\n<p>Em 2026, a Receita também ajustou a EFD-Reinf para registrar lucros e dividendos e distinguir situações com e sem retenção. Isso reforça a importância de manter data, beneficiário, valor bruto, eventual retenção e natureza do pagamento organizados.</p>\r\n\r\n<h2>Checklist antes de distribuir lucros</h2>\r\n<ol>\r\n<li>Confirme o lucro apurado e disponível.</li>\r\n<li>Revise prejuízos, reservas e restrições.</li>\r\n<li>Projete capital de giro e caixa.</li>\r\n<li>Defina o montante que será distribuído.</li>\r\n<li>Confira a participação ou regra societária.</li>\r\n<li>Separe pró-labore da distribuição.</li>\r\n<li>Some pagamentos do mês por sócio.</li>\r\n<li>Verifique a regra de IRRF vigente em 2026.</li>\r\n<li>Documente a deliberação.</li>\r\n<li>Registre corretamente a operação nas obrigações aplicáveis.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule a distribuição no Prazzu Tools</h2><p>A ferramenta organiza o lucro disponível, percentuais e valores por sócio para criar uma memória clara antes da decisão e do registro contábil.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/distribuicao-de-lucros\">Abrir Calculadora de Distribuição de Lucros</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> compare com o <a href=\"/ferramentas/simulador-pro-labore-ideal\">simulador de pró-labore</a> e confira a <a href=\"/ferramentas/capital-de-giro\">necessidade de capital de giro</a> antes de retirar caixa da empresa.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Posso distribuir todo o dinheiro que está no banco?</h3><p>Não é um critério seguro. Caixa e lucro são grandezas diferentes. Primeiro confirme o resultado e depois avalie quanto pode sair sem comprometer a operação.</p>\r\n<h3>Em 2026 todo lucro distribuído tem 10% de IRRF?</h3><p>Não. A regra mensal depende do valor pago pela mesma PJ à mesma pessoa física e existem exceções e regras de transição. É necessário verificar a situação concreta.</p>\r\n<h3>Posso somar pró-labore e lucro?</h3><p>Os valores podem até ser pagos próximos, mas devem manter natureza, cálculo e documentação separados.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Uma boa <strong>distribuição de lucros</strong> combina resultado contábil, caixa, regra societária e tributação vigente. Em 2026, acompanhar o total mensal entregue a cada sócio pessoa física passou a ser ainda mais importante para evitar erro na retenção e no registro da operação.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/distribuicao-de-lucros-como-calcular-socios.png', 'Distribuição de lucros entre sócios com participação, caixa e IRRF em 2026', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:28:19', 'distribuição de lucros', '[\"cálculo distribuição de lucros\", \"lucro por sócio\", \"participação societária\", \"IRRF lucros 2026\", \"dividendos 2026\", \"capital de giro\"]', 'Distribuição de lucros: cálculo por sócio em 2026', 'Veja como calcular distribuição de lucros por sócio, definir a base disponível, preservar caixa e conferir a nova retenção de IRRF aplicável em 2026.', NULL, 'blog/covers/distribuicao-de-lucros-como-calcular-socios.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:28:19');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `vertical_slug`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(26, NULL, 'Declaração de rendimentos: como preencher valores e período', 'declaracao-de-rendimentos-como-preencher', 'Veja como preencher declaração de rendimentos com identificação, período, origem, valores brutos ou líquidos e documentos de apoio sem misturar conceitos.', '<p>Uma <strong>declaração de rendimentos</strong> deve deixar claro quem declara, qual período está sendo informado, de onde vieram os valores e se eles são brutos ou líquidos. O documento perde utilidade quando usa apenas uma “renda média” sem mostrar a origem ou mistura faturamento da atividade com renda pessoal.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> identifique o declarante, defina o período, separe as fontes de renda, informe os valores com o mesmo critério e apresente total e média quando isso ajudar o destinatário.</div>\r\n\r\n<h2>O que é uma declaração de rendimentos?</h2>\r\n<p>É um documento declaratório usado para organizar informações sobre rendimentos recebidos em determinado período. Ele pode ser solicitado em processos de aluguel, crédito, cadastro, matrícula ou outras situações.</p>\r\n<p>O documento não substitui automaticamente comprovantes oficiais. A instituição que o recebe pode exigir extratos, notas fiscais, informes, recibos, declarações fiscais ou outros documentos.</p>\r\n\r\n<h2>1. Identifique quem está declarando</h2>\r\n<p>Inclua nome e os dados realmente necessários à finalidade. Evite inserir informações pessoais que não tenham utilidade para o destinatário.</p>\r\n<p>Quando a declaração for emitida por contador, empresa ou terceiro, deixe explícito quem presta a informação e em que qualidade.</p>\r\n\r\n<h2>2. Escolha um período objetivo</h2>\r\n<p>Prefira datas claras, como “janeiro a junho de 2026”, ou liste as competências individualmente. Um período verificável permite cruzar os valores com documentos de apoio.</p>\r\n<p>Evite calcular uma média usando apenas meses de maior renda. Se o objetivo é representar seis meses, use todos os seis meses previstos no critério.</p>\r\n\r\n<h2>3. Informe a origem dos rendimentos</h2>\r\n<p>Separe salário, pró-labore, prestação de serviços, aluguéis, comissões ou outras origens. Uma pessoa pode ter mais de uma fonte de renda e isso deve aparecer de forma organizada.</p>\r\n<p>“Faturamento da empresa” e “rendimento da pessoa física” não são sinônimos. Se o valor informado é faturamento, escreva isso expressamente.</p>\r\n\r\n<h2>4. Diferencie valor bruto e líquido</h2>\r\n<p>Um valor bruto é anterior a descontos ou despesas. Um valor líquido depende de quais descontos foram considerados. Não use a palavra “líquido” sem explicar o critério.</p>\r\n<p>Se a instituição pede renda bruta, não desconte tributos ou custos por conta própria. Use o conceito solicitado.</p>\r\n\r\n<h2>5. Como calcular total e média</h2>\r\n<p>Considere três meses com rendimentos de R$ 4.800, R$ 5.100 e R$ 5.300. O total do período é R$ 15.200. A média simples é R$ 15.200 ÷ 3 = aproximadamente R$ 5.066,67.</p>\r\n<p>Apresentar os três valores e a média é mais transparente do que declarar apenas R$ 5.066,67 sem informar como o número foi obtido.</p>\r\n\r\n<h2>6. Use documentos de apoio</h2>\r\n<p>Extratos, notas fiscais, recibos, informes de rendimentos e registros contábeis ajudam a sustentar a informação, conforme a origem. Antes de enviar, confirme quais documentos a instituição aceita.</p>\r\n<p>Se o documento contém dados pessoais de terceiros, reduza as informações ao mínimo necessário e proteja o arquivo durante o envio.</p>\r\n\r\n<h2>7. Assinatura e data</h2>\r\n<p>Inclua data de emissão e assinatura quando a finalidade exigir. Assinatura física ou eletrônica pode ser aceita ou exigida dependendo do processo.</p>\r\n<p>O gerador organiza o texto, mas não autentica por conta própria a veracidade dos rendimentos informados.</p>\r\n\r\n<h2>Checklist da declaração</h2>\r\n<ol>\r\n<li>Identifique o declarante.</li>\r\n<li>Defina a finalidade quando necessário.</li>\r\n<li>Informe o período.</li>\r\n<li>Liste as fontes de rendimento.</li>\r\n<li>Use o mesmo critério de bruto ou líquido.</li>\r\n<li>Calcule total e média corretamente.</li>\r\n<li>Separe faturamento de renda pessoal.</li>\r\n<li>Revise os documentos de apoio.</li>\r\n<li>Inclua data e assinatura quando aplicável.</li>\r\n<li>Guarde uma cópia do que foi enviado.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Gere a declaração no Prazzu Tools</h2><p>O Gerador de Declaração de Rendimentos organiza identificação, período, origem e valores para produzir um documento mais claro e conferível.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/declaracao-rendimentos\">Abrir Gerador de Declaração de Rendimentos</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> se o objetivo é declarar atividade profissional junto com a renda, use a <a href=\"/ferramentas/declaracao-trabalho-renda\">Declaração de Trabalho e Renda</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Declaração de rendimentos substitui comprovante de renda?</h3><p>Depende da instituição. Ela pode aceitar o documento ou exigir comprovação adicional.</p>\r\n<h3>Posso informar apenas a média mensal?</h3><p>Pode ser insuficiente. Quando possível, mostre os valores que formaram a média e o período correspondente.</p>\r\n<h3>Faturamento de MEI ou empresa é renda pessoal?</h3><p>Não automaticamente. Faturamento empresarial e rendimento da pessoa física são conceitos diferentes.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Uma <strong>declaração de rendimentos</strong> útil é específica: período, origem, critério e valores precisam estar claros. Quanto mais fácil for reproduzir o total e a média, menor o risco de o documento gerar dúvida ou ser recusado por falta de contexto.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/declaracao-de-rendimentos-como-preencher.png', 'Declaração de rendimentos com período, origem, valores e média mensal', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:28:19', 'declaração de rendimentos', '[\"como fazer declaração de rendimentos\", \"comprovante de renda\", \"declaração de renda\", \"renda mensal\", \"renda autônomo\", \"documento de rendimentos\"]', 'Declaração de rendimentos: como preencher corretamente', 'Veja como preencher declaração de rendimentos com período, origem, valores brutos ou líquidos, média mensal e documentos de apoio de forma clara.', NULL, 'blog/covers/declaracao-de-rendimentos-como-preencher.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:28:19'),
(27, NULL, 'Declaração de trabalho e renda: como fazer e quais dados incluir', 'declaracao-trabalho-renda-como-fazer', 'Aprenda como fazer declaração de trabalho e renda com atividade, período, remuneração, identificação e finalidade, mantendo os valores claros e verificáveis.', '<p>A <strong>declaração de trabalho e renda</strong> combina duas informações que precisam ser apresentadas separadamente: qual atividade a pessoa exerce e quais rendimentos estão sendo declarados. Um bom documento evita afirmar mais do que pode comprovar e deixa claro período, valor e responsabilidade do declarante.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> identifique o declarante, descreva a atividade sem exageros, informe o período, detalhe os rendimentos e deixe explícito se os valores são brutos, líquidos ou médias.</div>\r\n\r\n<h2>Quando esse documento costuma ser usado</h2>\r\n<p>Pode ser solicitado em processos de aluguel, crédito, cadastro, matrícula, abertura de conta ou outras situações em que a pessoa precisa demonstrar atividade profissional e renda.</p>\r\n<p>A aceitação varia conforme a instituição. Algumas exigem documentos complementares ou um formato específico.</p>\r\n\r\n<h2>1. Identifique a atividade profissional</h2>\r\n<p>Descreva a ocupação de forma objetiva: por exemplo, “prestação de serviços de design”, “atividade comercial” ou “trabalho autônomo na área de manutenção”. Evite títulos que não representem a atividade real.</p>\r\n<p>Se houver empresa ou nome empresarial relacionado, informe apenas se isso for pertinente à finalidade.</p>\r\n\r\n<h2>2. Informe desde quando a atividade é exercida</h2>\r\n<p>Datas ajudam o destinatário a entender a estabilidade da atividade. Use uma data de início ou um período aproximado apenas quando houver base para isso.</p>\r\n<p>Não invente antiguidade para tornar o documento mais convincente. Uma declaração falsa pode gerar consequências jurídicas.</p>\r\n\r\n<h2>3. Defina o período da renda</h2>\r\n<p>Se a renda varia, liste meses ou competências. Em vez de afirmar “renda média de R$ 6.000”, mostre o período usado para calcular a média.</p>\r\n<p>Uma renda sazonal pode exigir um período maior para representar melhor a realidade.</p>\r\n\r\n<h2>4. Separe faturamento e rendimento pessoal</h2>\r\n<p>Para autônomos e empresários, esse ponto é essencial. O total faturado pela atividade pode incluir custos, tributos e valores que não representam renda pessoal disponível.</p>\r\n<p>Se o documento declara faturamento, use a palavra “faturamento”. Se declara retirada ou rendimento pessoal, identifique a natureza correta.</p>\r\n\r\n<h2>5. Como apresentar renda variável</h2>\r\n<p>Imagine valores de R$ 4.500, R$ 6.000, R$ 5.400 e R$ 6.100 em quatro meses. O total é R$ 22.000 e a média simples é R$ 5.500. A declaração pode informar ambos e indicar que a média foi calculada com quatro competências.</p>\r\n<p>Isso é melhor do que selecionar apenas os melhores meses.</p>\r\n\r\n<h2>6. Documentos que podem acompanhar a declaração</h2>\r\n<p>Notas fiscais, recibos, extratos, contratos, informes e registros contábeis podem servir de apoio, dependendo da atividade. O destinatário é quem define quais documentos considera suficientes.</p>\r\n<p>Proteja dados pessoais e envie apenas o necessário para a finalidade.</p>\r\n\r\n<h2>7. Responsabilidade pelo conteúdo</h2>\r\n<p>O gerador não verifica automaticamente se uma pessoa realmente exerce determinada atividade ou recebe o valor declarado. Quem assina continua responsável pelas informações prestadas.</p>\r\n<p>Se o documento será usado em processo formal importante, revise o texto antes da assinatura e guarde uma cópia.</p>\r\n\r\n<h2>Checklist da declaração</h2>\r\n<ol>\r\n<li>Nome e identificação necessária.</li>\r\n<li>Descrição da atividade.</li>\r\n<li>Período de exercício.</li>\r\n<li>Período dos rendimentos.</li>\r\n<li>Valores mensais quando disponíveis.</li>\r\n<li>Total e média quando úteis.</li>\r\n<li>Indicação de bruto, líquido ou faturamento.</li>\r\n<li>Finalidade, se necessária.</li>\r\n<li>Data e assinatura.</li>\r\n<li>Documentos de apoio exigidos pelo destinatário.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Gere a declaração no Prazzu Tools</h2><p>O gerador organiza atividade, período e renda em um documento simples para revisão antes da assinatura.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/declaracao-trabalho-renda\">Abrir Declaração de Trabalho e Renda</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> para um documento focado apenas nos valores, utilize a <a href=\"/ferramentas/declaracao-rendimentos\">Declaração de Rendimentos</a>. Para registrar um pagamento específico, consulte o <a href=\"/ferramentas/emissor-de-recibos\">Emissor de Recibos</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>A declaração prova vínculo empregatício?</h3><p>Não por si só. Ela registra uma informação declarada e não substitui documentos trabalhistas ou análise jurídica da relação.</p>\r\n<h3>Autônomo pode fazer declaração de trabalho e renda?</h3><p>Sim, desde que as informações sejam verdadeiras e compatíveis com a atividade exercida e com os documentos que possam ser exigidos.</p>\r\n<h3>Precisa reconhecer firma?</h3><p>Depende da instituição e da finalidade. Confirme a exigência do destinatário antes de assumir que é necessário.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Uma <strong>declaração de trabalho e renda</strong> funciona melhor quando descreve fatos verificáveis: atividade, período e valores. Evite linguagem genérica ou exagerada e deixe claro o critério usado para chegar à renda declarada.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/declaracao-trabalho-renda-como-fazer.png', 'Declaração de trabalho e renda com atividade, período, remuneração e assinatura', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:28:19', 'declaração de trabalho e renda', '[\"declaração de trabalho\", \"declaração de renda\", \"comprovante de atividade\", \"trabalhador autônomo\", \"declaração profissional\", \"comprovante de renda autônomo\"]', 'Declaração de trabalho e renda: como fazer', 'Aprenda como fazer declaração de trabalho e renda com atividade, período, remuneração, identificação e finalidade, deixando os valores claros e verificáveis.', NULL, 'blog/covers/declaracao-trabalho-renda-como-fazer.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:28:19'),
(28, NULL, 'Contrato de prestação de serviços: cláusulas essenciais e cuidados', 'como-fazer-contrato-gerador-contratos', 'Veja como estruturar contrato de prestação de serviços com escopo, preço, prazo, entregas, responsabilidades, rescisão, confidencialidade e proteção de dados.', '<p>Um <strong>contrato de prestação de serviços</strong> útil reduz ambiguidades antes do trabalho começar. O documento deve explicar o que será feito, quanto será pago, quando haverá entrega, quais responsabilidades pertencem a cada parte e como a relação pode terminar.</p>\r\n<div class=\"alert alert-warning\"><strong>Importante:</strong> um gerador produz uma minuta inicial. Contratos com alto valor, dados sensíveis, propriedade intelectual relevante, exclusividade, risco regulatório ou relação trabalhista potencial merecem revisão jurídica específica.</div>\r\n\r\n<h2>1. Identifique corretamente as partes</h2>\r\n<p>Use os dados necessários para identificar contratante e contratado. Em pessoa jurídica, confira razão social, CNPJ e representante. Em pessoa física, use os dados pertinentes à finalidade.</p>\r\n<p>Evite incluir dados pessoais excessivos apenas porque um modelo antigo fazia isso. O contrato deve coletar o necessário para identificação e execução.</p>\r\n\r\n<h2>2. Descreva o objeto sem termos vagos</h2>\r\n<p>Frases como “prestação de serviços gerais” deixam espaço para interpretações diferentes. Informe atividade, entregáveis, limites e, quando possível, o que não está incluído.</p>\r\n<p>Se houver proposta comercial ou anexo técnico, o contrato pode fazer referência a esses documentos, desde que a versão esteja claramente identificada.</p>\r\n\r\n<h2>3. Defina escopo e critérios de aceite</h2>\r\n<p>Para projetos, registre quais entregas encerram cada etapa e quem aprova. Se houver revisões incluídas, determine quantidade ou critério para evitar retrabalho ilimitado.</p>\r\n<p>Uma cláusula de aceite pode definir prazo para manifestação e procedimento de correção de defeitos ou divergências.</p>\r\n\r\n<h2>4. Preço, vencimento e reajuste</h2>\r\n<p>Informe valor, periodicidade, forma de pagamento e vencimento. Se houver reajuste, explique índice, periodicidade e data-base. Custos reembolsáveis devem ter regra própria.</p>\r\n<p>Também vale definir o que ocorre em caso de atraso, respeitando os limites legais aplicáveis.</p>\r\n\r\n<h2>5. Prazo e cronograma</h2>\r\n<p>Diferencie prazo do contrato de prazo das entregas. Um contrato anual pode conter entregas mensais; um projeto de três meses pode depender de aprovações do cliente.</p>\r\n<p>Quando uma etapa depende de informações do contratante, deixe claro que atrasos no fornecimento podem afetar o cronograma.</p>\r\n\r\n<h2>6. Responsabilidades de cada parte</h2>\r\n<p>Liste obrigações do prestador e do cliente. Isso pode incluir fornecer acesso, aprovar materiais, disponibilizar dados, manter confidencialidade, realizar backups ou cumprir procedimentos internos.</p>\r\n<p>Quanto mais operacional for a obrigação, melhor descrevê-la de forma objetiva.</p>\r\n\r\n<h2>7. Rescisão e encerramento</h2>\r\n<p>Defina hipóteses de rescisão, aviso prévio contratual quando houver, pagamentos pendentes, entrega de materiais e tratamento de atividades em andamento.</p>\r\n<p>Evite cláusulas que apenas dizem “qualquer parte pode rescindir” sem explicar as consequências práticas.</p>\r\n\r\n<h2>8. Confidencialidade e propriedade intelectual</h2>\r\n<p>Se o serviço envolve informações confidenciais, defina o que deve ser protegido, por quanto tempo e quais exceções existem. Para criações, software, design, conteúdo ou documentação técnica, esclareça titularidade e licenças de uso.</p>\r\n\r\n<h2>9. Proteção de dados pessoais</h2>\r\n<p>Quando o serviço envolve dados pessoais, o contrato deve refletir os papéis das partes e as instruções de tratamento. A LGPD distingue controlador e operador, e contratos podem ser importantes para formalizar responsabilidades.</p>\r\n<p>Cláusulas úteis podem abordar finalidade, medidas de segurança, suboperadores, incidentes, devolução ou descarte de dados e limites de uso. O nível de detalhe deve acompanhar o risco e o tipo de tratamento.</p>\r\n\r\n<h2>10. Cuidado com risco de vínculo</h2>\r\n<p>Um contrato chamado “prestação de serviços” não elimina automaticamente a possibilidade de reconhecimento de vínculo de emprego se os fatos da relação apontarem para outra natureza. A realidade da execução importa.</p>\r\n<p>Não use cláusulas artificiais apenas para tentar descaracterizar uma relação que, na prática, funciona como emprego.</p>\r\n\r\n<h2>Checklist antes de assinar</h2>\r\n<ol>\r\n<li>Confirme os dados das partes.</li>\r\n<li>Descreva o objeto.</li>\r\n<li>Delimite o escopo.</li>\r\n<li>Defina entregas e aceite.</li>\r\n<li>Informe preço e vencimento.</li>\r\n<li>Defina prazo e reajuste.</li>\r\n<li>Liste responsabilidades.</li>\r\n<li>Trate rescisão e pendências.</li>\r\n<li>Revise confidencialidade e propriedade intelectual.</li>\r\n<li>Inclua proteção de dados quando aplicável.</li>\r\n<li>Revise anexos e versões.</li>\r\n<li>Busque revisão jurídica quando o risco justificar.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Gere uma minuta no Prazzu Tools</h2><p>O Gerador de Contratos ajuda a estruturar uma primeira minuta com dados das partes e cláusulas principais, para posterior revisão conforme o serviço e o risco.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/gerador-de-contratos\">Abrir Gerador de Contratos</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> após o pagamento do serviço, utilize o <a href=\"/ferramentas/emissor-de-recibos\">Emissor de Recibos</a> quando esse documento for adequado à operação.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Um contrato gerado automaticamente tem validade?</h3><p>A validade depende do conteúdo, das partes, da manifestação de vontade e dos requisitos aplicáveis ao negócio. Um gerador ajuda a estruturar o documento, mas não garante adequação jurídica ao caso.</p>\r\n<h3>Preciso incluir cláusula de LGPD em todo contrato?</h3><p>O nível de tratamento depende de haver dados pessoais e do papel de cada parte. Se o serviço envolve tratamento relevante, responsabilidades e instruções devem ser avaliadas.</p>\r\n<h3>Contrato PJ impede vínculo trabalhista?</h3><p>Não automaticamente. A natureza da relação também é avaliada pelos fatos concretos.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Um bom <strong>contrato de prestação de serviços</strong> transforma expectativas em regras verificáveis. Escopo, preço, prazo, responsabilidades, rescisão e dados pessoais precisam estar claros antes da execução, reduzindo conflito e facilitando o encerramento da relação.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/como-fazer-contrato-gerador-contratos.png', 'Contrato de prestação de serviços com escopo, preço, prazo e cláusulas essenciais', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:28:19', 'contrato de prestação de serviços', '[\"como fazer contrato de prestação de serviços\", \"cláusulas de contrato\", \"contrato prestador de serviços\", \"escopo de serviços\", \"rescisão contratual\", \"LGPD contrato\"]', 'Contrato de prestação de serviços: cláusulas essenciais', 'Veja como estruturar contrato de prestação de serviços com escopo, preço, prazo, entregas, responsabilidades, rescisão, confidencialidade e LGPD.', NULL, 'blog/covers/como-fazer-contrato-gerador-contratos.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:28:19'),
(29, NULL, 'Recibo de pagamento: como fazer, preencher e comprovar', 'como-fazer-recibo-de-pagamento', 'Aprenda como fazer recibo de pagamento com pagador, recebedor, valor, data, finalidade, forma de pagamento e quitação correta, inclusive em pagamentos parciais.', '<p>Um <strong>recibo de pagamento</strong> registra que uma pessoa ou empresa recebeu determinado valor, em uma data e por uma finalidade. Para ser realmente útil, ele precisa permitir que alguém entenda meses depois quem pagou, quem recebeu, quanto foi pago e qual obrigação aquele pagamento quitou.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> identifique pagador e recebedor, descreva a finalidade, informe valor e data, registre a forma de pagamento e deixe explícito se a quitação é total ou apenas parcial.</div>\r\n\r\n<h2>O que é um recibo de pagamento?</h2>\r\n<p>É um documento usado para reconhecer o recebimento de um valor. Ele pode acompanhar pagamentos de serviços, parcelas, aluguéis, reembolsos e outras obrigações, desde que a descrição corresponda ao que realmente aconteceu.</p>\r\n<p>O recibo não deve ser tratado como um texto genérico. Quanto melhor ele conectar pagamento e obrigação, maior sua utilidade para conciliação, cobrança e comprovação.</p>\r\n\r\n<h2>1. Identifique quem pagou e quem recebeu</h2>\r\n<p>Informe nomes ou razão social e os dados necessários à finalidade do documento. Em operações empresariais, pode ser útil registrar CNPJ; em operações com pessoa física, use apenas os dados necessários.</p>\r\n<p>O recebedor indicado deve ser coerente com quem reconhece o recebimento. Se o pagamento foi feito a um representante, registre essa situação quando for relevante.</p>\r\n\r\n<h2>2. Descreva exatamente o motivo</h2>\r\n<p>Evite descrições como “serviços” ou “pagamento diversos”. Prefira algo como “pagamento da parcela 2 de 4 referente ao serviço de manutenção previsto no contrato de 10/06/2026”.</p>\r\n<p>Essa descrição conecta recibo, contrato, cobrança e extrato e reduz o risco de a mesma obrigação ser cobrada novamente por falta de identificação.</p>\r\n\r\n<h2>3. Informe valor em números e por extenso</h2>\r\n<p>O valor numérico deve ser claro e usar a moeda correspondente. Escrever também por extenso ajuda na leitura e reduz ambiguidades em documentos impressos.</p>\r\n<p>Se o pagamento inclui juros, multa, desconto ou abatimento, considere discriminar esses componentes para que o total possa ser reproduzido.</p>\r\n\r\n<h2>4. Data do recebimento</h2>\r\n<p>Use a data em que o valor efetivamente foi recebido, e não apenas a data em que o recibo foi digitado. Se o documento for emitido posteriormente, essa diferença pode ser registrada quando necessário.</p>\r\n<p>A data é especialmente importante em pagamentos parcelados e em conciliações com extrato bancário.</p>\r\n\r\n<h2>5. Forma de pagamento</h2>\r\n<p>PIX, transferência, boleto, cartão, cheque ou dinheiro podem ser informados. Quando houver identificador de transação, número de parcela ou referência bancária, registre-o em observação ou campo próprio.</p>\r\n<p>Isso não substitui o comprovante bancário, mas facilita localizar a movimentação correspondente.</p>\r\n\r\n<h2>6. Quitação total ou parcial</h2>\r\n<p>Um dos pontos mais importantes é a extensão da quitação. Se foram pagos R$ 1.500 de uma dívida de R$ 4.500, o recibo deve indicar que se trata de pagamento parcial ou de uma parcela específica.</p>\r\n<p>Não use expressões de quitação integral quando ainda existe saldo pendente. Se a parcela encerra totalmente a obrigação, isso também pode ser declarado de forma objetiva.</p>\r\n\r\n<h2>7. Recibo não é automaticamente nota fiscal</h2>\r\n<p>Recibo e documento fiscal têm finalidades diferentes. A emissão de um recibo não elimina obrigações de nota fiscal, retenções ou registros tributários que possam existir na operação.</p>\r\n<p>Para atividades sujeitas à emissão de documento fiscal, mantenha cada documento cumprindo sua função.</p>\r\n\r\n<h2>8. Assinatura e validação</h2>\r\n<p>A assinatura do recebedor, física ou eletrônica conforme o contexto, reforça a identificação de quem reconheceu o pagamento. Em processos digitais, outros mecanismos de autenticação também podem ser usados.</p>\r\n<p>Guarde a versão final juntamente com o comprovante financeiro e, quando houver, o contrato ou cobrança que originou o pagamento.</p>\r\n\r\n<h2>Exemplo de descrição útil</h2>\r\n<p>Em vez de “Recebi R$ 1.500 por serviços”, uma descrição mais rastreável seria: “Recebi R$ 1.500,00, via PIX, referente à primeira parcela do serviço de desenvolvimento do site contratado em 10/06/2026”.</p>\r\n<p>Se houver saldo restante, acrescente que o pagamento é parcial e identifique as parcelas seguintes.</p>\r\n\r\n<h2>Checklist antes de emitir</h2>\r\n<ol>\r\n<li>Confirme o nome do pagador.</li>\r\n<li>Confirme o nome do recebedor.</li>\r\n<li>Informe o valor correto.</li>\r\n<li>Registre a data do recebimento.</li>\r\n<li>Descreva a finalidade.</li>\r\n<li>Informe a forma de pagamento.</li>\r\n<li>Identifique parcela ou referência.</li>\r\n<li>Defina se a quitação é parcial ou total.</li>\r\n<li>Revise assinatura ou validação.</li>\r\n<li>Guarde os documentos relacionados.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Emita um recibo no Prazzu Tools</h2><p>O Emissor de Recibos organiza pagador, recebedor, valor, finalidade e demais informações para gerar um documento mais rastreável.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/emissor-de-recibos\">Abrir Emissor de Recibos</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> relacione o pagamento ao <a href=\"/ferramentas/gerador-de-contratos\">contrato de prestação de serviços</a> e, quando necessário, use a <a href=\"/ferramentas/declaracao-rendimentos\">Declaração de Rendimentos</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Recibo de pagamento substitui nota fiscal?</h3><p>Não automaticamente. A operação pode exigir documento fiscal e outras obrigações mesmo que exista recibo.</p>\r\n<h3>Posso emitir recibo de pagamento parcial?</h3><p>Sim. Identifique a parcela, o valor pago e deixe claro que ainda existe saldo quando for o caso.</p>\r\n<h3>Recibo precisa ter assinatura?</h3><p>A forma de validação depende do contexto, mas identificar e validar quem reconhece o recebimento aumenta a utilidade do documento.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Um bom <strong>recibo de pagamento</strong> funciona como uma trilha de auditoria curta: partes, valor, data, motivo e extensão da quitação. Quanto mais específica for a ligação entre o recibo e a obrigação paga, mais fácil será comprovar e conciliar a operação depois.</p>', 1, 'Gestão Contábil', 'contabilidade', 'blog/covers/como-fazer-recibo-de-pagamento.png', 'Recibo de pagamento com pagador, recebedor, valor, data e finalidade', 'published', 0, '2026-07-27 13:45:49', '2026-07-27 20:31:19', 'recibo de pagamento', '[\"como fazer recibo de pagamento\", \"modelo de recibo\", \"recibo de pagamento simples\", \"recibo valor por extenso\", \"recibo de prestação de serviços\", \"emissor de recibos\"]', 'Recibo de pagamento: como fazer e preencher', 'Aprenda como fazer recibo de pagamento com pagador, recebedor, valor, data, finalidade, forma de pagamento e cuidados com quitação parcial ou total.', NULL, 'blog/covers/como-fazer-recibo-de-pagamento.png', 1, '2026-07-27 13:45:49', '2026-07-27 20:31:19'),
(30, NULL, 'Salário líquido 2026: como calcular INSS e IRRF passo a passo', 'salario-liquido-2026-como-calcular-inss-irrf', 'Aprenda a calcular salário líquido 2026 com INSS progressivo, IRRF, desconto simplificado, redução mensal, dependentes e demais descontos da folha.', '<p>Calcular <strong>salário líquido 2026</strong> exige separar remuneração, INSS, IRRF e descontos particulares. Não existe uma porcentagem única capaz de converter salário bruto em líquido, porque as contribuições e o imposto usam regras próprias e a folha pode conter benefícios, dependentes, pensão, faltas, adiantamentos e verbas variáveis.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> some as verbas do mês, determine o salário de contribuição, calcule o INSS por faixas, encontre a base do IRRF, compare as deduções permitidas e aplique a redução mensal de 2026 quando cabível. Depois desconte os demais itens da folha.</div>\r\n\r\n<h2>O que mudou no cálculo em 2026?</h2>\r\n<p>Para a competência janeiro de 2026 em diante, o INSS do empregado usa novas faixas previdenciárias. No imposto de renda, a tabela progressiva mensal permanece acompanhada de uma nova redução do imposto instituída pela Lei nº 15.270/2025.</p>\r\n<p>A redução pode zerar o IRRF para rendimentos tributáveis sujeitos à incidência mensal de até R$ 5.000,00 e diminui gradualmente entre R$ 5.000,01 e R$ 7.350,00.</p>\r\n\r\n<h2>Tabela do INSS 2026 para empregado</h2>\r\n<ul>\r\n<li>Até R$ 1.621,00: 7,5%.</li>\r\n<li>De R$ 1.621,01 a R$ 2.902,84: 9%.</li>\r\n<li>De R$ 2.902,85 a R$ 4.354,27: 12%.</li>\r\n<li>De R$ 4.354,28 a R$ 8.475,55: 14%.</li>\r\n</ul>\r\n<p>As alíquotas são progressivas. Isso significa que cada parte do salário de contribuição é calculada na faixa correspondente. Não multiplique todo o salário por 14% apenas porque a remuneração alcançou a última faixa.</p>\r\n\r\n<h2>Exemplo do INSS progressivo</h2>\r\n<p>Para conferir o cálculo, divida o salário de contribuição em parcelas. A primeira parcela é tributada a 7,5%, a seguinte a 9% e assim por diante até a faixa alcançada.</p>\r\n<p>Esse método também explica por que dois salários próximos não apresentam uma mudança abrupta de contribuição quando atravessam uma faixa.</p>\r\n\r\n<h2>Tabela mensal do IRRF em 2026</h2>\r\n<ul>\r\n<li>Base até R$ 2.428,80: sem imposto pela tabela progressiva.</li>\r\n<li>R$ 2.428,81 a R$ 2.826,65: 7,5%, dedução de R$ 182,16.</li>\r\n<li>R$ 2.826,66 a R$ 3.751,05: 15%, dedução de R$ 394,16.</li>\r\n<li>R$ 3.751,06 a R$ 4.664,68: 22,5%, dedução de R$ 675,49.</li>\r\n<li>Acima de R$ 4.664,68: 27,5%, dedução de R$ 908,73.</li>\r\n</ul>\r\n<p>A dedução mensal por dependente é R$ 189,59 e o limite mensal do desconto simplificado é R$ 607,20 em 2026.</p>\r\n\r\n<h2>Deduções legais ou desconto simplificado?</h2>\r\n<p>Na apuração mensal do IRRF, as deduções legais permitidas podem incluir contribuição previdenciária, dependentes e pensão alimentícia nas hipóteses legais. O desconto simplificado mensal pode substituir essas deduções quando aplicável.</p>\r\n<p>Compare as alternativas de acordo com a regra vigente e use a que for cabível. Não some o desconto simplificado às deduções legais como se fossem cumulativos.</p>\r\n\r\n<h2>Como funciona a redução do IRRF de 2026</h2>\r\n<p>Depois de calcular o imposto pela tabela progressiva, verifique a redução. Para rendimentos tributáveis de até R$ 5.000,00, a redução é limitada ao imposto apurado e pode levá-lo a zero. De R$ 5.000,01 a R$ 7.350,00, a redução é decrescente.</p>\r\n<p>Acima de R$ 7.350,00 de rendimento tributável sujeito à incidência mensal, essa redução específica deixa de ser aplicada.</p>\r\n\r\n<h2>Exemplo oficial: rendimento de R$ 5.000</h2>\r\n<p>Em exemplo publicado pela Receita Federal para janeiro de 2026, um salário bruto de R$ 5.000,00, sem outras deduções além da contribuição previdenciária, utiliza o desconto simplificado de R$ 607,20 por ser mais vantajoso naquele cenário.</p>\r\n<p>A base do IRRF fica em R$ 4.392,80. A tabela progressiva produz imposto de R$ 312,89 e a redução de 2026 zera esse IRRF. O salário líquido ainda sofre o desconto do INSS e qualquer outro desconto existente na folha.</p>\r\n\r\n<h2>Passo a passo do salário bruto ao líquido</h2>\r\n<ol>\r\n<li>Confirme a competência.</li>\r\n<li>Some salário e demais proventos.</li>\r\n<li>Separe as verbas conforme incidência.</li>\r\n<li>Calcule o INSS progressivamente.</li>\r\n<li>Determine as deduções do IRRF.</li>\r\n<li>Compare deduções legais e desconto simplificado quando aplicável.</li>\r\n<li>Calcule o IRRF pela tabela.</li>\r\n<li>Aplique a redução mensal de 2026.</li>\r\n<li>Subtraia benefícios, pensão, adiantamentos e demais descontos.</li>\r\n<li>Compare o líquido com o holerite.</li>\r\n</ol>\r\n\r\n<h2>O que pode alterar o líquido?</h2>\r\n<p>Horas extras, comissões, adicional noturno, dependentes, pensão alimentícia, vale-transporte, coparticipação em plano de saúde, faltas e adiantamentos podem alterar o resultado.</p>\r\n<p>Também é necessário cuidado com vínculos concomitantes. O INSS orienta que remunerações de mais de um vínculo sejam consideradas em conjunto para aplicação da contribuição mensal, respeitado o teto.</p>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule seu salário líquido no Prazzu Tools</h2><p>A Calculadora de Salário Líquido organiza INSS, IRRF e demais descontos para mostrar como o valor bruto chega ao líquido.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-salario-liquido\">Abrir Calculadora de Salário Líquido</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> depois do cálculo, confira <a href=\"/blog/salario-bruto-x-liquido-diferenca\">salário bruto x líquido</a> e use o <a href=\"/ferramentas/gerador-holerite\">Gerador de Holerite</a> para organizar os proventos e descontos.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Quem ganha R$ 5.000 não paga IRRF em 2026?</h3><p>A redução mensal pode zerar o imposto em rendimentos tributáveis de até R$ 5.000,00, conforme a regra vigente. Outros descontos, como INSS, continuam existindo.</p>\r\n<h3>INSS de 14% significa descontar 14% do salário inteiro?</h3><p>Não. Para empregado, empregado doméstico e trabalhador avulso, o cálculo é progressivo por faixas.</p>\r\n<h3>O desconto simplificado de R$ 607,20 sai do salário?</h3><p>Não. Ele é uma dedução usada para determinar a base do IRRF quando aplicável, e não um desconto isolado no pagamento.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>O <strong>salário líquido 2026</strong> deve ser calculado com a competência correta e uma memória que separe INSS, base do IRRF, deduções, redução do imposto e descontos particulares. Essa estrutura permite conferir o holerite e evita estimativas erradas por percentual único.</p>\r\n\r\n<h2>Fontes oficiais para conferência</h2>\r\n<ul>\r\n<li><a href=\"https://www.gov.br/receitafederal/pt-br/assuntos/meu-imposto-de-renda/tabelas/2026\" rel=\"nofollow noopener\" target=\"_blank\">Receita Federal — Tributação de 2026</a></li>\r\n<li><a href=\"https://www.gov.br/receitafederal/pt-br/assuntos/meu-imposto-de-renda/tabelas/exemplos-de-aplicacao-da-lei-15-270-2025\" rel=\"nofollow noopener\" target=\"_blank\">Receita Federal — Exemplos da Lei 15.270/2025</a></li>\r\n<li><a href=\"https://www.gov.br/inss/pt-br/direitos-e-deveres/inscricao-e-contribuicao/tabela-de-contribuicao-mensal\" rel=\"nofollow noopener\" target=\"_blank\">INSS — Tabela de contribuição mensal</a></li>\r\n</ul>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/salario-liquido-2026.png', 'Cálculo de salário líquido 2026 com INSS progressivo e IRRF', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:31:19', 'salário líquido 2026', '[\"calculadora salário líquido\", \"INSS 2026\", \"IRRF 2026\", \"salário bruto para líquido\", \"desconto simplificado IRRF\", \"quanto vou receber líquido\"]', 'Salário líquido 2026: calcule INSS e IRRF', 'Aprenda a calcular salário líquido 2026 com INSS progressivo, IRRF, desconto simplificado, redução mensal, dependentes e outros descontos da folha.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:31:19'),
(31, NULL, 'Salário bruto x líquido: diferença, descontos e comparação', 'salario-bruto-x-liquido-diferenca', 'Entenda salário bruto x líquido, quais descontos reduzem o pagamento, por que o FGTS não sai do salário e como comparar propostas sem usar percentual fixo.', '<p><strong>Salário bruto x líquido</strong> é uma comparação essencial para entender um holerite ou avaliar uma proposta de emprego. O bruto representa a remuneração antes dos descontos; o líquido é o valor final pago depois das retenções e demais descontos aplicáveis naquele mês.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> não converta bruto em líquido com uma porcentagem fixa. Primeiro identifique os proventos, depois calcule INSS e IRRF conforme a competência e, por fim, desconte benefícios, pensão, adiantamentos e outros itens individuais.</div>\r\n\r\n<h2>O que é salário bruto?</h2>\r\n<p>O salário bruto do mês pode incluir salário-base e outras verbas, como horas extras, adicional noturno, comissões e gratificações. Dependendo da finalidade da comparação, é importante distinguir salário-base de remuneração bruta total.</p>\r\n<p>Uma proposta de R$ 5.000 de salário-base, por exemplo, não deve ser comparada diretamente com outra que promete R$ 5.000 apenas quando metas e bônus são atingidos.</p>\r\n\r\n<h2>O que é salário líquido?</h2>\r\n<p>É o valor efetivamente pago após os descontos. Entre os mais comuns estão INSS, eventual IRRF, vale-transporte, pensão alimentícia, coparticipações, adiantamentos e faltas.</p>\r\n<p>Como parte desses itens varia de pessoa para pessoa, duas pessoas com o mesmo salário bruto podem receber líquidos diferentes.</p>\r\n\r\n<h2>Por que não existe uma porcentagem fixa?</h2>\r\n<p>O INSS do empregado é progressivo por faixas. O IRRF também possui tabela progressiva, deduções e, em 2026, uma redução mensal específica conforme o rendimento tributável.</p>\r\n<p>Além disso, benefícios e descontos pessoais alteram o pagamento. Por isso, fórmulas como “tire 20% do bruto” servem apenas como palpite e podem errar bastante.</p>\r\n\r\n<h2>FGTS reduz o salário líquido?</h2>\r\n<p>O depósito regular do FGTS é uma obrigação do empregador e não é um desconto retirado diretamente do salário do empregado. Ele faz parte do custo empresarial, mas não deve aparecer como uma retenção que reduz o líquido normal.</p>\r\n<p>Essa distinção é importante ao comparar salário líquido com custo total da contratação.</p>\r\n\r\n<h2>Salário-base, bruto e custo da empresa</h2>\r\n<p>São três conceitos diferentes. Salário-base é uma parcela contratual. O bruto reúne os proventos da folha antes dos descontos. O custo empresarial pode incluir FGTS, benefícios, contribuições patronais aplicáveis e provisões.</p>\r\n<p>Portanto, não conclua que o que “custa para a empresa” deveria ser recebido pelo empregado em dinheiro.</p>\r\n\r\n<h2>Como comparar duas propostas de emprego</h2>\r\n<ol>\r\n<li>Compare salário-base com salário-base.</li>\r\n<li>Separe bônus garantido de bônus condicionado a meta.</li>\r\n<li>Liste benefícios pagos pela empresa.</li>\r\n<li>Liste coparticipações e descontos.</li>\r\n<li>Simule INSS e IRRF pela mesma competência.</li>\r\n<li>Considere vale-transporte quando utilizado.</li>\r\n<li>Compare remuneração variável em cenários conservador e provável.</li>\r\n<li>Considere periodicidade de bônus e benefícios anuais.</li>\r\n</ol>\r\n\r\n<h2>Exemplo de comparação</h2>\r\n<p>Imagine uma proposta A com salário-base de R$ 5.000 e benefício sem coparticipação e uma proposta B com salário-base de R$ 5.300, mas com desconto mensal relevante em plano de saúde. Olhar apenas os R$ 300 de diferença não mostra qual líquido será maior.</p>\r\n<p>Faça duas simulações com os mesmos dados pessoais e depois compare também benefícios que não aparecem no líquido.</p>\r\n\r\n<h2>Por que o líquido muda de um mês para outro?</h2>\r\n<p>Horas extras, comissão, faltas, férias, adiantamento, bônus e alterações em benefícios podem modificar proventos, bases e descontos. Até mesmo uma pequena variação de remuneração pode alterar a faixa efetiva do IRRF.</p>\r\n<p>Ao investigar uma diferença, compare os holerites rubrica por rubrica em vez de olhar apenas a última linha.</p>\r\n\r\n<h2>Como ler o holerite</h2>\r\n<p>Comece pelos proventos, confira o total bruto e depois revise cada desconto. Em seguida, observe as bases informativas de INSS, FGTS e IRRF.</p>\r\n<p>O líquido deve ser compatível com o total de proventos menos os descontos registrados.</p>\r\n\r\n<h2>Erros comuns ao comparar bruto e líquido</h2>\r\n<ul>\r\n<li>tratar FGTS como desconto do trabalhador;</li>\r\n<li>aplicar a maior alíquota de INSS ao salário inteiro;</li>\r\n<li>aplicar 27,5% de IRRF diretamente sobre o bruto;</li>\r\n<li>ignorar benefícios com coparticipação;</li>\r\n<li>comparar bônus incerto com salário fixo;</li>\r\n<li>usar tabelas de outra competência.</li>\r\n</ul>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Converta salário bruto em líquido</h2><p>Use a Calculadora de Salário Líquido do Prazzu Tools para separar INSS, IRRF e demais descontos e comparar cenários com a mesma base.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-salario-liquido\">Abrir Calculadora de Salário Líquido</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> consulte o guia detalhado de <a href=\"/blog/salario-liquido-2026-como-calcular-inss-irrf\">salário líquido 2026</a> e aprenda <a href=\"/blog/como-preencher-holerite-proventos-descontos\">como preencher um holerite</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Salário líquido é sempre menor que o salário-base?</h3><p>Não necessariamente, porque o mês pode ter horas extras, comissões e outros proventos. O líquido é comparado ao total de proventos e descontos daquela competência.</p>\r\n<h3>FGTS é descontado do salário?</h3><p>O depósito regular do FGTS é feito pelo empregador e não reduz diretamente o líquido do empregado.</p>\r\n<h3>Posso estimar o líquido tirando 20% do bruto?</h3><p>Isso não produz uma estimativa confiável para todos. Use as tabelas da competência e os descontos reais.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>A comparação <strong>salário bruto x líquido</strong> fica simples quando cada camada é separada: salário-base, outros proventos, INSS, IRRF e descontos individuais. Essa leitura é mais útil para conferir a folha e comparar propostas do que qualquer percentual genérico.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/salario-liquido-2026.png', 'Comparação entre salário bruto, descontos e salário líquido', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:31:19', 'salário bruto x líquido', '[\"salário bruto e líquido\", \"salário bruto para líquido\", \"descontos no salário\", \"quanto cai na conta\", \"calculadora salário líquido\", \"comparar proposta de emprego\"]', 'Salário bruto x líquido: entenda a diferença', 'Entenda salário bruto x líquido, quais descontos reduzem o pagamento, por que o FGTS não sai do salário e como comparar propostas sem percentual fixo.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:31:19');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `vertical_slug`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(32, NULL, 'INSS e IRRF 2026: tabelas, cálculo no salário e redução do imposto', 'inss-irrf-2026-salario-tabelas-descontos', 'Confira INSS e IRRF 2026, veja as faixas progressivas, deduções, desconto simplificado e redução mensal do imposto e aprenda a conferir o cálculo da folha.', '<p><strong>INSS e IRRF 2026</strong> aparecem juntos na folha, mas são cálculos diferentes. O INSS do empregado usa faixas progressivas sobre o salário de contribuição; o IRRF parte de uma base própria, admite deduções e, em 2026, pode receber uma redução mensal instituída pela Lei nº 15.270/2025.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme a competência, classifique as verbas, calcule o INSS por faixas, determine a base do IRRF, compare deduções legais e desconto simplificado quando aplicável, calcule o imposto e só então aplique a redução mensal de 2026.</div>\r\n\r\n<h2>Tabela do INSS 2026</h2>\r\n<p>Para empregado, empregado doméstico e trabalhador avulso, as faixas válidas a partir da competência janeiro de 2026 são:</p>\r\n<ul>\r\n<li>Até R$ 1.621,00: 7,5%.</li>\r\n<li>De R$ 1.621,01 a R$ 2.902,84: 9%.</li>\r\n<li>De R$ 2.902,85 a R$ 4.354,27: 12%.</li>\r\n<li>De R$ 4.354,28 a R$ 8.475,55: 14%.</li>\r\n</ul>\r\n<p>O limite máximo do salário de contribuição em 2026 é R$ 8.475,55 para essa tabela.</p>\r\n\r\n<h2>Por que o INSS é progressivo?</h2>\r\n<p>Cada parcela do salário é alcançada pela alíquota da faixa em que se encontra. Quem chega à faixa de 14% não paga 14% sobre todo o salário de contribuição.</p>\r\n<p>Essa estrutura reduz saltos no desconto quando o salário ultrapassa o limite de uma faixa.</p>\r\n\r\n<h2>Vínculos concomitantes</h2>\r\n<p>O INSS informa que, quando empregado, doméstico ou trabalhador avulso possui mais de um vínculo, as remunerações devem ser consideradas em conjunto para aplicação da contribuição mensal, respeitado o limite máximo.</p>\r\n<p>O trabalhador precisa fornecer as informações necessárias para que a apuração considere corretamente os vínculos.</p>\r\n\r\n<h2>Tabela mensal do IRRF 2026</h2>\r\n<ul>\r\n<li>Até R$ 2.428,80 de base: sem imposto pela tabela.</li>\r\n<li>R$ 2.428,81 a R$ 2.826,65: 7,5% e dedução de R$ 182,16.</li>\r\n<li>R$ 2.826,66 a R$ 3.751,05: 15% e dedução de R$ 394,16.</li>\r\n<li>R$ 3.751,06 a R$ 4.664,68: 22,5% e dedução de R$ 675,49.</li>\r\n<li>Acima de R$ 4.664,68: 27,5% e dedução de R$ 908,73.</li>\r\n</ul>\r\n<p>A dedução mensal por dependente é R$ 189,59 e o limite mensal do desconto simplificado é R$ 607,20.</p>\r\n\r\n<h2>Base do IRRF não é simplesmente o salário bruto</h2>\r\n<p>Antes de aplicar a tabela, determine as deduções permitidas. Dependendo do caso, podem existir contribuição previdenciária, dependentes e pensão alimentícia. O desconto simplificado mensal pode substituir as deduções legais quando for aplicável.</p>\r\n<p>Não use simultaneamente desconto simplificado e todas as deduções legais como se fossem cumulativos.</p>\r\n\r\n<h2>Redução mensal do IRRF em 2026</h2>\r\n<p>A partir de janeiro de 2026, rendimentos tributáveis sujeitos à incidência mensal de até R$ 5.000,00 podem receber redução suficiente para zerar o imposto calculado.</p>\r\n<p>Entre R$ 5.000,01 e R$ 7.350,00, a redução é calculada pela fórmula prevista na legislação e diminui linearmente. A partir de R$ 7.350,00, a redução chega a zero.</p>\r\n\r\n<h2>A redução usa rendimento ou base do IRRF?</h2>\r\n<p>Esse detalhe é importante. Nos exemplos oficiais da Receita, a tabela de redução considera os rendimentos tributáveis sujeitos à incidência mensal, e não simplesmente a base já reduzida pelas deduções.</p>\r\n<p>Em um dos exemplos oficiais, salário de R$ 7.607,20 gera base do IRRF de R$ 7.000,00 após desconto simplificado, mas não recebe a redução porque o rendimento usado para verificar a tabela é superior a R$ 7.350,00.</p>\r\n\r\n<h2>Exemplo oficial com R$ 6.000</h2>\r\n<p>Em exemplo da Receita Federal, salário bruto de R$ 6.000,00 e contribuição previdenciária de R$ 649,60 resultam em base de IRRF de R$ 5.350,40 quando as deduções legais são mais vantajosas.</p>\r\n<p>A tabela progressiva gera IRRF de R$ 562,63. A redução calculada para o rendimento de R$ 6.000,00 é R$ 179,75, levando o imposto final do exemplo a R$ 382,88.</p>\r\n\r\n<h2>13º salário exige tratamento separado no INSS</h2>\r\n<p>O INSS informa que a remuneração relativa ao décimo terceiro não deve ser somada à remuneração mensal para enquadramento na tabela de salários de contribuição. A contribuição sobre o 13º é tratada separadamente.</p>\r\n<p>Isso é mais um motivo para não reaproveitar a memória do salário mensal sem verificar a natureza da verba.</p>\r\n\r\n<h2>Checklist para conferir INSS e IRRF</h2>\r\n<ol>\r\n<li>Confirme a competência.</li>\r\n<li>Classifique as verbas da folha.</li>\r\n<li>Determine o salário de contribuição.</li>\r\n<li>Calcule o INSS progressivamente.</li>\r\n<li>Identifique as deduções do IRRF.</li>\r\n<li>Compare deduções legais e simplificado.</li>\r\n<li>Aplique a tabela progressiva do IRRF.</li>\r\n<li>Calcule a redução de 2026 quando cabível.</li>\r\n<li>Revise vínculos concomitantes.</li>\r\n<li>Compare com a memória do sistema.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule INSS, IRRF e salário líquido</h2><p>A Calculadora de Salário Líquido do Prazzu Tools organiza as etapas para facilitar a conferência dos descontos da folha.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-salario-liquido\">Abrir Calculadora de Salário Líquido</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> acompanhe o passo a passo em <a href=\"/blog/salario-liquido-2026-como-calcular-inss-irrf\">salário líquido 2026</a> e consulte <a href=\"/blog/salario-bruto-x-liquido-diferenca\">salário bruto x líquido</a> para interpretar o resultado.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>INSS e IRRF usam a mesma base?</h3><p>Não. São cálculos distintos e cada um possui regras próprias de incidência e dedução.</p>\r\n<h3>A alíquota de 27,5% incide diretamente sobre todo o salário?</h3><p>Não. A tabela é aplicada à base do IRRF e possui parcela a deduzir; em 2026 ainda pode existir a redução mensal posterior.</p>\r\n<h3>Quem ganha até R$ 5.000 fica sem qualquer desconto?</h3><p>Não. A redução trata do IRRF. INSS e outros descontos da folha podem continuar existindo.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>Conferir <strong>INSS e IRRF 2026</strong> exige manter os dois cálculos separados e versionados pela competência. A progressividade previdenciária, as deduções do imposto e a nova redução mensal precisam aparecer na memória para que o resultado possa ser explicado e auditado.</p>\r\n\r\n<h2>Fontes oficiais para conferência</h2>\r\n<ul>\r\n<li><a href=\"https://www.gov.br/receitafederal/pt-br/assuntos/meu-imposto-de-renda/tabelas/2026\" rel=\"nofollow noopener\" target=\"_blank\">Receita Federal — Tributação de 2026</a></li>\r\n<li><a href=\"https://www.gov.br/receitafederal/pt-br/assuntos/meu-imposto-de-renda/tabelas/exemplos-de-aplicacao-da-lei-15-270-2025\" rel=\"nofollow noopener\" target=\"_blank\">Receita Federal — Exemplos da Lei 15.270/2025</a></li>\r\n<li><a href=\"https://www.gov.br/inss/pt-br/direitos-e-deveres/inscricao-e-contribuicao/tabela-de-contribuicao-mensal\" rel=\"nofollow noopener\" target=\"_blank\">INSS — Tabela de contribuição mensal</a></li>\r\n</ul>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/salario-liquido-2026.png', 'Tabelas de INSS e IRRF 2026 aplicadas ao cálculo do salário', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:31:19', 'INSS e IRRF 2026', '[\"tabela INSS 2026\", \"tabela IRRF 2026\", \"desconto INSS salário\", \"IRRF salário 2026\", \"redução IRRF 2026\", \"desconto simplificado IRRF\"]', 'INSS e IRRF 2026: tabelas e cálculo no salário', 'Confira INSS e IRRF 2026, faixas progressivas, deduções, desconto simplificado e redução mensal do imposto e veja como conferir o cálculo da folha.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:31:19'),
(33, NULL, 'Como calcular hora extra 50% e 100%: fórmula e exemplos', 'como-calcular-hora-extra-50-100', 'Aprenda como calcular hora extra 50% e 100%, descobrir o valor da hora normal, escolher o divisor correto e conferir DSR, banco de horas e jornada.', '<p><strong>Calcular hora extra</strong> corretamente começa pelo valor da hora normal. Depois, é preciso aplicar o adicional previsto para a situação, conferir a jornada contratual, o divisor usado pela folha, eventual banco de horas e os reflexos que podem surgir quando as horas extras são habituais.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> descubra o valor da hora normal, aplique o adicional correspondente, multiplique pela quantidade de horas extras e mantenha DSR, adicional noturno e outros reflexos em linhas separadas para facilitar a conferência.</div>\r\n\r\n<h2>Qual é o adicional mínimo de hora extra?</h2>\r\n<p>Na regra geral da CLT, a remuneração da hora extra deve ser pelo menos 50% superior à da hora normal. Instrumentos coletivos ou condições específicas podem estabelecer percentuais maiores.</p>\r\n<p>Por isso, 50% é uma referência mínima geral, não uma alíquota obrigatória para qualquer situação de qualquer categoria.</p>\r\n\r\n<h2>1. Encontre o valor da hora normal</h2>\r\n<p>Para empregado mensalista, a conta parte do salário mensal e do divisor compatível com a jornada. O divisor 220 é comum em jornadas de 44 horas semanais, mas não deve ser aplicado a todos os empregados.</p>\r\n<p>Jornadas de 40, 36 ou 30 horas semanais podem exigir outro divisor. Antes da fórmula, confira contrato, jornada e norma coletiva.</p>\r\n\r\n<h2>2. Como calcular hora extra de 50%</h2>\r\n<p>Em um exemplo com salário de R$ 2.200,00 e divisor 220, a hora normal vale R$ 10,00. Com adicional de 50%, cada hora extra vale R$ 15,00.</p>\r\n<p>Se o empregado realizar 10 horas extras nessa condição, o valor-base das horas extras será R$ 150,00, antes de DSR e demais reflexos eventualmente aplicáveis.</p>\r\n\r\n<h2>3. Como calcular hora extra de 100%</h2>\r\n<p>Usando a mesma hora normal de R$ 10,00, uma hora remunerada com adicional de 100% vale R$ 20,00. Porém, não presuma que todo domingo ou feriado gera automaticamente 100%.</p>\r\n<p>Escala, compensação, convenção coletiva, banco de horas e regras específicas do vínculo podem alterar o tratamento.</p>\r\n\r\n<h2>4. Não some apenas o adicional</h2>\r\n<p>Um erro comum é calcular 50% da hora normal e pagar apenas essa parcela. A hora extraordinária remunerada a 50% corresponde à hora normal acrescida de metade do seu valor.</p>\r\n<p>Em uma hora normal de R$ 10,00, o adicional isolado é R$ 5,00, mas a hora extra completa vale R$ 15,00.</p>\r\n\r\n<h2>5. Banco de horas muda o pagamento?</h2>\r\n<p>Quando existe banco de horas ou sistema de compensação válido, parte das horas pode ser compensada em vez de paga imediatamente, conforme as regras aplicáveis.</p>\r\n<p>O controle deve mostrar horas realizadas, horas compensadas, saldo e prazo. Não misture horas já compensadas com horas que efetivamente serão pagas na folha.</p>\r\n\r\n<h2>6. Hora extra noturna exige outra camada</h2>\r\n<p>Quando a hora extraordinária ocorre no período noturno, pode haver adicional noturno e regras de hora reduzida, além do adicional de hora extra. A ordem e a base do cálculo devem seguir a legislação e a norma coletiva aplicável.</p>\r\n<p>Para essa situação, use uma memória separando hora normal, adicional noturno, adicional de hora extra e quantidade de horas.</p>\r\n\r\n<h2>7. DSR sobre horas extras</h2>\r\n<p>Horas extras habituais repercutem no repouso semanal remunerado. O reflexo deve ser calculado separadamente, usando o calendário e o critério aplicável ao período.</p>\r\n<p>Evite usar uma fração fixa sem conferir dias úteis e repousos, especialmente em meses com feriados.</p>\r\n\r\n<h2>Exemplo de memória de cálculo</h2>\r\n<p>Uma memória clara pode ter as colunas: salário-base, jornada, divisor, valor da hora normal, quantidade de horas a 50%, quantidade de horas a 100%, valor de cada modalidade e DSR.</p>\r\n<p>Esse formato permite localizar rapidamente se a divergência veio do divisor, da quantidade de horas ou do percentual.</p>\r\n\r\n<h2>Checklist antes de fechar a folha</h2>\r\n<ol>\r\n<li>Confirme a jornada contratual.</li>\r\n<li>Valide o divisor.</li>\r\n<li>Confira o salário que compõe a hora normal.</li>\r\n<li>Separe horas de 50% e 100%.</li>\r\n<li>Revise banco de horas e compensações.</li>\r\n<li>Identifique horas noturnas.</li>\r\n<li>Calcule o DSR separadamente.</li>\r\n<li>Confira instrumento coletivo.</li>\r\n<li>Compare com o registro de ponto.</li>\r\n<li>Guarde a memória do cálculo.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule hora extra no Prazzu Tools</h2><p>A Calculadora de Hora Extra permite organizar salário, divisor, percentuais e quantidade de horas para conferir o valor antes do fechamento da folha.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-hora-extra\">Abrir Calculadora de Hora Extra</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> entenda o <a href=\"/blog/adicional-noturno-como-calcular-hora-reduzida\">adicional noturno</a> e o <a href=\"/blog/dsr-sobre-horas-extras-como-calcular\">DSR sobre horas extras</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Hora extra é sempre 50%?</h3><p>Não. A regra geral prevê pelo menos 50%, mas normas coletivas e situações específicas podem estabelecer percentual maior.</p>\r\n<h3>Divisor 220 serve para qualquer empregado?</h3><p>Não. O divisor deve ser compatível com a jornada e o regime aplicável.</p>\r\n<h3>Domingo é sempre hora extra de 100%?</h3><p>Não é seguro assumir isso sem analisar escala, compensação, repouso e norma coletiva.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p><strong>Calcular hora extra</strong> corretamente é mais do que multiplicar um percentual. Divisor, jornada, quantidade de horas, adicional noturno, banco de horas e DSR precisam estar explícitos para que o resultado possa ser reproduzido.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/hora-extra-adicional-noturno-dsr.png', 'Cálculo de hora extra 50% e 100% com divisor, jornada e DSR', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:34:17', 'calcular hora extra', '[\"hora extra 50%\", \"hora extra 100%\", \"valor da hora trabalhada\", \"divisor 220\", \"DSR sobre horas extras\", \"banco de horas\"]', 'Como calcular hora extra 50% e 100% passo a passo', 'Aprenda a calcular hora extra de 50% e 100%, encontrar o valor da hora normal, escolher o divisor correto e conferir DSR, jornada e banco de horas.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:34:17'),
(34, NULL, 'Adicional noturno: como calcular hora reduzida e hora extra', 'adicional-noturno-como-calcular-hora-reduzida', 'Aprenda a calcular adicional noturno, entender a hora reduzida de 52min30s, o período urbano das 22h às 5h e como tratar prorrogação e hora extra noturna.', '<p>O <strong>adicional noturno</strong> urbano combina pelo menos três informações: horário trabalhado, percentual adicional e conversão da hora noturna reduzida. Quando existem horas extras ou prorrogação da jornada, a memória de cálculo precisa mostrar essas etapas separadamente.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> para a regra urbana geral da CLT, identifique o trabalho entre 22h e 5h, considere a hora noturna de 52min30s, aplique o adicional mínimo de 20% e trate horas extras e prorrogações conforme a situação concreta.</div>\r\n\r\n<h2>Qual é o horário noturno urbano?</h2>\r\n<p>Para o trabalhador urbano na regra geral do art. 73 da CLT, considera-se noturno o trabalho executado entre 22 horas de um dia e 5 horas do dia seguinte.</p>\r\n<p>Outras categorias, como trabalhadores rurais, possuem regras próprias e não devem ser calculadas automaticamente com o mesmo período.</p>\r\n\r\n<h2>Qual é o percentual do adicional noturno?</h2>\r\n<p>A CLT estabelece adicional de pelo menos 20% sobre a hora diurna para o trabalho noturno urbano. Instrumentos coletivos podem prever percentual superior.</p>\r\n<p>Registre na memória qual percentual foi utilizado e sua origem.</p>\r\n\r\n<h2>O que significa hora reduzida de 52min30s?</h2>\r\n<p>A hora noturna urbana é computada como 52 minutos e 30 segundos. Isso significa que o relógio e a quantidade de horas noturnas legais não são necessariamente iguais.</p>\r\n<p>O período de sete horas de relógio entre 22h e 5h corresponde a oito horas noturnas reduzidas dentro da regra geral.</p>\r\n\r\n<h2>Como converter o período trabalhado</h2>\r\n<p>Em vez de multiplicar diretamente as horas de relógio pelo adicional, converta a duração noturna pelo critério aplicável e só depois calcule a remuneração.</p>\r\n<p>Essa etapa é importante em jornadas que começam antes das 22h ou terminam depois das 5h.</p>\r\n\r\n<h2>Exemplo simplificado</h2>\r\n<p>Se a hora diurna vale R$ 10,00 e o adicional noturno é 20%, a parcela adicional corresponde a R$ 2,00 por hora noturna considerada. O valor total depende da quantidade de horas noturnas convertidas.</p>\r\n<p>Quando houver hora extra, não some percentuais de maneira informal. Mostre cada componente da remuneração.</p>\r\n\r\n<h2>Hora extra noturna</h2>\r\n<p>Uma hora extraordinária dentro do período noturno pode reunir adicional noturno e adicional de hora extra. O cálculo deve respeitar a base e a ordem previstas na regra aplicável.</p>\r\n<p>Na prática, o sistema deve permitir identificar separadamente horas normais noturnas e horas extras noturnas.</p>\r\n\r\n<h2>Prorrogação depois das 5h</h2>\r\n<p>O art. 73 da CLT prevê aplicação das regras do trabalho noturno às prorrogações. A jurisprudência trabalhista também trata de situações específicas de jornada integralmente noturna e escalas.</p>\r\n<p>Não retire automaticamente o adicional no minuto em que o relógio passa das 5h sem conferir a jornada e o entendimento aplicável ao caso.</p>\r\n\r\n<h2>Jornada mista</h2>\r\n<p>Quando a jornada possui parte diurna e parte noturna, identifique cada intervalo. Apenas multiplicar toda a jornada pelo adicional pode superestimar o pagamento.</p>\r\n<p>O registro de ponto precisa permitir localizar onde começa e termina cada faixa.</p>\r\n\r\n<h2>Reflexos e habitualidade</h2>\r\n<p>Adicional noturno habitual pode integrar outras bases e reflexos conforme a legislação e a situação do vínculo. Por isso, a folha deve manter a verba identificada e não escondê-la dentro de um valor genérico de horas.</p>\r\n\r\n<h2>Checklist do adicional noturno</h2>\r\n<ol>\r\n<li>Confirme se a regra urbana é aplicável.</li>\r\n<li>Identifique o período efetivamente trabalhado.</li>\r\n<li>Converta a hora reduzida quando cabível.</li>\r\n<li>Confirme o percentual legal ou coletivo.</li>\r\n<li>Separe horas normais e extras.</li>\r\n<li>Analise a prorrogação após as 5h.</li>\r\n<li>Revise jornada mista.</li>\r\n<li>Confira reflexos e incidências.</li>\r\n<li>Compare com o ponto.</li>\r\n<li>Guarde a memória do cálculo.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule o adicional noturno no Prazzu Tools</h2><p>A Calculadora de Hora Extra também permite organizar cenários com adicional noturno, facilitando a conferência do valor da hora e dos adicionais.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-hora-extra\">Abrir Calculadora de Hora Extra</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> confira <a href=\"/blog/como-calcular-hora-extra-50-100\">como calcular hora extra 50% e 100%</a> e o <a href=\"/blog/dsr-sobre-horas-extras-como-calcular\">DSR sobre horas extras</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>A hora noturna urbana tem 60 minutos?</h3><p>Na regra geral do art. 73 da CLT, ela é computada como 52 minutos e 30 segundos.</p>\r\n<h3>O adicional noturno é sempre 20%?</h3><p>Vinte por cento é o mínimo da regra urbana geral; instrumento coletivo pode prever percentual superior.</p>\r\n<h3>Depois das 5h o adicional termina automaticamente?</h3><p>Não necessariamente. Prorrogações de jornada noturna precisam ser analisadas conforme a regra e a situação concreta.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>O <strong>adicional noturno</strong> deve ser conferido em etapas: período, hora reduzida, percentual e eventuais horas extras. Essa separação evita aplicar um percentual correto sobre uma quantidade de horas incorreta.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/hora-extra-adicional-noturno-dsr.png', 'Cálculo de adicional noturno com hora reduzida de 52 minutos e 30 segundos', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:34:17', 'adicional noturno', '[\"hora noturna reduzida\", \"52 minutos e 30 segundos\", \"hora extra noturna\", \"trabalho noturno CLT\", \"adicional noturno 20%\", \"prorrogação jornada noturna\"]', 'Adicional noturno: como calcular hora reduzida', 'Aprenda a calcular adicional noturno, hora reduzida de 52min30s, período urbano das 22h às 5h e como tratar prorrogação e hora extra noturna.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:34:17'),
(35, NULL, 'DSR sobre horas extras: como calcular com exemplo', 'dsr-sobre-horas-extras-como-calcular', 'Aprenda como calcular DSR sobre horas extras usando total das horas extras, dias úteis, domingos e feriados e veja por que a fração fixa de 1/6 pode falhar.', '<p>O <strong>DSR sobre horas extras</strong> representa o reflexo das horas extraordinárias habituais no repouso semanal remunerado. A conta precisa usar o período correto e deixar visíveis o total das horas extras, os dias úteis e os repousos considerados.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> some as horas extras remuneradas do período, confira o calendário, divida pelo número de dias úteis adotado na apuração e multiplique pelos repousos e feriados considerados, observando a regra aplicável ao empregado.</div>\r\n\r\n<h2>O que é DSR?</h2>\r\n<p>DSR é o descanso semanal remunerado. A legislação garante o repouso e trata da remuneração desse período, inclusive com reflexos de horas extraordinárias habituais em situações previstas.</p>\r\n<p>Na prática da folha, a memória deve explicar quais dias foram classificados como úteis e quais foram considerados repousos.</p>\r\n\r\n<h2>Horas extras entram no DSR?</h2>\r\n<p>Sim, a habitualidade das horas extras produz repercussão no repouso remunerado conforme a regra consolidada. O TST também possui entendimento sobre as repercussões do DSR majorado por horas extras habituais em outras parcelas.</p>\r\n<p>Por isso, a competência do cálculo e a habitualidade precisam ser analisadas, especialmente em passivos e recálculos antigos.</p>\r\n\r\n<h2>Fórmula usada em muitos cenários mensais</h2>\r\n<p>Uma forma comum de cálculo é: total remunerado de horas extras ÷ dias úteis × domingos e feriados considerados como repouso.</p>\r\n<p>A fórmula não elimina a necessidade de conferir o calendário e o critério da folha.</p>\r\n\r\n<h2>Exemplo simples</h2>\r\n<p>Se as horas extras remuneradas somam R$ 300,00, o período possui 25 dias úteis e 5 repousos/feriados, o cálculo didático é R$ 300,00 ÷ 25 × 5 = R$ 60,00.</p>\r\n<p>Esse exemplo demonstra a lógica. A folha real precisa usar as quantidades efetivamente aplicáveis ao período.</p>\r\n\r\n<h2>Por que 1/6 pode gerar erro?</h2>\r\n<p>Uma fração fixa ignora a quantidade real de domingos, feriados e dias úteis de cada mês. Meses diferentes podem gerar reflexos diferentes mesmo com o mesmo total de horas extras.</p>\r\n<p>Para conferência, prefira mostrar os números usados no cálculo em vez de apenas um percentual final.</p>\r\n\r\n<h2>Feriados entram na conta?</h2>\r\n<p>Feriados remunerados podem integrar a contagem de repousos conforme o critério adotado para a situação. O calendário local também importa, pois existem feriados municipais e estaduais.</p>\r\n<p>Não use o calendário de outra cidade ou estabelecimento sem validar a localização do empregado.</p>\r\n\r\n<h2>Como tratar faltas e perda do repouso?</h2>\r\n<p>Faltas injustificadas e outras ocorrências podem afetar o direito à remuneração do repouso em determinadas situações. Esse tratamento deve ser verificado antes de aplicar uma fórmula automática.</p>\r\n<p>O cálculo do reflexo não deve ignorar eventos da folha que mudem o direito ao DSR.</p>\r\n\r\n<h2>DSR majorado e outras parcelas</h2>\r\n<p>O TST consolidou entendimento de que a majoração do DSR decorrente de horas extras habituais repercute em outras parcelas salariais, observada a modulação temporal definida na jurisprudência.</p>\r\n<p>Esse ponto é relevante em férias, 13º, aviso-prévio e FGTS e merece atenção especial em cálculos retroativos.</p>\r\n\r\n<h2>Como auditar o cálculo</h2>\r\n<p>Guarde o total de horas extras por modalidade, o valor remunerado, o calendário, os dias úteis e os repousos. Se o sistema apresentar apenas o valor final, reproduza a conta em uma memória auxiliar para identificar diferenças.</p>\r\n\r\n<h2>Checklist do DSR</h2>\r\n<ol>\r\n<li>Confirme a competência.</li>\r\n<li>Some as horas extras do período.</li>\r\n<li>Separe modalidades de adicionais.</li>\r\n<li>Confira dias úteis.</li>\r\n<li>Confira domingos e feriados.</li>\r\n<li>Analise faltas que afetem o repouso.</li>\r\n<li>Calcule o reflexo.</li>\r\n<li>Revise repercussões em outras parcelas.</li>\r\n<li>Compare com a folha.</li>\r\n<li>Guarde o calendário usado.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule horas extras e DSR</h2><p>Use a Calculadora de Hora Extra do Prazzu Tools para organizar o valor das horas extras e apoiar a conferência dos reflexos.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-hora-extra\">Abrir Calculadora de Hora Extra</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> comece pelo guia de <a href=\"/blog/como-calcular-hora-extra-50-100\">hora extra 50% e 100%</a> e revise o <a href=\"/blog/adicional-noturno-como-calcular-hora-reduzida\">adicional noturno</a> quando houver trabalho à noite.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>DSR sobre hora extra é sempre 1/6?</h3><p>Não é uma regra universal segura. O calendário e o critério da apuração podem alterar o resultado.</p>\r\n<h3>Feriados podem entrar no DSR?</h3><p>Sim, conforme o cenário e o critério aplicável. Use o calendário correto do local de trabalho.</p>\r\n<h3>O DSR majorado repercute em outras verbas?</h3><p>A jurisprudência do TST reconhece repercussões em outras parcelas, observada a modulação e o período analisado.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>O <strong>DSR sobre horas extras</strong> fica mais confiável quando o cálculo mostra o total das horas extras, os dias úteis, os repousos e a fórmula utilizada. Essa transparência reduz ajustes manuais e facilita auditoria da folha.</p>', 4, 'Trabalhista', 'contabilidade', 'blog/covers/hora-extra-adicional-noturno-dsr.png', 'Cálculo de DSR sobre horas extras com dias úteis, domingos e feriados', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:34:17', 'DSR sobre horas extras', '[\"calcular DSR\", \"descanso semanal remunerado\", \"reflexo de horas extras\", \"DSR folha de pagamento\", \"hora extra habitual\", \"DSR domingos feriados\"]', 'DSR sobre horas extras: como calcular e conferir', 'Aprenda a calcular DSR sobre horas extras usando dias úteis, domingos e feriados e veja por que uma fração fixa pode gerar erro na folha.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:34:17'),
(36, NULL, 'DIFAL ICMS: como calcular alíquota, FCP e valor devido', 'difal-icms-como-calcular-passo-a-passo', 'Aprenda como calcular DIFAL ICMS, identificar alíquota interestadual de 4%, 7% ou 12%, conferir alíquota interna, FCP e método de base da operação.', '<p><strong>Calcular DIFAL</strong> do ICMS exige mais do que selecionar UF de origem e destino. A alíquota interestadual pode ser 4%, 7% ou 12% em diferentes situações, enquanto a alíquota interna e o FCP dependem da mercadoria, legislação do destino, benefícios e período.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme origem, destino, destinatário, mercadoria, alíquota interestadual, alíquota interna e FCP. Depois valide o método de base e a responsabilidade pelo recolhimento antes de gerar a guia.</div>\r\n\r\n<h2>O que é DIFAL?</h2>\r\n<p>DIFAL é o diferencial de alíquotas do ICMS. Em operações interestaduais alcançadas pela regra, parte da carga é destinada ao estado de destino conforme a legislação aplicável.</p>\r\n<p>A LC 190/2022 é uma referência central para operações destinadas a consumidor final não contribuinte, mas a análise concreta depende da operação e do contribuinte.</p>\r\n\r\n<h2>Quais dados reunir antes do cálculo</h2>\r\n<ul>\r\n<li>UF de origem e de destino;</li>\r\n<li>valor e base de cálculo;</li>\r\n<li>natureza da operação;</li>\r\n<li>situação do destinatário perante o ICMS;</li>\r\n<li>NCM e características da mercadoria;</li>\r\n<li>alíquota interestadual;</li>\r\n<li>alíquota interna do destino;</li>\r\n<li>FCP quando aplicável;</li>\r\n<li>benefícios ou reduções;</li>\r\n<li>método de cálculo exigido no caso.</li>\r\n</ul>\r\n\r\n<h2>Quando a alíquota interestadual é 12%?</h2>\r\n<p>A Resolução do Senado nº 22/1989 estabelece 12% como regra geral interestadual, ressalvadas hipóteses específicas de 7% e 4%.</p>\r\n<p>A direção da operação importa: origem e destino não podem ser invertidos na parametrização.</p>\r\n\r\n<h2>Quando a alíquota é 7%?</h2>\r\n<p>A regra de 7% alcança, em linhas gerais, operações originadas nas regiões Sul e Sudeste destinadas às regiões Norte, Nordeste e Centro-Oeste e ao Espírito Santo, observadas as demais condições.</p>\r\n<p>Uma operação de São Paulo para Bahia é exemplo clássico da direção sujeita à regra reduzida.</p>\r\n\r\n<h2>Quando a alíquota de 4% pode ser usada?</h2>\r\n<p>A Resolução do Senado nº 13/2012 prevê 4% para determinadas operações interestaduais com bens e mercadorias importados do exterior.</p>\r\n<p>Não basta marcar o produto como “importado”. Existem critérios de conteúdo de importação e exceções. A classificação precisa ser validada antes de forçar a alíquota.</p>\r\n\r\n<h2>Alíquota interna não deve ser inferida apenas pela UF</h2>\r\n<p>Um estado pode ter várias alíquotas internas conforme produto, NCM, essencialidade, benefício fiscal e período. Usar uma “alíquota padrão do estado” sem pesquisar a mercadoria pode gerar DIFAL incorreto.</p>\r\n<p>Registre a fonte e a vigência da alíquota interna usada na memória do cálculo.</p>\r\n\r\n<h2>Exemplo com base simples</h2>\r\n<p>Considere operação de São Paulo para Bahia com base de R$ 1.000,00, alíquota interestadual de 7% e alíquota interna confirmada de 18%. Em um exemplo didático de base simples, a diferença nominal é 11 pontos percentuais e o DIFAL seria R$ 110,00.</p>\r\n<p>Se houver FCP de 2% sobre a mesma base, o adicional seria R$ 20,00, totalizando R$ 130,00 no exemplo. A operação real pode exigir método diferente de base ou outras particularidades.</p>\r\n\r\n<h2>FCP deve aparecer separado</h2>\r\n<p>FCP não é a mesma coisa que DIFAL. A existência, percentual e base do Fundo de Combate à Pobreza variam conforme a legislação do destino e o produto.</p>\r\n<p>Mostre o FCP em linha própria para facilitar recolhimento e conferência.</p>\r\n\r\n<h2>Base simples e base por dentro</h2>\r\n<p>Algumas operações e estados adotam metodologias específicas para a composição da base. Não aplique automaticamente “base dupla” ou cálculo por dentro em qualquer cenário.</p>\r\n<p>A ferramenta deve permitir simulação, mas o método precisa ser confirmado na legislação aplicável à operação.</p>\r\n\r\n<h2>Quem é responsável pelo recolhimento?</h2>\r\n<p>A responsabilidade depende da situação do destinatário, da operação e da legislação. Em venda para consumidor final não contribuinte, existem regras próprias relacionadas ao DIFAL de destino.</p>\r\n<p>Antes de emitir guia, confirme quem recolhe, código, documento e prazo.</p>\r\n\r\n<h2>Checklist antes de recolher</h2>\r\n<ol>\r\n<li>Confirme origem e destino.</li>\r\n<li>Valide o destinatário.</li>\r\n<li>Identifique NCM e mercadoria.</li>\r\n<li>Determine 4%, 7% ou 12% corretamente.</li>\r\n<li>Pesquise a alíquota interna.</li>\r\n<li>Verifique FCP.</li>\r\n<li>Confirme benefícios e reduções.</li>\r\n<li>Valide o método de base.</li>\r\n<li>Confirme responsabilidade e guia.</li>\r\n<li>Guarde a fonte e a competência.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Calcule o DIFAL no Prazzu Tools</h2><p>A Calculadora de DIFAL organiza origem, destino, alíquotas e FCP para produzir uma memória de cálculo antes da conferência final.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-difal-icms\">Abrir Calculadora de DIFAL ICMS</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> confira o guia de <a href=\"/blog/aliquota-interestadual-icms-4-7-12\">alíquota interestadual de ICMS</a> e o conteúdo sobre <a href=\"/blog/fcp-no-difal-quando-aplicar-como-calcular\">FCP no DIFAL</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>DIFAL é sempre alíquota interna menos 12%?</h3><p>Não. A alíquota interestadual pode ser 4%, 7% ou 12% conforme a operação, e o método de cálculo pode ter particularidades.</p>\r\n<h3>Todo produto importado usa 4%?</h3><p>Não. A Resolução nº 13/2012 possui critérios e exceções que precisam ser conferidos.</p>\r\n<h3>FCP é sempre 2%?</h3><p>Não. O percentual e os produtos alcançados dependem da legislação do estado de destino.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p><strong>Calcular DIFAL</strong> com segurança é confirmar premissas antes da matemática. Alíquota interestadual, interna, FCP, base e responsabilidade devem permanecer visíveis para que o resultado seja auditável.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/difal-icms-fcp.png', 'Cálculo de DIFAL ICMS com alíquota interestadual, interna e FCP', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:34:17', 'DIFAL ICMS', '[\"calcular DIFAL\", \"calculadora DIFAL\", \"ICMS interestadual\", \"FCP DIFAL\", \"alíquota interna ICMS\", \"DIFAL consumidor final\"]', 'DIFAL ICMS: como calcular alíquota, FCP e valor', 'Aprenda a calcular DIFAL ICMS, identificar alíquota interestadual, conferir alíquota interna, FCP e método de base antes do recolhimento.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:34:17'),
(37, NULL, 'Alíquota interestadual de ICMS: quando usar 4%, 7% ou 12%', 'aliquota-interestadual-icms-4-7-12', 'Entenda quando usar alíquota interestadual de ICMS de 4%, 7% ou 12%, como origem e destino influenciam e quais cuidados tomar com mercadorias importadas.', '<p>A <strong>alíquota interestadual de ICMS</strong> não é escolhida livremente. Em operações entre estados, o percentual depende principalmente da origem, do destino e, em situações específicas, do enquadramento da mercadoria importada. Confundir 4%, 7% e 12% pode alterar o ICMS próprio e também cálculos posteriores, como o DIFAL.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> determine primeiro a UF de origem e a UF de destino. Depois verifique se a operação está sujeita à regra de 4% para bens e mercadorias importados. Se não estiver, analise a combinação origem/destino para identificar 7% ou 12%.</div>\r\n\r\n<h2>Quais são as principais alíquotas interestaduais?</h2>\r\n<p>Na regra geral, as operações interestaduais trabalham com três referências principais: 12%, 7% e 4%. Cada uma possui fundamento e condições próprias. A Resolução do Senado nº 22/1989 estabelece 12% como regra geral e 7% para determinadas operações originadas no Sul e Sudeste com destino ao Norte, Nordeste, Centro-Oeste e Espírito Santo. A Resolução nº 13/2012 trata de 4% para determinadas operações com bens e mercadorias importados.</p>\r\n\r\n<h2>Quando usar 12%?</h2>\r\n<p>A alíquota de 12% funciona como regra interestadual geral quando a operação não se enquadra na regra reduzida de 7% nem na regra de 4% para importados. A direção da operação é fundamental.</p>\r\n<p>Uma operação da Bahia para São Paulo, por exemplo, não segue a mesma lógica reduzida de uma operação de São Paulo para Bahia. O sistema deve trabalhar com a origem e o destino reais da saída.</p>\r\n\r\n<h2>Quando usar 7%?</h2>\r\n<p>A regra de 7% alcança, em linhas gerais, operações e prestações realizadas nas Regiões Sul e Sudeste destinadas às Regiões Norte, Nordeste e Centro-Oeste e ao Estado do Espírito Santo.</p>\r\n<p>Exemplos típicos incluem São Paulo para Bahia, Rio de Janeiro para Goiás, Paraná para Pernambuco e Santa Catarina para Espírito Santo. Esses exemplos ajudam a visualizar a direção, mas não eliminam a análise de mercadorias importadas ou de regras especiais.</p>\r\n\r\n<h2>Quando usar 4%?</h2>\r\n<p>A Resolução do Senado nº 13/2012 estabelece 4% para determinadas operações interestaduais com bens e mercadorias importados do exterior. A norma contém critérios e exceções, inclusive relacionados à industrialização e ao conteúdo de importação.</p>\r\n<p>Por isso, marcar um item apenas como “importado” não é suficiente para concluir automaticamente que a alíquota é 4%. O enquadramento precisa ser demonstrável.</p>\r\n\r\n<h2>Origem e destino precisam representar a operação real</h2>\r\n<p>Em empresas com filiais ou centros de distribuição, o estabelecimento emitente e a circulação real precisam ser considerados. Usar a UF da matriz apenas porque é o cadastro principal pode gerar uma alíquota incorreta.</p>\r\n<p>Antes de automatizar, confira de qual estabelecimento a mercadoria realmente sai e para qual estabelecimento ou consumidor ela se destina.</p>\r\n\r\n<h2>Alíquota interestadual não é alíquota interna</h2>\r\n<p>A alíquota interestadual é usada na operação entre estados. A alíquota interna pertence à legislação do estado e pode variar por produto, NCM, essencialidade, benefício fiscal e período.</p>\r\n<p>No DIFAL, as duas aparecem no mesmo cálculo, mas não são intercambiáveis. Uma alíquota interna “padrão” do estado não deve ser presumida para todos os produtos.</p>\r\n\r\n<h2>Exemplo SP para BA</h2>\r\n<p>Em uma operação comum de São Paulo para Bahia, sem enquadramento na regra de importados, a referência interestadual é 7%. Se a mesma mercadoria estiver corretamente enquadrada na Resolução nº 13/2012, a alíquota pode ser 4%.</p>\r\n<p>Esse exemplo mostra por que origem e destino não resolvem tudo quando há mercadoria importada.</p>\r\n\r\n<h2>Exemplo BA para SP</h2>\r\n<p>No sentido Bahia para São Paulo, a regra reduzida de 7% da Resolução nº 22/1989 não se aplica pela mesma direção. Em cenário geral, a referência tende a ser 12%, salvo regra específica para a operação.</p>\r\n\r\n<h2>Como validar mercadoria importada</h2>\r\n<p>Antes de selecionar 4%, verifique documentos de importação, industrialização e conteúdo de importação quando exigido. Dados do fornecedor e documentos fiscais anteriores podem ser relevantes para sustentar o enquadramento.</p>\r\n<p>Se a empresa não possui informação suficiente, não é recomendável forçar a alíquota apenas para concluir a emissão.</p>\r\n\r\n<h2>Relação com o DIFAL</h2>\r\n<p>A alíquota interestadual influencia diretamente a diferença nominal no DIFAL. Se a alíquota interna confirmada for 18%, uma operação com interestadual de 7% parte de diferença de 11 pontos percentuais; com 4%, a diferença nominal seria 14 pontos.</p>\r\n<p>Essa comparação ainda não substitui a verificação de base, FCP, benefício e método de cálculo.</p>\r\n\r\n<h2>Checklist antes de definir a alíquota</h2>\r\n<ol>\r\n<li>Confirme a UF de origem.</li>\r\n<li>Confirme a UF de destino.</li>\r\n<li>Identifique o estabelecimento emitente.</li>\r\n<li>Verifique se a mercadoria é importada.</li>\r\n<li>Analise a Resolução nº 13/2012 quando aplicável.</li>\r\n<li>Determine se a combinação origem/destino usa 7% ou 12%.</li>\r\n<li>Confira regras especiais e benefícios.</li>\r\n<li>Registre a fonte da parametrização.</li>\r\n<li>Revise o impacto no DIFAL.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule a operação no Prazzu Tools</h2><p>A Calculadora de DIFAL ICMS organiza origem, destino, alíquota interestadual, alíquota interna e FCP para facilitar a conferência.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-difal-icms\">Abrir Calculadora de DIFAL ICMS</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> entenda <a href=\"/blog/difal-icms-como-calcular-passo-a-passo\">como calcular o DIFAL ICMS</a> e confira o <a href=\"/blog/fcp-no-difal-quando-aplicar-como-calcular\">FCP no DIFAL</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>Venda de São Paulo para Bahia usa 7%?</h3><p>Na regra geral, essa direção está entre as operações sujeitas à alíquota de 7%, ressalvadas hipóteses específicas como a regra de importados.</p>\r\n<h3>Toda mercadoria importada usa 4%?</h3><p>Não. A Resolução nº 13/2012 possui critérios e exceções que precisam ser conferidos.</p>\r\n<h3>Posso usar uma alíquota interna padrão do estado?</h3><p>Não com segurança. Produto, benefício, NCM e período podem mudar a alíquota interna aplicável.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>A <strong>alíquota interestadual de ICMS</strong> pode ser automatizada quando origem, destino e enquadramento da mercadoria estão corretos. O erro surge quando 4%, 7% ou 12% viram opções manuais sem ligação com a operação real.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/difal-icms-fcp.png', 'Alíquota interestadual de ICMS de 4%, 7% e 12% por origem, destino e importação', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:37:14', 'alíquota interestadual de ICMS', '[\"ICMS 4% 7% 12%\", \"alíquota ICMS interestadual\", \"Resolução 22 1989\", \"Resolução 13 2012\", \"DIFAL\", \"ICMS importados\"]', 'ICMS interestadual: quando usar 4%, 7% ou 12%', 'Entenda quando usar alíquota interestadual de ICMS de 4%, 7% ou 12%, como origem e destino influenciam e quais cuidados tomar com importados.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:37:14');
INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `category_id`, `category`, `vertical_slug`, `cover_image_path`, `cover_image_alt`, `status`, `is_featured`, `published_at`, `content_updated_at`, `primary_keyword`, `related_keywords`, `meta_title`, `meta_description`, `canonical_url`, `social_image_path`, `should_index`, `created_at`, `updated_at`) VALUES
(38, NULL, 'FCP no DIFAL: quando aplicar, como calcular e conferir', 'fcp-no-difal-quando-aplicar-como-calcular', 'Entenda o FCP no DIFAL, como calcular o adicional, por que ele deve ficar separado e quais dados estaduais e do produto precisam ser confirmados.', '<p>O <strong>FCP no DIFAL</strong> precisa ser tratado separadamente porque o Fundo de Combate à Pobreza não é simplesmente uma parte da diferença entre alíquota interna e interestadual. Sua existência, percentual, base e forma de recolhimento dependem da legislação do estado de destino e do enquadramento da mercadoria ou serviço.</p>\r\n<div class=\"alert alert-info\"><strong>Resumo prático:</strong> confirme primeiro se o produto está sujeito ao FCP no estado de destino. Depois valide a alíquota e a base, calcule o valor em linha própria e só então some ao DIFAL quando a operação exigir.</div>\r\n\r\n<h2>O que é FCP?</h2>\r\n<p>FCP é a sigla usada para Fundo de Combate à Pobreza. Alguns estados utilizam denominações próprias, como FECP. O adicional está relacionado ao ICMS e é regulamentado na legislação estadual.</p>\r\n<p>Na prática, determinados produtos e operações recebem uma carga adicional que deve ser destacada separadamente na memória do cálculo.</p>\r\n\r\n<h2>FCP e DIFAL são a mesma coisa?</h2>\r\n<p>Não. O DIFAL representa o diferencial de alíquotas do ICMS conforme a operação. O FCP é um adicional que pode existir sobre determinadas mercadorias e situações.</p>\r\n<p>Somar tudo em uma única linha chamada “DIFAL” dificulta a conferência e pode causar erro na emissão da guia ou na escrituração.</p>\r\n\r\n<h2>FCP é sempre 2%?</h2>\r\n<p>Não. Embora 2% apareça com frequência em exemplos, não existe um percentual nacional único para todos os produtos e estados.</p>\r\n<p>A legislação estadual pode definir percentuais diferentes e restringir o adicional a determinados produtos, serviços ou faixas de tributação.</p>\r\n\r\n<h2>Todo produto paga FCP?</h2>\r\n<p>Também não. O alcance depende da legislação do estado de destino. Produto considerado essencial, supérfluo, sujeito a benefício ou classificado em determinada NCM pode ter tratamento diferente.</p>\r\n<p>Por isso, a pesquisa precisa partir do produto e da operação, não apenas da UF.</p>\r\n\r\n<h2>Como calcular em um exemplo simples</h2>\r\n<p>Considere base confirmada de R$ 1.000,00 e alíquota de FCP de 2% validada para aquela mercadoria e estado. Em uma simulação simples, o FCP seria R$ 20,00.</p>\r\n<p>Se o mesmo cenário tiver DIFAL de R$ 110,00, a memória pode mostrar R$ 110,00 de DIFAL e R$ 20,00 de FCP, totalizando R$ 130,00. O importante é manter as parcelas separadas.</p>\r\n\r\n<h2>Qual base usar?</h2>\r\n<p>A base pode acompanhar a operação ou receber tratamento específico conforme a legislação estadual. Não presuma que o FCP sempre utiliza exatamente a mesma base do DIFAL.</p>\r\n<p>Se houver redução de base, benefício ou método de cálculo específico, verifique se ele também afeta o adicional.</p>\r\n\r\n<h2>Por que a NCM importa?</h2>\r\n<p>A legislação estadual frequentemente relaciona o FCP a grupos de mercadorias. Uma descrição comercial como “bebida”, “cosmético” ou “eletrônico” pode ser insuficiente para identificar o tratamento correto.</p>\r\n<p>Use a classificação fiscal e as características reais do produto para pesquisar a regra.</p>\r\n\r\n<h2>A data de referência também importa</h2>\r\n<p>Percentuais e produtos alcançados podem mudar com alterações na legislação estadual. Uma alíquota válida hoje pode não ser a mesma de uma operação antiga.</p>\r\n<p>Registre a competência ou data da operação junto com a fonte consultada para permitir auditoria futura.</p>\r\n\r\n<h2>Como pesquisar a regra correta</h2>\r\n<p>Comece pela legislação e pelos portais oficiais do estado de destino. Tabelas privadas podem ajudar na triagem, mas não devem ser a única evidência quando existe impacto fiscal relevante.</p>\r\n<p>Guarde a referência da norma, data de acesso e, quando possível, o trecho ou tabela que sustentou a alíquota utilizada.</p>\r\n\r\n<h2>Como o FCP aparece no DIFAL</h2>\r\n<p>Quando a operação interestadual exige DIFAL e também está sujeita ao FCP, a memória deve apresentar cada parcela de forma independente.</p>\r\n<p>Isso facilita verificar se a alíquota interestadual foi correta, se a alíquota interna corresponde ao produto e se o adicional estadual foi aplicado somente quando devido.</p>\r\n\r\n<h2>Erros comuns</h2>\r\n<ul>\r\n<li>usar 2% como padrão nacional;</li>\r\n<li>aplicar FCP a todo produto do estado;</li>\r\n<li>calcular sobre base diferente da prevista;</li>\r\n<li>misturar FCP e DIFAL em uma única linha;</li>\r\n<li>usar legislação de competência diferente;</li>\r\n<li>ignorar benefício ou redução de base;</li>\r\n<li>pesquisar apenas pela UF sem analisar o produto.</li>\r\n</ul>\r\n\r\n<h2>Checklist para calcular FCP no DIFAL</h2>\r\n<ol>\r\n<li>Confirme o estado de destino.</li>\r\n<li>Identifique a mercadoria e a NCM.</li>\r\n<li>Pesquise se o produto está sujeito ao FCP.</li>\r\n<li>Valide o percentual vigente.</li>\r\n<li>Confirme a base do adicional.</li>\r\n<li>Revise benefícios e reduções.</li>\r\n<li>Calcule o FCP separadamente.</li>\r\n<li>Calcule o DIFAL em linha própria.</li>\r\n<li>Confirme a forma de recolhimento.</li>\r\n<li>Guarde a fonte e a competência.</li>\r\n</ol>\r\n\r\n<div class=\"card border-primary-subtle bg-primary-subtle my-4\"><div class=\"card-body\"><h2 class=\"h4\">Simule DIFAL e FCP no Prazzu Tools</h2><p>A Calculadora de DIFAL ICMS mantém alíquota interestadual, interna e FCP visíveis para que o resultado possa ser conferido antes do recolhimento.</p><a class=\"btn btn-primary prazzu-btn-primary\" href=\"/ferramentas/calculadora-difal-icms\">Abrir Calculadora de DIFAL ICMS</a></div></div>\r\n\r\n<p><strong>Veja também:</strong> confira <a href=\"/blog/aliquota-interestadual-icms-4-7-12\">quando usar 4%, 7% ou 12% no ICMS interestadual</a> e o guia de <a href=\"/blog/difal-icms-como-calcular-passo-a-passo\">cálculo do DIFAL</a>.</p>\r\n\r\n<h2>Perguntas frequentes</h2>\r\n<h3>FCP é sempre 2%?</h3><p>Não. A alíquota e os produtos alcançados dependem da legislação estadual.</p>\r\n<h3>Todo produto sujeito a DIFAL paga FCP?</h3><p>Não. O FCP depende do produto e da regra do estado de destino.</p>\r\n<h3>Posso somar FCP e DIFAL em um único campo?</h3><p>Para conferência e rastreabilidade, é melhor manter as parcelas separadas e mostrar o total apenas depois.</p>\r\n\r\n<h2>Conclusão</h2>\r\n<p>O <strong>FCP no DIFAL</strong> pode representar uma parcela pequena do total, mas gera inconsistência relevante se for aplicado ao produto errado ou com base incorreta. O cálculo seguro exige confirmar estado, produto, NCM, percentual, base e vigência antes do recolhimento.</p>', 3, 'Fiscal e Tributário', 'contabilidade', 'blog/covers/difal-icms-fcp.png', 'FCP no DIFAL separado do ICMS com alíquota, base e estado de destino', 'published', 0, '2026-07-27 17:56:04', '2026-07-27 20:37:14', 'FCP no DIFAL', '[\"Fundo de Combate à Pobreza\", \"FCP ICMS\", \"DIFAL e FCP\", \"calculadora FCP\", \"alíquota FCP por estado\", \"FECP\"]', 'FCP no DIFAL: quando aplicar e como calcular', 'Entenda quando o FCP entra no DIFAL, como calcular o adicional, por que ele deve ficar separado e quais regras estaduais e do produto precisam ser conferidas.', NULL, NULL, 1, '2026-07-27 17:56:04', '2026-07-27 20:37:14'),
(39, NULL, 'Como calcular turnover em Recursos Humanos', 'como-calcular-turnover', 'Entenda uma fórmula operacional de turnover e os cuidados ao comparar períodos.', '<p>Turnover é um indicador de rotatividade. Uma forma operacional de cálculo usa a média entre admissões e desligamentos, dividida pelo quadro médio do mesmo período.</p><p>Antes de comparar equipes ou meses, mantenha o mesmo critério de quadro médio e registre a metodologia usada pela organização.</p>', 5, 'Gestão de Pessoas', 'rh', NULL, NULL, 'published', 1, '2026-08-11 15:01:34', '2026-08-11 15:01:34', 'turnover rh', '[\"rotatividade\",\"gest\\u00e3o de pessoas\",\"indicadores de RH\"]', 'Como calcular turnover em RH | Prazzu Tools', 'Entenda uma fórmula operacional de turnover e como manter comparações consistentes em Recursos Humanos.', NULL, NULL, 1, '2026-08-11 15:01:34', '2026-08-11 15:01:34'),
(40, NULL, 'Indicadores de RH: como manter critérios consistentes', 'indicadores-de-rh-com-criterios-consistentes', 'Veja por que período, população e fórmula precisam ser consistentes ao acompanhar indicadores de RH.', '<p>Indicadores de Recursos Humanos ganham valor quando o critério de cálculo permanece estável ao longo do tempo.</p><p>Defina o período, a população observada e a fórmula antes de comparar equipes, unidades ou meses. Registre mudanças metodológicas para não interpretar uma alteração de critério como mudança real de desempenho.</p>', 5, 'Gestão de Pessoas', 'rh', NULL, NULL, 'published', 0, '2026-08-11 15:01:34', '2026-08-11 15:01:34', 'indicadores de rh', '[\"gest\\u00e3o de pessoas\",\"m\\u00e9tricas de RH\",\"turnover\"]', 'Indicadores de RH com critérios consistentes | Prazzu Tools', 'Entenda como manter período, população e fórmula consistentes ao acompanhar indicadores de Recursos Humanos.', NULL, NULL, 1, '2026-08-11 15:01:34', '2026-08-11 15:01:34');

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
(10, 'calculadora-ferias', '2026-07-23 11:45:13', '2026-07-23 11:45:13'),
(11, 'custo-funcionario-clt', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(12, 'simulador-fator-r', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(13, 'das-em-atraso', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(14, 'encargos-trabalhistas', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(15, 'comparador-clt-pj-autonomo', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(16, 'inss-patronal', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(17, 'capital-de-giro', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(18, 'fluxo-de-caixa', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(19, 'ponto-de-equilibrio', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(20, 'simulador-pro-labore-ideal', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(21, 'comissao-vendedores', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(22, 'gerador-holerite', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(23, 'simulador-admissao', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(24, 'reajuste-salarial', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(25, 'distribuicao-de-lucros', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(26, 'declaracao-rendimentos', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(27, 'declaracao-trabalho-renda', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(28, 'gerador-de-contratos', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(29, 'emissor-de-recibos', '2026-07-27 13:45:49', '2026-07-27 13:45:49'),
(30, 'calculadora-salario-liquido', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(31, 'calculadora-salario-liquido', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(32, 'calculadora-salario-liquido', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(33, 'calculadora-hora-extra', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(34, 'calculadora-hora-extra', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(35, 'calculadora-hora-extra', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(36, 'calculadora-difal-icms', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(37, 'calculadora-difal-icms', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(38, 'calculadora-difal-icms', '2026-07-27 17:56:04', '2026-07-27 17:56:04'),
(39, 'calculadora-turnover', '2026-08-11 15:01:34', '2026-08-11 15:01:34');

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
(42, '2026_07_23_000500_create_receipt_party_profiles_table', 2),
(43, '2026_07_24_000100_create_tool_feedback_table', 3),
(44, '2026_07_24_000200_create_tool_suggestions_table', 4),
(45, '2026_07_25_000100_create_tool_profile_tables', 5),
(46, '2026_08_10_000000_add_vertical_to_blog_content', 6),
(47, '2026_08_10_000100_add_vertical_to_global_services', 6),
(48, '2026_08_10_010000_seed_rh_vertical_proof_content', 6);

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
(1, NULL, '6olrhnsRUzp9WtmU5PHslbDLNoiyhKhnGPYjUQkb', '/', 'http://localhost:8000', 'Prazzu Tools — Ferramentas para contabilidade', 5, 'essa pagina é facil de entender e facil e encontrar as coisas que eu procuro fora que é muito bonita também', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-23 15:58:15', '2026-07-23 15:58:15'),
(2, 1, '7RLdo0x2LzvAIBO2oNivkR5TVOsgKMtwD8NThRbx', '/ferramentas', 'http://localhost:8000/ferramentas', 'Todas as ferramentas — Prazzu Tools', 5, 'tem muitas ferramentas isso vai ajudar muito', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-27 23:09:20', '2026-07-27 23:09:20');

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
  `vertical_slug` varchar(255) DEFAULT NULL,
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
-- Estrutura da tabela `tool_company_profiles`
--

CREATE TABLE `tool_company_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(160) NOT NULL,
  `legal_name` varchar(200) DEFAULT NULL,
  `document` text DEFAULT NULL,
  `office_name` varchar(160) DEFAULT NULL,
  `accountant_name` varchar(160) DEFAULT NULL,
  `accountant_registration` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tool_employee_profiles`
--

CREATE TABLE `tool_employee_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_profile_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(160) NOT NULL,
  `document` text DEFAULT NULL,
  `department` varchar(120) DEFAULT NULL,
  `role` varchar(120) DEFAULT NULL,
  `defaults` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tool_feedback`
--

CREATE TABLE `tool_feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `tool_slug` varchar(120) NOT NULL,
  `tool_name` varchar(255) NOT NULL,
  `tool_version` varchar(32) NOT NULL,
  `type` varchar(40) NOT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'new',
  `message` text NOT NULL,
  `attempted_action` text DEFAULT NULL,
  `path` varchar(512) NOT NULL,
  `url` text NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `user_agent` varchar(1024) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `tool_feedback`
--

INSERT INTO `tool_feedback` (`id`, `user_id`, `session_id`, `tool_slug`, `tool_name`, `tool_version`, `type`, `status`, `message`, `attempted_action`, `path`, `url`, `context`, `user_agent`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'L0fEIiAEZArSht6oQUUgpuxWKRBiYuaF7fapf7SC', 'calculadora-simples-nacional', 'Calculadora de Simples Nacional', '1.2.0', 'other', 'in_review', 'testes Conte mais detalhes', NULL, '/ferramentas/calculadora-simples-nacional', 'http://localhost:8000/ferramentas/calculadora-simples-nacional', '{\"source\":\"right-sidebar\",\"route_name\":\"tools.calculadora-simples-nacional.index\",\"page_title\":\"Calculadora de Simples Nacional \\u2014 Prazzu Tools\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-24 18:21:57', '2026-07-24 18:12:18', '2026-07-24 18:21:57'),
(2, 1, '7RLdo0x2LzvAIBO2oNivkR5TVOsgKMtwD8NThRbx', 'calculadora-hora-extra', 'Calculadora de Hora Extra, Adicional Noturno e DSR', '1.0.0', 'suggestion', 'new', 'por que não cria uma ferramenta xxx xxx xxxx xxxxx xxxxxx xxxxx', NULL, '/ferramentas/calculadora-hora-extra', 'http://localhost:8000/ferramentas/calculadora-hora-extra', '{\"source\":\"right-sidebar\",\"route_name\":\"tools.calculadora-hora-extra.index\",\"page_title\":\"Calculadora de Hora Extra, Adicional Noturno e DSR\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', NULL, '2026-07-27 23:10:34', '2026-07-27 23:10:34');

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

--
-- Extraindo dados da tabela `tool_runs`
--

INSERT INTO `tool_runs` (`id`, `user_id`, `tool_slug`, `tool_version`, `schema_version`, `rule_version`, `reference_date`, `status`, `input_payload`, `result_payload`, `normative_references`, `error_code`, `started_at`, `finished_at`, `expires_at`, `created_at`, `updated_at`) VALUES
('019f953d-7fd2-72a2-8151-a8d1c0557b4c', 1, 'calculadora-de-rescisao', '1.0.0', 1, '1.4.0', '2026-07-18', 'succeeded', 'eyJpdiI6Ii93VzJkVGZJYWJ1TS9tVzRiL2dubFE9PSIsInZhbHVlIjoiZm1OSVFrYTZzZGI0MGhFSmxuVzE2bUlZV1MwY2ZYbHdnSUltSmZZWGdlWUw3SVJEK1dON3g1Tk9lV043M0pwOFFxS09waUNhcng4OWxhZlZNSk5KQU5RZGkvaUtRbUVESEJqdlBYOEFuckI0Wlc2UkZTc2U3SlRmdlhGVUd2clNSak5XUUxiK1BBR285dU9lRkdVS2k5Tld2cUpVTldSZys2dzNOU0trSGExN0J1ZkNGdy8xZHdxaGYvQVA3TVBxck10NEY1c1gvUWcxbTdrUnRmSS9RUDAzNUYrM0ljL085elN4citEbjdvZnU4elNkMkxzcDJ6NlRrZjROSHhyQnBhQVllTy9NZTRNdTg1MEFQTHJOMTNEK0piZzdJUjVJWEJ5c2Q1cnFNS3NXbTh4dzRmWVYyaHcwbnlubGtxV2FVeEtZYlhxcldtbWRNcVU5SWo5SkhCR25TaTdzOGFZcVcxcVZHVzJGVDRtYTc0ZVAybzNsL2RpNm9FR0hkL3JRK1E2Qkx3V3pUdmxTY1VzMm1NUUwzelV6U1Qyd3M5bW5ncHpXSTA4OWlEdlh1dVBtVllDNlR2K2dNL0xDRHlURldqY2tLcGZobXFualh0MVQ1RG83UHVsRGViS2tpSkNlQU50QTRad043Rm02VllzNWFiV0J3NkJLdDZmVTVlTjdwS1c5K0FKREIySGdMbVZGQVZOMlFlWk00K2JRSzJGUEFqMzVySk9EajRuWlJHQWx0bmdKakVLeE9MN01sYlhOVUdiZUp4ekJoamxmSUJoQkVPZlBXaCtxbHNXdS9ScVRiNXBHSXQyRVBSbDRDWHdleTBWNllpSzhGYXJmRWVPMDlYbG5hZGtGL1preGQ1N25SUE5FME9LUFNzOS83RVE4OTZPR1RzUk11V0c0MHFFTlI4Y08zc2lrK1MvdGdxamtSV04vendSMWhaYk9HQURCeGNHb0t4eHYremI2eHpBVEFZajliQStJZjBNYk5JaGtVWTJXcHd6Mmltd1F4MVUwQnpZY1ZRRFhLekU5VUhXTlBGbDdmVFpwYjZGR1N0VUpzME0reTljWUNWY3psVnBrdStjQ0JNbk50My9hNkxvKyIsIm1hYyI6IjkxMmJlZWY3OGZhYmM0ODVlYjE1Njg1NWNiYjM2YTVhZDlmMDJmOTYwMzM3MWFhNzcxMGJhZTZjMzY3OTJlNTgiLCJ0YWciOiIifQ==', 'eyJpdiI6ImgzWHhuVXNrNjhvRUxQMzhQTmF0c2c9PSIsInZhbHVlIjoidUlaRlZVNDB1eWEzU09NamR0YUFpNHZPL2FXZ282WXRTTHRDYUJ4WWFqeG50MGtJK2lwdStXdlVVNXRFdmhyUFFMVkNLM0VpVlhWbzg5TFBMVmVjRWEyT0FjK2xKSE0yTmJFQnR6TVkwZkc4WEN0blVpQnI1TWs0WC80UHlOZ3VNcWI2Tmt2L1VpdUg1MmhkTU1Uek50MVFkN0dFQ3FnV2hSL0NTS2xnUVRrcG9UNTZnMThiV0xJeHBqMFo2alZlb2VyUUZSakFUb01ZcTJNMnBka1k0SEE2UTNvVVVMZkNUNEpldVJHRk0wS2xVeisvbU1tekRFWEttVjhGYTU4UzcwVVJFbnU5TklmWjJwaWdqOXZ3QVNxUDVpdEJXM1lXNzNKSHNwUkc0eGpLaG5zWndFUWpLYXEydjByVVpHRENpMzFZQWF4ekhtQ21YaG5ZQzJpZDgxM2hhUkFoc1VVaE1jZGhUY3FSVE1lT2RCbVFtaStvU1UyeWtvaG1SWDZlcGxMWHhqeTdycCs5SXFaV3JrYm1sWldxQVM4U09nL2I1UlkyY0lVTXcvOWRLU1lJb2NHbnUyQjUzTksvbVRVVlFrL3hzcytXY1JqeXMxSm9xQkdtd1JsdVY0V2ZtaTZ3dHczdUJ4WnhNU3pNUEVMQ3d2V3RYcmVjR01ZZHN5YUhjQ3FTMEpXRGs3RU9qVytqTldvK0pMS1lLL2hseFk3Nm91S09BK2l6NmJwK2k4Qkp5WU9vdnU2SUxyU2MyVjIxdlBWWDR3OUkzUFdWSG9ST1FFTVY1SUFnbG9oUUxRc0JoQ2kzbWZHSWVCRlJzTnlyc0pIOW12WjgrQ04yZmVlY0hORkhuMXlYL0YraVJTeUVBVEREMFkwR3hGYjNZejhNMzlZMnhzT1duVmtVWjRYNldZUmFOZzB6QWtURGI1ZTlDdDRUQTA3QmlqQWRnYnppMzRRM05QMG1XendBOWc5SkRYZG5wQlVSSEMyTHkwdjM1OVdLWUZkS3I2TGV6bi9yM29rUEJCOXBRUlRyR2dobW1MaTlSTWFFMm13RGVSeld2RXNuZWhpZzFCRmw1dXJQenFmQW1RWi9lbmF5QTNBSEJQRElxRHZUcTVFbkhCMkQ1a0lHbEpmaUpURlYxT3pNN2FMTllMd29RZ1RQT25qTUlaczFlRHZFSVNWNTgzcnZ4Y1hMWE9WVDFwc25VNUVHTTFqc0VGNmRETlpQMVBQTVVuTkZCemt1aEFqakRwcnRCYnk3eExVY2tGaUgrY0xsTWI0allINm4xMi9LUzNQWnVyNlFDMlpGenN6WE8rQWR5NUlmMmNBY0xhRUJ6eFBlcFJnRmV6K01kNWVrNmtTWjlIeDMzYkJlaDcxb0hOdFRiVWJuRnk5KzNZUDRCRGhzT1hDWlVnU0ErNXZaNHB4amw2THFLMzNzMUpPZ0dhTU5jKzBPM0NvWDhlR3JSOS93ZWpmcElIUmx2REpVbWY4bE14a3ZrK2MwNFhzY01mTjZvcXY1cjRPYUdiOTl4WFVCMVUvQW56L2t0dlc4Q2RHcXBsUFBzZXNVTk1yRXBFelZtZTVhd2lvOWdkTWhpK1dLT3BkUzk4Y3p0MWRSUk5haGc1bDlvOHpsUTdVKzN2SSs4cWp1VFltMDAzRHo3WmN0WWV3Slg3OWZIUTloOGkvbWRZRGVIS1lUNkY4NkpEL1d0WnNieFdLeUw2SDRmQ05jdFpmZVg3K3lSOWo2TUhCdm1CSmowRTRZMnJmL2ZYeEtkL29OMHM1K2YyanJNYW93b1BVWUlCNXYwV3F6WWhJbUloa3JPVGxrWUQ3TEFmM1oyM0ZCbGxsTUxuaG16TkdMck1NcTUwMXZITWFnS3JzaWVwQXRXOHhXdlppRCtXb1lTOWoxVXdBWEtoeTRwZHc3WXFQMjBwa0NzM251NUhVYnppNGxqRkIzWUtBeDN4MTZZc2JQNVQvTEFVckMvR2x5MUhNWkwxRmJlQW93L0xBK29YZGExeDh4T3ZVRWpwZEFnRjF3MWRJbFJXWFdJQVpPdE9EUyt1Q2plM1V4UDVtN1lUNzJrNHlvUjhHdkswZWpWTGdPRzVyaXBvVFRLbjNvbUFld0JOZ1pWMHhadHhYZ1JTdEpSTXdtRUZ3TEhjM29WZmk2Y3c9PSIsIm1hYyI6ImVhMDAxYzA2ZmQ1MzdjOGJjN2QwZDUwZTZmMWRlNjIxN2Q2MTgxMzUwYTM2ZTA4YTMzOGE3Y2I5ODFhMWRkODYiLCJ0YWciOiIifQ==', 'eyJpdiI6IjFHTk1ZUkxMZlJuQjB5d3JBQjRJWHc9PSIsInZhbHVlIjoiTlM5d2NGQ3FVQkRYSjZzM1hFTVdEQT09IiwibWFjIjoiZjY5NTJhNjc4NmIyY2M5MGE3ZWQ3NGFkYzZhNWZmZWM4NzRkNDM4NWUxNmMxMzE1M2VjNzNkNDRjYzQ1MzhlOSIsInRhZyI6IiJ9', NULL, '2026-07-24 17:47:43', '2026-07-24 17:47:43', '2027-01-20 17:47:43', '2026-07-24 20:47:43', '2026-07-24 20:47:43');

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
-- Estrutura da tabela `tool_suggestions`
--

CREATE TABLE `tool_suggestions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `problem` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `tool_suggestions`
--

INSERT INTO `tool_suggestions` (`id`, `user_id`, `session_id`, `name`, `email`, `problem`, `created_at`, `updated_at`) VALUES
(1, NULL, 'L0fEIiAEZArSht6oQUUgpuxWKRBiYuaF7fapf7SC', 'ferramenta teste 1', 'ricardo.s.a.dev@gmail.com', 'teste xxxx xxxx xxxx xxxx xxxx xxxxx xxxx xxxxx xxxxx xxxxx', '2026-07-24 19:51:32', '2026-07-24 19:51:32');

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
  ADD KEY `analytics_sessions_acquisition_campaign_started` (`acquisition_campaign_identifier`,`started_at`),
  ADD KEY `analytics_sessions_vertical_started` (`vertical_slug`,`started_at`);

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
  ADD KEY `blog_categories_is_active_index` (`is_active`),
  ADD KEY `blog_categories_vertical_slug_index` (`vertical_slug`);

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
  ADD KEY `blog_posts_category_id_foreign` (`category_id`),
  ADD KEY `blog_posts_vertical_slug_index` (`vertical_slug`);

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
  ADD KEY `analytics_events_acquisition_campaign_occurred` (`acquisition_campaign_identifier`,`occurred_at`),
  ADD KEY `analytics_events_vertical_occurred` (`vertical_slug`,`occurred_at`);

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
-- Índices para tabela `tool_company_profiles`
--
ALTER TABLE `tool_company_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tool_company_profiles_owner_name_unique` (`user_id`,`name`),
  ADD KEY `tool_company_profiles_owner_lookup` (`user_id`,`updated_at`);

--
-- Índices para tabela `tool_employee_profiles`
--
ALTER TABLE `tool_employee_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tool_employee_profiles_owner_name_unique` (`user_id`,`company_profile_id`,`name`),
  ADD KEY `tool_employee_profiles_company_profile_id_foreign` (`company_profile_id`),
  ADD KEY `tool_employee_profiles_owner_company_lookup` (`user_id`,`company_profile_id`,`updated_at`);

--
-- Índices para tabela `tool_feedback`
--
ALTER TABLE `tool_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tool_feedback_user_id_foreign` (`user_id`),
  ADD KEY `tool_feedback_tool_slug_status_created_at_index` (`tool_slug`,`status`,`created_at`),
  ADD KEY `tool_feedback_type_created_at_index` (`type`,`created_at`),
  ADD KEY `tool_feedback_session_id_index` (`session_id`),
  ADD KEY `tool_feedback_tool_slug_index` (`tool_slug`),
  ADD KEY `tool_feedback_type_index` (`type`),
  ADD KEY `tool_feedback_status_index` (`status`);

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
-- Índices para tabela `tool_suggestions`
--
ALTER TABLE `tool_suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tool_suggestions_user_id_foreign` (`user_id`),
  ADD KEY `tool_suggestions_created_at_index` (`created_at`),
  ADD KEY `tool_suggestions_session_id_index` (`session_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `platform_analytics_events`
--
ALTER TABLE `platform_analytics_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1121;

--
-- AUTO_INCREMENT de tabela `receipt_party_profiles`
--
ALTER TABLE `receipt_party_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tool_company_profiles`
--
ALTER TABLE `tool_company_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tool_employee_profiles`
--
ALTER TABLE `tool_employee_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tool_feedback`
--
ALTER TABLE `tool_feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tool_suggestions`
--
ALTER TABLE `tool_suggestions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Limitadores para a tabela `tool_company_profiles`
--
ALTER TABLE `tool_company_profiles`
  ADD CONSTRAINT `tool_company_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tool_employee_profiles`
--
ALTER TABLE `tool_employee_profiles`
  ADD CONSTRAINT `tool_employee_profiles_company_profile_id_foreign` FOREIGN KEY (`company_profile_id`) REFERENCES `tool_company_profiles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tool_employee_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `tool_feedback`
--
ALTER TABLE `tool_feedback`
  ADD CONSTRAINT `tool_feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Limitadores para a tabela `tool_suggestions`
--
ALTER TABLE `tool_suggestions`
  ADD CONSTRAINT `tool_suggestions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
