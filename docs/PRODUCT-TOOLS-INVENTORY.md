# Inventário oficial das ferramentas

A fonte executável é `config/product_tools.php`. Após a remoção dos 22 módulos específicos descartados, o catálogo possui **28 ferramentas oficiais**.

| ID | Ferramenta | Slug | Módulo | Vertical | Release |
|---:|---|---|---|---|---:|
| 1 | Calculadora de Custo de Funcionário CLT | `employee-cost-calculator` | `EmployeeCostCalculator` | contabilidade | 6 |
| 2 | Calculadora de Encargos Trabalhistas | `employer-payroll-tax-calculator` | `LaborChargesCalculator` | contabilidade | 7 |
| 3 | Simulador CLT × PJ × Autônomo | `employee-vs-contractor-calculator` | `EmploymentModelComparator` | contabilidade | 8 |
| 4 | Calculadora de Capital de Giro | `working-capital-calculator` | `WorkingCapitalCalculator` | contabilidade | 14 |
| 5 | Calculadora de Fluxo de Caixa | `cash-flow-calculator` | `CashFlowCalculator` | contabilidade | 15 |
| 6 | Calculadora de Ponto de Equilíbrio | `break-even-calculator` | `BreakEvenCalculator` | contabilidade | 16 |
| 7 | Calculadora de Precificação de Produtos | `margin-markup-calculator` | `MarginMarkupCalculator` | contabilidade | 17 |
| 8 | Calculadora de Comissão de Vendedores | `sales-commission-calculator` | `SalesCommissionCalculator` | contabilidade | 18 |
| 9 | Gerador de Holerite | `pay-stub-generator` | `PayslipGenerator` | contabilidade | 10 |
| 10 | Simulador de Admissão | `new-hire-cost-calculator` | `AdmissionSimulator` | contabilidade | 11 |
| 11 | Simulador de Demissão | `final-pay-severance-calculator` | `LaborTerminationCalculator` | contabilidade | 12 |
| 12 | Calculadora de Reajuste Salarial | `salary-increase-calculator` | `SalaryAdjustmentCalculator` | contabilidade | 13 |
| 13 | Calculadora de Distribuição de Lucros | `owner-distribution-calculator` | `ProfitDistributionCalculator` | contabilidade | 9 |
| 14 | Gerador de Declaração de Rendimentos | `earnings-statement-generator` | `IncomeStatementGenerator` | contabilidade | 19 |
| 15 | Gerador de Declaração de Trabalho/Renda | `employment-income-letter-generator` | `WorkIncomeStatementGenerator` | contabilidade | 20 |
| 16 | Calculadora de Salário Líquido | `take-home-pay-calculator` | `NetSalaryCalculator` | contabilidade | 21 |
| 17 | Calculadora de Hora Extra, Adicional Noturno e DSR | `overtime-pay-calculator` | `OvertimeCalculator` | contabilidade | 22 |
| 18 | Calculadora de Honorários Contábeis | `accounting-fee-calculator` | `AccountingFeesCalculator` | contabilidade | 1 |
| 19 | Validador Inteligente de CNPJ, CPF e IE | `us-tax-id-validator` | `BusinessDocumentValidator` | contabilidade | 2 |
| 20 | Gerador de Contratos | `contract-generator` | `ContractGenerator` | contabilidade | 3 |
| 21 | Emissor de Recibos | `receipt-generator` | `ReceiptIssuer` | contabilidade | 4 |
| 22 | Calculadora de Férias | `pto-calculator` | `VacationCalculator` | contabilidade | 5 |
| 23 | Calculadora de Turnover | `employee-turnover-calculator` | `TurnoverCalculator` | rh | 23 |
| 24 | Calculadora de Retenções na Nota Fiscal | `backup-withholding-calculator` | `InvoiceWithholdingCalculator` | contabilidade | 24 |
| 25 | Calculadora de Depreciação de Ativos | `asset-depreciation-calculator` | `AssetDepreciationCalculator` | contabilidade | 25 |
| 26 | Calculadora de Parcelamento Tributário | `irs-payment-plan-calculator` | `TaxInstallmentCalculator` | contabilidade | 26 |
| 27 | Simulador de Distribuição de Lucros com Balanço × sem Balanço | `owner-distribution-planner` | `ProfitDistributionBalanceSimulator` | contabilidade | 27 |
| 28 | Simulador da Reforma Tributária do Consumo | `sales-tax-calculator` | `TaxReformSimulator` | contabilidade | 28 |

## Regras do inventário

- Cada diretório em `app/Tools` deve existir exatamente uma vez no inventário.
- `expected_module_count` deve coincidir com o número real de módulos.
- IDs, slugs, chaves e `release_order` devem permanecer únicos.
- Remoções devem limpar catálogo, rotas, jornadas, testes, documentação e contratos Plus relacionados.
