# Validador Inteligente de CNPJ, CPF e IE

Módulo independente para validação, consulta e análise de documentos brasileiros.

## Estado atual — Lote 6

Disponível:

- validação matemática local de CPF e CNPJ;
- detecção automática do tipo de documento;
- diagnóstico de tamanho, repetição e dígitos verificadores;
- consulta cadastral individual de CNPJ;
- razão social, nome fantasia, situação, abertura, natureza jurídica e matriz/filial;
- endereço cadastral e atividades econômicas;
- separação entre validade matemática e disponibilidade do provedor;
- validação de Inscrição Estadual por estratégia estadual;
- suporte inicial a CE, ES, MA, MG, PA, PB, PE, PR, RJ, RS, SC, SE e SP;
- análise determinística de inconsistências cadastrais;
- importação em lote de CSV e XLSX;
- detecção de delimitador em CSV;
- leitura da primeira aba de arquivos XLSX;
- pré-visualização e mapeamento manual ou sugerido de colunas;
- processamento de até 500 registros por arquivo;
- identificação de documentos inválidos e duplicados;
- consulta cadastral opcional de até 50 CNPJs por processamento;
- cruzamento em lote de razão social, nome fantasia, UF, município e IE.

## Importação compartilhada

A leitura tabular genérica pertence ao Core em `app/Core/Imports`. O módulo conhece apenas o significado das colunas e as regras aplicadas a cada registro.

Os dados completos importados são mantidos temporariamente no cache por 30 minutos e vinculados ao usuário autenticado ou à sessão atual. A interface recebe apenas uma prévia de até oito linhas e um token aleatório.

Limites atuais:

- formatos: CSV, TXT delimitado e XLSX;
- tamanho máximo: 5 MB;
- máximo de 500 linhas por arquivo;
- primeira linha obrigatoriamente utilizada como cabeçalho;
- primeira aba utilizada em arquivos XLSX;
- até 50 consultas cadastrais externas por processamento.

A validação matemática de todos os documentos continua local e não depende do provedor externo.

## Motor de inconsistências

A análise é executada por `CompanyConsistencyAnalyzer`. Cada ocorrência possui código estável, severidade, mensagem, recomendação, valor informado e valor cadastral quando aplicável.

A indisponibilidade do provedor não é tratada como irregularidade da empresa. Ela gera apenas uma informação de que a análise não pôde ser concluída.

## Provedor cadastral

A implementação padrão utiliza a BrasilAPI por meio de `BrasilApiCompanyRegistryProvider`. A infraestrutura pode ser substituída sem alterar Application, Domain ou Presentation.

```env
BRASIL_API_BASE_URL=https://brasilapi.com.br/api
BRASIL_API_CONNECT_TIMEOUT=3
BRASIL_API_TIMEOUT=8
```

## Requisitos para Excel

A leitura de XLSX utiliza `ZipArchive` e `SimpleXML`. Caso essas extensões não estejam disponíveis no servidor, a importação CSV continua funcionando e a interface apresenta uma mensagem clara para arquivos Excel.

## Fora do escopo deste lote

- exportação CSV e Excel dos resultados;
- impressão dos relatórios;
- filtros avançados e download apenas das inconsistências;
- histórico persistente e métricas finais.

## Lote 7 — exportação e acabamento

- Exportação do processamento atual em CSV UTF-8 e Excel compatível (SpreadsheetML).
- Exportação filtrada para registros inválidos, duplicados ou com alertas/erros.
- Relatório para impressão e salvamento em PDF pelo navegador.
- Histórico autenticado por 90 dias contendo apenas nome/formato do arquivo e totais; documentos e dados cadastrais não são persistidos.
- Auditoria automática das execuções e exclusões por meio do Core.
- Métricas de processamento e exportação por meio do contrato `PlatformAnalytics`.
- Módulo promovido para a versão `1.0.0` e status ativo.
