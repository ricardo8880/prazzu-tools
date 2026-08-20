# Satisfação e Retorno — Lote 4 — Resolução percebida

## Objetivo

Distinguir “a ferramenta gerou um resultado” de “o resultado resolveu a necessidade do usuário”, sem transformar a jornada em pesquisa invasiva e sem alterar regras de domínio.

## Estado de entrada

O lote foi iniciado após reconstrução obrigatória na ordem:

1. ZIP original;
2. Satisfação e Retorno — Lote 1;
3. Satisfação e Retorno — Lote 2;
4. Satisfação e Retorno — Lote 3.

README da raiz, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, relatórios anteriores e `config/product_tools.php` foram tratados como referências obrigatórias.

## Implementação

- Criado feedback transversal de resolução com três respostas: `Sim`, `Parcialmente` e `Não`.
- A pergunta é renderizada no componente compartilhado de página, mas permanece escondida quando a página não contém um resultado real (`data-analytics-result` ou `data-testid="tool-result"`).
- Respostas `Parcialmente` e `Não` exigem somente um motivo principal; comentário permanece opcional.
- O feedback de resolução possui persistência própria e não é misturado à fila existente de problema/sugestão da ferramenta.
- A gravação passa por `StoreToolResolutionFeedback` + `ToolResolutionSubmission`, mantendo o controller HTTP fino e o contrato de validação no Core transversal de Feedback.
- Criado o evento canônico `tool.resolution.submitted`, registrado após persistência. Falha de Analytics não invalida a resposta já salva.
- Analytics de ferramentas passa a exibir respostas, Sim/Parcialmente/Não, cobertura do feedback, taxa confirmada de resolução e principais motivos de resolução incompleta.
- Foram adicionados testes arquitetural, unitário do contrato de resolução e HTTP da persistência para visitante.
- Rótulos administrativos que antes chamavam qualquer resultado de “problema resolvido” passaram a dizer “resultado entregue”, preservando a semântica histórica da métrica sem confundi-la com a nova evidência declarada pelo usuário.

## Limites preservados

- Nenhuma fórmula, alíquota, regra normativa, request de ferramenta, payload de cálculo, slug, rota de ferramenta, vertical, inventário, `release_order` ou classificação Essencial/Plus foi alterada.
- O componente de resolução não conhece módulos de domínio.
- O sistema não solicita login para responder.
- Nenhum dado do cálculo é enviado junto com a resposta.

## Próximo lote

Antes do Lote 5 desta frente, reconstruir obrigatoriamente **ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4**, reler os documentos obrigatórios e comparar o estado acumulado. O Lote 5 permanece dedicado a descoberta e jornadas orientadas ao problema, sem reabrir este contrato salvo regressão comprovada.
