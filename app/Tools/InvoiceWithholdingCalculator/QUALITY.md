# Qualidade — Calculadora de Retenções na Nota Fiscal

- Natureza: `Calculation`
- Dependência normativa: `High`
- Dados pessoais: `None`
- Integração externa: `None`
- Persistência: `History`
- Processamento: `Synchronous`
- Risco: `Tax`
- Atualização: `Unpredictable`
- Exportações: PDF e XLSX

O cálculo é paramétrico e não usa `float`: valores são processados com `Money` e percentuais com `Percentage`. A ferramenta não infere automaticamente a incidência de IRRF, INSS, ISS, PIS/Pasep, Cofins ou CSLL; o usuário confirma aplicabilidade, alíquota e base do caso concreto.
