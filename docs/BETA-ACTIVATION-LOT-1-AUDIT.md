# Ativação das ferramentas Beta — Lote 1 — diagnóstico executável

## Escopo

Este é o primeiro lote da frente criada para transformar as 37 ferramentas `beta` em ferramentas realmente úteis, confiáveis e elegíveis a `active`. O lote não promove status e não trata presença de arquivos como prova de qualidade. Ele cria uma fotografia executável do ponto de partida e registra os bloqueios que os lotes seguintes precisam eliminar.

A autoridade máxima continua sendo o `README.md` da raiz. Também foram relidos `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, `docs/TOOL_QUALITY.md` e `config/product_tools.php` antes das alterações.

## Estado de partida

- 50 ferramentas oficiais;
- 13 `active`;
- 37 `beta`;
- 0 `draft`;
- 49 ferramentas em Contabilidade e 1 em RH;
- Plus 141/141, dívida zero.

Nenhum slug, rota, ID, vertical, `release_order`, fórmula ou classificação Essencial/Plus foi alterado neste lote.

## Achado principal

Os bloqueios das Betas não se resumem ao status do manifesto. O diagnóstico executável encontrou:

- **0** das 37 Betas estruturalmente prontas para promoção;
- **24** com itens abertos no `QUALITY.md`;
- **15** com ao menos um artefato mínimo de qualidade ausente;
- **15** com golden cases sintéticos herdados do scaffold.

Há sobreposição entre os grupos.

Os golden cases sintéticos são particularmente importantes: módulos como `AdmissionSimulator`, `FactorRSimulator` e `WorkingCapitalCalculator` possuem arquivos `GoldenCases.php`, mas ainda usam entradas/resultados genéricos como `valid-typical-input`, `known-regression-input` e `calculation-or-document-completed`. Eles protegem a forma do contrato, não a correção do domínio. Esses casos precisam ser substituídos por entradas e resultados reais revisados antes de `active`.

## Diagnóstico por ferramenta

| Ferramenta | Bloqueio estrutural atual | Lote de correção previsto |
|---|---|---:|
| `WorkingCapitalCalculator` | 23 checkboxes abertos + golden cases sintéticos | 2 |
| `CashFlowCalculator` | 23 checkboxes abertos + golden cases sintéticos | 2 |
| `BreakEvenCalculator` | 23 checkboxes abertos + golden cases sintéticos | 2 |
| `SalesCommissionCalculator` | 23 checkboxes abertos + golden cases sintéticos | 2 |
| `SalaryAdjustmentCalculator` | 23 checkboxes abertos + golden cases sintéticos | 2 |
| `AssetDepreciationCalculator` | `QUALITY.md` ausente | 2 |
| `EmployeeCostCalculator` | 23 checkboxes abertos + golden cases sintéticos | 3 |
| `LaborChargesCalculator` | 23 checkboxes abertos + golden cases sintéticos | 3 |
| `EmploymentModelComparator` | 23 checkboxes abertos + golden cases sintéticos | 3 |
| `EmployerInssCalculator` | 23 checkboxes abertos + golden cases sintéticos | 3 |
| `PayslipGenerator` | 23 checkboxes abertos + golden cases sintéticos | 3 |
| `AdmissionSimulator` | 23 checkboxes abertos + golden cases sintéticos | 3 |
| `VacationCalculator` | 7 pendências explícitas de ativação | 3 |
| `FactorRSimulator` | 23 checkboxes abertos + golden cases sintéticos | 4 |
| `LateDasCalculator` | 23 checkboxes abertos + golden cases sintéticos | 4 |
| `SimplesNacionalCalculator` | sem `RiskProfile`, golden cases, quality test e `QUALITY.md` | 4 |
| `MeiToMicroenterpriseSimulator` | `QUALITY.md` ausente | 4 |
| `RetroactiveDasRegularizationCalculator` | `QUALITY.md` ausente | 4 |
| `CfopAdvisor` | sem golden cases, quality test e `QUALITY.md` | 5 |
| `SefazFiscalValidator` | sem golden cases, quality test e `QUALITY.md` | 5 |
| `IssCalculator` | `QUALITY.md` ausente | 5 |
| `IcmsCalculator` | sem golden cases, quality test e `QUALITY.md` | 5 |
| `PisCofinsCalculator` | 11 pendências no checklist | 6 |
| `PresumedProfitIrpjCsllCalculator` | `composer release:check` ainda pendente | 6 |
| `ActualProfitCalculator` | sem golden cases, quality test e `QUALITY.md` | 6 |
| `TaxRegimeComparator` | 18 pendências no checklist | 6 |
| `ProLaboreSimulator` | sem `RiskProfile`, golden cases e quality test; 1 pendência | 6 |
| `ProfitDistributionCalculator` | sem `RiskProfile`, golden cases e quality test; 1 pendência | 6 |
| `ProLaboreProfitDistributionCalculator` | 36 pendências no checklist | 6 |
| `ProfitDistributionBalanceSimulator` | `QUALITY.md` ausente | 6 |
| `TaxInstallmentCalculator` | `QUALITY.md` ausente | 6 |
| `TaxReformSimulator` | sem golden cases, quality test e `QUALITY.md` | 7 |
| `ContractGenerator` | revisão jurídica especializada + release check pendentes | 8 |
| `ReceiptIssuer` | 23 pendências no checklist | 8 |
| `IncomeStatementGenerator` | 23 checkboxes abertos + golden cases sintéticos | 8 |
| `WorkIncomeStatementGenerator` | 23 checkboxes abertos + golden cases sintéticos | 8 |
| `TurnoverCalculator` | sem golden cases e quality test | 8 |

Os lotes 9 e 10 continuam reservados para UX transversal e fechamento da evidência/gates; o lote 11 permanece auditoria final independente antes da promoção definitiva.

## Novo diagnóstico executável

Foi criado `scripts/check-beta-activation-readiness.php` e o alias Composer `beta:readiness`.

O script percorre somente ferramentas oficiais atualmente `beta` e verifica:

1. presença de `QUALITY.md`;
2. presença de `Quality/RiskProfile.php`;
3. presença de `Tests/Fixtures/GoldenCases.php`;
4. presença de `Tests/Unit/ToolQualityContractTest.php`;
5. quantidade de checkboxes abertos no `QUALITY.md`;
6. compatibilidade de slug entre manifesto, perfil de risco e suíte de casos;
7. cobertura dos tipos de golden case exigidos pelo `ToolRiskClassifier`;
8. presença dos marcadores sintéticos conhecidos do scaffold.

O comando é diagnóstico: Betas são esperadas durante esta frente, portanto a existência de bloqueios não torna o comando vermelho. Os lotes de correção devem usar a saída para provar redução progressiva da dívida.

## Critério de promoção preservado e endurecido

Uma Beta só poderá virar `active` quando, além de possuir os artefatos mínimos existentes no gate atual:

- o problema principal for resolvido completamente no Essencial;
- os golden cases forem casos reais e revisados, não tokens do scaffold;
- cálculos financeiros/fiscais/trabalhistas tiverem resultados e arredondamentos verificáveis;
- limitações e não aplicação forem explícitas;
- fonte e vigência normativa forem verificáveis quando aplicável;
- os testes requeridos pelo nível de risco existirem;
- o `QUALITY.md` refletir evidência real, não marcação administrativa;
- os gates de release aplicáveis estiverem verdes em ambiente compatível.

## Continuidade

Antes do Lote 2 desta frente, reconstruir obrigatoriamente a nova base na ordem:

**projeto atual enviado pelo usuário → Beta Activation Lote 1**.

Depois reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, este relatório, `docs/TOOL_QUALITY.md` e `config/product_tools.php`.
