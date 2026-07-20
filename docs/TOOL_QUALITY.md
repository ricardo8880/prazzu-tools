# Qualidade e risco das ferramentas

Este documento define o contrato mínimo de qualidade para novas ferramentas do Prazzu Tools. Ele complementa o README da raiz, que continua sendo a autoridade máxima do projeto.

## Classificação obrigatória

Toda ferramenta nova deve possuir um `ToolRiskProfile` antes da implementação do domínio. O perfil registra:

- natureza da ferramenta;
- dependência normativa;
- exposição a dados pessoais;
- dependência de integração externa;
- forma de persistência;
- modo de processamento;
- risco do resultado;
- frequência esperada de atualização;
- formatos de exportação.

O `ToolRiskClassifier` transforma esse perfil em requisitos objetivos. O nível de risco não controla acesso comercial e não transforma a plataforma em sistema de gestão. Ele apenas determina o rigor de validação necessário.

## Casos dourados

Casos dourados são exemplos de referência revisados e estáveis usados para proteger a correção do domínio contra regressões.

Cada caso deve declarar:

- identificador estável;
- título;
- tipo do cenário;
- entrada completa;
- resultado esperado;
- origem da referência;
- versão normativa, quando aplicável;
- política de arredondamento, quando aplicável;
- tags opcionais.

Valores decimais não podem ser armazenados como `float`. Use strings decimais ou inteiros na menor unidade.

Tipos disponíveis:

- `typical`: cenário comum;
- `boundary`: limite de faixa ou valor;
- `invalid_input`: entrada rejeitada;
- `non_applicable`: situação em que a regra não se aplica;
- `rounding`: comportamento de arredondamento;
- `normative_transition`: mudança de vigência;
- `regression`: erro já identificado que não pode reaparecer.

## Requisitos derivados

Toda ferramenta exige ao menos casos comuns, de fronteira e de entrada inválida.

Ferramentas com resultado financeiro, trabalhista ou tributário também exigem casos de arredondamento e de não aplicação.

Ferramentas normativas exigem caso de transição de vigência e metadados definidos em `docs/NORMATIVE_RULES.md`.

Ferramentas de risco alto ou crítico exigem ao menos um caso de regressão e testes de navegador.

O classificador também informa quando são obrigatórios:

- revisão por especialista;
- revisão de privacidade;
- testes de resiliência de integrações;
- testes de falha em filas;
- testes de exportação;
- testes de navegador.

## Onde os casos pertencem

Os contratos genéricos ficam no Core. Os casos concretos pertencem à própria ferramenta, preferencialmente em `app/Tools/NomeDaFerramenta/Tests/Fixtures/GoldenCases` ou estrutura equivalente definida pelo gerador oficial.

Uma ferramenta nunca importa os casos ou o perfil de risco de outra ferramenta.


## Integração com o gerador

O comando `make:tool` recebe as dimensões do perfil de risco como opções e gera:

- `Quality/RiskProfile.php` com a classificação executável;
- `Tests/Fixtures/GoldenCases.php` com os tipos mínimos iniciais;
- `Tests/Unit/ToolQualityContractTest.php` com o gate de ativação;
- `QUALITY.md` com os checklists específicos da ferramenta.

Casos provisórios são aceitos somente enquanto o manifesto permanecer em
`draft`. Ao ativar a ferramenta, o teste de qualidade exige casos completos,
referências aprovadas e todos os tipos derivados pelo `ToolRiskClassifier`.
O gerador nunca inventa fontes, resultados ou versões normativas.
