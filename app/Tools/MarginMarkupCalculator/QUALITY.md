# Qualidade — Calculadora de Margem e Markup

## Perfil

- Natureza: cálculo financeiro gerencial.
- Dependência normativa: nenhuma.
- Dados pessoais: nenhum no cálculo principal.
- Integração externa: opcional; não é requisito para a fórmula.
- Persistência: histórico autenticado.
- Resultado: financeiro estimativo.

## Evidência verificada

- [x] Cenário principal possui regressão monetária para custo total, preço, lucro e markup.
- [x] Custo total zero é rejeitado no domínio.
- [x] Soma de margem e deduções igual ou superior ao denominador válido é bloqueada.
- [x] Cálculo usa `Money`, `Percentage`, `IntegerRounding` e memória estruturada, sem `float` no domínio.
- [x] Política de HalfUp e diferença entre margem e markup estão documentadas no README.
- [x] Custos fixos precisam ser previamente rateados; o módulo não inventa rateio empresarial.
- [x] Essencial entrega o cálculo individual completo; lote, cenários, importação e histórico são produtividade Plus.
- [x] Casos dourados são derivados do teste unitário executável e não usam referência placeholder.
