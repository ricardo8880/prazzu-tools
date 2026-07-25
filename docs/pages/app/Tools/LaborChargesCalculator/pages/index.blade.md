# Calculadora de Encargos Trabalhistas

## Objetivo

Estimar o custo mensal provisionado de um empregado, discriminando benefícios,
13º, férias, terço constitucional, FGTS, CPP, RAT e terceiros.

## Funcionamento

- `GET /ferramentas/encargos-trabalhistas` abre o formulário público.
- `POST /ferramentas/encargos-trabalhistas` valida e calcula o caso individual.
- O usuário informa salário, benefícios, regime, RAT ajustado e terceiros.
- O domínio cria as provisões mensais, forma a base de incidência e calcula cada
  encargo conforme o enquadramento escolhido.
- O resultado é uma estimativa mensal, não uma folha oficial.

## Implementação principal

- **View:** `app/Tools/LaborChargesCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.encargos-trabalhistas.index` e
  `tools.encargos-trabalhistas.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A página estende `layouts.app`, declara título, meta description e canonical e
usa os componentes Blade compartilhados e Bootstrap.

## Conteúdos

- salário, benefícios e enquadramento patronal;
- RAT e terceiros como premissas revisáveis;
- custo mensal total;
- total das provisões;
- FGTS e encargos patronais;
- memória por rubrica e alerta profissional.

## Estados

- **Inicial:** regime geral e percentuais sugeridos.
- **Inválido:** salário, benefício, regime ou percentuais rejeitados.
- **Calculado:** quatro métricas, memória e alerta.
- **Simples Nacional:** componentes zerados ou aplicados conforme o regime
  selecionado, sempre visíveis na memória.

## Dependências

Usa `Money`, `Percentage`, contratos de cálculo, componentes Blade e Bootstrap.
Referências registradas: Leis nº 8.212/1991 e nº 8.036/1990 e orientações
oficiais consultadas em 25/07/2026.

## Manutenção

Revisar incidências, CCT, FPAS, FAP e benefícios antes de alterar fórmulas.
Preservar alíquotas explícitas e cálculo sem `float`. A feature Plus
`advanced_productivity` não possui rota ou interface; comparações de regime,
relatórios por funcionário, histórico, exportação e simulações ainda não estão
disponíveis.

## Validação mínima

- conferir GET público, layout, metadados e canonical;
- testar regime geral, Anexo IV e demais anexos;
- conferir cada provisão e encargo por cálculo manual;
- rejeitar salário nulo e percentuais fora dos limites;
- validar memória, alerta e responsividade.
