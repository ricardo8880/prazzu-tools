# Qualidade — Calculadora de Depreciação de Ativos

## Evidência revisada no Beta Activation — Lote 2

- [x] O cálculo trabalha com valor do bem, valor residual, base depreciável, vida útil e método de forma explícita.
- [x] Valor residual negativo ou maior/igual ao valor do bem é rejeitado.
- [x] Método linear preserva o residual e absorve diferenças de centavos no último período.
- [x] Métodos acelerados nunca depreciam abaixo do valor residual informado.
- [x] A projeção consolidada mantém o residual de ativos cuja vida útil terminou antes dos demais.
- [x] Todos os valores usam `Money`/`IntegerRounding`; o domínio não usa `float`.
- [x] Golden cases concretos cobrem residual típico, vida útil mínima, residual inválido, arredondamento, não aplicação e mudança de política.
- [x] Testes unitários protegem residual, métodos acelerados, múltiplos ativos e consolidação após o fim de um ativo.
- [x] O README deixa claro que vida útil, residual, enquadramento fiscal e elegibilidade não são inferidos pela ferramenta.
- [x] O Essencial resolve um ativo pelo método linear com residual; métodos adicionais, múltiplos ativos e consolidação permanecem Plus.

A ferramenta permanece `beta` até os gates globais dos lotes 10/11; a ferramenta é paramétrica e não substitui definição da política contábil/fiscal aplicável.
