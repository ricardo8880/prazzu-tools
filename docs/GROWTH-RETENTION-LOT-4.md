# Crescimento e Retenção — Lote 4 — Aquisição, SEO e confiança

## Objetivo

Melhorar aquisição orgânica e confiança sem transformar páginas de ferramenta em artigos genéricos, sem alterar fórmulas e sem fabricar prova social. A base foi reconstruída obrigatoriamente na ordem ZIP original → Lote 1 → Lote 2 → Lote 3 e os documentos obrigatórios foram relidos antes da alteração.

## SEO técnico por ferramenta

`ToolSeoContext` passa a reconhecer a ferramenta pela rota atual e entrega ao contexto SEO compartilhado:

- título e descrição do manifesto quando a view não fornece uma versão mais específica;
- palavras-chave da ferramenta combinadas com o contexto da vertical;
- URL canônica da rota principal da ferramenta, inclusive quando a mesma view é devolvida por uma rota de cálculo/ação;
- vertical real do módulo.

O layout usa essa URL canônica como fallback também para `og:url`. Views que já possuem `@section('title')`, `meta_description` ou canonical explícita continuam tendo prioridade.

## Dados estruturados

As 43 páginas oficiais passam a publicar JSON-LD derivado exclusivamente do catálogo:

- `WebApplication`, com nome, descrição, URL, idioma, execução web e acesso gratuito ao produto Essencial;
- `BreadcrumbList`, refletindo Home, vertical, catálogo e ferramenta.

Nenhuma marcação inclui avaliação, estrelas, preço comercial, quantidade de usuários, número futuro de ferramentas ou outra informação não comprovada.

Trinta e seis ferramentas já usam `x-tools.page` e recebem a infraestrutura automaticamente. As sete views históricas que ainda não usam esse componente receberam `x-tools.trust-seo` diretamente, sem refatoração estrutural arriscada.

## Conteúdo útil de confiança

As páginas padronizadas agora exibem uma seção curta `O que conferir antes de usar o resultado`, construída a partir da categoria e dos recursos Essenciais já declarados. Ela cobre:

- verificações relevantes antes de usar o resultado, com orientação diferente para fiscal, trabalhista, societário, validadores, documentos, conversores e calculadoras;
- transparência de memória/método/fontes quando o próprio manifesto declara essa capacidade;
- regra explícita de que cadastro é opcional e não libera uma fórmula mais correta;
- versão do módulo para tornar a evolução visível sem inventar uma data de atualização normativa.

As sete telas legadas já possuem conteúdo específico de interpretação, conferência ou limitações. Para não duplicar texto genérico, nelas o componente compartilhado publica apenas o JSON-LD e preserva integralmente a orientação existente.

## Rodapé

Foram removidas afirmações hardcoded que poderiam gerar desconfiança ou envelhecer mal:

- `+120 Ferramentas disponíveis`;
- `+50k Contadores utilizam`;
- `100% Seguro e confiável`;
- `Sempre atualizado`.

O espaço foi preservado com mensagens de produto que continuam verdadeiras conforme o catálogo cresce:

- Uso imediato — Sem cadastro obrigatório;
- Essencial completo — O problema principal é gratuito;
- Transparência — Premissas e memória quando aplicáveis;
- Catálogo em evolução — Novas soluções por vertical.

## Limites preservados

- README da raiz continua sendo a regra máxima e não foi alterado.
- Nenhum slug, fórmula, cálculo, migration, feature Plus, `release_order` ou item do inventário foi alterado.
- Nenhuma data normativa foi inferida genericamente.
- Nenhuma promessa de precisão absoluta ou substituição de fonte oficial foi adicionada.
- As páginas de vertical e campanhas continuam com seus próprios contextos SEO.

## Validação do lote

O lote adiciona testes para:

- orientação de confiança fiscal e documental;
- canonical, JSON-LD e conteúdo de confiança em uma ferramenta padronizada;
- preservação do conteúdo específico em ferramenta legada;
- remoção das métricas futuras do rodapé;
- cobertura das 43 views oficiais pela infraestrutura compartilhada.

No ambiente de preparação, `php -l` passou nos PHP alterados e `php artisan tools:check-architecture` encerrou sem violações. O catálogo carregado em runtime confirmou 43 ferramentas. A suíte PHPUnit continua bloqueada neste ambiente pela ausência das extensões PHP `dom`, `mbstring` e `xmlwriter`; essa limitação ambiental não foi contornada alterando dependências do projeto.

## Continuidade para o Lote 5

O próximo lote deve reconstruir obrigatoriamente ZIP original → Lote 1 → Lote 2 → Lote 3 → Lote 4. O escopo planejado é polimento, integração e validação final de toda a frente de crescimento: responsividade, estados vazios, rotas, autenticação, eventos de Analytics relevantes e regressão conjunta, sem ampliar produto por conveniência.
