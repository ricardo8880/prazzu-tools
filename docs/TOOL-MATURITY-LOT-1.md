# Maturidade das ferramentas — Lote 1

## Escopo

Este lote parte do estado original de 50 ferramentas oficiais e trata exclusivamente a maturidade declarada nos manifestos. Nenhum slug, rota pública, vertical, fórmula, escopo Essencial/Plus ou ordem de lançamento foi alterado.

## Estado encontrado

O inventário oficial possui 50 ferramentas implementadas. Nos manifestos, o estado inicial real era:

- 10 `active`;
- 40 `beta`;
- 0 `draft`.

Portanto, não havia Draft oficial a eliminar. A dívida estava concentrada em Betas com níveis diferentes de evidência de qualidade.

## Critério objetivo de promoção Beta → Active

Neste lote uma ferramenta Beta só pode ser promovida quando o próprio módulo já comprova, simultaneamente:

1. `Quality/RiskProfile.php` presente;
2. `Tests/Fixtures/GoldenCases.php` presente;
3. `Tests/Unit/ToolQualityContractTest.php` presente;
4. `QUALITY.md` presente e sem checklist aberto (`- [ ]`);
5. nenhuma feature Plus genérica proibida para ferramenta ativa (`advanced_productivity` ou `advanced_analysis`);
6. manifesto e teste de manifesto atualizados em conjunto.

O lote não inventa fonte normativa, caso dourado, revisão especializada ou aprovação de CI ausente apenas para promover status.

## Promoções realizadas

Sete módulos já possuíam toda a evidência acima e tiveram o status corrigido de `beta` para `active`:

- `DifalIcmsCalculator`;
- `DigitalCertificateAnalyzer`;
- `EcadRoyaltySimulator`;
- `IcmsStCalculator`;
- `InvoiceWithholdingCalculator`;
- `NetSalaryCalculator`;
- `OvertimeCalculator`.

Estado após o lote:

- 17 `active`;
- 33 `beta`;
- 0 `draft`.

## Por que as demais Betas não foram promovidas

As 33 Betas restantes possuem ao menos um bloqueio verificável no estado original, como ausência de artefatos do framework de qualidade atual, checklist `QUALITY.md` ainda aberto, golden cases pendentes ou revisão/CI explicitamente ainda não concluídos. Este lote mantém o status Beta nesses casos para não transformar ausência de evidência em falsa maturidade.

Exemplos objetivos encontrados:

- `ContractGenerator`: revisão jurídica especializada e `composer release:check` ainda marcados como pendentes;
- `PresumedProfitIrpjCsllCalculator`: `composer release:check` ainda pendente no checklist;
- `ProLaboreSimulator` e `ProfitDistributionCalculator`: golden cases ainda declarados como pendentes;
- diversos módulos mais antigos ainda possuem checklist de qualidade gerado com itens não confirmados;
- módulos recentes como `ActualProfitCalculator`, `CfopAdvisor`, `IcmsCalculator`, `SefazFiscalValidator` e `TaxReformSimulator` ainda não possuem o conjunto completo de artefatos exigido pelo critério deste lote.

## Gate arquitetural adicionado

`OfficialToolMaturityTest` passa a proteger três invariantes:

- nenhuma ferramenta oficial pode permanecer em `draft`;
- as sete promoções deste lote não podem regredir silenciosamente para Beta;
- toda ferramenta `active` submetida ao framework atual precisa manter `QUALITY.md`, `RiskProfile`, golden cases e `ToolQualityContractTest`, sem checklist aberto.

Quatro ferramentas ativas anteriores ao framework atual (`AccountingFeesCalculator`, `BusinessDocumentValidator`, `LaborTerminationCalculator` e `MarginMarkupCalculator`) ficam explicitamente registradas como legado, em vez de receber artefatos fictícios. A regularização delas deve ocorrer em lote de qualidade que produza evidência real.

## Inventário e compatibilidade

- quantidade oficial preservada em 50;
- 49 ferramentas continuam em `contabilidade` e 1 em `rh`;
- slugs, rotas, IDs e `release_order` permanecem inalterados;
- `config/product_tools.php` avança para schema `3.19.0` e `release_readiness = tool_maturity_lot_1_audited`.

## Validação

O lote deve ser validado pelos gates disponíveis localmente e, no CI oficial, por `composer release:check`. Ausência de extensões PHP no ambiente local não deve ser convertida em aprovação fictícia.

## Continuidade obrigatória

Antes do próximo lote desta frente, reconstruir o estado na ordem: ZIP original → Lote 1. Depois reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, este relatório e `config/product_tools.php` antes de qualquer alteração.
