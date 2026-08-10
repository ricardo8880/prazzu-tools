# Calculadora de IRPJ e CSLL — Lucro Presumido

Arquivo: `app/Tools/PresumedProfitIrpjCsllCalculator/Resources/views/index.blade.php`

## Objetivo

Permitir a apuração estimada trimestral de IRPJ, adicional de IRPJ e CSLL para pessoas jurídicas em geral no Lucro Presumido, dentro do escopo normativo versionado de 2026.

## Funcionamento

A página recebe o trimestre, receitas por grupo de atividade, adições tributáveis integralmente e, no bloco Plus, acumulados anteriores e créditos/retenções confirmados. O domínio resolve a regra normativa pela data de referência do trimestre, separa a parcela de receita sujeita ao percentual normal e ao percentual majorado de 2026, calcula as bases presumidas, aplica IRPJ, adicional e CSLL e devolve resultado padronizado com memória de cálculo.

O cálculo não usa `float`: valores monetários passam por `Money`, percentuais por `Percentage` e rateios usam `IntegerRounding` com `HalfUp`.

## Essencial

- seleção do trimestre de 2026;
- receitas de comércio/indústria, revenda de combustíveis elegível, transporte de passageiros e serviços em geral;
- adições tributáveis integralmente;
- bases de IRPJ e CSLL;
- IRPJ de 15%, adicional de 10% quando aplicável e CSLL de 9%;
- memória de cálculo, premissas, alertas e fonte normativa.

## Prazzu Plus

- múltiplas atividades no mesmo período;
- ajuste da faixa normal com receitas dos trimestres anteriores;
- créditos e retenções compensáveis informados pelo usuário;
- histórico autenticado;
- exportação PDF e XLSX.

## Estados e validações

- exige receita positiva em ao menos uma atividade;
- exige confirmação explícita do enquadramento e das premissas fiscais;
- não aceita acumulado anterior de IRPJ no 1º trimestre;
- não aceita acumulado anterior de CSLL antes do 3º trimestre, pois a transição da CSLL em 2026 começa no 2º trimestre;
- entradas monetárias usam os validadores compartilhados do Core;
- instituições financeiras e situações com regime/base/alíquota específica são declaradas fora do escopo.

## Dependências

- `App\Core\Money\Money` e `Percentage`;
- `App\Core\Math\IntegerRounding`;
- `App\Core\Normative\NormativeRuleResolver`;
- contratos compartilhados de cálculo, memória, histórico, Analytics e exportação;
- Bootstrap e componentes `x-tools.*`.

## Manutenção

Antes de alterar percentuais, limites ou vigência, atualizar a regra versionada em `Domain/Rules`, referências oficiais em `docs/NORMATIVE_RULES.md`, casos dourados, testes unitários e documentação do lote. Mudanças normativas não devem ser codificadas diretamente na view.
