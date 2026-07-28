# Lote Cirúrgico 1 — Inventário e governança das 32 ferramentas

## Objetivo

Consolidar o estado real do projeto num único inventário oficial, impedindo ferramentas escondidas, classificações paralelas e divergência entre código e documentação.

## Alterações realizadas

- As 32 pastas existentes em `app/Tools` foram declaradas no inventário oficial.
- A secção `additional_modules` foi removida do contrato executável.
- Foi definido `expected_module_count = 32`.
- Foram criadas validações para unicidade de ID, chave, nome, slug e módulo.
- Foi reforçada a verificação de presença de `Tool.php` e registo em `config/tools/modules.php`.
- A sobreposição de `ProLaboreProfitDistributionCalculator` foi movida para `functional_overlap_reviews`, sem esconder ou apagar a ferramenta.
- A documentação passou a declarar que as 32 ferramentas devem estar visíveis na página Ferramentas.

## Decisões preservadas

- Nenhum slug público foi alterado.
- Nenhuma rota foi removida.
- Nenhum módulo foi apagado.
- A ferramenta combinada permanece ativa até lote dedicado de migração.
- A correção da Home para exatamente 8 ferramentas recentes pertence ao Lote Cirúrgico 2.

## Critérios de aceite

- `app/Tools` contém 32 módulos.
- `config/product_tools.php` contém 32 entradas oficiais.
- Não há módulo classificado mais de uma vez.
- Não há IDs, chaves, nomes ou slugs repetidos.
- Todos os módulos possuem `Tool.php` e estão registados.
- Existe apenas uma revisão de sobreposição funcional, explicitamente ligada aos dois módulos substitutos.

## Validação executada

- Sintaxe PHP dos ficheiros alterados: validada.
- Verificação estática do inventário: validada.
- PHPUnit não pôde ser executado neste ambiente por ausência das extensões `dom`, `mbstring` e `xmlwriter`; a limitação já era conhecida no projeto e não foi ocultada.
