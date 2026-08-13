# Gerador de Declaração de Rendimentos

## Descrição

Organiza valores anuais previamente apurados em uma declaração revisável de rendimentos. A ferramenta não apura tributos nem substitui informes oficiais, escriturações ou obrigações acessórias.

## Funcionalidades

- consolidação de rendimentos brutos e deduções informadas;
- cálculo do valor líquido declarado;
- geração de texto documental revisável;
- memória dos valores utilizados e avisos de autenticidade.

## Experiência Essencial

O visitante gera integralmente uma declaração individual sem autenticação, com valores, texto e limitações visíveis antes da utilização.

## Prazzu Plus

Histórico, recuperação, duplicação e exportações profissionais representam continuidade e conveniência. Não alteram os valores ou o texto Essencial.

## Regras

- rendimentos, INSS, IRRF e outras deduções são entradas declaradas pelo utilizador;
- o líquido corresponde apenas a rendimentos brutos menos deduções informadas;
- deduções não podem superar os rendimentos brutos;
- identidade, documento, poderes da fonte pagadora, assinatura e autenticidade não são validados;
- o documento precisa ser conferido e assinado pela fonte pagadora antes do uso.

## Integração entre ferramentas

Não publica nem aceita contratos e não importa domínio de outras ferramentas.

## Dependências

Utiliza geração documental, políticas de dados sensíveis, histórico versionado, exportação e memória de cálculo do Core técnico.

## Histórico de versões

- `1.1.0`: memória documental, aviso de limitações e separação explícita entre organização de dados e apuração fiscal.
- `1.0.0`: gerador funcional inicial.

## Prazzu Plus — saneamento de monetização

Exportação em planilha (`spreadsheet_export`) é Plus; geração individual e PDF permanecem Essenciais.
A autorização usa exclusivamente o gate central `tool.feature` no modo monetizado.
