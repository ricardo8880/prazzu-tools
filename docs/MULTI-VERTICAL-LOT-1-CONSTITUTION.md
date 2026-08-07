# Evolução Multi-Nicho — Lote 1 — Constituição e contratos

## Objetivo

Formalizar a evolução do Prazzu Tools de uma plataforma percebida e documentada
como exclusiva de Contabilidade para uma única plataforma capaz de atender
múltiplas verticais de negócio, sem alterar o comportamento público atual.

Este lote é deliberadamente constitucional. Não cria `Vertical`,
`VerticalContext`, resolvers, tabelas, sessões, filtros ou comportamento de
runtime. Essas implementações pertencem aos lotes seguintes.

## Estado de origem analisado

O lote foi iniciado a partir do ZIP original do projeto e respeitou as fontes de
continuidade obrigatórias:

- `README.md`;
- `CORE_CANDIDATES.md`;
- `docs/IMPLEMENTATION-LOTS.md`;
- `config/product_tools.php`;
- relatórios de lotes já concluídos;
- código e testes arquiteturais existentes.

O inventário oficial permaneceu com exatamente 32 ferramentas. Nenhum slug,
rota, módulo ou estado de publicação foi alterado.

## Decisões constitucionais

### 1. Uma plataforma, múltiplas verticais

O Prazzu Tools continua sendo uma única aplicação e um único Core técnico.
Contabilidade passa a ser explicitamente a primeira vertical oficial e a
implementação de referência, não o domínio global da plataforma.

### 2. Vertical

`Vertical` representa um universo de negócio. O Core compartilhado não pode
conhecer uma lista fechada de nichos nem criar ramificações rígidas por nomes de
vertical.

### 3. VerticalContext

`VerticalContext` representa a vertical ativa da experiência. Sua implementação
fica reservada ao Lote 2. O contrato constitucional prevê `null` como fallback
válido para uma experiência Prazzu geral.

### 4. VerticalContext não é AcquisitionContext

`AcquisitionContext` continua existindo com sua responsabilidade atual de
representar intenção, campanha, palavra-chave ou origem. Em lote futuro ele pode
contribuir como uma das fontes para resolver `VerticalContext`, sem ser
substituído por ele.

### 5. Global x Vertical

Permanecem globais: Auth, usuários, planos/Billing, Analytics, Blog, Admin, E2E,
observabilidade, infraestrutura de SEO, busca, componentes e demais serviços do
Core técnico.

Podem ser específicos por vertical: identidade, conteúdo, ferramentas,
categorias de domínio, recomendações, configurações e estratégia/conteúdo SEO.

### 6. Não duplicação

Nova vertical não autoriza duplicar infraestrutura. Diferenças de conteúdo,
configuração e seleção de dados devem ser resolvidas por contexto, associação,
composição ou contratos compartilhados.

## Arquivos alterados neste lote

- `README.md` — identidade multi-nicho e constituição de Vertical/VerticalContext;
- `docs/ARCHITECTURE.md` — direção arquitetural alinhada à constituição;
- `docs/IMPLEMENTATION-LOTS.md` — continuidade formal da nova trilha de lotes;
- `tests/Architecture/MultiVerticalConstitutionTest.php` — gate constitucional básico;
- `docs/MULTI-VERTICAL-LOT-1-CONSTITUTION.md` — este relatório.

## Arquivos propositalmente não alterados

Não foram alterados:

- `config/product_tools.php`;
- manifests das ferramentas;
- `app/Core`;
- `app/Tools`;
- rotas;
- controllers;
- models;
- migrations;
- views;
- JavaScript/CSS;
- configuração de Analytics;
- configuração E2E.

Isso preserva a regra do lote: definir a nova arquitetura sem mudar o produto
que está em produção hoje.

## Validação

Antes das alterações:

- `php artisan tools:check-architecture` — aprovado;
- `php artisan analytics:check` — aprovado;
- PHPUnit não iniciou porque o ambiente disponível não possui as extensões PHP
  `dom`, `mbstring` e `xmlwriter`.

Após as alterações:

- `php artisan tools:check-architecture` — aprovado;
- `php artisan analytics:check` — aprovado;
- `php -l tests/Architecture/MultiVerticalConstitutionTest.php` — aprovado;
- PHPUnit continuou indisponível pelas mesmas extensões ausentes (`dom`,
  `mbstring` e `xmlwriter`).

A indisponibilidade das extensões do PHPUnit é uma limitação do ambiente de
análise e não foi tratada como falha do projeto.

## CORE_CANDIDATES

Este lote não confirmou reutilização concreta que justificasse promover uma nova
capacidade para o Core técnico. `Vertical` e `VerticalContext` são conceitos
arquiteturais planejados explicitamente para o Lote 2, e não uma abstração
extraída antecipadamente neste lote.

## Critério de conclusão

O Lote 1 está concluído quando:

1. a documentação oficial não define mais o Prazzu como exclusivamente contábil;
2. Contabilidade está explícita como primeira vertical;
3. Vertical e VerticalContext estão definidos arquiteturalmente;
4. Global x Vertical e a regra de não duplicação estão formalizados;
5. o fallback `VerticalContext = null` está registrado;
6. AcquisitionContext permanece preservado e distinto;
7. nenhum comportamento público foi modificado;
8. o próximo lote possui instruções de continuidade reproduzíveis.
