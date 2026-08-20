# Sistema de confiança normativa — Lote 4

## Objetivo

Fechar lacunas entre regras normativas já utilizadas por ferramentas fiscais/trabalhistas e a infraestrutura compartilhada de confiança existente, sem inventar fonte, competência, revisão ou promessa de atualização.

## Continuidade

O estado foi reconstruído obrigatoriamente em:

**ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4**

Antes das alterações foram relidos `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios dos Lotes 1–3, `docs/NORMATIVE_RULES.md` e `config/product_tools.php`.

## Infraestrutura preservada

O projeto já possuía:

- `NormativeRule`, `NormativeRuleMetadata` e `NormativeRuleSnapshot`;
- resolução por vigência e referência histórica;
- `NormativeTrustContent`;
- componente `x-tools.normative-trust`;
- gate de experiência para ferramentas que já expunham regras normativas estruturadas.

Por isso, este lote não criou um segundo sistema de confiança.

## Gerador DARF/GPS

A regra de acréscimos legais já implementava `NormativeRule` e possuía fontes oficiais, versão, vigência, data de verificação e responsável. A lacuna era de rastreabilidade/apresentação.

Alterações:

- o resultado passa a guardar `NormativeRuleSnapshot::fromRule(...)` usando a data de vencimento informada como `reference_date`;
- a interface passa a renderizar `x-tools.normative-trust` no resultado;
- a superfície mostra versão, vigência, verificação e links oficiais já registrados;
- os alertas existentes são reutilizados como premissas/limites, sem criar copy jurídica paralela.

Nenhuma fórmula de multa/juros, código de receita ou regra de vencimento foi alterada.

## Reforma Tributária do Consumo

A transição 2026–2033 já estava codificada e havia recebido correção normativa no Lote 2, mas ainda não implementava o contrato central `NormativeRule`.

Alterações:

- `ConsumptionTaxTransitionRule` agora implementa `NormativeRule` e `EffectiveDated`;
- identificador estável: `tax_reform.consumption_transition`;
- versão normativa: `2026.08.3`;
- vigência modelada: `01/01/2026` a `31/12/2033`;
- fontes oficiais estruturadas: EC 132/2023, LC 214/2025 e página explicativa da Receita Federal;
- última verificação registrada: `19/08/2026`;
- cada cálculo cria um snapshot usando `01/01/{ano_simulado}` como referência da etapa anual;
- a interface passa a exibir a superfície compartilhada de confiança junto das premissas paramétricas já existentes.

A LC 227/2026 e outras regulamentações não foram adicionadas artificialmente ao snapshot porque este lote só registra referências que sustentam diretamente o comportamento atualmente modelado.

## Gate automático

`NormativeTrustExperienceTest` mantém a cobertura histórica e adiciona uma invariável nova:

> todo módulo que cria `NormativeRuleSnapshot::fromRule` deve possuir `Resources/views/index.blade.php` e expor `<x-tools.normative-trust>`.

Isso reduz o risco de uma nova regra normativa ser corretamente estruturada no domínio, mas ficar invisível para o usuário por esquecimento de uma lista manual.

## O que não mudou

- nenhuma fórmula fiscal/trabalhista além das correções já consolidadas no Lote 2;
- nenhum slug, rota, ID, vertical ou `release_order`;
- nenhum status `active/beta`;
- nenhuma classificação Essencial/Plus;
- nenhuma persistência nova;
- nenhum texto do tipo “sempre atualizado”;
- nenhuma fonte de terceiro usada como substituta de fonte oficial.

## Validação executada

- lint PHP dos sete arquivos PHP alterados: aprovado;
- `php artisan tools:check-architecture`: aprovado sem violações;
- `php scripts/check-accounting-pains.php`: aprovado com 50 ferramentas oficiais, 49 em Contabilidade, 1 em RH e dívida Plus zero;
- smoke test direto da Reforma Tributária: snapshot `2026.08.3`, referência `2029-01-01` e três fontes oficiais;
- smoke test direto de DARF/GPS: snapshot `2026.1.0` com a data de vencimento como referência;
- auditoria equivalente ao novo gate encontrou nove módulos de produção criando `NormativeRuleSnapshot::fromRule`, todos com `x-tools.normative-trust`;
- PHPUnit não pôde executar porque o PHP deste ambiente não possui `dom`, `mbstring` e `xmlwriter`;
- `php artisan view:cache` também ficou bloqueado pela ausência de `DOMDocument`;
- a tentativa de lint integral do repositório excedeu o limite de execução do container, por isso a declaração de sintaxe deste lote fica restrita aos arquivos PHP efetivamente alterados.

## Continuidade para o Lote 5

Antes do próximo lote, reconstruir obrigatoriamente:

**ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4**

Reler novamente o README da raiz, `CORE_CANDIDATES.md`, este relatório, os relatórios anteriores, `docs/IMPLEMENTATION-LOTS.md` e `config/product_tools.php`.
