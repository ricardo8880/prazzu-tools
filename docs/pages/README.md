# Documentação oficial das páginas

Esta pasta contém a documentação oficial das páginas atualmente identificadas no projeto. Ela deve permanecer sincronizada com as views, rotas e fluxos da aplicação.

## Regra obrigatória

Antes de modificar qualquer página, leia o `README.md` da raiz, este arquivo e todos os documentos relacionados à página afetada. Se uma página não estiver listada ou não possuir documentação, crie sua documentação antes de concluir a alteração.

Os componentes reutilizáveis, layouts e partials não são tratados como páginas independentes nesta lista; suas mudanças devem ser registradas nos documentos de todas as páginas cujo comportamento seja afetado.

## Padrão para novas páginas

Cada nova documentação deve informar, no mínimo: objetivo, funcionamento, implementação principal, conteúdos, estados, dependências, regras de manutenção e validação mínima.

## Índice


### Documento de saída/ impressão

- [Batch Report.Blade](app/Tools/BusinessDocumentValidator/pages/print/batch-report.blade.md) — `app/Tools/BusinessDocumentValidator/Resources/views/print/batch-report.blade.php`
- [Resumo orientativo](app/Tools/FederalPaymentGuideGenerator/pages/pdf/report.blade.md) — `app/Tools/FederalPaymentGuideGenerator/Resources/views/pdf/report.blade.php`
- [Dados considerados](app/Tools/LaborTerminationCalculator/pages/pdf/report.blade.md) — `app/Tools/LaborTerminationCalculator/Resources/views/pdf/report.blade.php`
- [Report.Blade](app/Tools/MarginMarkupCalculator/pages/pdf/report.blade.md) — `app/Tools/MarginMarkupCalculator/Resources/views/pdf/report.blade.php`
- [Resumo do cálculo](app/Tools/ProLaboreProfitDistributionCalculator/pages/pdf/report.blade.md) — `app/Tools/ProLaboreProfitDistributionCalculator/Resources/views/pdf/report.blade.php`
- [Resumo](app/Tools/TaxRegimeComparator/pages/pdf/report.blade.md) — `app/Tools/TaxRegimeComparator/Resources/views/pdf/report.blade.php`
- [Resumo](app/Tools/VacationCalculator/pages/pdf/report.blade.md) — `app/Tools/VacationCalculator/Resources/views/pdf/report.blade.php`
- [conteúdo dinâmico](exports/browser-print.blade.md) — `resources/views/exports/browser-print.blade.php`

### Página administrativa

- [Novo contexto de aquisição | Prazzu Tools](admin/acquisition/create.blade.md) — `resources/views/admin/acquisition/create.blade.php`
- [Editar contexto de aquisição | Prazzu Tools](admin/acquisition/edit.blade.md) — `resources/views/admin/acquisition/edit.blade.php`
- [Jornadas de aquisição | Prazzu Tools](admin/acquisition/index.blade.md) — `resources/views/admin/acquisition/index.blade.php`
- [Aquisição | Analytics | Prazzu Tools](admin/analytics/acquisition.blade.md) — `resources/views/admin/analytics/acquisition.blade.php`
- [Público | Analytics | Prazzu Tools](admin/analytics/audience.blade.md) — `resources/views/admin/analytics/audience.blade.php`
- [Campanhas inteligentes](admin/analytics/campaigns.blade.md) — `resources/views/admin/analytics/campaigns.blade.php`
- [Funis | Analytics | Prazzu Tools](admin/analytics/funnels.blade.md) — `resources/views/admin/analytics/funnels.blade.php`
- [Dashboard Executivo | Analytics | Prazzu Tools](admin/analytics/index.blade.md) — `resources/views/admin/analytics/index.blade.php`
- [Insights Inteligentes | Analytics | Prazzu Tools](admin/analytics/insights.blade.md) — `resources/views/admin/analytics/insights.blade.php`
- [Tempo Real | Analytics | Prazzu Tools](admin/analytics/realtime.blade.md) — `resources/views/admin/analytics/realtime.blade.php`
- [Relatórios — Analytics](admin/analytics/reports.blade.md) — `resources/views/admin/analytics/reports.blade.php`
- [SEO:](admin/analytics/seo-post.blade.md) — `resources/views/admin/analytics/seo-post.blade.php`
- [SEO Analytics | Prazzu Tools](admin/analytics/seo.blade.md) — `resources/views/admin/analytics/seo.blade.php`
- [conteúdo dinâmico](admin/analytics/tool.blade.md) — `resources/views/admin/analytics/tool.blade.php`
- [Ferramentas | Analytics | Prazzu Tools](admin/analytics/tools.blade.md) — `resources/views/admin/analytics/tools.blade.php`
- [Analytics:](admin/blog/analytics-post.blade.md) — `resources/views/admin/blog/analytics-post.blade.php`
- [Analytics do blog | Prazzu Tools](admin/blog/analytics.blade.md) — `resources/views/admin/blog/analytics.blade.php`
- [Nova categoria do blog | Prazzu Tools](admin/blog/categories/create.blade.md) — `resources/views/admin/blog/categories/create.blade.php`
- [Editar categoria do blog | Prazzu Tools](admin/blog/categories/edit.blade.md) — `resources/views/admin/blog/categories/edit.blade.php`
- [Categorias do blog | Prazzu Tools](admin/blog/categories/index.blade.md) — `resources/views/admin/blog/categories/index.blade.php`
- [Nova postagem | Prazzu Tools](admin/blog/create.blade.md) — `resources/views/admin/blog/create.blade.php`
- [Editar postagem | Prazzu Tools](admin/blog/edit.blade.md) — `resources/views/admin/blog/edit.blade.php`
- [Administrar blog | Prazzu Tools](admin/blog/index.blade.md) — `resources/views/admin/blog/index.blade.php`
- [Administração | Prazzu Tools](admin/index.blade.md) — `resources/views/admin/index.blade.php`

### Página da plataforma

- [Minha conta — Prazzu Tools](account/show.blade.md) — `resources/views/account/show.blade.php`
- [Sobre o Prazzu Tools — Plataforma de ferramentas contábeis](pages/about.blade.md) — `resources/views/pages/about.blade.php`
- [conteúdo dinâmico](pages/content.blade.md) — `resources/views/pages/content.blade.php`
- [Planos — Prazzu Tools](pages/plans.blade.md) — `resources/views/pages/plans.blade.php`
- [Como precificar honorários contábeis com método](pages/resources/guides/accounting-fees-pricing.blade.md) — `resources/views/pages/resources/guides/accounting-fees-pricing.blade.php`
- [Recursos profissionais — Prazzu Tools](pages/resources/index.blade.md) — `resources/views/pages/resources/index.blade.php`
- [conteúdo dinâmico](pages/resources/listing.blade.md) — `resources/views/pages/resources/listing.blade.php`
- [Levantamento para precificação de honorários contábeis](pages/resources/models/accounting-fees-survey.blade.md) — `resources/views/pages/resources/models/accounting-fees-survey.blade.php`
- [Sugerir ferramenta — Prazzu Tools](pages/suggest-tool.blade.md) — `resources/views/pages/suggest-tool.blade.php`
- [conteúdo dinâmico](pages/tools/index.blade.md) — `resources/views/pages/tools/index.blade.php`
- [Blog Sitemap.Blade](seo/blog-sitemap.blade.md) — `resources/views/seo/blog-sitemap.blade.php`
- [Prazzu Tools — Ferramentas para contabilidade](welcome.blade.md) — `resources/views/welcome.blade.php`

### Página de autenticação

- [Recuperar senha — Prazzu Tools](auth/forgot-password.blade.md) — `resources/views/auth/forgot-password.blade.php`
- [Entrar — Prazzu Tools](auth/login.blade.md) — `resources/views/auth/login.blade.php`
- [Criar conta — Prazzu Tools](auth/register.blade.md) — `resources/views/auth/register.blade.php`
- [Criar nova senha — Prazzu Tools](auth/reset-password.blade.md) — `resources/views/auth/reset-password.blade.php`
- [Confirmar e-mail — Prazzu Tools](auth/verify-email.blade.md) — `resources/views/auth/verify-email.blade.php`

### Página de blog

- [Blog de contabilidade — Prazzu Tools](blog/index.blade.md) — `resources/views/blog/index.blade.php`
- [conteúdo dinâmico](blog/show.blade.md) — `resources/views/blog/show.blade.php`

### Página de ferramenta

- [Reajuste de Honorários Contábeis — Prazzu Tools](app/Tools/AccountingFeesCalculator/pages/adjustments/index.blade.md) — `app/Tools/AccountingFeesCalculator/Resources/views/adjustments/index.blade.php`
- [Contrato de Prestação de Serviços Contábeis — Prazzu Tools](app/Tools/AccountingFeesCalculator/pages/contract.blade.md) — `app/Tools/AccountingFeesCalculator/Resources/views/contract.blade.php`
- [Calculadora de Honorários Contábeis — Prazzu Tools](app/Tools/AccountingFeesCalculator/pages/index.blade.md) — `app/Tools/AccountingFeesCalculator/Resources/views/index.blade.php`
- [Proposta Comercial de Serviços Contábeis — Prazzu Tools](app/Tools/AccountingFeesCalculator/pages/proposal.blade.md) — `app/Tools/AccountingFeesCalculator/Resources/views/proposal.blade.php`
- [Validador Inteligente de CNPJ, CPF e IE — Prazzu Tools](app/Tools/BusinessDocumentValidator/pages/index.blade.md) — `app/Tools/BusinessDocumentValidator/Resources/views/index.blade.php`
- [Gerador Inteligente de DARF/GPS — Prazzu Tools](app/Tools/FederalPaymentGuideGenerator/pages/index.blade.md) — `app/Tools/FederalPaymentGuideGenerator/Resources/views/index.blade.php`
- [Conversor Fiscal de XML — Prazzu Tools](app/Tools/FiscalXmlConverter/pages/index.blade.md) — `app/Tools/FiscalXmlConverter/Resources/views/index.blade.php`
- [Calculadora de Rescisão Trabalhista — Prazzu Tools](app/Tools/LaborTerminationCalculator/pages/index.blade.md) — `app/Tools/LaborTerminationCalculator/Resources/views/index.blade.php`
- [Calculadora de Margem, Markup e Formação de Preço — Prazzu Tools](app/Tools/MarginMarkupCalculator/pages/index.blade.md) — `app/Tools/MarginMarkupCalculator/Resources/views/index.blade.php`
- [Calculadora de Pró-Labore e Distribuição de Lucros — Prazzu Tools](app/Tools/ProLaboreProfitDistributionCalculator/pages/index.blade.md) — `app/Tools/ProLaboreProfitDistributionCalculator/Resources/views/index.blade.php`
- [Calculadora de Simples Nacional — Prazzu Tools](app/Tools/SimplesNacionalCalculator/pages/index.blade.md) — `app/Tools/SimplesNacionalCalculator/Resources/views/index.blade.php`
- [Comparador Tributário — Prazzu Tools](app/Tools/TaxRegimeComparator/pages/index.blade.md) — `app/Tools/TaxRegimeComparator/Resources/views/index.blade.php`
- [Calculadora de Férias — Prazzu Tools](app/Tools/VacationCalculator/pages/index.blade.md) — `app/Tools/VacationCalculator/Resources/views/index.blade.php`
- [Planejamento de férias](app/Tools/VacationCalculator/pages/planner.blade.md) — `app/Tools/VacationCalculator/Resources/views/planner.blade.php`

### Página de histórico

- [Histórico de Honorários Contábeis — Prazzu Tools](app/Tools/AccountingFeesCalculator/pages/history/index.blade.md) — `app/Tools/AccountingFeesCalculator/Resources/views/history/index.blade.php`
- [Histórico do Validador — Prazzu Tools](app/Tools/BusinessDocumentValidator/pages/history/index.blade.md) — `app/Tools/BusinessDocumentValidator/Resources/views/history/index.blade.php`
- [Histórico de DARF/GPS](app/Tools/FederalPaymentGuideGenerator/pages/history/index.blade.md) — `app/Tools/FederalPaymentGuideGenerator/Resources/views/history/index.blade.php`
- [Histórico do Conversor Fiscal de XML](app/Tools/FiscalXmlConverter/pages/history/index.blade.md) — `app/Tools/FiscalXmlConverter/Resources/views/history/index.blade.php`
- [Histórico de Rescisões — Prazzu Tools](app/Tools/LaborTerminationCalculator/pages/history/index.blade.md) — `app/Tools/LaborTerminationCalculator/Resources/views/history/index.blade.php`
- [Detalhes do cálculo de rescisão — Prazzu Tools](app/Tools/LaborTerminationCalculator/pages/history/show.blade.md) — `app/Tools/LaborTerminationCalculator/Resources/views/history/show.blade.php`
- [Histórico de Margem e Markup — Prazzu Tools](app/Tools/MarginMarkupCalculator/pages/history/index.blade.md) — `app/Tools/MarginMarkupCalculator/Resources/views/history/index.blade.php`
- [Detalhes de Margem e Markup — Prazzu Tools](app/Tools/MarginMarkupCalculator/pages/history/show.blade.md) — `app/Tools/MarginMarkupCalculator/Resources/views/history/show.blade.php`
- [Histórico — Pró-Labore e Lucros](app/Tools/ProLaboreProfitDistributionCalculator/pages/history/index.blade.md) — `app/Tools/ProLaboreProfitDistributionCalculator/Resources/views/history/index.blade.php`
- [Simulação salva — Pró-Labore e Lucros](app/Tools/ProLaboreProfitDistributionCalculator/pages/history/show.blade.md) — `app/Tools/ProLaboreProfitDistributionCalculator/Resources/views/history/show.blade.php`
- [Histórico — Comparador Tributário](app/Tools/TaxRegimeComparator/pages/history/index.blade.md) — `app/Tools/TaxRegimeComparator/Resources/views/history/index.blade.php`
- [Comparação salva — Comparador Tributário](app/Tools/TaxRegimeComparator/pages/history/show.blade.md) — `app/Tools/TaxRegimeComparator/Resources/views/history/show.blade.php`
- [Histórico de férias](app/Tools/VacationCalculator/pages/history/index.blade.md) — `app/Tools/VacationCalculator/Resources/views/history/index.blade.php`

### Página de organização

- [Cadastrar empresa — Prazzu Tools](organizations/create.blade.md) — `resources/views/organizations/create.blade.php`
- [Convite empresarial — Prazzu Tools](organizations/invitations/show.blade.md) — `resources/views/organizations/invitations/show.blade.php`
- [conteúdo dinâmico](organizations/show.blade.md) — `resources/views/organizations/show.blade.php`
