# Arquitetura da Prazzu Tools

## Direção arquitetural

A plataforma é um monólito modular em Laravel. O núcleo compartilhado conhece os
contratos das ferramentas, mas não conhece suas regras internas. Uma ferramenta
pode depender do Core e nunca da implementação de outra ferramenta.

## Contrato mínimo

Todo módulo implementa `ToolModule` e fornece um `ToolManifest` imutável. O
manifesto contém apenas metadados estáticos e tipados:

- slug, nome e descrição;
- categoria;
- ícone e rota principal;
- versão semântica;
- acesso;
- status do ciclo de vida;
- posição, destaque e palavras-chave.

Categorias, acessos e estados são enums. Isso impede valores divergentes quando o
catálogo crescer.

## Capacidades opcionais

O módulo declara somente o que utiliza:

- `HasWebRoutes` para rotas web;
- `HasApiRoutes` para API;
- `HasViews` para namespace próprio de views;
- `HasMigrations` para persistência exclusiva.

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

## Catálogo e placeholders

`ToolCatalog` continua sendo a fonte única para home, busca, filtros, páginas e
barras laterais.

A configuração foi separada:

```text
config/tools/catalog.php      metadados estáticos dos placeholders
config/tools/metrics.php      números e destaques demonstrativos
config/tools/categories.php   apresentação das categorias
config/tools/modules.php      classes dos módulos reais
```

Quando um módulo real registra um slug já usado por um placeholder, seu manifesto
substitui os metadados provisórios. As métricas permanecem uma responsabilidade
separada e futuramente poderão vir de banco, cache ou observabilidade.

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

As rotas específicas continuam sendo carregadas antes da rota genérica do
placeholder.

## Ciclo de vida e acesso

Estados suportados:

```text
draft, internal, beta, active, maintenance, deprecated, retired
```

Acessos suportados:

```text
free, premium, authenticated, internal
```

Esses valores são declarações arquiteturais. A autorização e o controle de uso
serão implementados no lote de infraestrutura transversal.

## Compatibilidade

`ToolDefinition` foi mantido apenas como alias temporário de `ToolManifest`. Código
novo deve utilizar exclusivamente `ToolManifest`.

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

Continuam sendo evoluções futuras, entre outras:

- snapshots normativos completos e versionamento automatizado das regras;
- novos formatos de exportação, como PDF e XLSX nativos;
- processamento assíncrono especializado por capacidade;
- integração com a identidade central do ecossistema Prazzu.
