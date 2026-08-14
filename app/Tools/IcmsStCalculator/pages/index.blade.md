# Calculadora de ICMS-ST

## Objetivo

Estimar ICMS-ST de uma operação com parâmetros fiscais confirmados pelo usuário, mantendo ICMS próprio e DIFAL em ferramentas especializadas separadas.

## Funcionamento

O usuário informa competência, valores que compõem a base, MVA, alíquota interna e dados da operação. O domínio forma a base ST, calcula ICMS próprio quando necessário e determina ICMS-ST; recursos avançados permitem MVA ajustada, FCP e cenários interestaduais.

## Implementação principal

- View: `app/Tools/IcmsStCalculator/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Rotas: `Routes/web.php`.

## Conteúdos e estados

A página exibe base, MVA aplicada, ICMS próprio, ICMS-ST, FCP quando informado, memória e alertas. A incidência de ST, NCM/CEST, MVA, reduções e alíquotas são responsabilidade de enquadramento do usuário.

## Dependências

Reutiliza `Money`, `Percentage`, arredondamento, memória, histórico e exportação do Core. Não consulta tabela estadual ou cadastro de mercadoria em tempo de execução.

## Prazzu Plus

MVA ajustada, FCP, operação interestadual, múltiplos itens, exportação e histórico ampliam o caso de uso. A operação básica com MVA informada permanece Essencial.

## Regras de manutenção

Não deduzir incidência de ST por NCM/CEST sem fonte estadual versionada. Alterações de fórmula ou regra devem preservar parametrização, fonte e testes de fronteira.

## Validação mínima

Validar operação interna, interestadual, MVA ajustada, FCP, ICMS próprio informado/automático, múltiplos itens, arredondamento, entrada inválida, manifesto, catálogo, E2E e governança Plus.
