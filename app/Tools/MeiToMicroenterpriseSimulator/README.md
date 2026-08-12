# Simulador MEI → Microempresa

## Descrição

Ferramenta de planejamento para comparar o faturamento anual atual e projetado com o teto de receita do MEI e estimar o impacto econômico de uma futura operação como Microempresa. O módulo não realiza desenquadramento, não classifica CNAE/anexo e não substitui análise contábil do caso concreto.

## Funcionalidades

- comparação do faturamento anual projetado com o teto MEI vigente de referência;
- valor de folga ou excesso em relação ao teto;
- classificação da projeção em dentro do limite, excesso de até 20% ou excesso superior a 20%;
- projeção anual de faturamento, impostos e custos empresariais parametrizados;
- cálculo do peso dos custos sobre a receita e do ponto de diluição dos custos fixos;
- memória de cálculo, relatório PDF e planilha XLSX.

## Experiência Essencial

- faturamento anual atual;
- faturamento anual projetado;
- comparação com o teto MEI de referência de 2026, de R$ 81.000;
- indicação de folga ou excesso;
- leitura da faixa de excesso de até 20% ou acima de 20%;
- memória e avisos sobre os limites da simulação.

O Essencial resolve o problema principal sem exigir alíquota tributária, CNAE, anexo ou qualquer recurso Plus.

## Prazzu Plus

- alíquota efetiva estimada da ME informada pelo usuário;
- custo contábil e outros custos empresariais mensais;
- taxa de crescimento e projeção de 1 a 10 anos;
- custo tributário estimado, custo empresarial total e peso percentual sobre o faturamento;
- ponto de diluição dos custos fixos conforme percentual-alvo escolhido;
- relatório PDF e planilha XLSX por meio da infraestrutura compartilhada.

Durante a fase gratuita de lançamento, os recursos classificados como Plus continuam disponíveis conforme a política global do produto.

## Regras

A ferramenta usa referências normativas versionadas apenas para comparar o faturamento informado com os limites de receita do MEI. Ela não infere automaticamente o regime tributário posterior, o anexo do Simples Nacional, o Fator R, benefícios, ICMS/ISS fora do DAS ou obrigações acessórias.

Para 2026, o teto anual usado no cenário Essencial é de R$ 81.000. A faixa de 20% auxilia a leitura do impacto potencial do excesso, mas a data efetiva do desenquadramento depende do caso concreto, inclusive de a empresa estar ou não no ano de início de atividade.

Na projeção Plus, os limites de R$ 110.000 em 2027 e R$ 140.000 em 2028 seguem divulgação oficial de 29/06/2026. Para anos posteriores, o simulador mantém R$ 140.000 somente como referência projetiva e avisa que a regra vigente deve ser confirmada.

As estimativas de impostos da Microempresa usam exclusivamente a alíquota efetiva informada pelo usuário:

`impostos estimados = faturamento projetado × alíquota efetiva informada`

`custos fixos anuais = 12 × (custo contábil mensal + outros custos mensais)`

`custo empresarial total = impostos estimados + custos fixos anuais`

`ponto de diluição = custos fixos anuais ÷ percentual-alvo de peso fixo`

O ponto de diluição é econômico, não jurídico: indica o faturamento em que os custos fixos informados representam o percentual-alvo escolhido da receita.

### Referências normativas verificadas em 12/08/2026

- Portal do Empreendedor / Gov.br — condições para enquadramento como MEI e teto anual de R$ 81.000 em 2026.
- Portal do Simples Nacional / Receita Federal — regras de desenquadramento por excesso de receita e distinção entre excesso de até 20% e superior a 20%.
- Ministério do Empreendedorismo — “Teto do MEI”, publicado em 29/06/2026, com limites divulgados de R$ 110.000 para 2027 e R$ 140.000 para 2028.

## Dependências

- value objects `Money` e `Percentage` do Core;
- `IntegerRounding` e memória de cálculo compartilhada;
- infraestrutura compartilhada de exportação PDF/XLSX;
- componentes de formulário, resultado e validação do Prazzu Tools.

## Integração entre ferramentas

- Contratos publicados: Nenhum.
- Contratos aceitos: Nenhum.

## Integração com a plataforma

- Slug: `simulador-mei-microempresa`
- Rota principal: `tools.simulador-mei-microempresa.index`
- Namespace de views: `tools-simulador-mei-microempresa`
- Histórico: desabilitado
- Exportações: PDF e XLSX

## Histórico de versões

- `1.0.0` — implementação inicial em 12/08/2026 com Essencial, projeção Plus, custos, ponto de diluição e exportações.
