# Analytics 2.0

O Analytics do Prazzu é uma infraestrutura única do Core. Blog, ferramentas, autenticação, assinatura e futuras áreas publicam eventos; nenhum módulo mantém um Analytics próprio.

## Fluxo

1. O módulo publica um `AnalyticsEvent` pelo contrato `PlatformAnalytics`.
2. `AnalyticsContextResolver` complementa o evento com visitante, sessão, aquisição, dispositivo e localização disponível.
3. `AnalyticsEventRepository` persiste o evento auditável.
4. Consultas de aplicação transformam eventos em dashboards, funis, relatórios e insights.

Ferramentas não devem importar models, repositórios ou classes de infraestrutura de `Core/Analytics`.

## Publicação de eventos

```php
$analytics->track(AnalyticsEvent::make(
    name: 'tool.calculation.completed',
    channel: 'tool',
    properties: ['tool' => 'slug-da-ferramenta'],
));
```

O método legado `record()` permanece apenas para compatibilidade. Código novo deve preferir `track()`.

## Convenção de nomes

Use nomes em inglês, minúsculos e separados por ponto:

- `page.viewed`
- `blog.reading.started`
- `tool.calculation.completed`
- `subscription.started`

Não altere o significado de um evento já publicado. Quando o payload mudar de forma incompatível, evolua a versão do schema.

## Privacidade

- Não grave IP puro; use somente o hash produzido pelo Core.
- Não coloque CPF, CNPJ, e-mail, documentos ou conteúdo calculado em `metadata`.
- Respeite consentimento e a política de retenção configurada.
- Localização deve ser agregada e utilizada somente quando disponível de forma legítima.

## Desempenho

Dashboards administrativos utilizam cache curto configurado por `ANALYTICS_DASHBOARD_CACHE_SECONDS`. O painel em tempo real não utiliza esse cache.

A retenção é aplicada diariamente pelo comando:

```bash
php artisan analytics:prune
```

Para executar manualmente com outra janela:

```bash
php artisan analytics:prune --days=365 --chunk=1000
```

## Criação de uma nova análise

1. Crie uma Query em `Application/Queries`.
2. Mantenha regras de métrica fora de controllers e views.
3. Use filtros validados por Form Request.
4. Priorize agregações no banco; não carregue todos os eventos para agrupar em PHP.
5. Adicione teste unitário ou funcional.
6. Use Bootstrap antes de criar estilos adicionais.

## Funis

Funis padrão ficam em `config/analytics.php`. Funis personalizados são persistidos em `analytics_funnels` e `analytics_funnel_steps`. As etapas são avaliadas em ordem cronológica por visitante, sessão ou usuário.

## Operação

Comandos disponíveis:

- `analytics:generate-insights`
- `analytics:run-scheduled-reports`
- `analytics:prune`

Antes de publicar, execute:

```bash
php artisan route:list
php artisan test
php artisan migrate --pretend
```
