# Evolução Multi-Nicho — Lote 2 — Fundação de Vertical e VerticalContext

## Objetivo

Implementar a fundação genérica de `Vertical` e `VerticalContext` prevista pelo
Lote 1, preservando integralmente o comportamento público atual de Contabilidade
e sem antecipar a associação de ferramentas, artigos, recursos, Home, SEO ou
Analytics às verticais.

## Estado de origem analisado

Antes de qualquer alteração, o estado foi reconstruído a partir do ZIP original
e recebeu integralmente os arquivos do Lote 1. Foram relidos:

- `README.md`;
- `CORE_CANDIDATES.md`;
- `docs/IMPLEMENTATION-LOTS.md`;
- `docs/ARCHITECTURE.md`;
- `docs/MULTI-VERTICAL-LOT-1-CONSTITUTION.md`;
- `config/product_tools.php`;
- código atual de `AcquisitionContext`, providers, middleware e testes.

O catálogo permaneceu com exatamente 32 ferramentas oficiais e nenhum módulo,
slug ou rota pública foi alterado.

## Implementação

### 1. Vertical como dado de negócio

Foi criado o value object imutável `Vertical` e o contrato `VerticalRegistry`.
O registro inicial utiliza `config/verticals.php`, de modo que nomes de nichos
não façam parte do Core como enum ou ramificações condicionais.

`contabilidade` é a primeira entrada registrada e a vertical padrão atual. Uma
nova vertical pode ser adicionada como configuração sem alterar contratos ou o
resolver central.

### 2. VerticalContext ativo

`VerticalContext` é um serviço scoped por requisição. Ele mantém somente a
vertical ativa já resolvida e pode representar explicitamente `null`.

Isso evita transformar a vertical em estado global permanente ou acoplar o
contexto ao domínio de ferramentas.

### 3. Resolução por fontes ordenadas

`ResolveVerticalContext` recebe fontes pelo contrato `VerticalContextSource`.
Atualmente a ordem registrada é:

1. vertical explicitamente persistida na sessão;
2. mapeamento explícito proveniente de um `AcquisitionContext` já ativo;
3. vertical padrão da plataforma.

Se nenhuma fonte resolver uma vertical válida, o resultado é `null`.

O resolver não conhece `contabilidade`, `rh`, `financeiro` ou qualquer lista
fechada de nichos.

### 4. Sessão

`VerticalContextSession` oferece ativação, leitura e limpeza do contexto
explicitamente persistido. Slugs inexistentes são removidos da sessão e não
quebram a requisição.

Nenhuma rota de escolha de vertical foi criada neste lote. A infraestrutura de
sessão existe para os fluxos dos lotes seguintes, sem antecipar interface.

### 5. Integração com AcquisitionContext

`AcquisitionContext` continua intacto e independente. O novo
`AcquisitionVerticalContextSource` apenas lê o contexto de aquisição já resolvido
e consulta mapas opcionais de keyword/campanha em `config/verticals.php`.

Não foi adicionada propriedade de vertical às tabelas ou DTOs de aquisição. Isso
preserva o domínio atual e permite que uma futura migração de associação seja
feita apenas quando houver necessidade real.

### 6. Middleware transversal

Após `ShareActiveAcquisitionContext`, o middleware
`ResolveActiveVerticalContext` resolve a vertical, ativa o serviço scoped,
adiciona `vertical.context` aos atributos da requisição e compartilha
`activeVertical` com as views.

A ordem preserva Acquisition como uma possível fonte, sem fundir os dois
conceitos.

## Comportamento público preservado

A configuração padrão resolve `contabilidade`, portanto a experiência atual
continua exatamente como antes. Nenhuma Home, ferramenta, categoria, artigo,
recurso, busca ou metadata passa a ser filtrada neste lote.

O fallback geral continua disponível configurando `verticals.default = null` ou
quando nenhuma fonte produz uma vertical válida.

## Arquivos alterados/adicionados

- `config/verticals.php`;
- `app/Core/Verticals/**`;
- `app/Http/Middleware/ResolveActiveVerticalContext.php`;
- `app/Providers/CoreInfrastructureServiceProvider.php`;
- `bootstrap/app.php`;
- `composer.json` — descrição do pacote alinhada à constituição multi-nicho;
- `README.md` — estado do VerticalContext atualizado após a implementação;
- `docs/ARCHITECTURE.md`;
- `docs/IMPLEMENTATION-LOTS.md`;
- `tests/Unit/Core/Verticals/VerticalContextFoundationTest.php`;
- `tests/Feature/Verticals/ActiveVerticalContextTest.php`;
- `docs/MULTI-VERTICAL-LOT-2-FOUNDATION.md`.

## Fora do escopo deste lote

Não foram alterados:

- `config/product_tools.php` e os 32 manifests;
- `ToolManifest` e `ToolCatalog`;
- Blog, Resources e categorias;
- Home e sua composição de conteúdo;
- Analytics, SEO, sitemap e breadcrumbs;
- rotas/controllers para escolha de vertical;
- banco de dados ou migrations;
- E2E;
- regras de domínio das ferramentas.

Esses itens pertencem aos próximos lotes e não foram antecipados.

## CORE_CANDIDATES

Nenhum candidato existente foi promovido por reutilização oportunista. `Vertical`
e `VerticalContext` entram no Core porque foram definidos explicitamente como
fundação transversal pela constituição aprovada no Lote 1.

## Continuidade para o Lote 3

O próximo lote deve reconstruir novamente o projeto a partir do ZIP original,
aplicar em ordem os Lotes 1 e 2, reler suas documentações e então associar
ferramentas e conteúdo a verticais sem alterar os contratos públicos existentes.

A primeira migração deve tornar a Contabilidade explícita nos dados atuais antes
de introduzir qualquer segunda vertical real.

## Validação executada

Após a implementação:

- `php artisan tools:check-architecture` — aprovado;
- `php artisan analytics:check` — aprovado;
- `php artisan route:list --name=tools.index` — aprovado, confirmando bootstrap e registro do container;
- `config/product_tools.php` — confirmado com `expected_module_count = 32` e 32 ferramentas oficiais;
- `php -l` — aprovado para todos os arquivos PHP adicionados e para os arquivos de bootstrap/provider alterados;
- `composer.json` — JSON válido;
- PHPUnit não pôde iniciar porque o ambiente disponível continua sem as extensões `dom`, `mbstring` e `xmlwriter`;
- Pint também não pôde iniciar pelo mesmo bloqueio de extensões (`mbstring` e `xml`).

A tentativa de executar uma requisição HTTP completa no ambiente também encontrou
a ausência de `mbstring` dentro do Laravel (`mb_split`). Essas limitações já eram
do ambiente de análise e não foram tratadas como defeito do projeto.
