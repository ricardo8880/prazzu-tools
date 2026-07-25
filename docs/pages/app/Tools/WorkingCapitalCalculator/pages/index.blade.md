# Calculadora de Capital de Giro

## Objetivo

Calcular a necessidade operacional de capital de giro, o capital circulante
líquido e a necessidade adicional de recursos a partir de saldos de uma mesma
data-base.

## Funcionamento

- `GET /ferramentas/capital-de-giro` apresenta o formulário público.
- `POST /ferramentas/capital-de-giro` valida e calcula a posição.
- São informados caixa, contas a receber, estoques, outros ativos,
  fornecedores, obrigações operacionais, empréstimos e outros passivos.
- O domínio calcula ativos e passivos operacionais, NCG, ativo e passivo
  circulante, CCL e eventual lacuna de financiamento.
- Todos os campos devem representar a mesma data-base.

## Implementação principal

- **View:** `app/Tools/WorkingCapitalCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.capital-de-giro.index` e
  `tools.capital-de-giro.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A página estende `layouts.app`, declara título, meta description e canonical e
usa `<x-tools.page>`, formulário e resultado compartilhados.

## Conteúdos

- oito saldos circulantes com ajuda contextual;
- capital de giro necessário;
- necessidade de capital de giro;
- capital circulante líquido;
- necessidade adicional de recursos;
- memória das composições e fórmulas.

## Estados

- **Inicial:** todos os saldos começam em zero.
- **Inválido:** erros de formato ou valores negativos.
- **Calculado sem necessidade:** capital necessário ou lacuna podem ser zero.
- **Calculado com necessidade:** quatro métricas e memória detalhada.
- **Limpar:** link retorna à rota principal e remove o resultado da tela.

## Dependências

Depende de `Money`, contratos compartilhados de cálculo, regras monetárias,
componentes Blade, Bootstrap e motor interno do módulo.

## Manutenção

Não converter a página em controle financeiro contínuo. Novos indicadores
precisam preservar a distinção entre NCG e CCL e usar a mesma data-base. A
feature Plus `projections` está declarada, mas não possui rota ou interface;
projeções futuras, gráficos, cenários e histórico financeiro ainda não estão
disponíveis.

## Validação mínima

- verificar GET público, layout, SEO e canonical;
- testar NCG positiva, nula e negativa;
- testar CCL suficiente e lacuna de financiamento;
- conferir fórmulas e valores por cálculo manual;
- rejeitar saldos negativos e validar ação de limpar e responsividade.
