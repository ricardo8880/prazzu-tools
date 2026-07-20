# Módulos de ferramentas

Este diretório contém exclusivamente os módulos de ferramentas do Prazzu Tools.
O `README.md` da raiz é a autoridade máxima. Em caso de divergência, ele
prevalece sobre este guia e sobre qualquer implementação existente.

Cada ferramenta vive em `app/Tools/<NomeDaFerramenta>` e implementa
`App\Core\Tools\Contracts\ToolModule`.

## Limite do produto

Um módulo resolve uma tarefa pontual: recebe dados, calcula, valida, compara,
converte ou gera um resultado e encerra esse fluxo. Ele não administra a
operação do escritório ao longo do tempo.

CRM, cadastro de clientes, pipeline comercial, cobrança operacional, gestão de
documentos, tarefas, departamentos, workflow, colaboração e compartilhamento
de cálculos não pertencem ao Prazzu Tools. Propostas, contratos e relatórios
podem existir como gerações pontuais, desde que não criem um cadastro ou
processo de gestão paralelo.

Toda ferramenta possui duas experiências:

- **Essencial:** gratuita, correta, transparente e suficiente para resolver por
  completo o problema principal;
- **Prazzu Plus:** acrescenta produtividade, volume, automação, continuidade,
  cenários, análises ou formatos de conveniência.

O Plus nunca corrige, completa ou torna confiável um cálculo Essencial. Os dois
tiers reutilizam as mesmas regras de domínio.

## Estrutura obrigatória

Toda ferramenta deve possuir, desde a sua criação:

```text
app/Tools/NomeDaFerramenta/
├── Application/
├── Domain/
├── Infrastructure/
├── Presentation/
├── Resources/
├── Routes/
├── Tests/
├── README.md
└── Tool.php
```

As nove entradas são obrigatórias mesmo quando uma camada ainda não possui uma
implementação concreta. Uma camada vazia não autoriza mover sua responsabilidade
para outra camada.

## Responsabilidade das camadas

- `Application`: orquestra casos de uso por meio de Actions, chama o domínio e
  coordena contratos. Não contém regras de negócio.
- `Domain`: contém cálculos, regras, validações de domínio, value objects e
  serviços de domínio. Não depende do Laravel.
- `Infrastructure`: contém persistência, repositórios, APIs, armazenamento e
  adaptadores externos específicos do módulo.
- `Presentation`: contém controllers, Form Requests, resources HTTP e demais
  elementos de entrada e saída.
- `Resources`: contém views e, quando necessários, JavaScript e CSS específicos
  da ferramenta.
- `Routes`: contém as rotas próprias do módulo, sempre apontando para
  controllers.
- `Tests`: contém no mínimo `Unit` e `Feature`; interfaces complexas também
  exigem `Browser`.

O fluxo esperado de um controller é:

```text
Request -> Action -> Response
```

Controllers não calculam, não consultam banco, não chamam APIs, não implementam
autorização comercial e não geram PDF, CSV ou planilhas.

## Core e independência

Uma ferramenta pode depender dos contratos e value objects do Core, mas nunca
da implementação interna de outra ferramenta.

Antes de implementar qualquer capacidade, pergunte se outra ferramenta poderá
utilizá-la. Se a resposta for sim, a capacidade pertence ao Core. Isso inclui,
entre outros:

- histórico e favoritos;
- exportação, impressão, PDF, CSV e XLSX;
- analytics, auditoria e notificações;
- autenticação, autorização, planos e limites de uso;
- componentes Blade, máscaras e helpers reutilizáveis;
- `Money`, `Percentage`, datas e identificadores brasileiros.

Ferramentas declaram seus recursos; a plataforma decide acesso e política
comercial. Durante o lançamento gratuito, visitantes utilizam todos os recursos
públicos Essenciais e Plus, sem limites comerciais. Autenticação continua
exigida para aquilo que depende de identidade e persistência, como salvar
resultados, histórico e favoritos.

## Recursos do módulo

Rotas, views, migrations e assets específicos permanecem dentro do próprio
módulo. Exemplos:

```text
Routes/web.php
Resources/views/index.blade.php
Resources/js/index.js
Resources/css/index.css
Infrastructure/Database/Migrations/
```

JavaScript específico deve atuar somente dentro do escopo
`[data-tool="<slug>"]`. CSS próprio só é permitido quando Bootstrap e os
componentes compartilhados não forem suficientes.

## Manifesto e capacidades

O contrato mínimo expõe um `ToolManifest`. Cada recurso de produto é declarado
como `ToolFeature`, com chave estável, nome e `ToolFeatureTier::Essential` ou
`ToolFeatureTier::Plus`.

Todo módulo publicado deve declarar ao menos um recurso de cada tier. O acesso
do módulo público é `free`; Prazzu Plus e autenticação são decisões por recurso,
pois a solução Essencial é sempre pública.

O acesso é decidido pelo `ToolFeatureAccessGate` do Core. Rotas protegidas usam
o middleware central `tool.feature:<slug>,<feature>`. Nenhum módulo cria seu
próprio enum de tier, consulta plano, lê o modo comercial ou decide se o
lançamento é gratuito.

Capacidades técnicas opcionais continuam declaradas por contratos como:

- `HasWebRoutes`;
- `HasApiRoutes`;
- `HasViews`;
- `HasMigrations`;
- `HasServiceProviders`;
- `HasHistoryPolicy`.

O manifesto também declara slug imutável, versão semântica, categoria, acesso e
ciclo de vida. A ferramenta deve começar como `draft` e só pode se tornar
`active` após cumprir todo o checklist de qualidade do README raiz.

As divergências ainda presentes em módulos antigos estão registradas em
`docs/TOOL-ARCHITECTURE-DEBT.md`; elas não constituem exemplos válidos.

## Criando um módulo

```bash
php artisan make:tool CalculadoraRescisao \
    --slug=calculadora-rescisao \
    --category=trabalhista \
    --nature=calculation \
    --normative=high \
    --personal-data=common \
    --result-risk=labor \
    --update-frequency=unpredictable \
    --exports=pdf,csv
```

O gerador cria a estrutura inicial completa, incluindo perfil de risco, casos
dourados provisórios, contrato automatizado de qualidade e checklist de
segurança, privacidade e interface. Depois da geração:

1. documente descrição, funcionalidades, regras, dependências e histórico de
   versões;
2. defina entradas, saídas e casos de referência;
3. implemente regras no Domain e orquestração em Actions;
4. declare recursos Essenciais completos e recursos Plus realmente
   implementados;
5. escreva testes Unit e Feature, além de Browser quando aplicável;
6. execute o checklist obrigatório do README raiz.

## Regras invariáveis

1. O slug é imutável e único.
2. Rotas usam o prefixo `tools.<slug>.` e não possuem handlers em closures.
3. Views usam o namespace `tools-<slug>` e não carregam views de outra
   ferramenta.
4. Rotas, views, migrations e assets específicos permanecem no módulo.
5. O domínio recebe a data de referência e não consulta `now()` internamente.
6. Dinheiro e percentuais não usam `float`.
7. Regras de cobrança, login, limites ou gratuidade nunca são implementadas na
   ferramenta.
8. Nenhuma funcionalidade de ERP, CRM, gestão de clientes, workflow,
   colaboração ou compartilhamento de cálculos pertence ao Prazzu Tools.
9. Todo recurso Plus é autorizado pelo gate central e nunca por condição local.
10. O Essencial sempre entrega a resposta completa, com memória e transparência
    suficientes para solucionar o caso individual.
