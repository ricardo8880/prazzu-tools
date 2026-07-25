# Calculadora de Ponto de Equilíbrio

## Objetivo

Determinar o faturamento e a quantidade mínima de unidades necessários para
cobrir os custos fixos e variáveis de um produto ou serviço.

## Funcionamento

- `GET /ferramentas/ponto-de-equilibrio` exibe o formulário público.
- `POST /ferramentas/ponto-de-equilibrio` valida e calcula o caso individual.
- As entradas são custos fixos do período, preço de venda unitário e custo
  variável unitário.
- A margem de contribuição deve ser positiva.
- A quantidade é arredondada para cima até a primeira unidade inteira que cobre
  os custos; o faturamento usa essa quantidade.

## Implementação principal

- **View:** `app/Tools/BreakEvenCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.ponto-de-equilibrio.index` e
  `tools.ponto-de-equilibrio.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, declara título, meta description e canonical, e
compõe a interface com `<x-tools.page>`, painéis de formulário e resultado.

## Conteúdos

- formulário com três valores monetários;
- faturamento mínimo e quantidade mínima;
- margem de contribuição unitária e percentual;
- memória reproduzível das fórmulas;
- painel compartilhado dos tiers Essencial e Plus.

## Estados

- **Inicial:** formulário sem resultado.
- **Inválido:** erros para dinheiro malformado, preço não positivo ou custo
  variável maior ou igual ao preço.
- **Calculado:** quatro métricas e memória ficam disponíveis na mesma página.

## Dependências

Usa `Money`, o contrato compartilhado de resultado de cálculo, validações
monetárias, componentes Blade, Bootstrap e o domínio interno do módulo.

## Manutenção

Preservar o arredondamento para unidade inteira e evitar `float`. Alterações de
campos, fórmulas ou resumo exigem sincronização com o DTO, domínio, testes e este
documento. A feature Plus `advanced_productivity` ainda não possui rota ou
interface; simulações por produto, gráficos, histórico e exportações não devem
ser descritos como disponíveis.

## Validação mínima

- verificar GET público, HTML completo, SEO e canonical;
- testar ponto de equilíbrio exato e caso que exige arredondamento;
- testar custos fixos iguais a zero;
- rejeitar margem nula ou negativa antes de chamar o domínio;
- conferir formatação monetária, memória e comportamento responsivo.
