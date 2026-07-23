~~# Prazzu Tools

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



---

# 💡 Evolução Contínua da Plataforma (Instrução para Assistentes de IA)

Esta seção é destinada aos assistentes de IA que auxiliam no desenvolvimento deste projeto.

Enquanto executa qualquer tarefa (criação de ferramentas, correção de bugs, refatorações ou novas funcionalidades), observe continuamente se existe alguma oportunidade de evolução da plataforma.

Sempre que identificar uma melhoria que:

- reduza retrabalho futuro;
- elimine duplicação entre ferramentas;
- padronize comportamentos;
- simplifique a criação de novas ferramentas;
- torne o Core mais reutilizável;
- mantenha a arquitetura limpa, simples e leve;
- preserve a filosofia descrita neste README;

informe essa oportunidade ao final da resposta.

## Importante

Essas sugestões **não devem interromper a tarefa principal**.

Primeiro execute exatamente o que foi solicitado.

Depois, se identificar alguma oportunidade real de melhoria, apresente-a em uma seção separada chamada:

> **💡 Oportunidade de Evolução**

A sugestão deve conter:

- qual problema foi observado;
- por que isso pode gerar retrabalho no futuro;
- qual padronização ou melhoria pode resolver o problema;
- se a mudança vale a pena agora ou apenas quando mais ferramentas existirem.

## Evite sugestões desnecessárias

Não proponha abstrações apenas por serem "boas práticas".

Só sugira melhorias quando houver um benefício concreto para a evolução da plataforma.

A filosofia deste projeto é evoluir a arquitetura baseada em necessidades reais, e não em abstrações prematuras.


---

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

---

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

---

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

---

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

---

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

---

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

---

# Componentes visuais compartilhados

A interface das ferramentas deve utilizar os componentes Blade compartilhados antes de criar marcação própria. O objetivo é permitir que melhorias de acessibilidade, Bootstrap e experiência sejam aplicadas uma única vez em toda a plataforma.

Os componentes ficam em `resources/views/components/tools` e devem seguir estas regras:

- Bootstrap é a primeira opção para layout, formulários, alertas, cards, badges e responsividade;
- classes próprias já existentes podem complementar Bootstrap quando a identidade visual exigir;
- CSS novo só deve ser criado quando Bootstrap e os estilos existentes forem insuficientes;
- componentes compartilhados cuidam apenas de apresentação e não recebem regras de domínio;
- cada ferramenta continua responsável por seus textos, campos, cálculos e decisões específicas;
- novos componentes só são extraídos quando houver repetição real ou benefício transversal comprovado;
- a ferramenta deve permanecer utilizável e acessível sem depender de integração com outra ferramenta.

Componentes-base disponíveis:

```text
<x-tools.page>
<x-tools.form-panel>
<x-tools.integration-import>
<x-tools.form.input>
<x-tools.form.money>
<x-tools.form.select>
<x-tools.form.switch>
<x-tools.result-metric>
<x-tools.result-panel>
<x-tools.validation-summary>
```

A Calculadora de Simples Nacional é a ferramenta-piloto deste padrão. As demais ferramentas só devem ser migradas depois que o padrão estiver validado em uso real, evitando alterações em massa antes da estabilização.

---

# Contrato padrão de cálculo

Cálculos que precisem participar de recursos transversais da plataforma devem expor uma camada de aplicação baseada no contrato compartilhado em `app/Core/Tools/Calculation`. Esse contrato não substitui entidades, regras ou resultados de domínio específicos de cada ferramenta.

O padrão é composto por:

```text
ToolCalculationInput
ToolCalculator
ToolCalculationResult
ToolCalculationSummaryItem
ToolCalculationWarning
ToolCalculationAction
```

Regras obrigatórias:

- a entrada padronizada deve ser um DTO imutável e já normalizado;
- validação HTTP continua pertencendo ao `FormRequest`;
- regras e cálculos continuam pertencendo ao domínio da ferramenta;
- o resultado compartilhado organiza resumo, detalhes, alertas e próximas ações sem apagar o resultado de domínio;
- `schemaVersion` deve seguir versionamento semântico e evoluir quando a estrutura persistida mudar;
- contratos de integração podem ser anexados ao resultado, mas permanecem opcionais;
- uma ferramenta deve continuar funcionando sem histórico, exportação, compartilhamento ou integração;
- não se deve criar adaptadores vazios apenas para cumprir o padrão; o contrato é aplicado quando houver uso transversal real.

A Calculadora de Simples Nacional é a ferramenta-piloto. Seu fluxo principal utiliza `SimplesNacionalCalculationInput` e `StandardSimplesNacionalCalculator`, preservando a calculadora e o resultado tributário de domínio existentes. As demais ferramentas serão migradas somente após validação desse formato.

---

# Integração entre ferramentas

As ferramentas da plataforma podem reaproveitar dados e resultados produzidos
por outras ferramentas quando isso gerar valor real para o usuário, reduzir
preenchimentos repetidos ou permitir análises mais completas.

Essa interoperabilidade não altera o princípio de independência dos módulos.
Uma ferramenta pode consumir dados publicados por outra, mas nunca pode depender
diretamente de classes, tabelas ou detalhes internos de sua implementação.

## Princípios de integração

1. Toda ferramenta deve continuar funcionando de forma isolada.

2. Uma ferramenta não pode importar ou utilizar diretamente Actions, Services,
   Models, Repositories, DTOs, controllers ou outras classes internas de outra
   ferramenta.

3. A comunicação entre ferramentas deve ocorrer exclusivamente por contratos de
   dados estáveis, versionados e disponibilizados pelo Core técnico.

4. A ferramenta de origem publica somente dados necessários, semanticamente
   claros e permitidos para o contexto atual.

5. A ferramenta de destino declara os contratos que aceita e continua responsável
   por validar os dados antes de utilizá-los.

6. O reaproveitamento deve ser opcional, explícito e visível. A plataforma não
   deve preencher, recalcular ou alterar resultados silenciosamente.

7. O usuário deve poder revisar e confirmar os dados importados antes de executar
   o cálculo ou a ação da ferramenta de destino.

8. Dados compartilhados devem respeitar autenticação, organização, autorização,
   privacidade, consentimento e escopo do usuário.

9. Uma integração ausente, indisponível ou incompatível nunca pode impedir o
   funcionamento principal da ferramenta.

10. Integrações devem existir por valor de produto, e não apenas por possibilidade
    técnica.

## Contratos de integração

Cada conjunto de dados compartilhável deve possuir:

-   nome único;
-   versão explícita;
-   descrição objetiva;
-   ferramenta ou capacidade de origem;
-   campos obrigatórios e opcionais;
-   unidade, formato e significado dos valores;
-   regras de validação;
-   política de compatibilidade e evolução;
-   regras de autorização e privacidade.

Exemplos de contratos:

```text
company-profile:v1
company-tax-snapshot:v1
revenue-projection:v1
pricing-scenario:v1
labor-calculation-snapshot:v1
```

Os nomes acima são exemplos de modelagem. Um contrato somente passa a ser oficial
quando estiver definido e registrado no Core técnico.

## Manifesto da ferramenta

Toda nova ferramenta deve declarar em seu manifesto quais contratos publica e
quais contratos aceita.

```php
'integrations' => [
    'publishes' => [
        'company-tax-snapshot:v1',
    ],

    'accepts' => [
        'company-profile:v1',
        'revenue-projection:v1',
    ],
],
```

Uma lista vazia é válida quando não existir integração relevante. Nenhuma
ferramenta deve criar contratos artificiais apenas para aparentar integração com
a plataforma.

## Fluxo esperado

```text
Ferramenta de origem
        ↓
publica um contrato de dados
        ↓
Core valida autorização, escopo e compatibilidade
        ↓
usuário escolhe reaproveitar os dados
        ↓
ferramenta de destino importa os campos compatíveis
        ↓
usuário revisa e confirma
```

## Limites arquiteturais

Não é permitido:

-   criar dependência direta entre namespaces de ferramentas;
-   consultar tabelas privadas de outra ferramenta;
-   reutilizar Models pertencentes a outra ferramenta;
-   acessar Actions, Services, Repositories ou DTOs internos de outro módulo;
-   conhecer detalhes internos da implementação de outra ferramenta;
-   exigir que outra ferramenta esteja habilitada para executar o fluxo
    essencial;
-   compartilhar dados sem autorização e escopo adequados;
-   tornar uma integração obrigatória para resolver o problema básico;
-   duplicar no módulo um contrato ou serviço já mantido pelo Core.

Quando um conceito for verdadeiramente comum a várias ferramentas, ele deve ser
promovido para o Core técnico em vez de ser copiado ou importado de um módulo
específico.

## Checklist para novas ferramentas

Antes de concluir uma nova ferramenta, deve-se responder:

-   A ferramenta publica algum dado realmente útil para outras ferramentas?
-   Ela pode aceitar dados já produzidos pela plataforma?
-   Os contratos utilizados estão versionados e registrados no Core?
-   A integração reduz trabalho real para o usuário?
-   O reaproveitamento é opcional e visível?
-   O usuário consegue revisar os dados importados?
-   A ferramenta funciona normalmente sem qualquer integração?
-   Existem testes para contrato válido, ausente, incompatível e não autorizado?
-   A integração respeita a separação entre Essencial e Prazzu Plus?

## Governança automatizada das integrações

O comando oficial `make:tool` deve receber os contratos por meio das opções
`--publishes` e `--accepts`, sempre no formato versionado `nome-do-contrato:v1`.
O gerador deve criar o manifesto, a documentação, o checklist e o teste de
contratos mesmo quando as listas estiverem vazias.

Os testes arquiteturais devem bloquear:

- contratos declarados que não estejam registrados no Core técnico;
- imports de classes internas pertencentes a outra ferramenta;
- remoção dos stubs e seções obrigatórias de integração;
- geração de contratos com nome inválido, sem versão ou duplicado.

A existência de automação não substitui a análise de produto. Uma nova integração
só deve ser declarada quando reduzir trabalho real, permanecer opcional e manter
a ferramenta totalmente funcional de forma isolada.
-   A integração preserva os limites do Prazzu Tools e não cria funções de ERP,
    CRM ou gestão operacional?

---

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

---

# Identidade do produto: plataforma de ferramentas, não SaaS de gestão

Recursos de conta, histórico, favoritos, exportação ou eventual acesso Plus são
serviços de apoio às ferramentas. Eles não alteram a natureza do produto e não
devem evoluir para funcionalidades de ERP, CRM, workflow, gestão de escritório
ou colaboração operacional.

Uma proposta nova só pertence ao Prazzu Tools quando melhora a descoberta, o
uso, a execução ou a continuidade de uma ferramenta. Quando a proposta passa a
gerenciar pessoas, clientes, empresas, tarefas, processos ou operações, ela deve
ser direcionada ao Prazzu Core.

---

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

---

# O blog como parte da plataforma

O blog não é apenas uma área de notícias.

Ele faz parte da estratégia da plataforma.

Seu objetivo é produzir conteúdo técnico de alta qualidade para
responder dúvidas reais de profissionais da contabilidade, melhorar o
posicionamento orgânico da plataforma e conectar naturalmente o leitor
às ferramentas relacionadas.

Cada artigo deve gerar valor por si só, ao mesmo tempo em que apresenta
as soluções disponíveis dentro do ecossistema Prazzu Tools.

---

# Evolução contínua

O Prazzu Tools foi projetado para crescer continuamente.

Hoje a plataforma pode possuir poucas ferramentas.

No futuro poderá possuir dezenas ou centenas de módulos diferentes.

Novas funcionalidades compartilhadas poderão ser adicionadas ao Core sem
exigir alterações individuais em cada ferramenta.

Da mesma forma, novas ferramentas poderão ser desenvolvidas sem
comprometer a estabilidade da plataforma.

---

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

---

# Objetivo final

O objetivo do Prazzu Tools é tornar-se a principal plataforma de
ferramentas para contabilidade no Brasil.

A proposta é reunir calculadoras, validadores, geradores, conversores e
outras soluções pontuais confiáveis, acompanhadas de conteúdo técnico de
qualidade. Gestão empresarial, acompanhamento operacional e relacionamento
com clientes permanecem fora deste produto.

Cada nova ferramenta, cada novo artigo e cada novo recurso devem
aproximar a plataforma desse objetivo.

---

## Observação

Os princípios arquiteturais e regras de desenvolvimento descritos acima constituem a referência oficial do projeto. Evite duplicar diretrizes; toda nova regra deve complementar este documento, não repetir conteúdos já estabelecidos.

---

# Regras obrigatórias para criação e modificação

Estas regras são obrigatórias para qualquer pessoa ou assistente de IA que crie, altere, corrija ou refatore arquivos deste projeto.

## Leveza e otimização

Toda criação ou modificação deve manter o Prazzu Tools **leve, rápido e otimizado**.

Antes de adicionar código, dependências, estilos, scripts, consultas, componentes ou abstrações, verifique se a necessidade pode ser atendida com o que já existe no projeto. Evite duplicações, dependências desnecessárias, carregamentos globais sem necessidade, JavaScript excessivo, CSS repetido, consultas ineficientes e qualquer implementação que aumente o custo de manutenção ou prejudique o desempenho.

Uma alteração só é considerada concluída quando preserva ou melhora:

- o tempo de carregamento e a responsividade da interface;
- o tamanho dos recursos enviados ao navegador;
- a simplicidade da arquitetura;
- a reutilização das capacidades compartilhadas;
- a eficiência das consultas e operações;
- a facilidade de manutenção do código.

Não implemente uma solução mais pesada quando existir uma alternativa simples, nativa ou já disponível no projeto.

## Bootstrap antes de CSS próprio

Bootstrap é a base visual obrigatória do projeto. Utilize ao máximo seus componentes, utilitários, grid, formulários, espaçamentos, responsividade, acessibilidade e comportamentos antes de criar CSS próprio.

CSS personalizado só deve ser usado quando Bootstrap e os estilos compartilhados existentes não forem suficientes. Nesse caso:

1. mantenha o CSS mínimo, reutilizável e claramente isolado;
2. registre o código-fonte em `resources/css/app.css` ou no arquivo de estilo específico do módulo, quando existir uma entrada própria;
3. garanta que o recurso esteja incluído no fluxo do Vite e carregado pela página correspondente;
4. execute `npm run build` para que a alteração também seja refletida nos arquivos compilados servidos por Vite em `public/build`;
5. nunca considere a alteração completa modificando somente `app.css` sem validar o bundle gerado pelo Vite;
6. não edite manualmente arquivos versionados com hash dentro de `public/build/assets`, pois eles devem ser gerados pelo processo de build.

Em resumo: o estilo deve existir no código-fonte e também estar presente no bundle compilado por Vite. Alterar apenas uma dessas etapas é insuficiente.

## Documentação oficial das páginas

A documentação oficial das páginas está em [`docs/pages`](docs/pages/README.md).

Antes de criar, alterar ou remover qualquer página, rota visual ou fluxo de interface, é obrigatório:

1. ler este README por completo;
2. ler `docs/pages/README.md`;
3. ler **todos os arquivos de documentação relacionados às páginas afetadas**;
4. conferir se a implementação continua compatível com o objetivo, funcionamento, regras, estados e dependências documentados;
5. atualizar a documentação junto com a alteração de código.

Se existir uma página sem documentação, a pessoa ou assistente de IA responsável pela tarefa deve criar o arquivo correspondente em `docs/pages` antes de considerar o trabalho concluído.

Toda nova página deve nascer com sua documentação oficial. Toda página removida deve ter sua documentação removida ou marcada como descontinuada na mesma alteração.~~

