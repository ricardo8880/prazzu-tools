# Satisfação e Retorno — Lote 7 — Conta, favoritos e retorno

## Base reconstruída

Este lote foi iniciado somente após reconstruir o estado acumulado em **ZIP original → Lotes 1–5 já presentes no original analisado → Lote 6**, reler `README.md`, `CORE_CANDIDATES.md`, `docs/IMPLEMENTATION-LOTS.md`, os relatórios anteriores e `config/product_tools.php`.

A análise confirmou que a infraestrutura de conta, Meu Prazzu, favoritos autenticados, histórico, continuidade pós-resultado e newsletter já existia. Por isso, o lote não recria nenhuma dessas capacidades.

## Objetivo efetivamente pendente

O gap real do Lote 7 era permitir que um visitante percebesse o valor de **favoritar** antes de possuir conta, sem transformar autenticação em requisito para usar a ferramenta.

A experiência passa a funcionar assim:

1. o visitante continua vendo e usando a ferramenta completa;
2. a ação **Favoritar** também aparece para visitante;
3. ao escolher essa ação, ele recebe uma explicação contextual de que a conta gratuita serve para salvar a ferramenta e continuar depois;
4. cadastro e login preservam somente `source=tool_favorite` e o `tool` conhecido;
5. após autenticação válida por esse fluxo, o favorito é persistido de forma idempotente;
6. quem já possuía a ferramenta como favorita não a perde por entrar novamente pelo mesmo fluxo.

## Persistência e arquitetura

`UserToolFavorites` continua sendo a única infraestrutura transversal para favoritos de ferramentas. Foi adicionada a operação idempotente `favorite()`, separada de `toggle()`, porque um fluxo de intenção de retorno nunca pode remover acidentalmente um favorito existente.

Não foi criado cadastro paralelo, lista temporária de visitantes, CRM, projeto, tarefa, workflow ou perfil de cliente. O visitante não ganha persistência anônima: a persistência continua vinculada à conta, exatamente como determina o README.

## Comunicação da conta

As telas de cadastro e login passam a enfatizar benefícios de continuidade já existentes:

- recuperar resultados;
- favoritar ferramentas;
- repetir cálculos;
- continuar depois;
- acessar de outro dispositivo.

Quando a origem é `tool_favorite`, a mensagem é específica para a intenção demonstrada. Em todos os casos permanece explícito que a conta é opcional e que os cálculos continuam completos sem login.

## Analytics e privacidade

O evento já existente `account.created` pode receber a atribuição segura `source=tool_favorite` e `tool_slug` quando o slug pertence ao registro de ferramentas. Nenhum e-mail, payload de cálculo ou conteúdo de histórico é incluído nessa atribuição.

Não foi criado evento novo apenas para aumentar coleta. A finalidade é distinguir um cadastro iniciado por necessidade de continuidade de favorito usando a infraestrutura já existente.

## Newsletter

A newsletter existente foi revisada e preservada sem alteração. Ela já comunica atualizações relevantes de regras, tabelas, conteúdos e ferramentas e não precisa de uma segunda implementação neste lote.

## O que não foi alterado

Este lote não altera:

- fórmulas ou regras de cálculo;
- requests de ferramentas;
- resultados ou memória de cálculo;
- slugs e rotas canônicas das ferramentas;
- verticais;
- inventário oficial;
- `release_order`;
- Essencial/Plus;
- newsletter;
- histórico ou continuidade contextual do Lote 6.

## Validação

Foram adicionados testes cobrindo:

- presença de **Favoritar** para visitante sem bloquear o cálculo;
- explicação contextual antes do cadastro;
- persistência automática do favorito após cadastro originado dessa intenção;
- atribuição segura de `account.created`;
- login de conta existente com persistência idempotente do favorito;
- manutenção da proteção do endpoint autenticado de toggle.

No ambiente de análise, os arquivos PHP passam em verificação sintática. A execução do PHPUnit permanece bloqueada porque o PHP disponível não possui as extensões `dom`, `mbstring` e `xmlwriter`, requeridas pelo PHPUnit instalado no projeto.

## Continuidade para o Lote 8

Antes do Lote 8, reconstruir obrigatoriamente o estado acumulado em **ZIP original → Lote 6 → Lote 7**, reler os documentos obrigatórios da raiz, comparar novamente todo o código e somente então executar a consolidação final de UX, acessibilidade, Analytics, arquitetura, regressões e distribuição.
