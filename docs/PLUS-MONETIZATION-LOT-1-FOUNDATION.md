# Prazzu Plus — Lote 1 — Fundação de governança

## Escopo

Este lote cria a proteção transversal necessária antes de corrigir individualmente as ferramentas apontadas no Relatório de Ajustes — Prazzu Plus. Nenhum benefício de domínio foi inventado neste lote e nenhuma ferramenta teve seu escopo comercial alterado.

## Estado encontrado

- O Core já possuía `ToolFeatureAccessGate`, `DefaultToolFeatureAccessGate`, middleware `tool.feature:<slug>,<feature>` e `ToolFeatureRequestAuthorizer`.
- O modo `launch_free` já libera capacidades Plus publicamente sem alterar os manifestos.
- O modo monetizado já diferencia usuário Free e Plus no gate central.
- Havia 137 features Plus declaradas no estado de referência, incluindo as chaves genéricas `advanced_productivity` e `advanced_analysis` apontadas para saneamento.
- O `ToolModuleValidator` exigia ao menos uma feature Plus, mas não impedia uma ferramenta marcada como `active` de usar essas chaves genéricas.

## Alterações

1. `ToolModuleValidator` agora impede que uma ferramenta `active` seja registrada usando `advanced_productivity` ou `advanced_analysis`. Ferramentas Beta existentes permanecem compatíveis até seus lotes de correção.
2. Foi criado `PlusFeatureReadinessInspector`, integrado ao `tools:check-architecture` já executado pelo gate de qualidade.
3. Foi criado `config/plus_feature_governance.php`. A lista `legacy_debt` congela exatamente as features Plus existentes antes deste saneamento. Ela não declara que esses recursos estão corretos; apenas evita quebrar o catálogo antes dos lotes de domínio.
4. Qualquer nova feature Plus que não esteja nessa dívida legada precisa apresentar:
   - benefício com chave concreta, sem `advanced_productivity`/`advanced_analysis`;
   - evidência de implementação no módulo fora do manifesto;
   - gate pelo middleware `tool.feature` ou pelo `ToolFeatureRequestAuthorizer` central;
   - teste explícito no módulo contendo a feature e os perfis `SubscriptionPlan::Free` e `SubscriptionPlan::Plus`.
5. Foi criado um teste transversal que percorre todas as features Plus registradas e exige, em política monetizada, `Free → feature.plus_required` e `Plus → feature.plus_plan`.

## Regra de continuidade

Ao corrigir uma feature nos próximos lotes:

1. implementar o benefício real;
2. ligar o gate central;
3. criar teste Free × Plus da feature;
4. remover `slug:feature` de `plus_feature_governance.legacy_debt`;
5. executar `composer quality`/CI.

A remoção da dívida é deliberadamente a última etapa: ela transforma o item corrigido em contrato arquitetural obrigatório e impede regressão.

## Validação local

O ambiente usado para este lote não possui as extensões PHP `dom`, `mbstring` e `xmlwriter`, portanto o PHPUnit/Laravel não consegue inicializar integralmente aqui. Os arquivos alterados foram submetidos a `php -l`. O CI oficial já instala essas extensões e executa `composer release:check`, que inclui `tools:check-architecture` e a suíte de testes.
