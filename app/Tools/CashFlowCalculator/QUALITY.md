# Qualidade — Calculadora de Fluxo de Caixa

## Evidência revisada no Beta Activation — Lote 2

- [x] Entradas, saídas, movimento líquido, saldo final e geração operacional são reproduzíveis pela memória de cálculo.
- [x] Saldo inicial pode ser negativo; movimentações de entrada e saída não podem ser negativas.
- [x] Saldo final negativo gera alerta de risco de caixa e geração operacional negativa recebe alerta separado.
- [x] O cálculo usa regime de caixa de um único período e declara essa limitação ao usuário.
- [x] Todos os valores usam `Money`/centavos e não há `float` no domínio.
- [x] Golden cases concretos cobrem cenário típico, saldo zero, entrada inválida, centavos e não aplicação por competência.
- [x] Teste unitário protege simultaneamente saldo final negativo e geração operacional negativa.
- [x] Exportação, histórico e cenários reutilizam a infraestrutura existente sem dependência entre ferramentas.
- [x] O Essencial resolve o fluxo de um período por completo; comparação de cenários permanece Plus.

A ferramenta permanece `beta` até a validação global dos lotes 10/11; o lote não mascara limitações do ambiente como release aprovado.
