# Validador Inteligente de CNPJ, CPF e IE

## Descrição

Módulo independente para validação matemática, consulta cadastral e análise de
consistência de documentos brasileiros. A validade matemática local é separada
da disponibilidade de provedores externos.

## Funcionalidades

### Validação individual

- validação matemática local de CPF e CNPJ;
- detecção automática do tipo de documento;
- diagnóstico de tamanho, repetição e dígitos verificadores;
- consulta cadastral individual de CNPJ;
- razão social, nome fantasia, situação, abertura, natureza jurídica e
  matriz/filial;
- endereço cadastral e atividades econômicas;
- validação de Inscrição Estadual por estratégia estadual;
- suporte a CE, ES, MA, MG, PA, PB, PE, PR, RJ, RS, SC, SE e SP;
- análise determinística de inconsistências cadastrais.

### Processamento em lote

- importação de CSV, TXT delimitado e XLSX;
- detecção de delimitador em CSV;
- leitura da primeira aba de arquivos XLSX;
- pré-visualização e mapeamento manual ou sugerido de colunas;
- identificação de documentos inválidos e duplicados;
- consulta cadastral opcional e cruzamento de razão social, nome fantasia, UF,
  município e IE;
- exportação do resultado completo ou apenas de inconsistências em CSV e
  formato compatível com Excel;
- relatório para impressão e salvamento como PDF pelo navegador.

### Histórico e plataforma

- histórico autenticado por 90 dias, contendo somente metadados e totais;
- auditoria de execuções e exclusões por contratos do Core;
- métricas de processamento e exportação pelo contrato `PlatformAnalytics`.

## Experiência Essencial

A experiência gratuita valida individualmente CPF, CNPJ e Inscrição Estadual,
consulta o cadastro do CNPJ e apresenta a análise individual de inconsistências
completa. Nenhum diagnóstico correto fica condicionado ao Plus.

## Prazzu Plus

O Plus acrescenta produtividade para volumes maiores: importação e validação em
lote, mapeamento de colunas, exportações, relatório do lote e histórico.
Durante o lançamento, a política central libera esses recursos gratuitamente.

## Regras

- Validade matemática não depende de consulta externa.
- Indisponibilidade do provedor não é irregularidade da empresa e deve ser
  apresentada como análise não concluída.
- Cada inconsistência possui código estável, severidade, mensagem, recomendação
  e valores informado/cadastral quando aplicáveis.
- A primeira linha do arquivo é o cabeçalho e somente a primeira aba de XLSX é
  processada.
- O limite atual é 5 MB e 500 registros por arquivo.
- São permitidas até 50 consultas cadastrais externas por processamento.
- Dados completos importados ficam temporariamente no cache por 30 minutos; a
  interface recebe uma prévia de até oito linhas e um token aleatório.
- Documentos e dados cadastrais completos não são persistidos no histórico.
- Visitantes acessam validação, consulta, lote e exportação atual durante a fase
  gratuita. Login é requisito apenas para persistência e histórico.

## Dependências

### Core

- `app/Core/Imports` para leitura tabular e armazenamento temporário;
- sistema central de exportação e impressão;
- contratos de histórico, auditoria e analytics;
- value objects compartilhados de CPF, CNPJ e datas.

### Provedor cadastral

A implementação padrão utiliza a BrasilAPI por meio de
`BrasilApiCompanyRegistryProvider`. O contrato permite substituir o provedor
sem alterar Application, Domain ou Presentation.

```env
BRASIL_API_BASE_URL=https://brasilapi.com.br/api
BRASIL_API_CONNECT_TIMEOUT=3
BRASIL_API_TIMEOUT=8
```

### Servidor

A leitura de XLSX utiliza `ZipArchive` e `SimpleXML`. Sem essas extensões, CSV
continua disponível e a interface deve informar claramente a indisponibilidade
de Excel.

O módulo não depende de outra ferramenta.

## Histórico de versões

- `1.0.0` — Lote 7: exportações, impressão, histórico por 90 dias, auditoria,
  métricas e promoção para status ativo.
- `0.x` — Lotes 1 a 6: validação de CPF/CNPJ/IE, consulta cadastral, motor de
  inconsistências, importação compartilhada, pré-visualização e processamento
  em lote.
