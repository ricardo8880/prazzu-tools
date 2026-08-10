# PRAZZU — Evolução Multi-Vertical — Lote 5 — Serviços Globais

## Objetivo

Tornar os serviços globais do Prazzu conscientes de `VerticalContext` sem duplicar
infraestrutura. Vertical deve funcionar como dimensão, filtro ou configuração em
Analytics, SEO, sitemap, breadcrumbs, busca, Admin e observabilidade.

## Base reconstruída

O trabalho partiu novamente do ZIP original. Como o ZIP original contém o projeto sob
`prazzu-tools/` e os lotes incrementais usam caminhos relativos à raiz do projeto, os
Lotes 3 e 4 foram reaplicados explicitamente dentro dessa pasta antes da análise. O
README, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios anteriores e
o inventário executável foram relidos antes de qualquer mudança.

## Analytics

O Analytics continua sendo uma única infraestrutura. `analytics_sessions` e
`platform_analytics_events` ganharam `vertical_slug` nullable e indexado. O valor é
resolvido a partir do `VerticalContext` já ativo na requisição e é persistido junto às
demais dimensões de contexto.

`AnalyticsReportQuery` passou a aceitar filtro e breakdown por vertical. A dimensão foi
adicionada ao relatório administrativo e à análise de qualidade de dados. Nenhum evento,
controller, pipeline ou banco específico por nicho foi criado.

## SEO

Foi criado `VerticalSeoContext`, serviço compartilhado que lê defaults globais e
configurações por slug em `config/seo.php`. O layout global deixou de ter fallback
contábil hardcoded. Páginas podem continuar sobrescrevendo title/description; páginas
centrais de ferramentas e Blog passam a solicitar defaults contextuais.

A configuração por vertical representa conteúdo/estratégia, não uma engine separada.

## Sitemap

O sitemap de ferramentas continua usando `ToolCatalog`, portanto herda a filtragem de
vertical já implementada no Lote 3. O sitemap do Blog passou a aplicar o scope de
vertical aos posts publicados/indexáveis. Rotas e views de sitemap permanecem únicas.

## Breadcrumbs e navegação

Foi adicionado um contexto compartilhado de breadcrumb para expor a vertical ativa sem
classes específicas por nicho. O componente compartilhado de página de ferramenta e os
pontos centrais do Blog/Resources podem apresentar a vertical ativa preservando suas
rotas existentes.

## Busca

Nenhum mecanismo novo foi criado. `HomeSearchController` e as páginas de ferramentas
continuam consumindo `ToolCatalog::search()`, que já respeita `VerticalContext` desde o
Lote 3. Reimplementar busca neste lote criaria uma segunda fonte de verdade e violaria
o README.

## Admin

O Admin continua global. Postagens e categorias do Blog podem ser filtradas por
`vertical_slug`; formulários permitem selecionar uma vertical registrada. A validação
usa `verticals.registered`, sem enum fechado no Core. Uma postagem só pode ser salva
com categoria da mesma vertical.

## Observabilidade

O middleware que resolve `VerticalContext` adiciona a vertical ao contexto global de
log da requisição (`global` quando nula). Isso permite segmentação operacional sem
criar stacks de observabilidade por nicho.

## Compatibilidade preservada

- Contabilidade continua sendo a vertical padrão atual;
- nenhuma rota ou slug público foi alterado;
- nenhuma ferramenta ou fórmula de domínio foi modificada;
- Blog, Analytics, SEO, Admin, Auth, Billing e E2E continuam únicos;
- `AcquisitionContext` continua independente de `VerticalContext`;
- nenhuma segunda vertical real foi cadastrada;
- `VerticalContext = null` continua representando fallback global.

## Validação

Foram executados lint PHP dos arquivos alterados, `php artisan tools:check-architecture`,
`php artisan analytics:check` e bootstrap/listagem de rotas. Os gates disponíveis
permaneceram aprovados. A suíte PHPUnit integral continua dependente das extensões PHP
que não estão disponíveis no ambiente de montagem (`dom`, `mbstring` e `xmlwriter`).

## Próximo lote

O próximo passo é o **Lote 6 — segunda vertical mínima como prova arquitetural**. Ele
deve provar a generalização adicionando uma vertical nova com o mínimo de conteúdo e
1 ou 2 ferramentas, sem copiar infraestrutura. Se o Core precisar conhecer o nome da
nova vertical ou se Analytics/SEO/Blog/Admin precisarem ser duplicados, a arquitetura
ainda não está pronta.

## CORE_CANDIDATES

O lote não ativou gatilhos de promoção de candidatos de domínio. Os novos contextos de
SEO e breadcrumb são capacidades transversais do próprio escopo multi-vertical e foram
mantidos pequenos, sem abstrações específicas por ferramenta.
