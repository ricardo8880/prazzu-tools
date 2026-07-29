# Capturador frontend das jornadas — Lote 2

## Regra de ativação

O capturador compartilhado é carregado pela entrada global, mas permanece inativo até que o módulo implemente `HasAnalyticsJourney`. Somente formulários resolvidos pela declaração do módulo recebem listeners.

Ele não procura formulários `POST` genericamente e não presume que histórico, favoritos, exclusão, autenticação, exportação ou qualquer outra ação seja um cálculo.

## Convenções

Cada jornada pode declarar seletores CSS explícitos. Quando omitidos, aplicam-se as convenções:

- formulário: `[data-analytics-form="<form-key>"]`;
- campo: `[data-analytics-field="<field-key>"]`;
- resultado: `[data-analytics-result="<form-key>"]`;
- ação: elemento com `data-analytics-action` e `data-analytics-form`.

Ações reconhecidas pelo capturador compartilhado:

- `calculate` no envio do formulário;
- `export` com `data-analytics-format`;
- `share` com `data-analytics-method`.

A ação só é publicada se estiver declarada na jornada do formulário.

## Privacidade

O JavaScript nunca serializa `FormData`, valores, texto, ficheiros ou resultados. Os eventos contêm apenas chaves semânticas declaradas, contagens, percentagens, códigos nativos de validação e tempos.

## Eventos

- Primeiro foco ou interação: `tool.started`.
- Primeira visita a uma etapa: `tool.step.changed`.
- Primeiro preenchimento válido de cada campo: `tool.field.completed`.
- Primeiro erro de cada tipo por campo: `tool.validation.error`.
- Envio: `tool.calculation.started`.
- Retorno com marcador de resultado: `tool.calculation.executed` e `tool.result.viewed`.
- Ações declaradas: `tool.result.exported` e `tool.shared`.
- Saída após iniciar, sem envio ou resultado: `tool.abandoned`.

## Continuidade

O Lote 3 deve selecionar pilotos representativos, implementar `HasAnalyticsJourney` nos respetivos módulos e adicionar os marcadores de formulário, campo, resultado e ações necessários. Nenhuma ferramenta foi ativada neste lote.
