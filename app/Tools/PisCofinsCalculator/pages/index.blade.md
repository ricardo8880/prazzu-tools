# Calculadora PIS e COFINS

## Objetivo

Apurar PIS/Pasep e Cofins nos regimes cumulativo e não cumulativo dentro do escopo normativo declarado pelo módulo, usando bases já classificadas pelo usuário e sem inferir tratamentos especiais.

## Funcionamento

O usuário informa competência, regime, receita tributável, retenções e, quando aplicável, bases de crédito. O domínio calcula débitos, créditos e saldo conforme a regra versionada da competência.

## Implementação principal

- View: `app/Tools/PisCofinsCalculator/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Regra normativa: consultar os artefatos normativos do próprio módulo.
- Rotas: `Routes/web.php`.

## Conteúdos e estados

Exibe apuração por contribuição, créditos, retenções, saldo, memória de cálculo, premissas e alertas. Monofasia, alíquota zero, suspensão, substituição, importação, benefícios e regimes setoriais não são inferidos automaticamente.

## Dependências

Reutiliza objetos de valor monetário/percentual, memória, histórico e exportação do Core. A Reforma Tributária do Consumo permanece em ferramenta própria para não misturar vigências e regimes.

## Prazzu Plus

Créditos agregados, múltiplas operações, detalhamento de créditos, comparação de regimes, exportações e histórico ampliam análise e produtividade sem esconder a apuração principal.

## Regras de manutenção

Qualquer alteração de alíquotas, créditos, vigência ou transição deve ser versionada por competência e sustentada por fonte oficial e casos de regressão. Não incorporar CBS/IBS como continuação silenciosa da mesma regra.

## Validação mínima

Validar cumulativo e não cumulativo, créditos, retenções, saldo mínimo, múltiplas operações, comparação, entradas inválidas, manifesto, catálogo, Analytics, E2E, Plus e casos normativos.
