# Inventário oficial das 20 ferramentas

A fonte executável deste inventário é `config/product_tools.php`. Este documento explica as decisões de produto; em caso de divergência, o README da raiz continua sendo a regra máxima.

## Estado atual

O projeto possui 29 módulos em `app/Tools`. As 20 ferramentas oficiais agora correspondem a 20 módulos independentes. `ProLaboreSimulator` e `ProfitDistributionCalculator` substituem, no catálogo prioritário, o antigo módulo combinado.

Os oito módulos complementares continuam preservados. `ProLaboreProfitDistributionCalculator` permanece como nono módulo adicional, classificado como compatibilidade legada preservada até auditoria específica de migração, para não quebrar URL, histórico ou integrações existentes.

## Significado dos estados

- `alignment_required`: o módulo existe, mas ainda precisa ser confrontado integralmente com o escopo Essencial e Plus.
- `rename_and_scope_alignment`: além da conformidade funcional, nome público e escopo precisam ser alinhados com compatibilidade.
- `implemented`: o alinhamento previsto no lote correspondente foi implementado.
- `legacy_compatibility`: módulo fora do catálogo oficial mantido temporariamente para compatibilidade.

## Regras de alteração

- Toda mudança na lista oficial deve atualizar `config/product_tools.php`, este documento e os testes arquiteturais.
- Uma ferramenta Essencial deve resolver completamente o problema básico sem autenticação.
- Plus acrescenta produtividade, volume, automação, continuidade, cenários ou conveniência; nunca corrige um cálculo Essencial incompleto.
- Ferramentas não podem depender diretamente de classes internas de outro módulo.
- Slugs públicos não devem ser alterados sem redirecionamento e teste de compatibilidade.
- Os módulos adicionais não entram automaticamente no escopo dos lotes das 20 ferramentas.


## Estado após o Lote 10

As 20 ferramentas oficiais estão marcadas como `implemented` no inventário executável. Essa marca confirma o alinhamento do catálogo e a conclusão dos lotes de implementação; a aprovação operacional de release continua condicionada ao `composer release:check` no CI oficial.
