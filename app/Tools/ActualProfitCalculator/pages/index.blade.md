# Calculadora de Lucro Real

## Objetivo

Oferecer apuração assistida e transparente de IRPJ e CSLL no Lucro Real sem absorver PIS/Cofins, CBS/IBS ou a função de comparação de regimes tributários.

## Funcionamento

O usuário informa período, lucro contábil, adições, exclusões e, quando aplicável, saldos compensáveis de prejuízo fiscal do IRPJ e base negativa da CSLL. O domínio forma as bases e aplica a regra normativa compartilhada de IRPJ/CSLL.

## Implementação principal

- View: `app/Tools/ActualProfitCalculator/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Regra compartilhada: `app/Core/Tax/Normative/ActualProfitIncomeTaxRule.php`.
- Rotas: `Routes/web.php`.

## Conteúdos e estados

Exibe entradas fiscais, resultado de IRPJ, adicional quando aplicável, CSLL, total estimado, memória de cálculo, premissas e alertas. Entradas inválidas ou incompatíveis são rejeitadas antes do domínio.

## Dependências

Não depende de serviço externo. A regra compartilhada existe por reutilização concreta também no Comparador Tributário; PIS/Cofins e Reforma do Consumo permanecem em módulos próprios.

## Prazzu Plus

`tax_base_diagnostics` acrescenta diagnóstico detalhado das bases e ajustes. O cálculo principal permanece Essencial.

## Regras de manutenção

Mudanças de alíquota, limite, adicional, compensação ou vigência devem ser tratadas como alteração normativa explícita, com fonte oficial, competência e regressão. Não duplicar a regra equivalente dentro do módulo.

## Validação mínima

Validar casos comuns, adicional, bases zeradas, compensações, entradas inválidas, manifesto, catálogo, rota, E2E, governança Plus e regressão da regra compartilhada com o Comparador Tributário.
