# Prazzu Plus — Lote 8 — Auditoria final de monetização

## Base reconstruída

Este lote foi executado sobre o estado acumulado reconstruído na ordem obrigatória:

1. ZIP original;
2. Prazzu Plus Lote 1;
3. Prazzu Plus Lote 2;
4. Prazzu Plus Lote 3;
5. Prazzu Plus Lote 4;
6. Prazzu Plus Lote 5;
7. Prazzu Plus Lote 6;
8. Prazzu Plus Lote 7.

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, todos os relatórios de monetização anteriores e `config/product_tools.php`.

## Objetivo

Fechar a revisão P3 sem reabrir escopo de domínio: confirmar catálogo, manifesto, autorização, testes e CI dos contratos saneados, tornar regressões identificáveis e preparar o projeto para uma ativação operacional controlada de `monetized`.

## Resultado da auditoria

- catálogo oficial: 43 ferramentas;
- ferramentas com ao menos uma feature Plus: 43;
- features Plus declaradas: 137;
- contratos saneados nos Lotes 2–6: 61;
- dívida legada congelada e fora do escopo funcional deste relatório: 76;
- matriz comercial dos 137 contratos: Free bloqueado e Plus permitido no modo monetizado;
- nenhuma chave genérica `advanced_productivity` ou `advanced_analysis` permanece no catálogo;
- nenhuma regressão `tools.plus.*` foi encontrada no estado acumulado.

A auditoria deliberadamente não converte as 76 features antigas em contratos estritos apenas para zerar uma métrica. Elas já existiam antes do Relatório de Ajustes e não foram apontadas como parte das 38 correções funcionais. Qualquer saneamento adicional dessas features deve ocorrer em lote explícito, com evidência real de implementação, gate e teste.

## Snapshot exato dos contratos saneados

O Lote 7 possuía apenas um piso numérico de 61 contratos estritos. Isso permitiria, em tese, que um contrato já corrigido desaparecesse e outro entrasse em seu lugar sem reduzir a contagem.

O Lote 8 adiciona `plus_feature_governance.strict_contracts`, com o conjunto exato das 61 features saneadas. `PlusFeatureReadinessInspector` e `PlusFeatureGovernanceContractTest` agora exigem igualdade exata entre esse snapshot e o catálogo fora da dívida legada.

Assim, uma feature saneada não pode:

- desaparecer do manifesto silenciosamente;
- retornar para `legacy_debt`;
- ser substituída por outra apenas para manter a contagem;
- perder o contrato estrutural sem falha no gate arquitetural.

## Interface e promessa comercial

O componente compartilhado `tool-feature-tiers` lê diretamente o manifesto registrado e separa `Essential` de `Plus`; no modo `launch_free` ele identifica explicitamente que o Plus está liberado no lançamento. As ferramentas que possuem experiências Plus dedicadas continuam usando seus painéis/ações próprios, protegidos pelos gates individuais implementados nos lotes de domínio.

O modo padrão do repositório permanece `launch_free`. O Lote 8 não altera `.env.example` para `monetized`, porque ativação comercial é decisão operacional/deploy e não deve ser escondida em um patch de código. Para ativar, o ambiente deve definir `PRAZZU_COMMERCIAL_MODE=monetized` após a aprovação do `composer release:check` no CI compatível.

## Validação

- lint PHP dos arquivos alterados;
- matriz equivalente Free × Plus das 137 features;
- `tools:check-architecture` sem violações `tools.plus.*`;
- consistência exata de 61 contratos estritos + 76 legados = 137 declarados;
- integridade do inventário de 43 ferramentas;
- pacote final gerado apenas com arquivos alterados neste lote.

As violações arquiteturais históricas que não pertencem ao contrato Prazzu Plus continuam fora deste lote. O PHPUnit integral continua dependente do ambiente oficial com as extensões documentadas no projeto.

## Ativação comercial

O código fica preparado para a mudança operacional de `launch_free` para `monetized`, mas este lote não altera o valor padrão do ambiente. A ativação deve ocorrer somente após o CI oficial concluir `composer release:check` com sucesso e com a configuração de planos/assinaturas disponível no ambiente de produção.

## Continuidade

Este é o último lote do Relatório de Ajustes — Prazzu Plus. Alterações futuras em features comerciais devem partir do estado acumulado original → Lotes 1–8 e atualizar explicitamente os snapshots de governança quando houver adição, remoção ou saneamento de contrato Plus.
