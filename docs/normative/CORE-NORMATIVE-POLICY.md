# Política do Core normativo

Este documento complementa o README da raiz e não o substitui.

## Regras obrigatórias

1. Taxas, faixas, limites e fórmulas legais devem pertencer ao domínio da ferramenta ou a um catálogo normativo compartilhado quando houver reutilização real.
2. Toda regra normativa precisa de identificador estável, versão semântica, vigência, fonte oficial, data de verificação e responsável.
3. O cálculo deve resolver a regra pela data de referência; controllers não escolhem versões.
4. Resultados persistidos devem guardar `NormativeRuleSnapshot`, permitindo reprodução histórica.
5. Valores monetários usam `Money`; percentuais usam `Percentage`; divisões usam `IntegerRounding`. `float` não é aceito para valores financeiros.
6. A memória deve registrar fórmula, entradas normalizadas, resultado e política de arredondamento.
7. Estimativas devem ser marcadas com `is_estimate=true` e listar premissas e limitações.
8. Nenhuma tabela oficial é adicionada ao Core sem fonte verificável e caso dourado real.

## Fluxo de adoção

Os lotes 3 a 9 devem migrar cada ferramenta para este padrão sem criar dependência direta entre módulos. O Core fornece contratos e objetos de valor; cada ferramenta mantém as suas regras de negócio e linguagem de produto.
