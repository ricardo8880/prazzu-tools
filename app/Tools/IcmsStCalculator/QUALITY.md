# Qualidade — Calculadora de ICMS-ST

- Natureza: `Calculation`
- Dependência normativa: `High`
- Dados pessoais: `None`
- Integração externa: `None`
- Persistência: `History`
- Processamento: `Synchronous`
- Risco: `Tax`
- Atualização: `Unpredictable`
- Exportações: PDF e XLSX

Os casos dourados cobrem cenário típico, fronteira, inválido, arredondamento, não aplicável, transição normativa e regressão. O domínio usa `Money` e `Percentage`, sem `float` para cálculos financeiros. A ferramenta é deliberadamente paramétrica porque ICMS-ST depende da legislação estadual, do produto e da operação.
