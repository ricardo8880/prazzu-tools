# Qualidade — Calculadora de Comissão de Vendedores

## Evidência revisada no Beta Activation — Lote 2

- [x] Base comissionável é faturamento menos estornos; estorno acima do faturamento é rejeitado.
- [x] Comissão-base e bônus usam `Money::percentage` e não usam `float` no domínio.
- [x] Meta e bônus são opcionais no cálculo individual; o usuário não precisa preencher zero artificialmente.
- [x] A meta, quando informada, é avaliada sobre a mesma base líquida de estornos usada na comissão.
- [x] O resultado mostra base, comissão-base, bônus, total e atingimento da meta.
- [x] Limites contratuais, competência, teto e devoluções futuras não são inferidos silenciosamente.
- [x] Golden cases concretos cobrem meta atingida, base zerada, estorno inválido, percentual fracionário, não aplicação e mudança de política.
- [x] Testes unitários protegem meta atingida e cálculo sem meta.
- [x] O cálculo individual permanece Essencial; processamento em lote continua produtividade Plus.

A ferramenta permanece `beta` até os gates globais dos lotes 10/11. A ferramenta é paramétrica e não afirma existir percentual legal universal de comissão.
