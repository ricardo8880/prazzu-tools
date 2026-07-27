# Inventário oficial das 23 ferramentas

A fonte executável deste inventário é `config/product_tools.php`. Este documento explica as decisões de produto; em caso de divergência, o README da raiz continua sendo a regra máxima.

## Estado atual

O projeto possui **32 módulos em `app/Tools`**. O catálogo oficial possui **23 ferramentas independentes**: as 20 ferramentas auditadas no ciclo original até o Lote 10 e as três ferramentas da expansão promovidas no Lote 14.

As ferramentas promovidas na expansão são:

- `NetSalaryCalculator` — Calculadora de Salário Líquido;
- `OvertimeCalculator` — Calculadora de Hora Extra, Adicional Noturno e DSR;
- `DifalIcmsCalculator` — Calculadora DIFAL / ICMS Interestadual + FCP.

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
- Módulos adicionais não entram automaticamente no catálogo oficial; a promoção exige decisão explícita em lote de integração.

## Estado após o Lote 15 da expansão

As **23 ferramentas oficiais** estão marcadas como `implemented` no inventário executável. As três ferramentas da expansão preservam seus slugs e contratos públicos dos Lotes 11, 12 e 13.

O Lote 14 consolidou descoberta de produto, inventário executável e contratos arquiteturais. O Lote 15 auditou a expansão, completou os gates de qualidade dos módulos de Hora Extra e DIFAL e protegeu a distribuição contra caches Laravel gerados. O estado continua em 32 módulos, sendo 23 oficiais e 9 adicionais.

O inventário executável registra `expansion_lot_15_audited`. A aprovação operacional de release continua condicionada ao `composer release:check` no CI oficial com todas as extensões requeridas.
