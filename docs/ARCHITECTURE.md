# Arquitetura da Prazzu Tools

## Direção arquitetural

A plataforma é um monólito modular em Laravel. O núcleo compartilhado conhece os
contratos das ferramentas, mas não conhece suas regras internas. Uma ferramenta
pode depender do Core e nunca da implementação de outra ferramenta.

O monólito continua sendo uma plataforma de soluções pontuais. Módulos recebem
dados, executam uma capacidade e entregam um resultado; CRM, ERP, workflow,
gestão de clientes, colaboração e compartilhamento de cálculos pertencem a outro
produto do ecossistema.

## Contrato mínimo

Todo módulo implementa `ToolModule` e fornece um `ToolManifest` imutável. O
manifesto contém apenas metadados estáticos e tipados:

- slug, nome e descrição;
- categoria;
- ícone e rota principal;
- versão semântica;
- acesso;
- status do ciclo de vida;
- posição, destaque e palavras-chave;
- recursos classificados entre solução Essencial e Prazzu Plus.

Categorias, acessos e estados são enums. Isso impede valores divergentes quando o
catálogo crescer.

## Recursos de produto

Cada capacidade entregue ao usuário é um `ToolFeature` com:

- chave estável em `snake_case`;
- nome legível;
- `ToolFeatureTier::Essential` ou `ToolFeatureTier::Plus`.

O tier Essencial resolve integralmente o problema principal e permanece
público. O Plus acrescenta produtividade, volume, automação, continuidade,
análises, cenários ou formatos adicionais. Ambos reutilizam o mesmo domínio; o
Plus não possui uma versão "correta" de um cálculo Essencial incompleto.

`ToolModuleValidator` exige ao menos um recurso de cada tier e chaves sem
duplicidade. `DefaultToolFeatureAccessGate` combina manifesto, feature flags,
política comercial e plano efetivo. A camada HTTP aplica essa decisão pelo
middleware `tool.feature:<slug>,<feature>`.

Durante `launch_free`, recursos públicos Essenciais e Plus ficam liberados sem
limites comerciais. Requisitos de identidade permanecem independentes: salvar,
consultar histórico ou favoritos ainda pode exigir autenticação.

## Capacidades opcionais

O módulo declara somente o que utiliza:

- `HasWebRoutes` para rotas web;
- `HasApiRoutes` para API;
- `HasViews` para namespace próprio de views;
- `HasMigrations` para persistência exclusiva;
- `HasServiceProviders` para adaptadores do módulo;
- `HasHistoryPolicy` para declarar a projeção segura do histórico central.

Novas capacidades devem representar comportamento transversal real. Elas não
devem ser criadas apenas para antecipar possibilidades.

## Registro

As classes são registradas explicitamente em `config/tools/modules.php`, divididas
em grupos para reduzir conflitos de edição. Os grupos servem apenas para
organização do registro; a categoria oficial continua no manifesto.

`ToolRegistry`:

- instancia módulos pelo container;
- exige implementação de `ToolModule`;
- indexa módulos por slug;
- rejeita duplicidades;
- expõe módulos e manifestos separadamente.

Não há varredura de diretórios durante requisições.

## Catálogo real

`ToolCatalog` continua sendo a fonte única para home, busca, filtros, páginas e
barras laterais. Ele projeta exclusivamente os manifestos reais registrados no
`ToolRegistry`; configurações provisórias e números demonstrativos não fazem
parte do catálogo público.

A configuração mantém somente a taxonomia e o registro de módulos:

```text
config/tools/categories.php   apresentação das categorias
config/tools/modules.php      classes dos módulos reais
```

Contagens de uso e popularidade só podem aparecer quando forem produzidas pela
infraestrutura real de Analytics. O catálogo não inventa métricas nem publica
ferramentas ainda inexistentes.

## Rotas

Módulos com rotas web implementam `HasWebRoutes`. O carregador falha de forma
explícita quando o arquivo declarado não existe, evitando módulos parcialmente
registrados.

Convenção:

```text
tools.<slug>.index
tools.<slug>.calculate
tools.<slug>.export
tools.<slug>.history
```

Links do catálogo usam diretamente a `route_name` do manifesto. Não existe rota
genérica nem página provisória para uma ferramenta sem implementação real.

## Ciclo de vida e acesso

Estados suportados:

```text
draft, internal, beta, active, maintenance, deprecated, retired
```

A solução Essencial de cada ferramenta é pública e resolve integralmente o
problema principal. Recursos de produtividade, volume, histórico e automação são
declarados individualmente como Prazzu Plus no manifesto e autorizados pela
política central.

O enum de acesso separa apenas ferramentas públicas das internas:

```text
free, internal
```

Ferramentas publicadas usam `free`: elas não podem transformar o módulo inteiro
em Prazzu Plus nem exigir autenticação. A restrição comercial e a necessidade de
identidade pertencem a recursos específicos.
O plano efetivo, individual ou concedido por licença empresarial, é resolvido
pelo Core. Ferramentas não conhecem organizações, assinaturas ou vagas.

## Fundamentos compartilhados

O Core disponibiliza value objects para dinheiro, percentuais, arredondamento,
datas de referência, competências, períodos, vigência, CPF, CNPJ e referências
normativas. Esses componentes não contêm fórmulas específicas de ferramentas.

Regras obrigatórias:

- valores monetários não usam `float`;
- cálculos recebem uma data de referência explícita;
- regras temporais declaram vigência;
- CPF e CNPJ são tratados como identificadores, não números;
- fontes normativas ficam ligadas às regras que justificam.

Consulte `docs/LOT-4-ACCOUNTING-FOUNDATIONS.md`.

## Infraestrutura compartilhada atual

O Core já oferece contratos e implementações centrais para acesso comercial,
feature flags, histórico de execuções, auditoria, métricas de uso, importação,
exportação para impressão, integrações externas, identidade e organizações.
Ferramentas devem consumir esses pontos por contrato e não recriar mecanismos
equivalentes dentro do módulo.

O domínio empresarial existente no Core é limitado à distribuição de licenças
Plus. Ele não cria workspace, compartilha resultados nem oferece gestão
operacional.

Continuam sendo evoluções futuras, entre outras:

- snapshots normativos completos e versionamento automatizado das regras;
- novos formatos de exportação, como PDF e XLSX nativos;
- processamento assíncrono especializado por capacidade;
- integração com a identidade central do ecossistema Prazzu.
