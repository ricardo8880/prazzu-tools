# Qualidade — Calculadora de Capital de Giro

## Evidência revisada no Beta Activation — Lote 2

- [x] Fórmulas de NCG, CCL, necessidade adicional e folga estão explícitas no domínio e na memória de cálculo.
- [x] O resultado diferencia déficit de financiamento de folga de capital circulante, sem esconder saldo negativo.
- [x] Todos os valores monetários usam `Money`/centavos; o domínio não usa `float`.
- [x] O `FormRequest` impede saldos negativos e exige todas as rubricas do cálculo Essencial.
- [x] Premissas de mesma data-base, classificação operacional/financeira e sazonalidade estão visíveis.
- [x] Golden cases concretos cobrem cenário típico, fronteira, entrada inválida, centavos e não aplicação.
- [x] Testes unitários protegem o caso com déficit e o caso com folga.
- [x] Exportação e histórico reutilizam a infraestrutura compartilhada do Core.
- [x] O Essencial entrega integralmente NCG, CCL, déficit/folga e memória; cenários continuam produtividade Plus.

A ferramenta permanece `beta` nesta frente até o gate global de ativação dos lotes 10/11; este arquivo não declara `composer release:check` executado neste ambiente.
