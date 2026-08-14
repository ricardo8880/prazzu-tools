# Experiência do Usuário — Lote 3 — Home orientada ao problema

## Objetivo

Evoluir a Home existente para começar pela necessidade do usuário, sem reconstruir busca, catálogo, favoritos, recentes, continuidade, aquisição ou Analytics.

Este lote cobre exclusivamente o item 6 da rodada de UX:

- orientar a Home por `o que você precisa resolver?` em vez de apresentar a plataforma primeiro como catálogo.

## Estado de origem

A base foi reconstruída na ordem obrigatória:

`ZIP original → UX Lote 1 → UX Lote 2`

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php` e os relatórios dos Lotes 1 e 2 desta rodada.

## Mudanças

- O hero padrão de Contabilidade passou a começar pela pergunta `O que você precisa resolver hoje?`.
- A vertical de RH recebeu a mesma lógica com linguagem própria da vertical, sem conteúdo contábil.
- A busca inteligente existente virou a ação principal do hero e ganhou um rótulo visível orientado à tarefa.
- O placeholder passou a demonstrar linguagem natural de problema, em vez de pedir apenas o nome de uma ferramenta.
- A Home de Contabilidade ganhou quatro atalhos de intenção reais: rescisão, CNPJ/CPF, impostos e custo de funcionário.
- Todos os atalhos foram conferidos contra `ToolCatalog::search()` e retornam pelo menos uma ferramenta oficial.
- Os atalhos usam a busca existente com `source=home_search`, preservando a telemetria de demanda já consolidada.
- A faixa de categorias foi reposicionada editorialmente como caminho secundário: `Outra forma de encontrar → Explore pelo tipo de tarefa`.
- A linguagem do painel de busca inteligente muda somente na Home; o catálogo continua com sua copy anterior apesar de reutilizar o mesmo JavaScript.
- A seção dos oito lançamentos continua usando exatamente `ToolCatalog::latest(8)`, conforme regra obrigatória do README. Apenas o título editorial foi atualizado.
- Contextos de aquisição continuam podendo substituir hero, CTA e título de ferramentas; os atalhos genéricos de problema não aparecem durante uma Home contextual.

## Limites preservados

- Nenhuma ferramenta, fórmula, controller de cálculo, rota, slug, vertical, `release_order` ou inventário foi alterado.
- A regra das oito ferramentas mais recentes da Home foi preservada integralmente.
- Busca, favoritos, recentes, `Continue de onde parou`, Meu Prazzu e Analytics não foram recriados.
- Nenhum payload de cálculo ou dado pessoal foi adicionado à Home.
- Nenhum evento novo de Analytics foi criado; os atalhos reutilizam `home.search.submitted` pelo fluxo existente.
- Nenhum candidato de `CORE_CANDIDATES.md` foi ativado: a mudança reutiliza capacidades compartilhadas existentes.

## Continuidade obrigatória

O próximo lote desta rodada deve reconstruir a base na ordem:

`ZIP original → UX Lote 1 → UX Lote 2 → UX Lote 3`

Depois deve reler novamente os documentos obrigatórios da raiz, os relatórios desta rodada, os lotes anteriores relevantes e `config/product_tools.php`.

O próximo escopo planejado é Analytics e retenção: identificar abandono nas ferramentas e estabelecer retorno/repetição de problemas resolvidos como métricas centrais, reutilizando a infraestrutura de Analytics já existente.

## Validação executada

- `php artisan tools:check-architecture`: aprovado, sem violações;
- `php artisan analytics:check`: aprovado;
- `node --check resources/js/app.js`: aprovado;
- `php -l config/home.php`: aprovado;
- `php -l tests/Feature/Platform/HomeProblemOrientedExperienceTest.php`: aprovado;
- compilação direta de `resources/views/welcome.blade.php` com `BladeCompiler`: aprovada;
- `git diff --check`: aprovado;
- os quatro atalhos contábeis foram resolvidos contra `ToolCatalog::search()` e todos retornaram resultado;
- PHPUnit não iniciou porque o PHP do ambiente não possui `dom`, `mbstring` e `xmlwriter`;
- `php artisan view:cache` também ficou bloqueado pela ausência de `DOMDocument` no renderer do terminal.

As limitações finais são ambientais e não foram contornadas alterando dependências do projeto.
