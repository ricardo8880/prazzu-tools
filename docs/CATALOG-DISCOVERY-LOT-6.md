# Catálogo e descoberta — Lote 6

## Objetivo

Fechar lacunas de descoberta do catálogo sem criar uma segunda registry de recomendações, um motor de busca externo ou qualquer comportamento de gestão. O lote parte obrigatoriamente do estado reconstruído em ZIP original → Lotes 1, 2, 3, 4 e 5.

## Estado encontrado

O projeto já possuía busca inteligente no frontend, jornadas por problema, próximos passos editoriais, favoritos persistidos, histórico/continuidade e seleção editorial de ferramentas. A principal lacuna era de integração: no catálogo completo, o payload da busca inteligente enviava `recentSlugs` e `featuredSlugs` permanentemente vazios, embora o JavaScript já soubesse usar esses sinais.

Também existia diferença de semântica entre a busca rápida e a busca completa: o frontend tratava os termos individualmente e ranqueava relevância, enquanto `ToolCatalog::search()` exigia a frase normalizada inteira na mesma ordem.

## Mudanças

- `ToolCatalogController` reutiliza `UserToolContinuityQuery`, `UserToolFavorites` e `ToolCatalog::featured()` para alimentar os sinais já suportados pela busca inteligente.
- Favoritos são filtrados pela vertical/catálogo ativo antes de chegar à superfície pública.
- Ferramentas recentes também respeitam a vertical ativa.
- O catálogo geral autenticado mostra até seis “Seus atalhos”, priorizando favoritas e depois ferramentas usadas recentemente.
- Os atalhos somem quando existe busca ou categoria ativa, para não competir com a intenção explícita do usuário.
- `ToolCatalog::search()` passa a aceitar termos em qualquer ordem e a ordenar resultados por relevância de nome, palavras-chave, categoria, descrição e slug.
- Nenhuma fórmula, regra normativa, slug, rota, `release_order`, status, vertical ou classificação Essencial/Plus foi alterada.

## Decisão arquitetural

Nenhuma nova abstração foi criada. Continuidade, favoritos, seleção editorial e jornadas já pertencem ao Core compartilhado e foram apenas conectados ao catálogo. A heurística de busca permanece dentro de `ToolCatalog`, pois é comportamento do próprio catálogo e ainda não justifica mecanismo externo ou indexador separado.

## Gates

`CatalogDiscoveryLot6Test` cobre:

- busca multi-termo independente da ordem;
- prioridade de correspondência por nome;
- propagação de favoritas, recentes e sugestões editoriais ao smart search;
- exposição dos atalhos somente no catálogo geral;
- reutilização das fontes compartilhadas existentes, sem dependência de módulos concretos.

## Continuidade

O próximo lote deve reconstruir obrigatoriamente ZIP original → Lotes 1, 2, 3, 4, 5 e 6 e reler os documentos obrigatórios antes de qualquer alteração.
