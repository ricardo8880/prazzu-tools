# Relatório consolidado de custos CLT

- **Tipo:** Documento de saída/impressão
- **Implementação principal:** `app/Tools/EmployeeCostCalculator/Resources/views/report-batch.blade.php`
- **Fluxo:** `tools.custo-funcionario-clt.batch.print`
- **Status:** ativo

## Objetivo

Gerar uma versão imprimível do cálculo em lote com totais, funcionários,
departamentos e projeção-base de doze meses.

## Funcionamento e conteúdos

A partial recebe o resultado produzido por `CalculateEmployeeBatch` e uma
empresa opcional. Exibe quantidade, custos mensal e anual, detalhamento por
funcionário, consolidação por departamento e a premissa explícita da projeção.

## Estados e dependências

A identificação empresarial é opcional. O fluxo exige lote validado e utiliza o
`BrowserPrintExporter`; autenticação só é necessária para reutilizar um perfil
persistente.

## Regras de manutenção

- Reutilizar exclusivamente dados entregues pela Action.
- Preservar a premissa da projeção e o alerta de revisão trabalhista.
- Não introduzir gestão de folha, departamentos ou funcionários.

## Validação mínima

Validar totais, linhas individuais, consolidação por departamento, doze
competências e renderização com e sem empresa.
