# Inventário oficial das ferramentas

A fonte executável deste inventário é `config/product_tools.php`. O README da raiz continua sendo a regra máxima do projeto.

## Estado consolidado no Lote Cirúrgico 1

Após o Lote 6 multi-nicho, o projeto possui **33 módulos em `app/Tools`**, todos integrantes de um único inventário oficial. Os 32 módulos anteriores permanecem associados à vertical `contabilidade` e `TurnoverCalculator` inaugura a vertical `rh`. Não existe mais a classificação de ferramentas “complementares” ou “adicionais” para fins de produto: todas devem permanecer registadas e visíveis na página **Ferramentas**.

Este lote não remove módulos nem altera slugs públicos. A ferramenta combinada `ProLaboreProfitDistributionCalculator` permanece temporariamente no inventário com o estado `implemented`, porque apresenta sobreposição funcional com `ProLaboreSimulator` e `ProfitDistributionCalculator`. A sua retirada só pode ocorrer num lote de migração dedicado, com compatibilidade de rotas, histórico e métricas e sem reduzir a quantidade oficial vigente declarada no inventário.

## Regras obrigatórias do inventário

- O diretório `app/Tools` deve conter a quantidade declarada por `expected_module_count`. O Lote 6 é o lote explícito de expansão que elevou essa quantidade de 32 para 33.
- Cada módulo deve aparecer exatamente uma vez em `config/product_tools.php`.
- Cada entrada oficial deve declarar uma `vertical` registrada e coincidir com a vertical do respectivo `ToolManifest`.
- IDs, chaves, nomes e slugs do inventário devem ser únicos.
- Cada módulo deve possuir `Tool.php` e estar registado em `config/tools/modules.php`.
- Todas as ferramentas da vertical ativa devem estar visíveis na página de ferramentas; no fallback global, o catálogo pode exibir todas as ferramentas oficiais.
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

O módulo `ProLaboreProfitDistributionCalculator` foi mantido com o slug histórico, porém reposicionado como **Planejador de Retirada de Sócios**. O escopo público agora é a composição consolidada da retirada e a comparação de cenários; os módulos `ProLaboreSimulator` e `ProfitDistributionCalculator` continuam responsáveis pelos cálculos especializados isolados. Assim, as 32 entradas históricas permanecem preservadas sem uma ferramenta escondida ou um terceiro formulário com propósito idêntico; o Lote 6 adiciona a 33ª entrada em RH.
