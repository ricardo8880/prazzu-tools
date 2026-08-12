# Calculadora de DAS Retroativo + Regularização do Simples

## Escopo distinto da Calculadora de DAS em Atraso

`das-em-atraso` atualiza um principal de DAS já conhecido. Esta ferramenta **reconstitui um principal estimado** a partir da competência, faturamento e alíquota efetiva informada, e então atualiza o débito. O Plus consolida várias competências e cria um plano financeiro de regularização.

## Essencial

- competência;
- faturamento e alíquota efetiva informada;
- vencimento, data de atualização e Selic acumulada informada;
- principal estimado = faturamento × alíquota;
- multa de 0,33% ao dia, limitada a 20%;
- juros = Selic acumulada informada + 1% do mês de pagamento/atualização;
- total e memória normativa.

## Plus

- até 12 competências;
- consolidação da dívida;
- cronograma financeiro de quitação;
- cenários de prazo;
- PDF/XLSX.

O cronograma não é parcelamento oficial. A ferramenta não recalcula PGDAS-D, anexo, Fator R, segregações, monofásicos, substituição tributária ou benefícios. A regra de mora reutiliza `App\Core\Tax\Normative\LateDasRule`, já compartilhada com a Calculadora de DAS em Atraso.

## Referências verificadas em 12/08/2026

Receita Federal/Sicalc e Manual PGDAS-D: multa de mora de 0,33% ao dia limitada a 20%; juros com Selic a partir do mês seguinte ao vencimento e 1% no mês do pagamento, conforme metodologia oficial aplicável.
