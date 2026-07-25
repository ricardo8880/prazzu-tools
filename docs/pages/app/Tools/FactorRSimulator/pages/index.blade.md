# Simulador de Fator R

## Objetivo

Calcular o Fator R a partir dos acumulados dos doze meses anteriores e indicar
o enquadramento matemático entre os Anexos III e V, quando a atividade estiver
sujeita a essa regra.

## Funcionamento

- `GET /ferramentas/simulador-fator-r` exibe o formulário público.
- `POST /ferramentas/simulador-fator-r` valida FS12 e RBT12 e executa o cálculo.
- Fator igual ou superior a 28% indica Anexo III; inferior indica Anexo V.
- O resultado mostra a folha de referência correspondente a 28% da receita e a
  diferença de folha necessária para alcançar o limite.
- Casos de acumulados zerados seguem as regras explícitas do domínio.

## Implementação principal

- **View:** `app/Tools/FactorRSimulator/Resources/views/index.blade.php`
- **Rotas:** `tools.simulador-fator-r.index` e
  `tools.simulador-fator-r.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A view estende `layouts.app`, declara título, descrição SEO e canonical e usa os
componentes compartilhados de página, formulário, resultado e validação.

## Conteúdos

- entradas FS12 e RBT12 com ajuda contextual;
- Fator R formatado;
- anexo indicado pela razão matemática;
- folha necessária e diferença até o limite;
- memória de cálculo e versão da regra;
- aviso sobre atividade, segregação de receitas e PGDAS-D.

## Estados

- **Inicial:** acumulados iniciados em zero.
- **Inválido:** dinheiro malformado ou negativo.
- **Calculado abaixo de 28%:** indicação do Anexo V e diferença de folha.
- **Calculado em 28% ou acima:** indicação do Anexo III.
- **Acumulados zerados:** resultado conforme tratamento explícito do domínio.

## Dependências

Usa `Money`, `Percentage`, contratos de cálculo e componentes Blade. Referências
registradas: Resolução CGSN nº 140/2018, art. 26, alterações consultadas em
25/07/2026 e Manual do PGDAS-D.

## Manutenção

A página não determina se a atividade está sujeita ao Fator R. Alterações
normativas exigem revisão da constante de versão, casos de fronteira, testes e
deste documento. A feature Plus `advanced_productivity` ainda não tem fluxo:
simulação mensal/anual, projeções, cenários, alertas, planejamento e histórico
não estão disponíveis.

## Validação mínima

- confirmar GET público, layout, SEO e canonical;
- testar abaixo, exatamente em e acima de 28%;
- testar FS12 e RBT12 zerados nas combinações suportadas;
- conferir arredondamento, diferença de folha e memória;
- verificar aviso normativo e comportamento responsivo.
