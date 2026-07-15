# Prazzu Tools

## O que é o Prazzu Tools

O **Prazzu Tools** é uma plataforma modular criada para se tornar a
principal referência em ferramentas digitais para profissionais da
contabilidade.

Mais do que disponibilizar calculadoras, o projeto tem como objetivo
reunir em um único ambiente tudo aquilo que faz parte da rotina de
escritórios contábeis, departamentos financeiros, consultores e
profissionais da área fiscal.

A plataforma é construída para evoluir continuamente, recebendo novas
ferramentas, novos recursos e novos serviços sem comprometer a
estabilidade das funcionalidades já existentes.

Cada decisão arquitetural foi pensada para permitir crescimento a longo
prazo.

------------------------------------------------------------------------

# Nossa visão

Acreditamos que o profissional da contabilidade deve encontrar, em um
único lugar, ferramentas confiáveis, conteúdo técnico atualizado e
recursos que realmente aumentem sua produtividade.

O objetivo do Prazzu Tools não é apenas reduzir o tempo gasto em
cálculos.

O objetivo é construir um ecossistema completo capaz de acompanhar toda
a rotina do profissional contábil.

Conforme a plataforma evolui, novas ferramentas passam a fazer parte
desse ecossistema, mantendo sempre a mesma experiência de uso, a mesma
qualidade e os mesmos princípios.

------------------------------------------------------------------------

# Filosofia da plataforma

Toda ferramenta publicada no Prazzu Tools segue exatamente a mesma
filosofia.

Cada uma possui duas experiências:

-   **Boa (Gratuita)**
-   **Excelente (Prazzu Plus)**

A versão gratuita deve resolver completamente o problema que se propõe a
resolver.

Ela nunca deve apresentar resultados incompletos, esconder cálculos
importantes ou criar limitações artificiais apenas para incentivar uma
assinatura.

O profissional precisa conseguir realizar seu trabalho normalmente
utilizando apenas a versão gratuita.

O Prazzu Plus representa a evolução da mesma ferramenta.

Os recursos Plus existem para entregar mais produtividade através de
funcionalidades como:

-   histórico;
-   comparações;
-   projeções;
-   automações;
-   inteligência;
-   relatórios;
-   exportações;
-   acompanhamento contínuo;
-   recursos avançados.

Em outras palavras:

> O usuário assina o Prazzu Plus para trabalhar melhor, nunca para
> conseguir calcular corretamente.

Essa filosofia orienta todas as ferramentas da plataforma.

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

# Arquitetura

O projeto segue um princípio simples.

> Tudo que pertence ao domínio de uma ferramenta permanece dentro da
> própria ferramenta.

> Tudo que pode ser reutilizado por duas ou mais ferramentas pertence ao
> Core da plataforma.

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

# Core compartilhado

O Core representa a infraestrutura comum da plataforma.

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

# Modelo de assinatura

Existe apenas um plano da plataforma.

Ao assinar o **Prazzu Plus**, o usuário desbloqueia os recursos Plus de
todas as ferramentas.

A assinatura nunca é realizada individualmente por ferramenta.

Isso significa que cada novo módulo lançado passa automaticamente a
integrar o ecossistema Plus.

------------------------------------------------------------------------

# Experiência Plus diária

O Prazzu Tools acredita que o profissional deve conhecer os recursos
avançados antes de decidir assinar.

Por isso, usuários gratuitos podem experimentar diariamente a
experiência Plus.

Essa política existe para demonstrar, através do uso real, quanto os
recursos avançados aumentam a produtividade.

O objetivo nunca é limitar a versão gratuita.

O objetivo é mostrar naturalmente o valor da experiência Plus.

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
-   foco total na rotina do profissional contábil.

Esses princípios orientam tanto o desenvolvimento técnico quanto a
evolução do produto.

------------------------------------------------------------------------

# Objetivo final

O objetivo do Prazzu Tools é tornar-se a principal plataforma de
ferramentas para contabilidade no Brasil.

Mais do que disponibilizar calculadoras, a proposta é construir um
ecossistema completo onde profissionais encontrem ferramentas
confiáveis, conteúdo técnico de qualidade e recursos capazes de aumentar
sua produtividade diariamente.

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

## 3. Tudo que pode ser reutilizado pertence ao Core

Sempre que uma funcionalidade puder ser utilizada por duas ou mais
ferramentas, ela deixa de ser responsabilidade da ferramenta e passa a
fazer parte da plataforma.

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

Nenhuma ferramenta implementa regras de cobrança.

Cada módulo apenas informa quais recursos são:

-   gratuitos;
-   Prazzu Plus.

Quem decide se determinado usuário pode utilizar um recurso é a
plataforma.

Isso permite alterar planos, assinaturas ou políticas comerciais sem
modificar nenhuma ferramenta.

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

Ao assinar o Prazzu Plus, o usuário desbloqueia os recursos Plus de
todas as ferramentas da plataforma.

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
-   Compartilhamento
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
-   CRM;
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

# 24. Autenticação

O comportamento deverá seguir o padrão:

Visitante:

-   calcular;
-   visualizar resultado;
-   imprimir;
-   exportar cálculo atual.

Usuário autenticado:

-   histórico;
-   repetir;
-   excluir;
-   favoritos;
-   PDFs históricos.

------------------------------------------------------------------------

# 25. Capabilities

Toda ferramenta deverá declarar claramente suas capacidades.

Exemplos:

-   Calculation
-   History
-   BrowserPrint
-   PDF
-   CSV
-   XLSX
-   Metrics
-   Audit
-   Favorites
-   Authentication
-   Premium

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
