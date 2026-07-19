# Catálogo de eventos do Analytics

Este documento registra o estado encontrado no início da padronização do Analytics.
O `README.md` da raiz continua sendo a regra superior do projeto.

## Regra oficial

Eventos novos devem usar nomes em inglês, minúsculos e separados por pontos:

```text
dominio.entidade.acao
```

Exemplos oficiais:

```text
page.viewed
blog.reading.started
tool.calculation.completed
account.created
subscription.started
```

O catálogo tipado está em:

```text
app/Core/Analytics/Domain/Enums/AnalyticsEventName.php
```

## Compatibilidade do Lote 1

O Lote 1 não altera gravação, consultas, dashboards, funis ou dados históricos.
Os nomes atuais continuam sendo aceitos por `AnalyticsEvent` exatamente como antes.

O resolvedor abaixo apenas registra a equivalência entre nomes legados e nomes oficiais; ele ainda não está conectado ao fluxo de persistência ou leitura:

```text
app/Core/Analytics/Domain/Services/AnalyticsEventNameResolver.php
```

## Inventário de aliases conhecidos

| Nome atual/legado | Nome oficial planejado |
|---|---|
| `blog_post_view` | `blog.post.viewed` |
| `blog_reading_started` | `blog.reading.started` |
| `blog_reading_completed` | `blog.reading.completed` |
| `blog_abandoned` | `blog.reading.abandoned` |
| `blog_share` | `blog.shared` |
| `blog_download` | `blog.downloaded` |
| `blog_comment` | `blog.commented` |
| `blog_tool_click` | `blog.tool.clicked` |
| `blog_scroll` | `blog.scroll.measured` |
| `blog_time_spent` | `blog.time.spent` |
| `tool.calculation_started` | `tool.calculation.started` |
| `tool.calculation_completed` | `tool.calculation.completed` |
| `tool.time_spent` | `tool.time.spent` |
| `tool.history_viewed` | `tool.history.viewed` |
| `tool.plus_used` | `tool.plus.used` |
| `tool.exported` | `tool.result.exported` |
| `result.exported` | `tool.result.exported` |
| `user.registered` | `account.created` |
| `plus.subscribed` | `subscription.started` |
| `business_document_validator.batch_processed` | `business-document-validator.batch.processed` |
| `business_document_validator.batch_exported` | `business-document-validator.batch.exported` |

## Pontos consumidores protegidos antes da troca

A próxima etapa deve considerar, em conjunto:

- captura no middleware e no JavaScript;
- blog e ferramentas;
- configuração de dashboard e funis;
- queries de aquisição, audiência, blog, ferramentas e executivo;
- insights;
- relatórios e exportações;
- testes e dados históricos.

A sequência segura permanece:

```text
ler legado e oficial
→ escrever somente oficial
→ migrar histórico
→ validar equivalência
→ remover compatibilidade desnecessária
```

## Compatibilidade e normalização histórica

A aplicação grava somente nomes canônicos. Consultas continuam aceitando aliases legados durante a transição.

A migration `2026_07_15_001300_normalize_analytics_event_names.php` converte os nomes históricos conhecidos de forma idempotente. Ela não altera propriedades, datas, visitantes, sessões ou demais dimensões dos eventos.

Eventos de negócio não são inferidos a partir de qualquer requisição `POST`. O middleware registra `tool.calculation.completed` somente para ações explicitamente classificadas como produtoras de resultado válido. Operações de CRM, propostas, importações preliminares, repetições de histórico e exclusões não contam como cálculos concluídos.
