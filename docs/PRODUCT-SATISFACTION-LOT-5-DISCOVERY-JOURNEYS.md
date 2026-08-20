# Satisfação do Produto — Lote 5 — Descoberta por problema e jornadas

## Objetivo

Reduzir a carga de escolha de um catálogo com 50 ferramentas e ajudar quem conhece o problema, mas não o nome exato da ferramenta, a encontrar um ponto de entrada útil. A mudança não cria gestão, tarefas ou workflows; jornadas são somente navegação editorial entre utilitários independentes.

## Estado de partida

O lote foi construído obrigatoriamente sobre ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4. O README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `config/product_tools.php` e a infraestrutura de jornadas preexistente foram relidos antes da implementação.

## Decisão arquitetural

`config/tools/journeys.php` já é a fonte editorial dos próximos passos de cada ferramenta. Para evitar uma segunda registry e sequências divergentes, `config/tools/discovery_journeys.php` guarda somente `key`, título, descrição, ícone e `start_slug`.

`App\Core\Tools\Discovery\Application\ProblemJourneyCatalog` resolve a ferramenta inicial pelo catálogo oficial e deriva até três passos adicionais com `ToolCatalog::nextSteps()`. Se a ferramenta não existir na vertical ativa ou a sequência não tiver pelo menos duas etapas, a jornada não é exibida.

## Superfícies

A Home mostra “O que você quer resolver?” fora de contextos de aquisição. O catálogo mostra “Encontre pela rotina” somente em seu estado geral, sem busca e sem categoria ativa. Ambas reutilizam o mesmo componente Blade.

Contabilidade começa com quatro rotinas: Funcionários e folha, Simples Nacional, Sócios e retiradas e Financeiro da empresa. RH não recebe uma sequência artificial enquanto tiver somente `calculadora-turnover`.

## Analytics

O clique de entrada usa `source=problem_journey` com chave, placement e posição. O middleware só registra `discovery.problem-journey.opened` quando a chave é válida para a vertical e o slug aberto é exatamente o `start_slug`. Nenhum valor de cálculo ou dado pessoal entra no evento.

Foi adicionado o funil “Descoberta por rotina”: jornada iniciada → resultado concluído.

## Limites preservados

- As oito ferramentas recentes da Home não foram alteradas.
- Não houve alteração em `config/product_tools.php`.
- Não houve alteração de slugs, rotas, fórmulas, domínio, regras normativas ou tiers.
- A configuração editorial não duplica os passos de `config/tools/journeys.php`.
- Nenhuma jornada cruza verticais.
- Jornada é descoberta/navegação, não workflow persistido.

## Continuidade

O Lote 6 deve partir de: ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5, seguido de nova análise acumulada antes de qualquer modificação.
