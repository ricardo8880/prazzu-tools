# Prazzu — Roteamento Canônico Multi-Vertical

## Decisão consolidada

A URL pública determina a vertical ativa. O namespace canônico é `\/tools\/{vertical}`.
A aplicação continua única; a vertical é contexto e não uma aplicação separada.

## Superfícies por vertical

- `\/tools\/{vertical}` — Home;
- `\/tools\/{vertical}\/ferramentas`;
- `\/tools\/{vertical}\/ferramentas\/{slug}`;
- `\/tools\/{vertical}\/blog`;
- `\/tools\/{vertical}\/recursos`;
- sitemaps de Blog e ferramentas dentro do mesmo namespace.

Home, catálogo, Blog, Recursos, SEO, breadcrumbs, recomendações e Analytics usam o
mesmo `VerticalContext` resolvido pela URL. A URL tem prioridade sobre sessão,
AcquisitionContext e vertical padrão.

## Slugs públicos

O identificador interno não precisa ser exposto na URL. No estado atual:

- `contabilidade` -> `contabil`;
- `rh` -> `rh`.

O mapeamento pertence a `config/verticals.php`.

## Isolamento

Uma ferramenta só pode responder dentro da vertical declarada em seu `ToolManifest`.
Exemplo: `calculadora-turnover` responde em `\/tools\/rh\/ferramentas\/calculadora-turnover`
e deve retornar 404 quando chamada sob `\/tools\/contabil`. O mesmo princípio vale
para posts e Recursos.

## Globais

Somente `\/planos` e `\/sobre` são superfícies públicas de conteúdo globais.
Autenticação, Admin, Analytics e demais serviços técnicos continuam compartilhados,
mas não representam sites/verticais independentes.

## Compatibilidade

`\/`, `\/ferramentas`, `\/blog` e `\/recursos` antigos funcionam como pontes para as
URLs canônicas. Não criar infraestrutura paralela para preservar caminhos legados.
