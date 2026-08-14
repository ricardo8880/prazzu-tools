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
    name: AnalyticsEventName::ToolCalculationCompleted->value,
    channel: 'tool',
    properties: ['tool' => 'slug-da-ferramenta'],
));
```

O método legado `record()` permanece apenas para compatibilidade. Código novo deve preferir `track()`.

O nome deve vir de `AnalyticsEventName`; strings literais de eventos não devem ser adicionadas ao código de produção.

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
- `analytics:check`

Antes de publicar, execute:

```bash
php artisan analytics:check
php artisan route:list
php artisan test
php artisan migrate --pretend
```

## Contrato de jornada das ferramentas

Ferramentas que precisem declarar formulários, etapas, campos e ações mensuráveis devem implementar `App\Core\Tools\Analytics\Contracts\HasAnalyticsJourney` e devolver um `ToolAnalyticsJourney`.

O contrato declara apenas identificadores semânticos. Rótulos digitados, valores de campos, documentos, nomes, e-mails, CPF, CNPJ e qualquer conteúdo calculado não pertencem à telemetria.

O endpoint `POST /analytics/tools` aceita os eventos públicos de jornada e somente os metadados definidos por `ToolAnalyticsMetadata`. Chaves desconhecidas são descartadas; tipos, limites e identificadores são validados antes da publicação.

A implementação dos módulos deve reutilizar esse contrato em vez de criar listeners, payloads ou serviços de Analytics próprios.

## Métricas de UX e retorno

O painel de ferramentas usa a jornada declarada pelas 50 ferramentas para separar três etapas de experiência:

1. `tool.opened` — abriu a ferramenta;
2. `tool.started` — começou a interagir com a tarefa principal;
3. `tool.result.viewed` — o resultado principal chegou efetivamente à área visível.

As taxas de início e de resultado após início devem ser usadas para localizar a transição com maior perda antes de propor um redesign. `tool.abandoned`, `tool.validation.error`, etapa e campo complementam o diagnóstico, mas não substituem o funil.

A métrica central de retorno usa resultados válidos concluídos (`tool.calculation.completed` e o evento oficial de lote do Validador de Documentos):

- **Problemas resolvidos**: quantidade de resultados válidos concluídos;
- **Pessoas que resolveram**: usuários autenticados ou visitantes persistentes identificáveis que concluíram pelo menos um resultado;
- **Voltaram e resolveram**: essas pessoas concluíram novamente em pelo menos dois dias distintos dentro do período selecionado;
- **Taxa de retorno**: `voltaram e resolveram / pessoas que resolveram`.

Dois cálculos no mesmo dia não contam como retorno. Sessões isoladas e eventos sem identidade persistente também não são usados para afirmar recorrência, evitando inflar retenção. Nenhum valor digitado, documento ou conteúdo do resultado é necessário para essas métricas.
