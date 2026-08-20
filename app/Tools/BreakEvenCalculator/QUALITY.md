# Qualidade — Calculadora de Ponto de Equilíbrio

## Evidência revisada no Beta Activation — Lote 2

- [x] O domínio rejeita preço não positivo e margem de contribuição não positiva.
- [x] A quantidade mínima usa teto inteiro e o faturamento é calculado sobre a primeira unidade inteira que cobre os custos.
- [x] O resultado mostra a folga de contribuição criada pelo arredondamento para unidade inteira.
- [x] Custos fixos iguais a zero produzem ponto de equilíbrio zero com explicação explícita.
- [x] Proteção de overflow impede multiplicação monetária fora do intervalo suportado.
- [x] Golden cases concretos cobrem cenário típico, fronteira, domínio inválido, arredondamento e custo variável incompleto.
- [x] Testes unitários protegem arredondamento, margem inválida e caso sem custos fixos.
- [x] README exige incluir tributos, comissões e perdas no custo variável quando aplicáveis.
- [x] O Essencial entrega quantidade, faturamento, margem, contribuição, folga e memória; cenários alternativos são Plus.

A ferramenta permanece `beta` até a auditoria global de ativação dos lotes 10/11.
