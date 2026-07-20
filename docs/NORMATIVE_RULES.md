# Governança de Regras Normativas

Este documento complementa o README da raiz e define o contrato obrigatório para ferramentas que dependem de legislação, tabelas oficiais, atos normativos ou decisões judiciais.

## Quando aplicar

A governança normativa é obrigatória quando o resultado da ferramenta puder mudar por causa de vigência legal, atualização de tabela oficial, entendimento regulatório ou decisão judicial.

Ferramentas sem dependência normativa não devem criar metadados artificiais apenas para cumprir este documento.

## Contrato obrigatório

Cada conjunto de regras normativas deve implementar `App\Core\Normative\Contracts\NormativeRule` e fornecer `NormativeRuleMetadata` com:

- identificador estável da regra;
- versão semântica;
- início e, quando aplicável, fim da vigência;
- ao menos uma referência com URL oficial;
- data da última verificação;
- responsável pela verificação.

O identificador representa o conjunto lógico da regra e não deve mudar quando apenas a versão ou a vigência mudar.

## Versionamento

A versão deve seguir versionamento semântico.

- **major**: mudança incompatível na interpretação ou no resultado;
- **minor**: nova faixa, cenário ou comportamento compatível;
- **patch**: correção que não altera o contrato público da regra.

Toda alteração capaz de modificar um resultado deve gerar uma nova versão. É proibido editar silenciosamente uma versão que já tenha sido usada em cálculos persistidos.

## Vigência

Cálculos novos devem resolver a regra pela data de referência do fato gerador, competência ou evento analisado, nunca pela data atual do servidor quando essas datas forem diferentes.

O catálogo de uma mesma regra não pode possuir períodos sobrepostos. Lacunas de vigência devem falhar explicitamente em vez de selecionar uma regra por aproximação.

## Histórico

A reprodução de um cálculo histórico deve usar simultaneamente:

- identificador da regra;
- versão registrada;
- data de referência original.

A resolução histórica deve falhar quando a versão não existir ou não tiver sido vigente na data registrada. Uma atualização normativa nunca deve recalcular ou sobrescrever silenciosamente resultados históricos.

## Fontes e verificação

Referências devem apontar para fontes oficiais. Materiais de terceiros podem apoiar a interpretação, mas não substituem a fonte oficial no contrato normativo.

A data de verificação não pode ser anterior à publicação da referência. O responsável registrado deve permitir rastrear quem ou qual equipe validou a regra.

## Testes mínimos

Toda regra normativa deve possuir testes para:

- seleção da versão vigente;
- transição entre vigências;
- ausência de regra aplicável;
- sobreposição de períodos;
- duplicidade de versão;
- recuperação da versão histórica;
- rejeição de fonte não oficial;
- ao menos um caso de referência validado pelo domínio responsável.

## Responsabilidades

Os contratos, resolutores e objetos reutilizáveis pertencem ao Core. Fórmulas, faixas e interpretações específicas permanecem no Domain da ferramenta.

Controllers, views e infraestrutura não podem decidir qual regra normativa aplicar.
