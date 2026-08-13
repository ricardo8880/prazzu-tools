# Crescimento e Retenção — Lote 3 — Continuidade e Descoberta

## Objetivo

Aumentar a chance de retorno e de descoberta de uma segunda ferramenta sem transformar o Prazzu Tools em SaaS de gestão, sem exigir login para usar ferramentas e sem criar persistência anônima que viole a regra do README.

A base foi reconstruída obrigatoriamente na ordem ZIP original → Crescimento e Retenção Lote 1 → Crescimento e Retenção Lote 2 antes de qualquer alteração deste lote.

## Home autenticada

Para usuários autenticados, a Home ganhou uma seção separada `Continue de onde parou`, com até quatro ferramentas usadas recentemente na vertical ativa.

A seção:

- não altera as oito ferramentas mais recentes da Home;
- não lê nem renderiza `input_payload` ou `result_payload`;
- reutiliza a rota real de repetição quando ela já existe;
- caso não exista repetição, abre a ferramenta normalmente;
- oferece acesso ao `Meu Prazzu` como superfície completa de continuidade;
- não aparece durante uma Home contextual de aquisição, preservando a prioridade da campanha ativa.

## Continuidade temporária do visitante

O visitante também pode voltar à Home durante a mesma sessão e reencontrar até quatro ferramentas que acabou de abrir.

Para obedecer literalmente ao README, essa memória usa `sessionStorage`, nunca banco, cookie de identidade ou `localStorage`. São armazenados somente slugs de ferramentas, no máximo oito para ordenação e quatro exibidos na Home. Valores digitados, resultados e payloads não entram nessa memória.

A seção deixa explícito que os atalhos ficam apenas na sessão. Encerrar a sessão do navegador pode eliminá-los, preservando a regra de que persistência, sincronização e histórico dependem de conta.

## Continuidade promovida ao Core

O Lote 2 havia registrado a leitura agregada do `Meu Prazzu` como candidato ao Core, condicionada a uma segunda reutilização concreta. A Home autenticada confirmou esse gatilho.

Foi criado `App\Core\Tools\History\Application\Queries\UserToolContinuityQuery`, que agora concentra:

- contagem de resultados salvos;
- contagem de favoritos;
- número de ferramentas usadas;
- ferramentas recentes;
- favoritos recentes;
- resumo de histórico por ferramenta;
- apresentação segura das rotas de abrir, histórico e repetir já existentes.

`AccountController` passou a consumir essa consulta e deixou de duplicar a implementação. Nenhuma migration ou tabela nova foi criada.

## Ferramentas relacionadas por jornada real

`ToolCatalog::related()` deixou de depender somente de categoria e palavras-chave.

O novo `config/tools/journeys.php` declara o próximo passo editorial das 43 ferramentas oficiais. Exemplos:

- Salário Líquido → Custo de Funcionário CLT → Hora Extra → Férias → Rescisão;
- Simples Nacional → Fator R → DAS em atraso → DAS retroativo → MEI/Microempresa;
- Capital de Giro → Fluxo de Caixa → Ponto de Equilíbrio → Margem/Markup → Honorários;
- Pró-Labore → Distribuição de Lucros → simulação baseada em balanço → declaração de rendimentos.

A ordem editorial tem prioridade. Se uma jornada tiver menos itens válidos na vertical atual, a heurística histórica de mesma categoria e palavras-chave completa as vagas. Não há cruzamento automático de verticais.

A vertical de RH continua com uma única ferramenta oficial, portanto `calculadora-turnover` não recebe sugestões artificiais de Contabilidade.

## Limites preservados

- README da raiz não foi alterado.
- As oito ferramentas principais da Home continuam derivadas de `release_order` e limitadas a oito.
- Nenhum slug, fórmula, feature Essencial/Plus ou inventário oficial foi alterado.
- Nenhum cálculo passou a exigir conta.
- Nenhum dado de cálculo de visitante é persistido.
- Nenhum histórico paralelo foi criado.
- Nenhum CRM, tarefa, workflow ou gestão operacional foi adicionado.
- Home contextual de aquisição continua tendo prioridade sobre a personalização de retorno.

## Cobertura adicionada

`HomeContinuityTest` cobre a intenção de:

- mostrar somente ferramentas recentes pertencentes ao usuário autenticado;
- não renderizar payloads privados na Home;
- não misturar histórico de outro usuário;
- entregar ao visitante apenas a estrutura de continuidade temporária.

`ToolCatalogRelatedTest` passou a validar:

- prioridade da jornada editorial;
- ausência da ferramenta atual nas recomendações;
- cobertura das 43 ferramentas oficiais no mapa editorial;
- inexistência de slugs editoriais fora do inventário oficial.

## Validação do lote

No ambiente de preparação:

- `php -l` passou em todos os PHP alterados e adicionados;
- `node --check resources/js/app.js` passou;
- `php artisan tools:check-architecture` encerrou com zero violações;
- `php artisan route:list --path=minha-conta` confirmou as rotas da conta após a extração da consulta compartilhada;
- a resolução real de `ToolCatalog` confirmou a ordem editorial esperada para Salário Líquido, Simples Nacional e Capital de Giro;
- a compilação direta de `welcome.blade.php` e `account/show.blade.php` concluiu normalmente;
- `php artisan view:cache` continua impedido pela ausência de `DOMDocument` no PHP deste ambiente;
- o PHPUnit continua impedido pelas extensões PHP ausentes já registradas nos lotes anteriores (`dom`, `mbstring` e `xmlwriter`);
- o build Vite não pôde iniciar porque o `node_modules` recebido no ZIP não contém o pacote nativo Linux opcional `@rollup/rollup-linux-x64-gnu`; o JavaScript alterado foi validado por parser com `node --check`.

## Continuidade para o Lote 4

Antes do próximo lote de crescimento e retenção, reconstruir obrigatoriamente a base na ordem:

ZIP original → Crescimento e Retenção Lote 1 → Crescimento e Retenção Lote 2 → Crescimento e Retenção Lote 3.

Reler README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md` e os três relatórios de crescimento antes de qualquer alteração.

O escopo planejado para o Lote 4 é aquisição, SEO e confiança nas páginas das ferramentas e no rodapé, sem gerar conteúdo genérico em massa nem alterar fórmulas por conveniência.
