# Expansão — Lote 11 — Calculadora de Salário Líquido

## Objetivo

Adicionar a ferramenta `NetSalaryCalculator` sem quebrar a arquitetura modular, mantendo a Experiência Essencial completa e usando o Prazzu Plus apenas para profundidade e produtividade.

## Escopo entregue

- cálculo mensal de salário líquido CLT para competência de 2026;
- INSS progressivo do segurado empregado, com teto previdenciário de 2026;
- IRRF mensal de 2026, incluindo deduções legais, desconto simplificado e redução mensal;
- proventos tributáveis e não tributáveis adicionais;
- dependentes, pensão judicial e descontos informados pelo usuário;
- memória de cálculo e snapshots normativos;
- exportação CSV e impressão/PDF pelo serviço compartilhado;
- histórico somente quando o usuário está autenticado e a política compartilhada permite persistência;
- manifesto, qualidade, golden cases, testes unitários, feature e arquitetura do módulo;
- registro no catálogo de módulos como expansão, sem alterar o inventário oficial de 20 ferramentas auditado no Lote 10.

## Decisão de Core ativada neste lote

A regra mensal de IRRF 2026 já existia em `ProLaboreSimulator` e `ProLaboreProfitDistributionCalculator` e passou a ser necessária também em `NetSalaryCalculator`. Como a regra é equivalente, transversal e sem condicionais de domínio, foi promovida ao Core em:

- `App\Core\Tax\Normative\MonthlyPersonalIncomeTaxRule`;
- `App\Core\Tax\Normative\MonthlyIrrfBracket`.

Os dois módulos de Pró-Labore foram ajustados para consumir a regra compartilhada. O INSS do empregado permaneceu dentro do domínio de Salário Líquido porque não é equivalente ao INSS de contribuinte individual/pró-labore.

## Fontes normativas verificadas

- INSS 2026 do segurado empregado: Portaria Interministerial MPS/MF nº 13/2026, publicada e divulgada pelo INSS/Gov.br.
- IRRF mensal 2026: Receita Federal, tabela mensal de 2026 e Lei nº 15.270/2025, com desconto simplificado e redução mensal.

As URLs oficiais e a data de verificação ficam registradas nos metadados normativos usados pelo cálculo.

## Limites explícitos

A ferramenta não tenta substituir uma folha completa. O cálculo assume um único vínculo CLT regular e não cobre, neste lote:

- múltiplos vínculos previdenciários;
- férias;
- 13º salário;
- rescisão;
- regimes previdenciários especiais;
- apuração jurídica automática de vale-transporte, alimentação, plano de saúde ou outros descontos.

Esses valores podem ser informados quando já apurados pelo usuário, conforme o contrato da ferramenta.

## Verificações executadas

### Aprovadas

- `php scripts/lint-php.php`: 1.459 arquivos PHP sem erro de sintaxe.
- `php artisan tools:check-architecture`: arquitetura validada sem violações.
- smoke tests diretos do domínio/Core:
  - IRRF compartilhado de 2026;
  - salário de R$ 5.000,00;
  - salário de R$ 10.000,00 e teto do INSS;
  - regressão do `ProLaboreSimulator`;
  - regressão do `ProLaboreProfitDistributionCalculator`.

### Não executadas por limitação do ambiente

- PHPUnit: o PHP disponível no ambiente não possui `dom`, `mbstring` e `xmlwriter`.
- Pint: o PHP disponível no ambiente não possui `mbstring` e `xml`.

Os testes correspondentes foram adicionados ao projeto e permanecem disponíveis para execução em um ambiente PHP que satisfaça os requisitos do projeto.

## Continuidade obrigatória para o próximo lote

Antes de iniciar o próximo lote de expansão:

1. reler o ZIP original recebido do usuário;
2. reler este lote e todos os lotes de expansão já entregues;
3. reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md` e `config/product_tools.php`;
4. preservar a ferramenta `NetSalaryCalculator` e a promoção do IRRF mensal ao Core;
5. não promover regras adicionais ao Core sem novo uso equivalente comprovado.
