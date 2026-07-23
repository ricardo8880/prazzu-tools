# Histórico dos lotes da Central de Recursos

Este documento mantém a continuidade entre os lotes. Antes de iniciar um novo
lote, devem ser analisados novamente:

1. o README do projeto, tratado como regra máxima;
2. o projeto-base recebido para o lote atual;
3. este histórico e todos os arquivos criados ou ajustados nos lotes anteriores;
4. a ferramenta relacionada ao conteúdo que será desenvolvido.

## Lote 1 — Fundação editorial e visual

Base: projeto completo recebido em `prazzu-tools(1).zip`.

Entregas:

- catálogo editorial centralizado em `config/resources.php`;
- páginas específicas para a Central de Recursos, Guias e Modelos;
- componente reutilizável de card;
- estados editoriais explícitos;
- primeiro guia e primeiro modelo cadastrados como `preparing`;
- padrão editorial documentado;
- testes do contrato mínimo dos recursos.

Decisão de escopo:

O lote não publica o conteúdo final. Ele prepara uma estrutura leve, sem banco
de dados ou CMS prematuro, porque existem apenas dois recursos inaugurais. Uma
infraestrutura persistente só deve ser considerada quando edição administrativa,
versionamento editorial ou volume real justificarem a mudança.

## Lote 2 — Guia de precificação de honorários contábeis

Referências relidas antes da implementação:

- README do projeto;
- projeto completo recebido em `prazzu-tools(1).zip`;
- todos os arquivos incrementais do Lote 1;
- domínio, interface, rotas e testes do módulo `AccountingFeesCalculator`.

Entregas:

- publicação do guia `precificacao-de-honorarios-contabeis`;
- rota pública exclusiva para recursos publicados;
- método em sete etapas alinhado aos fatores reais da calculadora;
- levantamento de dados, exemplo prático, erros comuns, revisão de escopo,
  checklist e perguntas frequentes;
- CTA contextual para a Calculadora de Honorários Contábeis;
- proteção contra acesso direto a recursos não publicados;
- estilos responsivos próprios para artigos de recursos;
- testes do conteúdo e do estado editorial.

Decisão de escopo:

O guia ensina diagnóstico e tomada de decisão, mas não replica nem substitui o
cálculo da ferramenta. Não foram adicionadas regras jurídicas, tabelas de preço
ou valores de mercado sem fonte. O conteúdo permanece gerencial, prático e
perene, com aviso explícito de que a decisão final depende da realidade do
escritório e do contrato.

Próximo lote previsto:

Produção do modelo de levantamento para precificação de honorários, analisando
novamente o README, o projeto-base e os Lotes 1 e 2 antes da implementação.

## Lote 3 — Modelo de levantamento para precificação de honorários

Referências relidas antes da implementação:

- README do projeto;
- projeto completo recebido em `prazzu-tools(1).zip`;
- todos os arquivos incrementais dos Lotes 1 e 2;
- guia publicado no Lote 2;
- domínio e limites do módulo `AccountingFeesCalculator`.

Entregas:

- publicação do modelo `levantamento-para-precificacao-de-honorarios`;
- planilha XLSX sem macros com cinco abas: orientação, levantamento, escopo,
  complexidade e consolidação;
- validações simples para estados de confirmação e classificação de escopo;
- página pública com instruções, limites de uso, download e ligação com a
  Calculadora de Honorários;
- aviso para evitar dados pessoais desnecessários e uso como cadastro permanente;
- testes do estado publicado, conteúdo da página e existência do arquivo.

Decisão de escopo:

O modelo é um instrumento pontual para organizar o diagnóstico. Ele não calcula
preços, não define tabelas de mercado, não armazena informações na plataforma e
não cria funções de CRM ou gestão. A Calculadora de Honorários continua sendo a
ferramenta responsável por organizar os fatores de cálculo, e proposta e
contrato permanecem documentos próprios do processo profissional.

Próximo lote previsto:

Revisão estratégica e de consistência da Central de Recursos, incluindo
navegação, estados editoriais, acessibilidade, SEO e integração entre guia,
modelo e ferramenta, sem ampliar o catálogo apenas por volume.


## Lote 4 — Revisão estratégica e integração da jornada

Referências relidas antes da implementação:

- README do projeto;
- projeto completo recebido em `prazzu-tools(1).zip`;
- todos os arquivos incrementais dos Lotes 1, 2 e 3;
- guia, modelo, catálogo, rotas, controlador e testes acumulados;
- metadados compartilhados já suportados pelo layout principal.

Entregas:

- metadados editoriais de SEO centralizados no catálogo;
- canonical, Open Graph e dados estruturados JSON-LD para os recursos publicados;
- breadcrumbs semânticos com lista ordenada;
- jornada cruzada entre guia, modelo e Calculadora de Honorários;
- componente compartilhado de próximos passos;
- foco visível e respeito a preferência de movimento reduzido;
- testes para SEO, dados estruturados e integração entre recursos.

Decisão de escopo:

O lote consolida a experiência dos dois recursos publicados sem ampliar o catálogo.
Não foi criado CMS, banco editorial, rastreamento próprio de downloads ou nova
camada de serviços, pois o volume atual não justifica essas abstrações. Guia,
modelo e calculadora permanecem independentes e complementares.
