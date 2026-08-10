# Evolução Multi-Nicho — Lote 3 — Catalogação de Ferramentas e Conteúdo

## Objetivo

Tornar a vertical de Contabilidade explícita nos dados e conteúdos atuais, usando as fontes oficiais já existentes e o `VerticalContext` criado no Lote 2, sem alterar slugs, rotas, regras de domínio ou criar infraestrutura paralela.

## Estado de origem analisado

O trabalho partiu novamente do ZIP original recebido. Antes das alterações foram relidos o `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `docs/ARCHITECTURE.md`, os relatórios dos Lotes 1 e 2, `config/product_tools.php`, `ToolManifest`, `ToolRegistry`, `ToolCatalog`, Blog, Resources e os 32 módulos oficiais.

O estado confirmado antes do lote era: Lote 1 concluído, Lote 2 concluído, 32 ferramentas oficiais, `contabilidade` como primeira vertical registrada e nenhuma ferramenta/conteúdo ainda associado explicitamente a uma vertical.

## Implementação

### Ferramentas

`ToolManifest` ganhou o campo obrigatório `vertical`. Todos os 32 módulos oficiais declaram `contabilidade`, mantendo categoria e vertical como conceitos distintos.

`config/product_tools.php`, fonte executável do inventário, também registra a vertical de cada entrada oficial. Um gate arquitetural verifica que inventário e manifestos continuam coerentes e que a vertical declarada existe no registro oficial.

### Catálogo, busca e recomendações

`ToolCatalog` passou a considerar a vertical ativa em `VerticalContext`. Quando existe contexto, apenas ferramentas daquela vertical entram em listagens, busca, filtros, categorias derivadas e recomendações. Quando o contexto é `null`, o catálogo continua global.

Nenhuma rota, slug, controller específico por vertical ou catálogo paralelo foi criado.

### Recursos

Os itens atuais em `config/resources.php` passaram a declarar `vertical = contabilidade`. `ContentPageController` filtra os itens pela vertical ativa e mantém comportamento global quando o contexto é `null`.

Views, URLs, slugs e conteúdo dos recursos não foram alterados.

### Blog

Foi adicionada uma migration incremental com `vertical_slug` em `blog_posts` e `blog_categories`. Os registros existentes são associados a `contabilidade` durante a migration.

`BlogPost` e `BlogCategory` ganharam scope reutilizável por vertical. O Blog público aplica o contexto ativo em listagem, busca, destaque, leitura e relacionados. Novos posts e categorias recebem a vertical ativa/padrão sem introduzir nova interface administrativa neste lote.

A engine do Blog continua única e compartilhada.

### Categorias

As categorias técnicas/de apresentação das ferramentas não foram transformadas em verticais. A associação de negócio reside no `ToolManifest`. Categorias do Blog passam a carregar `vertical_slug`, pois são conteúdo editorial pertencente a uma vertical.

## Compatibilidade preservada

- 32 ferramentas oficiais preservadas;
- nenhum slug público alterado;
- nenhuma rota pública alterada;
- nenhuma regra de cálculo alterada;
- Home não foi redesenhada/contextualizada além do efeito natural do catálogo já filtrado;
- Analytics, SEO, sitemap, breadcrumbs, Auth, Billing, Admin global e E2E não foram duplicados;
- `VerticalContext = null` mantém fallback global.

## Arquivos principais alterados

- `app/Core/Tools/Data/ToolManifest.php`;
- `app/Core/Tools/ToolCatalog.php`;
- os 32 `app/Tools/*/Tool.php`;
- `config/product_tools.php`;
- `config/resources.php`;
- `app/Blog/Models/BlogPost.php`;
- `app/Blog/Models/BlogCategory.php`;
- `app/Http/Controllers/Blog/BlogController.php`;
- `app/Http/Controllers/Platform/ContentPageController.php`;
- controllers administrativos de Blog necessários para persistir a associação;
- `database/migrations/2026_08_10_000000_add_vertical_to_blog_content.php`;
- testes e documentação arquitetural correspondentes.

## Fora do escopo

Permanecem para lotes seguintes:

- composição completa da Home por vertical;
- identidade/Hero/CTA contextual;
- Analytics com dimensão de vertical;
- SEO, sitemap e breadcrumbs conscientes de vertical;
- filtros administrativos explícitos por vertical;
- observabilidade segmentada;
- segunda vertical real (RH ou outra);
- alterações de E2E específicas de múltiplas verticais.

## Continuidade para o Lote 4

O próximo lote deve reconstruir novamente o projeto a partir do ZIP original e reaplicar em ordem os Lotes 1, 2 e 3 antes de qualquer alteração. A Home deve então usar `VerticalContext` e as entidades já catalogadas para compor Hero, CTAs, ferramentas, recursos, artigos e recomendações, mantendo uma única infraestrutura e fallback global.

## CORE_CANDIDATES

Nenhuma abstração oportunista foi promovida. A associação de vertical utiliza contratos já aprovados (`VerticalContext`, `ToolManifest`, inventário, Blog e Resources) e não cria um segundo catálogo nem uma hierarquia paralela.
