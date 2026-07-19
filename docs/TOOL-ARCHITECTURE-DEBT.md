# Dívida arquitetural das ferramentas existentes

Atualizado em 18/07/2026. O `README.md` da raiz continua sendo a autoridade
máxima; este documento apenas registra divergências encontradas no código
legado para que elas não sejam tratadas como precedente.

## Regra para novas ferramentas

Novos módulos devem partir de `php artisan make:tool`, iniciar em `draft` e
seguir `app/Tools/README.md`. Implementações legadas listadas abaixo não podem
ser copiadas. O comando `php artisan tools:check-architecture` valida estrutura,
camadas de recursos, namespaces, documentação, migrations, rotas, controllers e
dependências básicas.

## Decisões de produto consolidadas

- O CRM e o cadastro comercial foram removidos da Calculadora de Honorários.
- O compartilhamento público de cálculos foi removido de Accounting e Margin.
- O catálogo deixou de publicar ferramentas placeholder e métricas fictícias.
- Cada manifesto real declara `ToolFeature` nos tiers Essencial e Prazzu Plus;
  o acesso passa pelo gate central da plataforma.

Essas remoções encerram desvios de produto e não constituem funcionalidades
pendentes. CRM, colaboração e compartilhamento de cálculos não devem ser
reintroduzidos no Prazzu Tools.

## Prioridade 0 — Core compartilhado

1. **Histórico:** Accounting ainda persiste histórico em tabela própria.
   Simples, Business Document, Labor e Margin utilizam contratos e DTOs do Core
   para gravação, consulta e exclusão sem conhecer Eloquent. O recorder central
   devolve `ToolRunHandle`, e o gate impede que Application ou Presentation
   dependam de models internos do Core. Accounting ainda persiste histórico em
   tabela própria e deve ser migrado antes do encerramento da dívida.
2. **Favoritos:** Accounting mantém favorito na tabela própria de cálculo. O
   Core precisa de um gerenciador de favoritos vinculado à execução central.
3. **Persistência e tier:** Accounting, Labor e o cálculo individual de Margin
   ainda salvam automaticamente para qualquer pessoa autenticada. Antes da
   monetização, o registro deve consultar centralmente a capacidade Plus de
   histórico, sem bloquear o cálculo Essencial.
4. **Declaração de dados:** Margin precisa revisar a classificação de nomes,
   custos e cenários comerciais mantidos no histórico.

## Prioridade 1 — domínio e apresentação

1. Projeções e alertas do Simples utilizam `Money`, `Percentage` e serviços
   puros de domínio. O gate `tools.no-financial-float` impede a reintrodução de
   `float` em Application e Domain.
2. Impressão direta foi removida das views das ferramentas. O componente
   compartilhado `tools.print-button` concentra a integração com a impressão do
   navegador, e o gate `tools.no-direct-browser-print` impede implementações
   locais.
3. Os componentes visuais comuns `intro`, `form-panel`, `validation-summary`,
   `result-panel`, `history-actions`, `export-button` e `print-button` são
   obrigatórios e validados pelo gate arquitetural.

## Critério de encerramento

Uma dívida só sai deste registro quando código, dados, documentação e testes
estiverem migrados; o gate arquitetural correspondente deve ser adicionado no
mesmo lote para impedir regressão.
