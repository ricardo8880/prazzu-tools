# Simulador CLT × PJ × Autônomo

## Objetivo

Comparar, em um único caso mensal, a remuneração líquida estimada e o custo para
a empresa nas modalidades CLT, PJ e autônomo. A comparação é financeira e não
determina vínculo empregatício, legalidade da contratação ou tributação
definitiva.

## Funcionamento

- `GET /ferramentas/comparador-clt-pj-autonomo` abre o formulário público.
- `POST /ferramentas/comparador-clt-pj-autonomo` valida e compara os modelos.
- CLT recebe salário, benefícios e percentuais estimados de descontos do
  trabalhador e encargos da empresa.
- PJ recebe nota mensal, percentual de tributos e despesas próprias.
- Autônomo recebe remuneração bruta, descontos e encargo empresarial.
- O resultado ordena as modalidades, destaca o maior líquido e mantém custo
  empresarial e líquido separados.

## Implementação principal

- **View:** `app/Tools/EmploymentModelComparator/Resources/views/index.blade.php`
- **Rotas:** `tools.comparador-clt-pj-autonomo.index` e
  `tools.comparador-clt-pj-autonomo.calculate`
- **Controller:** `Presentation/Controllers/ToolController`
- **Validação:** `Presentation/Requests/ExecuteToolRequest`
- **Action:** `Application/Actions/CalculateTool`
- **Domínio:** `Domain/Services/Calculator`

A página estende `layouts.app`, fornece título, meta description e canonical e
usa `<x-tools.page>` e os componentes compartilhados sobre Bootstrap.

## Conteúdos

- três grupos de premissas mensais;
- métricas de líquido e custo por modalidade;
- indicação do maior líquido estimado;
- tabela comparativa;
- ressalva sobre vínculo e incidências;
- apresentação dos tiers da ferramenta.

## Estados

- **Inicial:** formulário com percentuais estimados sugeridos, sempre editáveis.
- **Inválido:** erros monetários ou percentuais fora de 0% a 100%.
- **Calculado:** métricas, tabela de comparação e ressalva jurídica.
- **Líquido negativo:** pode ocorrer quando despesas superam receita e deve ser
  exibido como consequência das premissas, não como recomendação.

## Dependências

Depende de `Money`, `Percentage`, contratos compartilhados de cálculo,
validações monetárias, componentes Blade e Bootstrap. Não importa regras de
outra ferramenta.

## Manutenção

Os percentuais são estimativas informadas, não tabelas oficiais automáticas.
Manter a ressalva de que a ferramenta não valida pejotização ou vínculo. A
feature Plus `advanced_productivity` não possui rota ou interface; gráficos,
cenários, histórico, comparações ilimitadas e exportações ainda não estão
disponíveis.

## Validação mínima

- conferir GET público, documento HTML, SEO e canonical;
- testar as três modalidades com valores conhecidos;
- validar ordenação e empate de líquidos;
- rejeitar percentuais fora dos limites e dinheiro inválido;
- verificar tabela, ressalva, responsividade e ausência de recomendação jurídica.
