# Calculadora de Fluxo de Caixa

## Objetivo

Calcular a posição prevista de caixa de um único mês a partir do saldo inicial,
das entradas e das saídas informadas.

## Funcionamento

- `GET /ferramentas/fluxo-de-caixa` abre a calculadora pública.
- `POST /ferramentas/fluxo-de-caixa` valida e calcula o período.
- O saldo inicial pode ser negativo; recebimentos e pagamentos devem ser
  valores não negativos.
- Entradas somam vendas e outras entradas.
- Saídas somam pagamentos operacionais, tributos, investimentos,
  financiamentos e outras saídas.
- O resultado apresenta movimento líquido, saldo final e geração operacional.

## Implementação principal

- **View:** `app/Tools/CashFlowCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.fluxo-de-caixa.index` e
  `tools.fluxo-de-caixa.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A página estende `layouts.app`, configura título, meta description e canonical,
e utiliza os componentes visuais compartilhados sobre Bootstrap.

## Conteúdos

- formulário de saldo inicial, três grupos de entradas e saídas;
- saldo final previsto;
- movimento líquido;
- total de entradas e de saídas;
- geração operacional;
- memória das somas e subtrações.

## Estados

- **Inicial:** valores de movimento iniciados em zero.
- **Validação inválida:** erros de formato ou valores negativos nas
  movimentações.
- **Calculado:** cinco métricas e memória detalhada na própria página.
- **Saldo negativo:** resultado permitido e exibido como diagnóstico, sem
  bloquear o cálculo.

## Dependências

Depende de `Money`, contratos compartilhados de cálculo, regras monetárias de
validação, componentes Blade e Bootstrap. O cálculo permanece interno e
independente de outras ferramentas.

## Manutenção

Esta página representa somente um mês e não mantém livro-caixa ou gestão
financeira contínua. Preservar essa fronteira de produto. A feature Plus
`advanced_productivity` não possui fluxo implementado; múltiplos meses,
dashboard, gráficos, cenários, histórico e exportação só podem ser documentados
depois de existirem.

## Validação mínima

- conferir GET público, layout, SEO e canonical;
- validar cenário com saldo inicial positivo e negativo;
- rejeitar entradas ou saídas negativas;
- conferir cada total e a geração operacional por cálculo manual;
- testar todos os valores zerados e responsividade da tabela de memória.
