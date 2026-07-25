# Calculadora de DAS em Atraso

## Objetivo

Estimar multa, juros e valor total de um DAS pago após o vencimento, mantendo
visíveis as premissas utilizadas. A página não emite a guia oficial.

## Funcionamento

- `GET /ferramentas/das-em-atraso` exibe o formulário público.
- `POST /ferramentas/das-em-atraso` valida e calcula a atualização.
- O usuário informa principal, vencimento, data de pagamento e Selic acumulada
  até o mês anterior.
- A multa é calculada a 0,33% por dia e limitada a 20%.
- Em atraso, os juros usam a Selic acumulada informada mais 1% referente ao mês
  do pagamento.
- Pagamento até o vencimento mantém multa e juros zerados.

## Implementação principal

- **View:** `app/Tools/LateDasCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.das-em-atraso.index` e
  `tools.das-em-atraso.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, configura título, meta description e canonical e
compõe formulário, resultado e memória com componentes compartilhados.

## Conteúdos

- valor original e datas;
- Selic acumulada como premissa explícita;
- total atualizado, multa, juros e dias em atraso;
- memória detalhada;
- aviso de que a emissão oficial ocorre no PGDAS-D/Portal do Simples.

## Estados

- **Inicial:** data de pagamento sugerida como a data corrente e Selic zerada.
- **Inválido:** erros de dinheiro, datas ou percentual.
- **Sem atraso:** principal sem acréscimos.
- **Em atraso:** multa progressiva, juros e total.
- **Multa limitada:** após o número necessário de dias, permanece em 20%.

## Dependências

Depende de `Money`, `Percentage`, datas imutáveis, contratos de cálculo,
componentes Blade e Bootstrap. A Selic não é consultada automaticamente.

## Manutenção

Preservar a data de referência recebida e não substituir a entrada da Selic por
valor desatualizado. Mudanças normativas exigem revisão da versão de regra e dos
casos de fronteira. A feature Plus `advanced_productivity` ainda não tem fluxo;
múltiplos períodos, lote, relatório, histórico e exportação não estão
disponíveis.

## Validação mínima

- verificar GET público, layout, SEO e canonical;
- testar pagamento no vencimento, um dia após e após atingir o teto da multa;
- conferir Selic mais 1% e total por cálculo manual;
- rejeitar principal inválido e percentual fora do limite;
- validar memória, aviso e comportamento responsivo.
