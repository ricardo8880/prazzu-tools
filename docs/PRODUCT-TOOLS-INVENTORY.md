# Inventário oficial das 32 ferramentas

A fonte executável deste inventário é `config/product_tools.php`. O README da raiz continua sendo a regra máxima do projeto.

## Estado consolidado no Lote Cirúrgico 1

O projeto possui **exatamente 32 módulos em `app/Tools`** e os 32 passam a integrar um único inventário oficial. Não existe mais a classificação de ferramentas “complementares” ou “adicionais” para fins de produto: todas devem permanecer registadas e visíveis na página **Ferramentas**.

Este lote não remove módulos nem altera slugs públicos. A ferramenta combinada `ProLaboreProfitDistributionCalculator` permanece temporariamente no inventário com o estado `implemented`, porque apresenta sobreposição funcional com `ProLaboreSimulator` e `ProfitDistributionCalculator`. A sua retirada só pode ocorrer num lote de migração dedicado, com compatibilidade de rotas, histórico e métricas e sem reduzir o catálogo abaixo das 32 ferramentas definidas pelo produto.

## Regras obrigatórias do inventário

- O diretório `app/Tools` deve conter exatamente 32 módulos enquanto não houver lote explícito de expansão ou substituição.
- Cada módulo deve aparecer exatamente uma vez em `config/product_tools.php`.
- IDs, chaves, nomes e slugs do inventário devem ser únicos.
- Cada módulo deve possuir `Tool.php` e estar registado em `config/tools/modules.php`.
- Todas as 32 ferramentas devem estar visíveis na página de ferramentas.
- Nenhum módulo pode ficar escondido por classificação documental paralela.
- Nenhuma ferramenta pode ser apagada apenas por semelhança de nome; remoções exigem auditoria funcional e estratégia de compatibilidade.
- A Home é tratada separadamente: deve mostrar exatamente as 8 ferramentas mais recentes, sem alterar a visibilidade do catálogo completo.

## Estado de implementação

- `implemented`: ferramenta ativa e incluída no inventário consolidado.
- `migration_pending`: ferramenta ativa, mas com sobreposição funcional formalmente identificada e migração pendente.

## Sobreposição funcional controlada

`ProLaboreProfitDistributionCalculator` reúne cálculo de pró-labore e distribuição de lucros, capacidades que também existem nos módulos independentes `ProLaboreSimulator` e `ProfitDistributionCalculator`.

Após o Lote Cirúrgico 3, a experiência pública foi convertida numa ponte para as duas ferramentas independentes. Ela **ainda não é apagada**, porque isso deixaria o projeto com 31 ferramentas e poderia quebrar URL, histórico, favoritos, Analytics ou integrações. O lote de migração deverá decidir e implementar uma substituição realmente distinta antes da remoção definitiva.

## Alteração do catálogo

Toda alteração deve atualizar conjuntamente:

1. `config/product_tools.php`;
2. este documento;
3. `docs/IMPLEMENTATION-LOTS.md`;
4. testes arquiteturais do inventário;
5. qualquer estratégia de redirecionamento ou migração aplicável.


## Resolução da sobreposição funcional — Lote Cirúrgico 4

O módulo `ProLaboreProfitDistributionCalculator` foi mantido com o slug histórico, porém reposicionado como **Planejador de Retirada de Sócios**. O escopo público agora é a composição consolidada da retirada e a comparação de cenários; os módulos `ProLaboreSimulator` e `ProfitDistributionCalculator` continuam responsáveis pelos cálculos especializados isolados. Assim, as 32 entradas permanecem visíveis sem uma ferramenta escondida ou um terceiro formulário com propósito idêntico.
