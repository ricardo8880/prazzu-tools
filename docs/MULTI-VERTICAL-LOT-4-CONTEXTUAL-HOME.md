# PRAZZU — Evolução Multi-Vertical — Lote 4 — Home Contextual

## Objetivo

Fazer a Home pública responder ao `VerticalContext` sem criar aplicações, controllers,
views ou engines paralelas por nicho, preservando integralmente a experiência atual de
Contabilidade e mantendo `AcquisitionContext` como conceito independente.

## Base reconstruída

Este lote foi desenvolvido sobre o ZIP original do projeto com as alterações do Lote 3
reaplicadas antes do início. O Lote 3 já contém e documenta a continuidade dos Lotes 1
e 2, incluindo `Vertical`, `VerticalContext`, associação das 32 ferramentas a
`contabilidade` e filtragem do `ToolCatalog`.

Antes das mudanças foram relidos o README da raiz, os relatórios multi-vertical já
presentes e os contratos reais da Home, `ToolCatalog`, middleware e contexto de
aquisição.

## Decisão arquitetural

A Home técnica continua única:

```text
/
 -> HomeController
 -> BuildContextualHome
 -> welcome.blade.php
```

A experiência é montada por composição:

```text
VerticalContext
 -> escolhe a base da Home
 -> global quando null
 -> configuração da vertical quando existente

AcquisitionContext (opcional)
 -> personaliza Hero/CTA da base já escolhida
```

Essa ordem preserva a diferença constitucional entre os dois contextos:
`VerticalContext` define o universo de negócio e `AcquisitionContext` define intenção,
campanha ou origem específica.

## Configuração da Home

`config/home.php` preserva as chaves históricas de Contabilidade para compatibilidade
e adiciona dois contratos explícitos:

- `home.global`: experiência geral da plataforma para `VerticalContext = null`;
- `home.verticals.<slug>`: conteúdo específico de uma vertical registrada.

Contabilidade é a primeira configuração de referência. Uma vertical registrada que
ainda não possua conteúdo próprio não provoca erro nem recebe conteúdo contábil por
acidente: a Home usa o fallback global.

## Ferramentas e categorias

A Home não ganhou catálogo paralelo. `featuredTools` continua vindo de
`ToolCatalog::latest(8)` e as categorias continuam vindo de `ToolCatalog::categories()`.
Como o catálogo já respeita `VerticalContext` desde o Lote 3, a Home herda a mesma
segmentação e preserva a regra constitucional de fonte única.

A ordem editorial de lançamento continua sendo `release_order`; nenhum manifesto,
slug ou quantidade oficial de ferramentas foi alterado neste lote.

## Metadados da view

`welcome.blade.php` deixou de fixar título e description contábeis. Esses valores agora
fazem parte da base selecionada da Home. Isso evita vazamento de identidade de
Contabilidade no fallback global sem criar uma engine SEO específica, que continua
reservada ao Lote 5.

## Compatibilidade

Para a configuração padrão atual (`verticals.default = contabilidade`), Hero, CTA,
título, description, categorias e ferramentas continuam equivalentes ao comportamento
anterior. Os contratos históricos `config('home.hero')`, `config('home.cta')` e
`config('home.tools_section_title')` permanecem válidos.

## Testes e gates

Foram adicionadas proteções para:

- Home global quando `VerticalContext = null`;
- fallback global de uma vertical registrada sem configuração de Home;
- ausência de controllers específicos para Contabilidade, RH ou Financeiro;
- manutenção da configuração histórica de Contabilidade como referência;
- dependência do builder compartilhado em `VerticalContext`.

Os gates `php artisan tools:check-architecture` e `php artisan analytics:check`
continuaram aprovados. A execução PHPUnit permanece indisponível no ambiente de
montagem porque as extensões PHP `dom`, `mbstring` e `xmlwriter` não estão instaladas.
Todos os arquivos PHP alterados/adicionados passaram por `php -l`.

## Fora de escopo

Este lote não implementa:

- segunda vertical real;
- Analytics segmentado por vertical;
- engine SEO contextual;
- sitemap contextual;
- breadcrumbs contextuais;
- Admin contextual;
- observabilidade por vertical;
- infraestrutura duplicada de Home, Blog, Auth, Billing ou E2E.

## Próximo lote

O próximo passo é o **Lote 5 — Serviços Globais conscientes de vertical**. Ele deve
propagar a dimensão de vertical para Analytics, SEO, sitemap, breadcrumbs, busca,
Admin e observabilidade onde aplicável, sem criar serviços paralelos por nicho.
