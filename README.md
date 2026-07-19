# Prazzu Tools

## O que é o Prazzu Tools

O **Prazzu Tools** é uma plataforma de ferramentas profissionais voltada a
contadores, profissionais contábeis e escritórios de contabilidade de todos os
portes e especialidades.

Seu propósito é reunir ferramentas realmente úteis para a rotina contábil,
como calculadoras, validadores, simuladores, conversores, geradores,
comparadores e utilitários técnicos. Cada ferramenta deve resolver um problema
real de forma completa, objetiva, confiável e transparente.

O Prazzu Tools **não é um SaaS de gestão**. Ele não pretende administrar a
operação de um escritório, controlar clientes, tarefas, processos,
departamentos, documentos operacionais ou fluxos de trabalho. Essas
responsabilidades pertencem ao **Prazzu Core**, produto separado do ecossistema
Prazzu.

A identidade deste projeto é simples:

> O Prazzu Tools é uma plataforma especializada em ferramentas 100% úteis para
> contadores e escritórios de contabilidade.

Cada ferramenta possui estrutura, domínio, regras, interface e testes próprios.
Uma ferramenta não deve depender de outra nem interferir em seu funcionamento.
Isso permite criar, alterar ou remover módulos com baixo risco de regressão nas
demais áreas da plataforma.

A independência dos módulos não autoriza duplicação. Tudo que for genérico ou
reutilizável deve permanecer fora do domínio das ferramentas e ser oferecido
pela infraestrutura compartilhada do Prazzu Tools. Por exemplo, se várias
ferramentas geram PDF, elas devem utilizar o mesmo serviço compartilhado de PDF,
em vez de manter bibliotecas ou implementações próprias em cada módulo.

Esse princípio também se aplica a exportações, histórico, favoritos, analytics,
autenticação, armazenamento, notificações, inteligência artificial e qualquer
outra capacidade transversal.

A plataforma foi projetada para receber muitas ferramentas ao longo do tempo,
sem transformar o projeto em um sistema monolítico e sem recriar soluções que já
existem.

------------------------------------------------------------------------

# Nossa visão

Acreditamos que o profissional da contabilidade deve encontrar, em um
único lugar, ferramentas confiáveis para resolver rapidamente problemas
reais e bem delimitados.

O objetivo do Prazzu Tools não é apenas reduzir o tempo gasto em
cálculos.

O objetivo é construir o melhor catálogo de soluções pontuais para a
rotina contábil, sem transformar a plataforma em sistema de gestão.

Conforme a plataforma evolui, novas ferramentas passam a fazer parte
desse ecossistema, mantendo sempre a mesma experiência de uso, a mesma
qualidade e os mesmos princípios.

------------------------------------------------------------------------

# Filosofia da plataforma

Toda ferramenta publicada no Prazzu Tools segue exatamente a mesma
filosofia.

A plataforma distingue duas categorias de capacidade:

-   **Essencial**
-   **Prazzu Plus**

A capacidade **Essencial** é gratuita e resolve por completo o problema
principal da ferramenta, com a mesma correção, transparência e qualidade
que existirá em qualquer modalidade.

O **Prazzu Plus** acrescenta produtividade, volume, automação, continuidade,
análises, cenários e formatos de conveniência. Ele nunca libera uma parte
necessária da fórmula, corrige um resultado incompleto ou esconde informações
necessárias para solucionar o caso básico.

Essa classificação existe para organizar o produto e preparar sua futura
monetização. Ela não autoriza limitar artificialmente cálculos, esconder
resultados ou degradar a experiência do visitante.

Durante a fase inicial de lançamento, todas as ferramentas e todos os
recursos Prazzu Plus estarão disponíveis gratuitamente, sem limite de
uso e sem exigir autenticação.

A pessoa deve conseguir entrar na plataforma e utilizar imediatamente a
capacidade máxima de cada ferramenta.

O login não existe para liberar cálculos ou recursos avançados. Ele
existe para permitir persistência e continuidade, incluindo:

-   histórico;
-   resultados salvos;
-   favoritos;
-   recuperação posterior;
-   sincronização entre dispositivos;
-   preferências pessoais;
-   recursos vinculados à identidade do usuário.

O visitante pode calcular, comparar, projetar, exportar e utilizar os
recursos avançados, mas seus dados permanecem temporários e podem ser
perdidos ao atualizar a página, fechar o navegador ou encerrar a sessão.

No futuro, quando a cobrança for ativada, o Prazzu Plus continuará
representando produtividade, automação e conveniência, nunca a correção
do cálculo básico.

Em outras palavras:

> Acesso às ferramentas não depende de conta. Persistência,
> sincronização e histórico dependem de conta.

Essa filosofia orienta todas as ferramentas da plataforma.

------------------------------------------------------------------------

# Papel do Prazzu Tools no Ecossistema

O Prazzu Tools faz parte do ecossistema Prazzu, porém possui uma missão
específica: resolver rapidamente problemas através de ferramentas para
profissionais da contabilidade.

Cada produto do ecossistema possui responsabilidades próprias:

-   Prazzu Tools: ferramentas;
-   Prazzu Blog: conteúdo técnico;
-   Prazzu Learn: educação;
-   Prazzu Core: gestão empresarial;
-   Prazzu Connect: conexão entre clientes e contadores;
-   Conta Prazzu: identidade única dos usuários.

Nenhum produto deverá assumir responsabilidades que pertencem a outro
produto do ecossistema.

------------------------------------------------------------------------

# Uma plataforma, não um conjunto de ferramentas

Embora cada ferramenta seja desenvolvida de forma totalmente
independente, todas fazem parte da mesma plataforma.

Isso significa que funcionalidades como:

-   autenticação;
-   usuários;
-   planos;
-   assinatura;
-   permissões;
-   favoritos;
-   histórico;
-   analytics;
-   notificações;
-   exportações;
-   inteligência artificial;
-   dashboard administrativo;
-   métricas de utilização;
-   blog;
-   SEO;
-   conteúdo institucional;

pertencem ao Core da plataforma e são compartilhadas por todas as
ferramentas.

Cada módulo implementa apenas aquilo que pertence ao seu domínio.

Todo o restante é responsabilidade da infraestrutura compartilhada.

------------------------------------------------------------------------

------------------------------------------------------------------------

# Limites do Produto

O Prazzu Tools é exclusivamente uma plataforma de ferramentas para o universo
contábil.

Ele não implementa funcionalidades de ERP, CRM, gestão de clientes, gestão de
escritório, workflow empresarial, departamentos, tarefas, processos internos,
controle operacional ou colaboração entre usuários.

Sempre que uma funcionalidade ultrapassar a missão de uma plataforma de
ferramentas, ela deverá ser avaliada para outro produto do ecossistema,
como o Prazzu Core, e não incorporada ao Prazzu Tools.

# Arquitetura

O projeto segue um princípio simples.

> Tudo que pertence exclusivamente ao domínio de uma ferramenta permanece
> dentro da própria ferramenta.

> Tudo que pode ser reutilizado por duas ou mais ferramentas pertence à
> infraestrutura compartilhada do Prazzu Tools.

Neste documento, a expressão **Core técnico** significa somente o núcleo
compartilhado interno deste repositório. Ela não deve ser confundida com o
**Prazzu Core**, que é outro produto do ecossistema e concentra funcionalidades
de gestão empresarial.

Isso permite que qualquer ferramenta evolua sem gerar impactos nas
demais.

Ao mesmo tempo, qualquer melhoria implementada no Core passa
automaticamente a beneficiar toda a plataforma.

Essa separação reduz acoplamento, facilita manutenção e torna o
crescimento do projeto previsível.

------------------------------------------------------------------------

# Ferramentas independentes

Cada ferramenta possui seu próprio domínio.

Ela pode possuir:

-   regras de negócio;
-   controllers;
-   services;
-   views;
-   testes;
-   manifest;
-   configurações específicas;
-   recursos gratuitos;
-   recursos Plus.

Uma ferramenta nunca depende internamente de outra.

Ela apenas utiliza os serviços disponibilizados pelo Core quando
necessário.

Essa arquitetura permite adicionar novas ferramentas continuamente sem
alterar as já existentes.

------------------------------------------------------------------------

# Infraestrutura compartilhada

O Core técnico representa a infraestrutura comum do Prazzu Tools.

Entre suas responsabilidades estão:

-   autenticação;
-   gerenciamento de usuários;
-   planos;
-   assinaturas;
-   autorização;
-   analytics;
-   histórico;
-   notificações;
-   exportações;
-   IA;
-   catálogo de ferramentas;
-   blog;
-   dashboard administrativo;
-   SEO compartilhado;
-   serviços reutilizáveis.

As ferramentas conhecem apenas os contratos do Core.

Elas nunca conhecem detalhes de implementação.

------------------------------------------------------------------------

# Identidade do produto: plataforma de ferramentas, não SaaS de gestão

Recursos de conta, histórico, favoritos, exportação ou eventual acesso Plus são
serviços de apoio às ferramentas. Eles não alteram a natureza do produto e não
devem evoluir para funcionalidades de ERP, CRM, workflow, gestão de escritório
ou colaboração operacional.

Uma proposta nova só pertence ao Prazzu Tools quando melhora a descoberta, o
uso, a execução ou a continuidade de uma ferramenta. Quando a proposta passa a
gerenciar pessoas, clientes, empresas, tarefas, processos ou operações, ela deve
ser direcionada ao Prazzu Core.

------------------------------------------------------------------------

# Modelo de assinatura e fase gratuita de lançamento

Existe apenas um produto comercial da plataforma: o **Prazzu Plus**.

O Prazzu Plus pode ser concedido por duas modalidades independentes:

-   **assinatura individual**, contratada pela própria pessoa;
-   **plano empresarial**, contratado por uma empresa como um pacote de
    acessos Plus para seus colaboradores.

A assinatura nunca será realizada individualmente por ferramenta. Quando
a monetização for ativada, um acesso Prazzu Plus deverá liberar os
recursos Plus de todas as ferramentas presentes e futuras, independentemente
se sua origem é individual ou empresarial.

## Plano empresarial e distribuição de acessos

O plano empresarial não cria um workspace compartilhado, não transforma o
Prazzu Tools em ERP e não concede à empresa acesso aos dados pessoais de uso
dos colaboradores.

Seu objetivo é exclusivamente permitir que uma empresa:

1. crie seu cadastro empresarial;
2. contrate uma quantidade de acessos Prazzu Plus;
3. convide colaboradores;
4. atribua as vagas contratadas aos membros ativos;
5. remova ou redistribua uma vaga quando necessário.

Cada colaborador utiliza sua própria Conta Prazzu e mantém de forma
independente:

-   login;
-   histórico;
-   favoritos;
-   resultados;
-   preferências;
-   eventual assinatura individual.

A empresa administra somente vínculos e licenças. Históricos, cálculos,
resultados e preferências não são compartilhados automaticamente.

Quando um colaborador deixa a empresa ou perde sua vaga, somente o benefício
Plus empresarial é removido. A conta pessoal e seus dados continuam existindo,
e uma assinatura individual ativa continua válida.

O Core é responsável por resolver a origem efetiva do acesso Plus:

-   assinatura individual ativa; ou
-   vaga vinculada a uma assinatura empresarial ativa.

As ferramentas consultam apenas o plano efetivo fornecido pelo Core. Elas não
devem conhecer organizações, membros, convites, cobrança, contratos ou vagas.
Qualquer gestão empresarial avançada ou funcionalidade colaborativa pertence
ao Prazzu Core, não ao Prazzu Tools.

Durante a fase inicial de lançamento:

-   nenhuma cobrança será realizada;
-   visitantes e usuários autenticados terão acesso integral;
-   recursos classificados como Plus funcionarão sem limites;
-   nenhuma ferramenta poderá exigir login para calcular ou utilizar sua
    capacidade máxima;
-   a infraestrutura de planos, permissões, limites e cobrança deverá
    ser preservada e mantida pronta para ativação futura;
-   a gratuidade deverá ser controlada centralmente pela plataforma,
    nunca por condicionais espalhadas dentro das ferramentas.

A fase gratuita é uma política comercial temporária da plataforma, não
uma remoção do modelo Plus.

Quando a cobrança for ativada, a mudança deverá ocorrer por política
central, configuração ou feature flag, sem reconstruir os módulos e sem
alterar sua lógica de domínio.

------------------------------------------------------------------------

# O blog como parte da plataforma

O blog não é apenas uma área de notícias.

Ele faz parte da estratégia da plataforma.

Seu objetivo é produzir conteúdo técnico de alta qualidade para
responder dúvidas reais de profissionais da contabilidade, melhorar o
posicionamento orgânico da plataforma e conectar naturalmente o leitor
às ferramentas relacionadas.

Cada artigo deve gerar valor por si só, ao mesmo tempo em que apresenta
as soluções disponíveis dentro do ecossistema Prazzu Tools.

------------------------------------------------------------------------

# Evolução contínua

O Prazzu Tools foi projetado para crescer continuamente.

Hoje a plataforma pode possuir poucas ferramentas.

No futuro poderá possuir dezenas ou centenas de módulos diferentes.

Novas funcionalidades compartilhadas poderão ser adicionadas ao Core sem
exigir alterações individuais em cada ferramenta.

Da mesma forma, novas ferramentas poderão ser desenvolvidas sem
comprometer a estabilidade da plataforma.

------------------------------------------------------------------------

# Compromissos do projeto

Todo desenvolvimento do Prazzu Tools deve respeitar os seguintes
princípios:

-   resultados corretos acima de tudo;
-   transparência nos cálculos;
-   experiência gratuita realmente útil;
-   monetização baseada em produtividade, nunca em limitação artificial;
-   arquitetura modular;
-   baixo acoplamento entre ferramentas;
-   infraestrutura compartilhada para funcionalidades comuns;
-   código limpo e de fácil manutenção;
-   evolução contínua da plataforma;
-   foco em problemas reais, pontuais e bem delimitados da rotina contábil;
-   nenhuma evolução em direção a ERP, CRM ou SaaS de gestão;
-   separação inequívoca entre o Core técnico deste projeto e o produto Prazzu Core.

Esses princípios orientam tanto o desenvolvimento técnico quanto a
evolução do produto.

------------------------------------------------------------------------

# Objetivo final

O objetivo do Prazzu Tools é tornar-se a principal plataforma de
ferramentas para contabilidade no Brasil.

A proposta é reunir calculadoras, validadores, geradores, conversores e
outras soluções pontuais confiáveis, acompanhadas de conteúdo técnico de
qualidade. Gestão empresarial, acompanhamento operacional e relacionamento
com clientes permanecem fora deste produto.

Cada nova ferramenta, cada novo artigo e cada novo recurso devem
aproximar a plataforma desse objetivo.

# Princípios Arquiteturais

Os princípios abaixo fazem parte da arquitetura do Prazzu Tools e devem
orientar toda evolução da plataforma.

Eles existem para garantir consistência, baixo acoplamento e facilidade
de manutenção à medida que novas ferramentas e novos serviços forem
adicionados.

## 1. O Core pertence à plataforma

O Core representa a infraestrutura compartilhada da plataforma.

Toda funcionalidade reutilizável deve ser implementada nele.

Exemplos:

-   autenticação;
-   usuários;
-   planos;
-   assinaturas;
-   permissões;
-   analytics;
-   histórico;
-   notificações;
-   exportações;
-   inteligência artificial;
-   SEO compartilhado;
-   catálogo de ferramentas;
-   dashboard administrativo;
-   blog;
-   serviços reutilizáveis.

As ferramentas apenas utilizam os contratos disponibilizados pelo Core.

Nunca devem conhecer detalhes internos de implementação.

------------------------------------------------------------------------

## 2. Ferramentas são independentes

Cada ferramenta representa um domínio de negócio próprio.

Ela deve conter apenas aquilo que pertence ao seu domínio.

Uma ferramenta nunca deve depender diretamente de outra.

Novas ferramentas devem poder ser adicionadas, removidas ou evoluídas
sem causar impacto nas demais.

------------------------------------------------------------------------

## 3. Tudo que pode ser reutilizado pertence à infraestrutura compartilhada

Sempre que uma funcionalidade puder ser utilizada por duas ou mais
ferramentas, ela deixa de ser responsabilidade da ferramenta e passa a
fazer parte da infraestrutura compartilhada da plataforma.

Cada ferramenta pode conter apenas sua integração e suas regras específicas de
uso. A biblioteca, o adaptador ou o serviço genérico deve existir uma única vez.
Por exemplo, quatro ferramentas que exportam PDF devem compartilhar o mesmo
serviço de PDF; nenhuma delas deve instalar ou manter uma solução paralela.

Exemplos:

-   geração de PDF;
-   exportação;
-   analytics;
-   histórico;
-   IA;
-   notificações;
-   permissões;
-   favoritos.

Isso evita duplicação de código e mantém a arquitetura consistente.

------------------------------------------------------------------------

## 4. A ferramenta conhece apenas seu domínio

Uma ferramenta não deve conhecer:

-   sistema de login;
-   gateway de pagamento;
-   Google Analytics;
-   geração de PDF;
-   armazenamento do histórico;
-   inteligência artificial;
-   outras ferramentas.

Ela apenas executa sua lógica de negócio.

Quando precisar de um serviço compartilhado, deve utilizar o Core.

------------------------------------------------------------------------

## 5. Monetização pertence à plataforma

Nenhuma ferramenta implementa regras de cobrança, gratuidade promocional
ou limites de uso.

Cada módulo apenas declara suas capacidades e, quando aplicável, quais
recursos pertencem à categoria Prazzu Plus.

Quem decide se determinado visitante ou usuário pode utilizar um recurso
é a plataforma.

Na fase inicial, a política central deverá liberar gratuitamente todas
as capacidades e ignorar limites comerciais, preservando a estrutura que
permitirá ativá-los futuramente.

Isso permite alterar planos, assinaturas, limites ou políticas
comerciais sem modificar nenhuma ferramenta.

------------------------------------------------------------------------

## 6. A experiência gratuita deve ser completa

Toda ferramenta precisa resolver corretamente o problema que se propõe a
resolver.

A versão gratuita nunca deve ser propositalmente limitada para
incentivar uma assinatura.

Os recursos Plus devem agregar produtividade, automação e conveniência,
nunca corrigir limitações artificiais.

------------------------------------------------------------------------

## 7. O Prazzu Plus é único

Existe apenas um plano de assinatura.

Durante a fase inicial, seu acesso será concedido gratuitamente a todos,
inclusive visitantes, por uma política central da plataforma.

Quando a cobrança for ativada, a assinatura Prazzu Plus desbloqueará os
recursos Plus de todas as ferramentas da plataforma.

Novas ferramentas passam automaticamente a integrar o ecossistema Plus.

------------------------------------------------------------------------

## 8. O blog faz parte da plataforma

O blog é uma frente estratégica do Prazzu Tools.

Seu objetivo é produzir conteúdo técnico de qualidade, responder dúvidas
reais, fortalecer o posicionamento orgânico da plataforma e conectar
naturalmente os leitores às ferramentas relacionadas.

O blog não deve ser tratado como um módulo isolado.

Ele faz parte do ecossistema da plataforma.

------------------------------------------------------------------------

## 9. Analytics é compartilhado

Toda ferramenta deve registrar apenas eventos relevantes.

Exemplos:

-   ferramenta aberta;
-   cálculo realizado;
-   recurso Plus utilizado;
-   exportação;
-   histórico salvo.

A plataforma centraliza esses eventos e disponibiliza métricas
administrativas, sem que as ferramentas conheçam a implementação
utilizada.

------------------------------------------------------------------------

## 10. A arquitetura deve favorecer crescimento

Toda decisão técnica deve considerar que o projeto continuará evoluindo.

Novas ferramentas, novos recursos e novos serviços deverão poder ser
adicionados sem necessidade de reestruturar a plataforma.

Sempre que houver dúvida entre uma solução rápida e uma solução
consistente com a arquitetura, deve prevalecer a segunda.

------------------------------------------------------------------------

## Regra geral

Sempre que uma nova funcionalidade for desenvolvida, a primeira pergunta
deve ser:

> Esta responsabilidade pertence à ferramenta ou pertence à plataforma?

> Depois, deverá ser respondida uma segunda pergunta:
>
> Esta responsabilidade pertence ao Prazzu Tools ou a outro produto do
> ecossistema Prazzu?

Se a resposta for "mais de uma ferramenta utilizará isso", então essa
funcionalidade deve fazer parte do Core.

Se a resposta for "isso resolve um problema específico desta
ferramenta", então ela deve permanecer dentro do módulo correspondente.

Essa regra é a principal responsável por manter o Prazzu Tools
organizado, escalável e preparado para evoluir continuamente.

# Padrão Oficial de Desenvolvimento de Ferramentas

**Versão:** 1.0 **Projeto:** Prazzu Tools

------------------------------------------------------------------------

# 1. Objetivo

Este documento define o padrão oficial para o desenvolvimento de todas
as ferramentas do Prazzu Tools.

Seu objetivo é garantir que todas as ferramentas possuam a mesma
arquitetura, os mesmos padrões de qualidade, a mesma experiência para o
usuário e a mesma facilidade de manutenção.

Este documento é a autoridade máxima para o desenvolvimento de qualquer
ferramenta do projeto.

Caso exista qualquer divergência entre implementações, este documento
prevalece.

------------------------------------------------------------------------

# 2. Filosofia do Projeto

O projeto deve crescer continuamente sem se transformar em um sistema
desorganizado.

Toda decisão de arquitetura deve priorizar:

-   reutilização de código;
-   baixo acoplamento;
-   alta coesão;
-   facilidade de manutenção;
-   consistência entre ferramentas;
-   evolução contínua do Core.

Nenhuma ferramenta pode ser desenvolvida pensando apenas nela.

Toda implementação deve considerar o crescimento futuro do projeto.

------------------------------------------------------------------------

# 3. Antes de iniciar qualquer ferramenta

Antes de escrever qualquer linha de código é obrigatório:

1.  Analisar o projeto inteiro.
2.  Analisar todas as ferramentas existentes.
3.  Analisar todos os lotes anteriores da ferramenta.
4.  Analisar toda a estrutura do Core.
5.  Procurar implementações semelhantes.
6.  Verificar possibilidade de reutilização.
7.  Somente depois iniciar o desenvolvimento.

É proibido iniciar uma implementação sem essa análise.

------------------------------------------------------------------------

# 4. Regra de Ouro

Antes de implementar qualquer funcionalidade, obrigatoriamente deverá
ser respondida a seguinte pergunta:

> Outra ferramenta poderá utilizar essa funcionalidade?

Se a resposta for **SIM**, a implementação deverá ser criada no Core.

Se a resposta for **NÃO**, ela poderá permanecer dentro da ferramenta.

Essa é a regra mais importante deste projeto.

------------------------------------------------------------------------

# 5. Estrutura Obrigatória

Toda ferramenta deverá possuir obrigatoriamente:

``` text
Application/
Domain/
Infrastructure/
Presentation/
Resources/
Routes/
Tests/
README.md
Tool.php
```

Nenhuma ferramenta poderá possuir estrutura diferente sem justificativa
arquitetural.

------------------------------------------------------------------------

# 6. Organização das Pastas

A ferramenta deverá existir exclusivamente em:

``` text
app/Tools/NomeDaFerramenta
```

É proibido espalhar arquivos da ferramenta pelo projeto.

------------------------------------------------------------------------

# 7. Responsabilidade das Camadas

## Controller

O Controller apenas:

-   recebe Request;
-   chama Actions;
-   retorna Response.

É proibido:

-   calcular;
-   consultar banco diretamente;
-   gerar PDF;
-   gerar CSV;
-   montar planilhas;
-   conter regras de negócio.

------------------------------------------------------------------------

## Application

Responsável por:

-   orquestrar casos de uso;
-   chamar domínio;
-   coordenar serviços;
-   montar respostas.

Não possui regras de negócio.

------------------------------------------------------------------------

## Domain

Responsável por:

-   todas as regras de negócio;
-   cálculos;
-   validações de domínio;
-   objetos de valor;
-   serviços de domínio.

O domínio nunca poderá depender do Laravel.

------------------------------------------------------------------------

## Infrastructure

Responsável por:

-   banco;
-   APIs;
-   integrações;
-   armazenamento;
-   repositórios.

------------------------------------------------------------------------

## Presentation

Responsável por:

-   controllers;
-   requests;
-   resources;
-   views.

------------------------------------------------------------------------

# 8. Regras de Reutilização

É proibido duplicar código entre ferramentas.

Caso duas ferramentas precisem da mesma funcionalidade, ela deverá ser
movida para o Core.

Nunca deverão existir duas implementações diferentes para resolver
exatamente o mesmo problema.

------------------------------------------------------------------------

# 9. Funcionalidades que obrigatoriamente pertencem ao Core

Sempre deverão ser implementadas no Core:

-   Exportação PDF
-   Exportação CSV
-   Exportação XLSX
-   Impressão
-   Histórico
-   Favoritos
-   Auditoria
-   Métricas
-   Limites de uso
-   Helpers
-   Máscaras
-   Componentes Blade
-   Objetos Money
-   Objetos Percentage
-   Objetos Date
-   Serviços reutilizáveis
-   Traits reutilizáveis
-   Policies reutilizáveis

É proibido implementar essas funcionalidades diretamente dentro de uma
ferramenta.

------------------------------------------------------------------------

# 10. Componentes Visuais

As ferramentas deverão utilizar componentes visuais reutilizáveis.

É proibido copiar HTML entre ferramentas.

Sempre que possível deverão ser utilizados componentes Blade.

Exemplos:

-   FormPanel
-   ResultPanel
-   ExportButton
-   HistoryPanel
-   AlertPanel
-   ValidationSummary
-   FormActions
-   Header
-   Layout

------------------------------------------------------------------------

# 11. Bootstrap

Bootstrap sempre terá prioridade.

Somente será permitido criar CSS próprio quando Bootstrap e os
componentes existentes não forem suficientes.

Nunca criar CSS apenas por preferência pessoal.

------------------------------------------------------------------------

------------------------------------------------------------------------

# 11.1. Padrão Visual Obrigatório (UI/UX)

O Prazzu Tools é uma plataforma única composta por diversas ferramentas
independentes.

Embora cada ferramenta possua funcionalidades específicas, **todas devem
seguir o mesmo padrão visual e de organização da interface**, garantindo
uma experiência consistente para o usuário.

O usuário nunca deve precisar reaprender a utilizar uma ferramenta
apenas porque ela possui um layout diferente.

## Consistência Visual

Toda ferramenta deverá manter o mesmo padrão de organização dos
elementos da interface.

Exemplos:

-   posição do título;
-   descrição da ferramenta;
-   localização dos botões principais;
-   posição dos recursos complementares;
-   formulários;
-   área de resultados;
-   tabelas;
-   cards;
-   alertas;
-   paginação;
-   filtros.

Não é permitido alterar a posição desses elementos apenas por
preferência do desenvolvedor.

Caso exista um padrão adotado pela plataforma, ele deverá ser seguido.

## Navegação Secundária

Quando uma ferramenta possuir funcionalidades complementares, como:

-   Histórico;
-   Reajustes;
-   Propostas;
-   Contratos;
-   Exportações;
-   Favoritos;

essas funcionalidades deverão aparecer sempre na mesma região da
interface, respeitando o padrão visual adotado pela plataforma.

Se determinada funcionalidade não fizer sentido para aquela ferramenta,
ela simplesmente não deverá existir.

É proibido alterar sua posição apenas por preferência estética.

## Organização dos Formulários

Os formulários devem seguir sempre a mesma lógica visual:

1.  Informações principais;
2.  Configurações intermediárias;
3.  Opções avançadas;
4.  Botões de ação.

Essa organização deve permanecer consistente entre todas as ferramentas.

## Organização dos Resultados

Sempre que possível, os resultados deverão seguir o mesmo padrão visual,
utilizando:

-   cards Bootstrap;
-   resumo principal;
-   detalhamento do cálculo;
-   observações;
-   recomendações;
-   informações complementares.

## Componentes Reutilizáveis

Sempre que um novo padrão visual for criado e puder ser utilizado por
outras ferramentas, ele deverá ser transformado em componente
reutilizável.

É proibido criar versões diferentes da mesma interface em ferramentas
distintas.

## Evolução do Layout

Antes de iniciar qualquer novo lote ou nova ferramenta é obrigatório:

1.  analisar todas as ferramentas existentes;
2.  identificar o padrão visual atualmente adotado;
3.  verificar se existe um componente semelhante;
4.  manter a consistência da plataforma.

Caso um padrão visual seja melhorado durante o desenvolvimento de uma
ferramenta, essa melhoria deverá servir como referência para as próximas
implementações e, quando aprovado, ser aplicada gradualmente às demais
ferramentas.

## Objetivo

A plataforma deve transmitir a sensação de um único produto.

Independentemente da ferramenta utilizada, o usuário deve reconhecer
imediatamente que continua dentro do ecossistema Prazzu Tools.

A consistência da experiência do usuário é tão importante quanto a
consistência da arquitetura do sistema.

# 11.1 Largura global da plataforma

O shell principal da plataforma deverá utilizar toda a largura
disponível até o limite máximo de **1920 px**.

O comportamento obrigatório é:

-   abaixo de 1920 px: largura de 100%;
-   em 1920 px: largura total disponível;
-   acima de 1920 px: conteúdo centralizado com largura máxima de 1920
    px;
-   televisores e monitores ultrawide não poderão esticar
    indefinidamente o conteúdo.

A implementação deverá priorizar utilitários, grid e containers do
Bootstrap. CSS próprio somente poderá ser utilizado quando o Bootstrap e
o estilo compartilhado existente não forem suficientes.

O limite de 1920 px pertence ao shell global. Áreas de leitura,
formulários ou conteúdos específicos podem possuir limites internos
menores quando isso melhorar legibilidade e usabilidade, desde que
mantenham o padrão visual compartilhado.

------------------------------------------------------------------------

# 12. Classes CSS

É proibido utilizar classes CSS sem conhecer sua finalidade.

Exemplo:

``` text
prazzu-tool-card
```

Essa classe pertence exclusivamente aos cards do catálogo.

Ela nunca poderá ser utilizada em:

-   formulários;
-   resultados;
-   histórico;
-   relatórios;
-   tabelas;
-   páginas internas.

------------------------------------------------------------------------

# 13. Controllers

Controllers devem ser extremamente pequenos.

Idealmente:

``` text
Request

↓

Action

↓

Response
```

Nunca:

``` text
Request

↓

Controller

↓

Cálculo

↓

Banco

↓

Exportação

↓

View
```

------------------------------------------------------------------------

# 14. Exportações

Toda exportação deverá utilizar o sistema central de Export.

É proibido criar exportadores próprios dentro de uma ferramenta.

Toda ferramenta apenas fornecerá:

-   conteúdo;
-   dados;
-   nome do arquivo;
-   título.

O restante deverá ser responsabilidade do Core.

------------------------------------------------------------------------

# 15. Histórico

Toda ferramenta que possuir histórico deverá utilizar o sistema central.

Não deverá existir histórico duplicado.

Caso exista necessidade específica, ela deverá ser documentada antes da
implementação.

------------------------------------------------------------------------

# 16. Banco de Dados

Controllers nunca consultarão banco diretamente.

Toda persistência deverá ocorrer através da camada apropriada.

------------------------------------------------------------------------

# 17. Objetos Financeiros

É proibido utilizar float para dinheiro.

Sempre utilizar:

-   Money
-   Percentage
-   Objetos específicos do Core

------------------------------------------------------------------------

# 18. Rotas

Cada ferramenta possuirá suas próprias rotas.

Nunca utilizar closures.

Todas deverão utilizar Controllers.

------------------------------------------------------------------------

# 19. Views

Cada ferramenta possuirá namespace próprio.

É proibido utilizar views de outra ferramenta.

------------------------------------------------------------------------

# 20. README

Toda ferramenta deverá possuir README atualizado contendo:

-   descrição;
-   funcionalidades;
-   regras;
-   dependências;
-   histórico de versões.

------------------------------------------------------------------------

# 21. Testes

Toda ferramenta deverá possuir testes.

Obrigatoriamente:

-   Unit
-   Feature

Quando possuir interface complexa:

-   Browser Test

------------------------------------------------------------------------

# 22. Testes Obrigatórios

Antes de finalizar qualquer lote deverão ser executados:

``` bash
php artisan optimize:clear

php artisan route:list

php artisan test

vendor/bin/pint --test

php artisan view:cache

php artisan view:clear
```

------------------------------------------------------------------------

# 23. Testes Funcionais Obrigatórios

Toda ferramenta deverá ser validada manualmente.

Checklist mínimo:

-   consegue digitar em todos os inputs;
-   selects funcionam;
-   máscaras funcionam;
-   botão limpar funciona;
-   botão calcular funciona;
-   mensagens aparecem;
-   erros aparecem;
-   resultado correto;
-   exportação correta;
-   histórico correto;
-   impressão correta;
-   mobile correto;
-   desktop correto;
-   sem erro JavaScript;
-   sem erro Laravel.

------------------------------------------------------------------------

# 24. Autenticação, persistência e identidade Prazzu

Autenticação nunca será requisito para utilizar uma ferramenta,
visualizar um resultado completo ou acessar recursos Prazzu Plus
enquanto a política gratuita de lançamento estiver ativa.

O comportamento deverá seguir o padrão:

Visitante:

-   utilizar todas as ferramentas;
-   utilizar recursos Essenciais e Prazzu Plus;
-   calcular sem limite;
-   visualizar o resultado completo;
-   comparar e projetar quando a ferramenta oferecer essas capacidades;
-   imprimir;
-   exportar o cálculo atual;
-   manter apenas estado temporário, sem garantia de recuperação após
    atualização, fechamento do navegador ou encerramento da sessão.

Usuário autenticado:

-   possuir todas as capacidades disponíveis ao visitante;
-   salvar resultados;
-   acessar histórico;
-   repetir cálculos salvos;
-   excluir registros próprios;
-   utilizar favoritos;
-   acessar PDFs e exportações históricas;
-   recuperar dados posteriormente;
-   sincronizar dados e preferências quando a plataforma oferecer essa
    capacidade.

É proibido utilizar login como barreira para liberar cálculo, resultado,
comparação, projeção, exportação atual ou qualquer capacidade máxima da
ferramenta durante a fase gratuita.

Chamadas para cadastro devem comunicar corretamente seu benefício. O
padrão é convidar a pessoa a criar uma conta gratuita para salvar e
recuperar resultados, nunca para desbloquear o cálculo.

## 24.0 Plano Empresarial

O plano empresarial existe exclusivamente para permitir que uma empresa
forneça acesso ao Prazzu Plus para seus colaboradores.

Cada colaborador deverá possuir:

-   login próprio;
-   histórico próprio;
-   resultados próprios;
-   favoritos próprios;
-   preferências próprias.

O plano empresarial compartilha apenas o direito de utilização da
assinatura contratada. O Prazzu Tools não implementa compartilhamento
automático de históricos, cálculos ou dados entre colaboradores.

## 24.1 Conta local e futura identidade única Prazzu

Atualmente, o cadastro e a autenticação pertencem ao Prazzu Tools.

No futuro, uma plataforma central de identidade permitirá que a pessoa
utilize uma única conta em todos os projetos Prazzu, incluindo Prazzu
Tools, Prazzu Cores e produtos futuros.

O cadastro local deverá ser preparado para essa evolução sem depender de
um serviço central que ainda não existe.

A tabela de usuários deverá possuir um identificador externo dedicado à
futura conta Prazzu, com as seguintes características:

-   nullable durante a fase local;
-   unique quando preenchido;
-   indexado;
-   independente do identificador local;
-   independente do e-mail;
-   capaz de receber UUID, ULID ou código opaco emitido pela identidade
    central;
-   imutável após a vinculação, salvo processo administrativo formal.

O e-mail nunca deverá ser utilizado como chave definitiva de integração
entre os projetos, pois pode ser alterado pelo usuário.

A futura integração deverá ocorrer através de contratos do Core e não
poderá acoplar ferramentas individuais ao serviço de identidade.

------------------------------------------------------------------------

# 25. Capabilities

Toda ferramenta deverá declarar claramente, em seu `ToolManifest`, os
recursos que realmente oferece. Cada `ToolFeature` possui uma chave estável,
um nome e exatamente um dos seguintes tiers:

-   `Essential`: solução gratuita e completa do problema principal;
-   `Plus`: produtividade, volume, automação, continuidade, análise ou
    conveniência adicional.

Uma ferramenta publicada deve possuir ao menos um recurso Essencial e um
recurso Plus. O Core valida essa declaração e decide o acesso por recurso. É
proibido transformar a ferramenta inteira em Prazzu Plus ou implementar gates
comerciais particulares dentro do módulo.

Capacidades técnicas, como rotas, views, migrations e política de histórico,
continuam sendo declaradas pelos contratos do Core. Autenticação e tier são
requisitos independentes: um histórico pode ser Plus e também exigir login por
depender da identidade do titular.

------------------------------------------------------------------------

# 26. Performance

Nenhuma ferramenta poderá executar consultas desnecessárias.

Toda consulta deverá possuir justificativa.

Evitar N+1.

------------------------------------------------------------------------

# 27. Segurança

Toda ferramenta deverá:

-   validar entradas;
-   proteger dados sensíveis;
-   utilizar CSRF;
-   respeitar autenticação;
-   respeitar autorização.

------------------------------------------------------------------------

# 28. Evolução do Core

Sempre que surgir uma nova funcionalidade reutilizável ela deverá ser
adicionada ao Core.

Nunca deverá ser criada diretamente dentro da ferramenta.

Exemplo:

Hoje existe:

``` text
BrowserPrintExporter
```

Amanhã surge XLSX.

Não criar:

``` text
FerramentaXlsxExporter
```

Criar:

``` text
Core/Export/XlsxExporter
```

Todas as ferramentas utilizarão esse serviço.

------------------------------------------------------------------------

# 29. Alterações Globais

Nenhuma ferramenta poderá modificar comportamento global do sistema sem
justificativa.

Qualquer alteração global deverá beneficiar todas as ferramentas.

------------------------------------------------------------------------

# 30. Fluxo Oficial de Desenvolvimento

Toda ferramenta seguirá obrigatoriamente este fluxo:

``` text
Nova Ferramenta

↓

Analisar Projeto

↓

Analisar Core

↓

Analisar Ferramentas Existentes

↓

Existe implementação semelhante?

↓

SIM

↓

Reutilizar

↓

NÃO

↓

Outra ferramenta poderá utilizar?

↓

SIM

↓

Criar no Core

↓

NÃO

↓

Criar dentro da ferramenta

↓

Criar testes

↓

Executar checklist

↓

Executar `composer release:check` e gerar o pacote com `scripts/package-distribution.ps1`

↓

Concluir lote
```

------------------------------------------------------------------------

# 31. Erros Proibidos

Nunca poderá acontecer:

-   código duplicado;
-   exportadores diferentes;
-   históricos diferentes;
-   CSS duplicado;
-   helpers duplicados;
-   componentes Blade duplicados;
-   cálculos em Controllers;
-   consultas ao banco em Controllers;
-   regras de negócio fora do Domain;
-   utilização de classes CSS sem conhecer sua finalidade;
-   criação de funcionalidades reutilizáveis dentro da ferramenta.

------------------------------------------------------------------------

# 32. Definição de Ferramenta Pronta

Uma ferramenta somente poderá ser considerada concluída quando atender
todos os itens abaixo:

-   arquitetura correta;
-   padrão deste documento respeitado;
-   testes passando;
-   Pint aprovado;
-   views compiladas;
-   rotas compiladas;
-   sem erros JavaScript;
-   sem erros Laravel;
-   responsiva;
-   acessível;
-   exportação funcionando;
-   histórico funcionando;
-   componentes reutilizados corretamente;
-   nenhuma duplicação de código.

Somente após cumprir todos esses requisitos a ferramenta poderá mudar de
**Draft** para **Active**.

------------------------------------------------------------------------

# 33. Compromisso Arquitetural

A partir deste documento, todas as ferramentas do Prazzu Tools deverão
seguir rigorosamente estas regras.

Nenhuma exceção poderá ser criada sem justificativa arquitetural formal.

O objetivo deste padrão é garantir que o projeto continue crescendo de
forma organizada, escalável e sustentável, evitando código duplicado,
implementações conflitantes e manutenção excessiva.

Este documento deverá ser revisado sempre que a arquitetura do projeto
evoluir, tornando-se a referência oficial para todo o desenvolvimento
futuro do Prazzu Tools.

------------------------------------------------------------------------

# Contas empresariais e licenças Plus

Uma conta empresarial no Prazzu Tools não cria um ambiente compartilhado e não altera a natureza das contas pessoais.

Cada pessoa continua possuindo:

- login próprio;
- histórico próprio;
- favoritos próprios;
- resultados e preferências próprios;
- responsabilidade individual sobre seus dados.

A empresa atua apenas como contratante e pagadora de uma quantidade de acessos Prazzu Plus. Esses acessos podem ser atribuídos a membros vinculados à organização, sem compartilhar automaticamente qualquer dado entre eles.

O vínculo empresarial nunca transfere a propriedade da conta pessoal para a empresa. Quando uma pessoa deixa a organização, ela perde somente o benefício Plus concedido por aquele vínculo. Sua conta, seus dados e qualquer assinatura individual permanecem independentes.

O Core da plataforma é responsável por decidir a origem do acesso Plus:

- assinatura individual ativa; ou
- licença empresarial ativa.

As ferramentas não conhecem organizações, membros, convites, contratos, cobrança ou quantidade de vagas. Elas consultam apenas os contratos de acesso disponibilizados pelo Core.

O Prazzu Tools não implementará colaboração, compartilhamento de cálculos, departamentos, workflow, gestão de clientes ou outras funções de ERP. Qualquer gestão empresarial avançada pertence ao Prazzu Core.

## 24.2 Origem do acesso Prazzu Plus

O acesso Prazzu Plus de uma pessoa pode possuir duas origens independentes:

- assinatura individual ativa;
- vaga empresarial ativa, vinculada a uma assinatura empresarial ativa.

O Core deverá resolver a origem do acesso. Ferramentas não podem consultar empresas,
membros, assinaturas empresariais ou vagas diretamente. Para uma ferramenta existe
apenas o plano efetivo retornado pelo Core.

A assinatura empresarial define uma quantidade contratada de vagas. Cada vaga ativa
pode ser atribuída somente a um membro ativo da mesma empresa. Uma pessoa não perde
sua conta, histórico, favoritos, resultados ou preferências quando uma vaga é liberada;
ela perde somente o benefício Plus fornecido por aquela empresa.

É proibido conceder acesso empresarial quando a assinatura estiver pendente,
inadimplente, suspensa, cancelada, ainda não iniciada ou encerrada.

## 24.3 Fluxo empresarial validado

O fluxo empresarial do Prazzu Tools é composto exclusivamente por administração de
licenças:

1. uma pessoa autenticada cria a empresa e se torna responsável;
2. a empresa recebe uma assinatura com uma quantidade contratada de vagas;
3. o responsável ou um administrador gera um link individual de convite, com uso único e validade limitada;
4. a pessoa que recebeu o link entra ou cria sua própria Conta Prazzu e confirma o vínculo;
5. uma vaga disponível pode ser atribuída a um membro ativo;
6. o Core passa a resolver o plano efetivo como individual ou empresarial;
7. ao liberar a vaga ou desativar o vínculo, apenas o benefício empresarial é removido.

A renovação ou substituição de uma assinatura empresarial deve liberar qualquer vaga
antiga do membro antes de criar a vaga na assinatura vigente. Uma vaga pertencente a
assinatura suspensa, cancelada, encerrada ou substituída não pode impedir a atribuição
correta em uma assinatura ativa.

## 24.4 Limites de autorização e privacidade

O painel empresarial pode exibir somente informações necessárias para administrar:

- perfil da empresa;
- responsável;
- membros e respectivos papéis empresariais;
- convites;
- quantidade de vagas contratadas, ocupadas e disponíveis;
- situação da licença empresarial de cada membro.

É proibido disponibilizar no painel empresarial histórico, favoritos, cálculos,
resultados, preferências pessoais, senha, assinatura individual ou qualquer outro dado
privado da conta do membro.

Administradores empresariais não são administradores internos da plataforma. Papéis de
empresa e papéis globais da aplicação permanecem separados.

## 24.5 Regras de integridade

A implementação deve manter as seguintes invariantes:

- apenas membros ativos da mesma empresa podem receber vagas;
- a quantidade de vagas ativas nunca pode ultrapassar o limite contratado;
- uma vaga liberada não concede acesso Plus;
- uma assinatura sem vigência ativa não concede acesso Plus;
- desativar um membro libera suas vagas, sem excluir sua conta;
- aceitar convite não altera a propriedade da empresa;
- cada link de convite pode ser aceito uma única vez e deve expirar ou ser revogado conforme seu estado;
- a assinatura individual do usuário nunca é alterada pela licença empresarial;
- ferramentas não importam modelos, ações ou serviços do domínio empresarial;
- toda decisão sobre a origem do Plus permanece no Core.

## 24.6 Validação obrigatória

Alterações no domínio empresarial devem ser acompanhadas por testes que cubram, no
mínimo:

- migrations e colunas essenciais;
- criação da empresa e do responsável;
- geração de links individuais, expiração, revogação, restauração, uso único e aceite autenticado;
- autorização de responsáveis, administradores, membros e pessoas externas;
- atribuição, limite, liberação e renovação de vagas;
- assinatura ativa, suspensa, cancelada e fora da vigência;
- coexistência entre plano individual e licença empresarial;
- preservação da conta e dos dados pessoais ao sair da empresa;
- independência arquitetural das ferramentas em relação ao domínio empresarial.

A revisão arquitetural deve falhar caso qualquer arquivo dentro de `app/Tools` passe a
depender diretamente de organizações, membros, assinaturas empresariais, vagas ou ações
do domínio empresarial.
