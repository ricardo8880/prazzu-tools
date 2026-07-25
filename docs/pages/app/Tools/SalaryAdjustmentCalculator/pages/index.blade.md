# Calculadora de Reajuste Salarial

## Objetivo

Calcular o novo salário e os efeitos financeiros de um reajuste percentual,
com eventual parcela fixa e meses retroativos.

## Funcionamento

- `GET /ferramentas/reajuste-salarial` exibe o formulário público.
- `POST /ferramentas/reajuste-salarial` valida e executa o cálculo.
- Entradas: salário atual, percentual, aumento fixo e quantidade de meses
  retroativos.
- O resultado mostra novo salário, diferença mensal, retroativo e impacto anual.
- O impacto anual considera 12 salários, 13º e adicional constitucional de
  férias, sem encargos patronais.

## Implementação principal

- **View:** `app/Tools/SalaryAdjustmentCalculator/Resources/views/index.blade.php`
- **Rotas:** `tools.reajuste-salarial.index` e
  `tools.reajuste-salarial.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, define título, descrição SEO e canonical e usa
componentes Blade compartilhados e Bootstrap.

## Conteúdos

- formulário de salário, percentual, parcela fixa e retroatividade;
- novo salário;
- diferenças mensal e retroativa;
- impacto anual;
- memória das fórmulas;
- aviso sobre limitações de convenções coletivas.

## Estados

- **Inicial:** valor fixo e meses retroativos iniciados em zero.
- **Inválido:** salário, percentual, parcela ou meses rejeitados.
- **Calculado sem retroativo:** diferença retroativa igual a zero.
- **Calculado com retroativo:** quatro métricas e memória detalhada.

## Dependências

Usa `Money`, `Percentage`, contratos compartilhados de cálculo, componentes
Blade, validações monetárias e Bootstrap.

## Manutenção

A ferramenta não interpreta CCT. Pisos, tetos, compensações e cláusulas
específicas devem permanecer sob revisão profissional. Preservar cálculo sem
`float` e descrição do impacto anual. A feature Plus `advanced_productivity`
não possui rota ou interface; lote, histórico por funcionário, exportações e
relatórios não estão disponíveis.

## Validação mínima

- verificar GET público, layout, SEO e canonical;
- testar somente percentual, somente valor fixo e combinação;
- testar zero e limite de meses retroativos;
- conferir impacto anual e arredondamento por cálculo manual;
- rejeitar salário inválido e percentual fora de 0% a 100%.


## Estado após o Lote 5

A página deve apresentar resultados coerentes com a memória estruturada do domínio e deixar visíveis as premissas estimativas ou os valores informados manualmente pelo usuário.
