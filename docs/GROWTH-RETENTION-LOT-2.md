# Crescimento e Retenção — Lote 2 — Meu Prazzu

## Objetivo

Transformar a área autenticada existente em um ponto de continuidade útil sem converter o Prazzu Tools em SaaS de gestão. A URL `/minha-conta` e a autenticação permanecem intactas; a experiência passa a se chamar `Meu Prazzu` e destaca somente persistência pessoal já prevista no README.

## Continuidade entregue

O hub mostra, para o usuário autenticado:

- quantidade de resultados salvos;
- quantidade de resultados favoritos;
- quantidade de ferramentas que já possuem histórico naquela conta;
- até quatro ferramentas recentes para continuar de onde parou;
- até seis resultados favoritos;
- resumo dos históricos mais recentes por ferramenta.

A consulta usa apenas metadados de `tool_runs` e `tool_run_favorites`. O hub não lê nem renderiza `input_payload` ou `result_payload`, preservando dados sensíveis dentro das ferramentas responsáveis.

## Refazer sem criar comportamento paralelo

O hub não implementa um mecanismo global novo de repetição. Quando a ferramenta já expõe a rota padronizada `*.history.repeat`, `Meu Prazzu` reutiliza essa rota e mostra `Refazer cálculo`. Quando não existe esse contrato, o usuário recebe `Usar novamente`, que apenas abre a ferramenta.

Essa decisão evita assumir que todos os payloads históricos podem ser restaurados da mesma forma e preserva as adaptações específicas já existentes em cada domínio.

## Favoritos e histórico

Favoritos são lidos diretamente do sistema compartilhado `tool_run_favorites`. Históricos são agregados a partir de `tool_runs` com isolamento por `user_id` e status concluído. Não foi criada tabela, migration, serviço de persistência ou modelo paralelo.

Quando existe uma rota `*.history.index`, o hub oferece acesso direto ao histórico da ferramenta. Ferramentas sem essa superfície continuam acessíveis pela rota principal sem inventar páginas inexistentes.

## Navegação

Os pontos autenticados principais no cabeçalho e navegação mobile passaram de `Minha conta` para `Meu Prazzu`, reforçando a função de continuidade. A rota pública continua sendo `/minha-conta`, preservando compatibilidade.

A gestão de senha, confirmação de e-mail, acessos empresariais e preparação para Conta Prazzu unificada foram mantidas, mas aparecem depois das áreas de continuidade.

## Limites preservados

- Nenhum cálculo exige login.
- Nenhuma feature Essencial ou Plus foi reclassificada.
- Nenhum slug, fórmula, catálogo ou `release_order` foi alterado.
- Nenhuma funcionalidade de CRM, tarefas, workflow ou gestão operacional foi adicionada.
- O hub é estritamente pessoal: organizações não recebem acesso a históricos, favoritos ou resultados dos membros.

## Candidato ao Core

A leitura agregada de continuidade foi registrada em `CORE_CANDIDATES.md`, mas não foi extraída neste lote. O gatilho correto será uma segunda reutilização concreta, especialmente se a Home personalizada do Lote 3 precisar dos mesmos dados.

## Validação do lote

O teste `AccountContinuityHubTest` foi adicionado para cobrir:

- isolamento dos resultados por usuário;
- contagens de histórico, favoritos e ferramentas usadas;
- reutilização da rota real de repetição quando disponível;
- ausência de valores dos payloads na página;
- estado vazio útil para contas sem histórico.

No ambiente de preparação:

- `php -l` passou nos arquivos PHP alterados;
- `php artisan tools:check-architecture` encerrou com zero violações;
- a compilação direta do Blade de `account.show` concluiu normalmente;
- a inspeção do `ToolRegistry` confirmou 43 manifests válidos, 17 rotas de histórico, 14 rotas reais de repetição e 5 rotas de detalhe utilizáveis pelo hub;
- o PHPUnit não pôde iniciar porque o PHP disponível não possui `dom`, `mbstring` e `xmlwriter`, a mesma limitação ambiental já registrada no Lote 1;
- `php artisan view:cache` também não pôde concluir porque a saída de erro do Termwind depende de `DOMDocument`; por isso a nova view foi compilada diretamente pelo `BladeCompiler`.

A comparação final foi feita contra uma base limpa reconstruída na ordem ZIP original → Lote 1. Caches e logs gerados durante validação não fazem parte do pacote.

## Continuidade para o Lote 3

O próximo lote deve reconstruir a base na ordem ZIP original → Lote 1 → Lote 2. O escopo planejado é continuidade e descoberta na Home, ferramentas recentes e melhoria das relações entre ferramentas, sem duplicar a consulta agregada caso a reutilização do `Meu Prazzu` confirme o gatilho de promoção ao Core.
