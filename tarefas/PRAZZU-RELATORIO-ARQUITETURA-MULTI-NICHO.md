# PRAZZU — Relatório de Evolução Arquitetural para Plataforma Multi-Nicho

## 1. Objetivo deste documento

Este documento registra, de forma detalhada, a intenção de evolução do projeto Prazzu.

Ele deve servir como **fonte de contexto para futuras implementações**, principalmente quando o projeto for analisado por uma IA, desenvolvedor ou arquiteto que ainda não conhece a decisão de produto descrita aqui.

A regra principal é:

> O projeto atual está funcionando bem, sua arquitetura e suas regras devem ser preservadas. A mudança pretendida não é uma reescrita do sistema, nem a criação de vários sistemas independentes. O objetivo é adaptar o projeto atual para que a mesma plataforma possa atender múltiplos nichos de negócio.

Hoje, o projeto é essencialmente voltado para **Contabilidade**.

No futuro, o Prazzu deverá atender muitos outros nichos, como:

- RH;
- Financeiro;
- Jurídico;
- Marketing;
- Logística;
- Engenharia;
- Saúde;
- Agronegócio;
- e quaisquer outros nichos que façam sentido futuramente.

Contabilidade deve ser entendida como **a primeira implementação de referência**, e não como o domínio definitivo ou global da plataforma.

---

# 2. Estado atual do projeto

Atualmente, o Prazzu funciona como um projeto focado em Contabilidade.

Isso significa que:

- a Home atual é voltada para o público contábil;
- as ferramentas existentes são voltadas para Contabilidade;
- o conteúdo existente é majoritariamente contábil;
- o SEO atual foi pensado para Contabilidade;
- os contextos e campanhas existentes nasceram a partir do projeto contábil;
- a experiência geral do usuário transmite a sensação de que o Prazzu é uma plataforma de Contabilidade.

Essa estrutura **não está errada**.

O projeto atual deve ser preservado e utilizado como base para a expansão.

A intenção não é desmontar a aplicação atual.

A intenção é transformar:

```text
Prazzu = projeto de Contabilidade
```

em:

```text
Prazzu = plataforma para múltiplos nichos
```

onde:

```text
Contabilidade = uma vertical da plataforma
RH            = outra vertical
Financeiro    = outra vertical
...
```

---

# 3. Visão final da plataforma

A visão de alto nível é:

```text
                              PRAZZU
                                │
        ┌───────────────────────┼────────────────────────┐
        │                       │                        │
     VERTICAIS               SHARED                  ANALYTICS
        │                       │                        │
  ┌─────┼────────┐        ┌─────┼──────────┐             │
  │     │        │        │     │          │             │
Contab. RH   Financeiro   Blog Planos   Recursos       Global
  │     │        │                                         │
Tools Tools    Tools                                    recebe tudo
  │     │        │                                         │
  └─────┴────────┴──── experiência específica              │
                                                         filtros
                                                        por vertical
```

O ponto principal é:

> A experiência pode parecer um projeto completamente diferente para cada nicho, mas tecnicamente continua sendo a mesma plataforma.

Uma pessoa de Contabilidade deve sentir que está usando uma plataforma feita para Contabilidade.

Uma pessoa de RH deve sentir que está usando uma plataforma feita para RH.

Uma pessoa do Financeiro deve sentir que está usando uma plataforma feita para Financeiro.

Mesmo assim, por baixo, tudo continua compartilhando a mesma infraestrutura.

---

# 4. Conceito central: Vertical

O projeto deve ganhar um conceito estrutural chamado **Vertical**.

Vertical representa o nicho ou universo de negócio ao qual determinado conteúdo ou funcionalidade pertence.

Exemplos:

```text
vertical = contabilidade
vertical = rh
vertical = financeiro
vertical = juridico
vertical = marketing
vertical = logistica
...
```

A arquitetura **não deve conhecer previamente uma lista fechada de nichos**.

Isto deve ser evitado:

```php
if ($vertical === 'contabilidade') {
    ...
}

if ($vertical === 'rh') {
    ...
}

if ($vertical === 'financeiro') {
    ...
}
```

O sistema deve conhecer apenas:

```text
Vertical
```

e trabalhar com qualquer vertical cadastrada.

## Regra arquitetural

> Contabilidade, RH, Financeiro ou qualquer outro nicho devem ser dados/configuração de negócio, e não ramificações rígidas da arquitetura.

Adicionar um novo nicho no futuro não deve exigir reestruturar o Core da aplicação.

---

# 5. O que significa "duplicar o projeto"

Quando se fala em “duplicar o que existe hoje para outros nichos”, isso deve ser entendido como **duplicação conceitual de produto e conteúdo**, e não como duplicação técnica da aplicação.

Exemplo:

Hoje existe uma experiência de Contabilidade com:

- Home;
- Hero;
- ferramentas;
- categorias;
- artigos;
- recursos;
- CTAs;
- FAQs;
- SEO;
- campanhas;
- textos;
- recomendações.

Para RH será criada uma experiência equivalente:

- Hero de RH;
- ferramentas de RH;
- categorias de RH;
- artigos de RH;
- recursos de RH;
- CTAs de RH;
- FAQs de RH;
- SEO de RH;
- campanhas de RH;
- textos de RH;
- recomendações de RH.

Mas os componentes, serviços, infraestrutura e contratos devem continuar sendo compartilhados.

## Não fazer

```text
HomeContabilidadeController
HomeRHController
HomeFinanceiroController

ContabilidadeAnalytics
RHAnalytics
FinanceiroAnalytics

ContabilidadeBlog
RHBlog
FinanceiroBlog

ContabilidadeSEO
RHSEO
FinanceiroSEO
```

## Preferir

```text
Home
+ VerticalContext

Blog
+ VerticalContext

Analytics
+ vertical

SEO
+ vertical

Ferramentas
+ vertical
```

---

# 6. VerticalContext

A plataforma deve possuir um conceito de **VerticalContext**.

Ele representa a vertical que está ativa para a experiência atual do usuário.

Exemplo:

```text
VerticalContext = contabilidade
```

ou:

```text
VerticalContext = rh
```

ou:

```text
VerticalContext = null
```

`null` representa uma experiência padrão/genérica do Prazzu, caso nenhum contexto tenha sido identificado.

O VerticalContext deve ser uma camada transversal utilizada por diferentes partes do sistema.

```text
                     VerticalContext
                           │
      ┌────────────────────┼─────────────────────┐
      │                    │                     │
     Home              Ferramentas              Blog
      │                    │                     │
      ├────────────── Recursos / SEO ────────────┤
      │                                          │
      └──────────── Analytics / E2E ─────────────┘
```

---

# 7. Como o contexto pode ser descoberto

O VerticalContext não precisa vir de uma única fonte.

Ele pode ser descoberto a partir de diferentes sinais.

Exemplos:

- campanha;
- palavra-chave;
- AcquisitionContext;
- ferramenta acessada;
- artigo acessado;
- busca realizada;
- filtro escolhido;
- link específico;
- preferência anterior;
- escolha manual;
- origem da navegação;
- contexto salvo em sessão.

A arquitetura deve permitir que diferentes resolvers contribuam para a determinação da vertical.

Exemplo conceitual:

```text
Campanha
     ─┐
Ferramenta acessada
     ─┤
Artigo acessado
     ─┤
Busca
     ─┤──> Resolve VerticalContext
Filtro
     ─┤
Preferência
     ─┘
```

---

# 8. Relação com o AcquisitionContext já existente

O projeto atual já possui um conceito contextual relacionado a aquisição, campanhas e palavras-chave.

Esse conceito **não deve ser descartado**.

Também não deve ser confundido com Vertical.

São conceitos diferentes.

## Vertical

Responde:

> Qual universo de negócio está ativo para este usuário?

Exemplo:

```text
Vertical = RH
```

## AcquisitionContext

Responde:

> Por qual intenção, campanha ou origem específica esse usuário chegou?

Exemplo:

```text
AcquisitionContext = rescisao-tiktok-01
```

A relação ideal é:

```text
AcquisitionContext
       │
       └── pertence ou resolve para uma Vertical
```

Exemplo:

```text
AcquisitionContext:
    keyword: rescisao-tiktok
    vertical: rh
```

A experiência pode então carregar:

```text
VerticalContext    = rh
AcquisitionContext = rescisao-tiktok
```

O VerticalContext define o universo geral da experiência.

O AcquisitionContext pode personalizar ainda mais:

- Hero;
- CTA;
- campanha;
- mensagem;
- funil;
- tracking;
- origem;
- criativo;
- vídeo;
- anúncio.

---

# 9. Home

A Home atual é fortemente voltada para Contabilidade.

Esse comportamento deve continuar existindo para Contabilidade.

Ao expandir a plataforma, existem duas estratégias arquiteturalmente aceitáveis.

## Estratégia preferencial: uma Home contextual

Existe uma única Home técnica:

```text
/
```

mas seu conteúdo é montado conforme o VerticalContext.

Exemplo:

```text
VerticalContext = contabilidade

Home:
- Hero contábil
- ferramentas contábeis
- artigos contábeis
- recursos contábeis
- CTAs contábeis
```

Para RH:

```text
VerticalContext = rh

Home:
- Hero de RH
- ferramentas de RH
- artigos de RH
- recursos de RH
- CTAs de RH
```

Para alguém sem contexto:

```text
VerticalContext = null

Home:
- apresentação geral do Prazzu
- principais nichos
- ferramentas em destaque
- conteúdo geral
```

## Estratégia alternativa

A arquitetura também pode permitir variações específicas de Home por vertical, caso isso se torne necessário no futuro.

O importante é:

> Mesmo se houver variações de Home, elas não devem criar infraestrutura paralela.

Os mesmos serviços, componentes, contratos e fontes de dados devem ser reutilizados.

---

# 10. Ferramentas

Ferramentas são uma das partes realmente específicas por vertical.

Cada nicho pode ter quantidades e tipos completamente diferentes de ferramentas.

Exemplo:

```text
Contabilidade
├── ferramenta A
├── ferramenta B
├── ferramenta C
└── ...

RH
├── calculadora de férias
├── rescisão
├── folha
└── ...

Financeiro
├── juros
├── fluxo de caixa
├── financiamento
└── ...
```

Não existe obrigação de equivalência.

Uma vertical pode ter:

```text
5 ferramentas
```

ou:

```text
200 ferramentas
```

## Regra

Toda ferramenta específica de negócio deve declarar sua vertical.

Exemplo conceitual:

```text
tool:
    slug: calculadora-ferias
    vertical: rh
```

Ferramentas compartilhadas por mais de uma vertical podem futuramente permitir múltiplas associações, se fizer sentido.

---

# 11. Página de Ferramentas

A infraestrutura da página de Ferramentas deve continuar compartilhada.

Não criar:

```text
FerramentasContabilidade
FerramentasRH
FerramentasFinanceiro
```

Preferir:

```text
Ferramentas
+ VerticalContext
```

Assim:

```text
contexto = rh
```

mostra ferramentas de RH.

```text
contexto = contabilidade
```

mostra ferramentas contábeis.

```text
contexto = null
```

pode mostrar:

- todas as ferramentas;
- filtros por nicho;
- destaques;
- busca global;
- categorias.

---

# 12. Blog

O Blog deve ser **um único sistema**.

Não devem existir Blogs separados por vertical.

Tecnicamente:

```text
1 Blog
```

Para o usuário, porém, a experiência pode parecer:

```text
Blog de Contabilidade
```

ou:

```text
Blog de RH
```

ou:

```text
Blog Financeiro
```

porque o conteúdo exibido será contextual.

Exemplo:

```text
/blog
VerticalContext = rh
```

pode mostrar:

- férias;
- folha;
- rescisão;
- benefícios;
- legislação trabalhista.

Enquanto:

```text
/blog
VerticalContext = contabilidade
```

pode mostrar:

- impostos;
- fiscal;
- empresas;
- tributário;
- Simples Nacional.

## Associação de conteúdo

Artigos devem poder possuir uma vertical.

Exemplo:

```text
article:
    title: Como calcular férias
    vertical: rh
```

Também deve ser considerada a possibilidade de conteúdo:

```text
vertical = global
```

ou associado a múltiplas verticais, quando o assunto for transversal.

---

# 13. Planos e assinatura

Planos devem ser **globais**.

Não existe intenção de criar:

```text
Plano Contabilidade
Plano RH
Plano Financeiro
```

A intenção é algo como:

```text
Prazzu Pro
R$ X / mês
```

O usuário paga uma assinatura da plataforma.

Essa assinatura pode dar acesso às ferramentas de múltiplas verticais conforme as regras comerciais do produto.

Exemplo:

```text
Usuário assina Prazzu
        │
        ├── ferramentas de Contabilidade
        ├── ferramentas de RH
        ├── ferramentas Financeiras
        └── futuras verticais
```

A vertical organiza a experiência.

A assinatura pertence ao Prazzu.

---

# 14. Analytics

Analytics deve continuar sendo **um único sistema global**.

Não criar Analytics separados por nicho.

Exemplo de evento:

```text
event: tool_used
vertical: rh
tool: calculadora-ferias
user: ...
plan: ...
campaign: ...
```

Outro:

```text
event: article_view
vertical: contabilidade
article: simples-nacional
```

O Analytics deve permitir:

```text
Prazzu completo
```

e filtros por:

```text
vertical
tool
article
campaign
plan
user
origem
etc.
```

## Regra

> Toda telemetria específica de negócio deve carregar a dimensão de vertical quando aplicável.

---

# 15. SEO

O sistema de SEO deve permanecer compartilhado.

Não criar engines separadas.

O SEO deve receber o contexto da vertical.

Exemplo:

```text
SEO
+ VerticalContext
+ recurso atual
```

e gerar:

- title;
- description;
- canonical;
- schema;
- Open Graph;
- keywords;
- breadcrumbs;
- sitemap;
- dados estruturados.

Cada vertical poderá ter estratégias e conteúdos diferentes sem duplicar a engine.

---

# 16. Recursos

Recursos devem seguir a mesma filosofia do Blog.

A infraestrutura é global.

O conteúdo pode ser:

```text
vertical = contabilidade
vertical = rh
vertical = financeiro
vertical = global
```

A experiência mostra o conteúdo relevante para o contexto atual.

---

# 17. E2E

O E2E deve continuar sendo **único e compartilhado**.

Não criar:

```text
E2EContabilidade
E2ERH
E2EFinanceiro
```

O framework deve descobrir/testar as ferramentas independentemente da vertical.

A vertical deve ser apenas mais uma dimensão do cenário.

Exemplo conceitual:

```text
scenario:
    vertical: rh
    tool: calculadora-ferias
```

O mesmo runner executa:

```text
Contabilidade
RH
Financeiro
Jurídico
...
```

Os comandos oficiais atualmente preservados devem continuar sendo respeitados:

```bash
npm run e2e:test:actions
npm run e2e:test:tool <slug>
php artisan test
```

Qualquer evolução do E2E deve preservar essa filosofia de infraestrutura única.

---

# 18. Autenticação, usuários e permissões

Autenticação deve continuar global.

Um usuário é usuário do Prazzu, não usuário de uma aplicação separada de Contabilidade ou RH.

Evitar:

```text
UserContabilidade
UserRH
UserFinanceiro
```

Preferir:

```text
User
```

com relações, permissões, preferências e contexto quando necessário.

---

# 19. Administração

O Admin deve continuar global.

A administração pode permitir filtrar e gerenciar entidades por vertical.

Exemplos:

```text
Admin > Ferramentas > RH
Admin > Artigos > Contabilidade
Admin > Campanhas > Financeiro
```

mas isso deve ser filtragem/contexto.

Não criar painéis administrativos duplicados por nicho.

---

# 20. Componentes e UI

Componentes devem continuar compartilhados sempre que possível.

Exemplo:

```text
Hero
ToolCard
ArticleCard
CTA
FAQ
Breadcrumb
PricingCard
Search
Filters
```

Esses componentes recebem dados/contexto.

Não devem ser copiados somente porque o nicho mudou.

Exemplo correto:

```text
Hero
+ conteúdo RH
```

e:

```text
Hero
+ conteúdo Contabilidade
```

em vez de:

```text
HeroRH
HeroContabilidade
```

salvo quando existir uma diferença estrutural real e comprovada.

---

# 21. Core e Shared

A arquitetura deve preservar uma camada compartilhada clara.

Exemplo conceitual:

```text
Core / Shared
├── Analytics
├── Acquisition
├── VerticalContext
├── SEO
├── Auth
├── Billing
├── Blog engine
├── Resources
├── E2E
├── UI
├── Observability
├── Search
└── Infrastructure
```

Enquanto as verticais fornecem:

```text
Vertical
├── identidade
├── conteúdo
├── ferramentas
├── categorias
├── SEO específico
├── recomendações
└── configurações de domínio
```

---

# 22. Regra de não duplicação

Esta deve ser uma das regras mais importantes do projeto.

> Uma nova vertical deve reutilizar a plataforma existente e implementar somente aquilo que difere por domínio.

É proibida a duplicação de infraestrutura compartilhada apenas para criar um novo nicho.

## Antes de duplicar qualquer coisa, perguntar:

1. Isso realmente possui comportamento diferente?
2. Ou apenas possui dados/conteúdo diferente?
3. Pode ser resolvido com `VerticalContext`?
4. Pode ser resolvido com configuração?
5. Pode ser resolvido com associação de dados?
6. Pode ser resolvido com composição?
7. Pode ser resolvido com um contrato compartilhado?

Se a diferença for apenas conteúdo, configuração ou seleção de dados, não deve existir duplicação estrutural.

---

# 23. Contabilidade como referência

O projeto atual de Contabilidade deve se tornar a **primeira vertical oficial**.

Isso significa:

```text
Projeto atual
     ↓
adaptado
     ↓
Vertical: Contabilidade
```

A funcionalidade existente deve continuar funcionando.

A migração não deve exigir reescrever todas as ferramentas.

Sempre que possível, deve-se:

- adicionar o conceito de vertical;
- associar o conteúdo atual à vertical `contabilidade`;
- manter os módulos atuais;
- manter os contratos atuais;
- preservar URLs ou usar redirects quando necessário;
- preservar testes;
- preservar comportamento;
- preservar arquitetura.

---

# 24. Processo desejado para criar uma nova vertical

A arquitetura estará madura quando criar uma nova vertical for algo próximo de:

```text
1. Registrar a vertical
2. Definir nome e slug
3. Definir identidade e conteúdo
4. Cadastrar ferramentas
5. Associar artigos
6. Associar recursos
7. Configurar SEO
8. Criar campanhas/contextos de aquisição
9. Publicar
```

Sem precisar criar:

- novo Analytics;
- novo E2E;
- novo sistema de planos;
- nova autenticação;
- novo Blog;
- novo Admin;
- nova infraestrutura;
- nova arquitetura.

---

# 25. Estrutura conceitual final

```text
                               PRAZZU
                                  │
                     Plataforma compartilhada
                                  │
          ┌───────────────────────┼────────────────────────┐
          │                       │                        │
   VerticalContext            Serviços Globais          Dados Globais
          │                       │                        │
   ┌──────┼───────┐         ┌─────┼────────┐         ┌────┼────┐
   │      │       │         │     │        │         │         │
Contab.   RH   Financeiro  Blog  Planos Recursos   Analytics  Users
   │      │       │
 Tools   Tools   Tools
   │      │       │
 Conteúdo específico
   │
 futuras verticais
```

Uma representação ainda mais simples:

```text
                 PRAZZU
                    │
             Vertical ativa
                    │
      ┌─────────────┼─────────────┐
      │             │             │
    Home       Ferramentas       Blog
      │             │             │
 Recursos         SEO          Conteúdo
      │             │             │
      └─────────────┼─────────────┘
                    │
           infraestrutura comum
                    │
        Analytics / E2E / Auth /
        Planos / Admin / Core
```

---

# 26. O que NÃO está sendo proposto

Este projeto NÃO pretende:

- criar uma aplicação Laravel separada para cada nicho;
- criar um banco separado para cada vertical;
- criar um Blog separado para cada nicho;
- criar Analytics separados;
- criar sistemas de assinatura separados;
- criar autenticações separadas;
- criar Admins separados;
- duplicar componentes apenas para trocar textos;
- duplicar controllers sem necessidade;
- duplicar infraestrutura de testes;
- transformar RH, Financeiro ou Contabilidade em cópias independentes do projeto;
- abandonar as regras arquiteturais atuais;
- reescrever o projeto do zero.

---

# 27. O que ESTÁ sendo proposto

O projeto pretende:

- manter a aplicação atual;
- manter a arquitetura atual;
- manter as regras atuais que funcionam;
- introduzir Vertical como conceito de negócio;
- transformar Contabilidade na primeira vertical;
- permitir múltiplas verticais;
- contextualizar Home, Ferramentas, Blog, Recursos e SEO;
- manter Planos globais;
- manter Analytics global;
- manter E2E global;
- manter autenticação global;
- manter infraestrutura global;
- reutilizar os contextos de aquisição existentes;
- permitir experiências que pareçam específicas para cada nicho;
- evitar duplicação técnica;
- permitir expansão futura com baixo custo estrutural.

---

# 28. Princípios arquiteturais obrigatórios

## Princípio 1 — Preservar o que já funciona

A expansão deve ser incremental.

Não reescrever partes estáveis apenas para adequá-las a uma nova nomenclatura.

---

## Princípio 2 — Vertical é contexto, não aplicação

Uma vertical representa um nicho de negócio.

Ela não representa uma aplicação separada.

---

## Princípio 3 — Conteúdo pode variar; infraestrutura não

É aceitável ter:

```text
conteúdo RH
conteúdo Contabilidade
conteúdo Financeiro
```

Não é aceitável duplicar:

```text
Analytics
Auth
Billing
E2E
Admin
Blog engine
```

sem uma necessidade arquitetural real.

---

## Princípio 4 — Contabilidade não pode permanecer implícita

Nenhum novo código compartilhado deve assumir que:

```text
Prazzu = Contabilidade
```

Quando a lógica for específica de Contabilidade, ela deve pertencer explicitamente à vertical correspondente.

---

## Princípio 5 — Nenhuma lista fechada de nichos

O código compartilhado não deve ser escrito pensando somente em:

```text
contabilidade
rh
financeiro
```

Esses nomes são exemplos.

A plataforma deve suportar novas verticais sem modificar o Core.

---

## Princípio 6 — Contexto deve ser propagado

Quando uma vertical estiver ativa, os sistemas relevantes devem conseguir consultá-la:

- Home;
- Ferramentas;
- Blog;
- Recursos;
- SEO;
- Analytics;
- recomendações;
- aquisição;
- breadcrumbs;
- navegação;
- busca;
- E2E, quando aplicável.

---

## Princípio 7 — Fallback sempre deve existir

Quando não houver uma vertical válida:

```text
VerticalContext = null
```

a plataforma deve continuar funcionando com uma experiência Prazzu padrão.

---

## Princípio 8 — Testes devem proteger a arquitetura

A suíte de testes deve garantir que:

- adicionar uma nova vertical não exija alterar infraestrutura;
- ferramentas saibam sua vertical;
- Analytics receba vertical quando aplicável;
- contexto inexistente não quebre a aplicação;
- fallback funcione;
- aquisição possa resolver vertical;
- conteúdo seja filtrado corretamente;
- nenhuma vertical vaze conteúdo de outra sem intenção.

---

# 29. Estratégia sugerida de implementação

A expansão deve ser realizada em lotes pequenos e verificáveis.

## Lote 1 — Constituição e contratos

Objetivo:

Definir a nova regra arquitetural sem alterar comportamento público.

Inclui:

- atualizar README;
- documentar Vertical;
- documentar VerticalContext;
- definir Global x Vertical;
- definir regras de não duplicação;
- definir fallback;
- testes arquiteturais básicos.

Resultado:

O projeto continua funcionando exatamente como antes.

---

## Lote 2 — Fundação de VerticalContext

Objetivo:

Adicionar a infraestrutura genérica de vertical.

Inclui:

- representação da Vertical;
- resolver;
- contexto ativo;
- sessão/persistência quando aplicável;
- integração com AcquisitionContext;
- primeira vertical: `contabilidade`.

Resultado:

O projeto atual continua aparentando ser Contabilidade, mas agora isso é explícito.

---

## Lote 3 — Ferramentas e conteúdo contextual

Objetivo:

Associar recursos existentes a verticais.

Inclui:

- ferramentas;
- artigos;
- recursos;
- categorias;
- recomendações;
- busca;
- filtros.

Resultado:

Contabilidade continua igual, mas o sistema já consegue receber uma segunda vertical.

---

## Lote 4 — Home e experiência contextual

Objetivo:

Permitir que a experiência inicial responda ao contexto.

Inclui:

- Hero;
- destaques;
- ferramentas;
- artigos;
- recursos;
- CTAs;
- fallback padrão.

Resultado:

Uma mesma estrutura pode parecer um projeto de Contabilidade, RH, Financeiro etc.

---

## Lote 5 — Serviços globais conscientes de vertical

Objetivo:

Garantir segmentação sem duplicação.

Inclui:

- Analytics;
- SEO;
- sitemap;
- breadcrumbs;
- aquisição;
- Admin;
- busca;
- observabilidade.

Resultado:

Toda a infraestrutura continua global, mas pode ser filtrada/segmentada por vertical.

---

## Lote 6 — Prova de expansão

Objetivo:

Provar que a arquitetura realmente é genérica.

Criar uma segunda vertical mínima, por exemplo RH, apenas como prova arquitetural.

Não precisa ter dezenas de ferramentas.

Basta possuir:

- registro;
- conteúdo básico;
- 1 ou 2 ferramentas;
- artigos;
- contexto;
- Analytics;
- SEO;
- E2E.

Se adicionar RH exigir copiar infraestrutura, a arquitetura ainda não está pronta.

---

# 30. Critérios de sucesso

A migração será considerada bem-sucedida quando:

1. Contabilidade continuar funcionando como hoje.
2. O usuário contábil continuar sentindo que usa um produto especializado em Contabilidade.
3. Uma nova vertical puder ser criada sem duplicar Core.
4. Home puder responder a contexto.
5. Ferramentas puderem ser filtradas por vertical.
6. Blog puder parecer específico por nicho usando uma única engine.
7. Analytics continuar único.
8. Planos continuarem globais.
9. Auth continuar global.
10. E2E continuar único.
11. AcquisitionContext continuar funcionando.
12. AcquisitionContext puder ajudar a resolver VerticalContext.
13. SEO puder ser segmentado por vertical.
14. Conteúdo não vazar entre verticais sem intenção.
15. Fallback padrão funcionar sem contexto.
16. `php artisan test` continuar sendo a validação principal do backend.
17. `npm run e2e:test:actions` continuar funcionando.
18. `npm run e2e:test:tool <slug>` continuar funcionando.
19. Nova vertical não exigir novo Analytics, Blog, Billing, Auth, Admin ou E2E.
20. README permanecer como fonte de verdade arquitetural.

---

# 31. Instruções para futuras IAs que analisarem este projeto

Se você é uma IA analisando este documento antes de implementar mudanças no Prazzu, siga estas regras:

1. Não trate o projeto como uma reescrita.
2. Analise a arquitetura existente antes de propor nova estrutura.
3. Preserve as regras atuais do README que continuam válidas.
4. Evolua a arquitetura de forma incremental.
5. Contabilidade é a primeira vertical, não o Core.
6. Não duplique infraestrutura para novas verticais.
7. Diferencie claramente conteúdo de infraestrutura.
8. Utilize VerticalContext para comportamento contextual.
9. Não confunda VerticalContext com AcquisitionContext.
10. AcquisitionContext pode ser uma fonte para determinar VerticalContext.
11. Planos são globais.
12. Analytics é global.
13. E2E é global.
14. Auth é global.
15. Admin é global.
16. Blog é uma única engine.
17. Recursos são uma única infraestrutura.
18. Ferramentas pertencem a verticais.
19. Conteúdo pode pertencer a uma ou várias verticais.
20. Sempre mantenha fallback global.
21. Nunca escreva arquitetura limitada a três nichos.
22. Considere que podem existir dezenas ou centenas de verticais.
23. Uma nova vertical deve ser principalmente configuração, conteúdo e ferramentas.
24. Antes de criar uma classe específica de vertical, prove que há comportamento realmente diferente.
25. Antes de alterar URLs, rotas ou estrutura física, procure todas as referências e preserve compatibilidade quando necessário.
26. Sempre rode os testes existentes após cada lote.
27. O README da raiz deve continuar funcionando como constituição do projeto.

---

# 32. Resumo executivo

O Prazzu atual é uma excelente base de Contabilidade.

A expansão pretendida não substitui esse projeto.

Ela transforma o projeto atual em uma plataforma capaz de servir múltiplos nichos.

O modelo final deve permitir que:

```text
usuário de Contabilidade
→ veja uma experiência de Contabilidade

usuário de RH
→ veja uma experiência de RH

usuário Financeiro
→ veja uma experiência Financeira

usuário sem contexto
→ veja uma experiência geral do Prazzu
```

Enquanto tecnicamente existe:

```text
1 aplicação
1 Core
1 Analytics
1 Blog
1 sistema de planos
1 autenticação
1 Admin
1 E2E
1 infraestrutura
```

e múltiplas:

```text
Verticais
├── Contabilidade
├── RH
├── Financeiro
├── Jurídico
├── Marketing
├── ...
```

Cada vertical deve implementar somente:

```text
conteúdo específico
+
ferramentas específicas
+
configuração/identidade específica
```

O princípio final da evolução é:

> **O Prazzu deve parecer muitos produtos especializados para o usuário, mas continuar sendo uma única plataforma bem arquitetada por baixo.**

---

# 33. Regra-mestra

Se houver dúvida durante qualquer implementação futura, utilizar esta regra:

> **Preserve o projeto atual, torne Contabilidade explícita como uma vertical e generalize apenas o necessário para que o mesmo modelo possa atender qualquer outro nicho sem duplicar infraestrutura.**

Essa é a intenção central deste documento.
