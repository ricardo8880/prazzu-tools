# Qualidade — Calculadora de Rescisão Trabalhista

## Perfil

- Natureza: cálculo trabalhista.
- Dependência normativa: alta.
- Dados pessoais: sensíveis quando o histórico é utilizado.
- Integração externa: nenhuma para executar o cálculo.
- Persistência: histórico autenticado e criptografado conforme política do módulo.
- Resultado: trabalhista estimativo.
- Atualização: revisão ao menos anual e sempre que regra aplicável mudar.

## Evidência verificada

- [x] Dispensa sem justa causa, pedido de demissão, justa causa e acordo mútuo possuem testes unitários independentes.
- [x] Aviso proporcional e projeção da data de término possuem regressão com valores conhecidos.
- [x] Contratos por prazo determinado/experiência possuem rejeição de combinação incompatível e caso do art. 479.
- [x] Remuneração variável e férias em dobro possuem regressão sobre a base integrada.
- [x] Emprego doméstico possui regressões específicas para indenização compensatória e pedido de demissão.
- [x] INSS progressivo e redução do IRRF de 2026 possuem testes dedicados em `PayrollTaxCalculatorTest`.
- [x] O README registra a Portaria Interministerial MPS/MF nº 13/2026 para a tabela previdenciária adotada.
- [x] O README explicita limites para estabilidade, normas coletivas, afastamentos, médias complexas e decisões judiciais.
- [x] Memória de cálculo estruturada é protegida por teste.
- [x] Dinheiro utiliza `Money`, sem `float` no domínio.
- [x] Histórico declara todos os campos de entrada como sensíveis e retenção de 180 dias.
- [x] Casos dourados cobrem cenário típico, fronteira, inválido, arredondamento, não aplicação, transição normativa e regressão.
- [x] Essencial entrega verbas, descontos, tributos, FGTS e relatório atual; Plus adiciona apenas continuidade.
