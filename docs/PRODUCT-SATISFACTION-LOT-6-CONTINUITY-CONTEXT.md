# Satisfação e Retorno — Lote 6 — Continuidade e histórico contextual

## Objetivo

Fazer o usuário autenticado reconhecer rapidamente o trabalho anterior e retomar o ponto certo sem criar uma nova camada de gestão, CRM, tarefas ou processos.

## Estado de partida

O lote foi iniciado sobre o estado acumulado obrigatório **ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5**. Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos lotes anteriores e `config/product_tools.php`.

A análise confirmou que a infraestrutura principal de continuidade já existia antes desta frente: Home autenticada, `Meu Prazzu`, histórico compartilhado, resultados salvos, favoritos, ações de repetição e a query transversal `UserToolContinuityQuery`. Por isso, o lote não recria persistência nem outra página de continuidade.

## Implementação

Foi criada a capacidade opcional `ProvidesHistoryContext`. Uma ferramenta que possui informação suficientemente segura e significativa no payload já persistido pode fornecer um rótulo curto para contextualizar seu histórico. O Core apenas solicita e apresenta esse texto; não conhece slugs nem interpreta regras fiscais, tributárias ou trabalhistas.

`ToolHistoryContextResolver` centraliza a resolução e possui fallback silencioso: ferramentas que não implementam o contrato continuam exibindo a referência existente, sem quebra de compatibilidade.

A formatação genérica de mês/trimestre ficou em `HistoryPeriodFormatter`, compartilhada após uso equivalente em múltiplas ferramentas.

A primeira cobertura foi aplicada onde já existe contexto temporal explícito e não sensível:

1. Calculadora de Salário Líquido — competência;
2. Hora Extra — competência;
3. ICMS-ST — competência;
4. PIS/Cofins — período;
5. IRPJ e CSLL — Lucro Presumido — mês ou trimestre;
6. Simples Nacional — mês de referência.

Exemplos de rótulos produzidos:

- `Agosto/2026`;
- `3º trimestre/2026`.

## Superfícies atualizadas

O mesmo contexto passa a ser reaproveitado em:

- Home autenticada — “Continue de onde parou”;
- `Meu Prazzu` — cartões de continuidade;
- listagem do histórico compartilhado;
- detalhe de um cálculo salvo.

Quando não existe contexto específico, a experiência preserva a referência cronológica anterior.

## Privacidade e limites

- O payload persistido é lido somente no servidor para derivar o rótulo.
- O payload completo não é enviado para a Home nem para `Meu Prazzu`.
- Nenhum campo sensível de funcionário, cliente, documento ou empresa foi usado como rótulo.
- Nenhuma fórmula, request, regra normativa, slug, rota, vertical, `release_order`, inventário ou tier Essencial/Plus foi alterado.
- Não foi criado um formatador universal de títulos baseado em nomes de campos nem condicionais por slug no Core.
- Ferramentas sem um contexto seguro continuam com o comportamento anterior.

## Continuidade obrigatória

Antes do Lote 7, reconstruir e analisar novamente **ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4 → Lote 5 → Lote 6**, reler os documentos obrigatórios e comparar o estado acumulado antes de qualquer alteração.
