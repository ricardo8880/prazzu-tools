# Calculadora de Comissão de Vendedores

## Objetivo

Calcular a comissão de um vendedor em um único período usando faturamento,
percentual-base, meta e bônus por atingimento.

## Funcionamento

- `GET /ferramentas/comissao-vendedores` abre o formulário público.
- `POST /ferramentas/comissao-vendedores` valida e calcula o caso.
- A comissão-base é o faturamento multiplicado pelo percentual informado.
- Quando existe meta positiva e ela é alcançada, o bônus é aplicado sobre o
  faturamento.
- Meta igual a zero desativa meta, bônus e percentual de atingimento.

## Implementação principal

- **View:** `app/Tools/SalesCommissionCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.comissao-vendedores.index` e
  `tools.comissao-vendedores.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A página estende `layouts.app`, declara título, meta description e canonical e
usa componentes compartilhados sobre Bootstrap.

## Conteúdos

- faturamento e percentual-base;
- meta e percentual de bônus;
- comissão-base;
- bônus por meta;
- comissão total e atingimento;
- memória reproduzível.

## Estados

- **Inicial:** meta e bônus iniciados em zero.
- **Inválido:** erros monetários ou percentuais fora dos limites.
- **Sem meta:** indicação textual de meta não definida.
- **Meta não atingida:** bônus zerado.
- **Meta atingida:** bônus somado e percentual de atingimento exibido.

## Dependências

Depende de `Money`, `Percentage`, contratos de cálculo, validações monetárias,
componentes Blade e Bootstrap. Não depende de cadastro de vendedores.

## Manutenção

Manter explícito que o bônus atual incide sobre faturamento. Uma regra diferente
exige novo campo e novo caso de domínio, não condição escondida na view. A
feature Plus `advanced_productivity` não possui fluxo; regras personalizadas,
relatórios por vendedor, histórico e exportação ainda não estão disponíveis.

## Validação mínima

- conferir GET público, layout, metadados e canonical;
- testar meta inexistente, abaixo, exatamente igual e acima;
- testar percentuais iguais a zero e nos limites;
- conferir comissão e atingimento por cálculo manual;
- rejeitar dinheiro e percentuais inválidos e validar responsividade.
