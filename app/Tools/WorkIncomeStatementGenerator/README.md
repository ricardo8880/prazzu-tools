# Gerador de Declaração de Trabalho/Renda

## Descrição

Gera uma declaração particular de atividade e renda a partir dos dados fornecidos pelo declarante. O texto não cria nem reconhece vínculo empregatício e não autentica identidade, renda ou assinatura.

## Funcionalidades

- geração de declaração particular de atividade e renda;
- validação da coerência entre início da atividade e emissão;
- apresentação de renda mensal e texto revisável;
- memória documental e avisos de limitação.

## Experiência Essencial

O visitante gera integralmente uma declaração individual sem autenticação e revê o conteúdo antes de imprimir ou exportar.

## Prazzu Plus

Histórico, recuperação, duplicação e exportações profissionais representam produtividade e continuidade. Não alteram o conteúdo Essencial.

## Regras

- o início declarado não pode ser posterior à data de emissão;
- renda e atividade são reproduzidas como declarações do utilizador;
- a relação não recebe qualificação jurídica automática;
- a aceitação depende da instituição destinatária e dos comprovativos exigidos;
- o documento precisa ser revisto e assinado pelo declarante.

## Integração entre ferramentas

Não publica nem aceita contratos e não importa domínio de outras ferramentas.

## Dependências

Utiliza geração documental, políticas de dados sensíveis, histórico versionado, exportação e memória de cálculo do Core técnico.

## Histórico de versões

- `1.1.0`: memória documental, datas coerentes, avisos contra presunção de vínculo e limitações de autenticidade.
- `1.0.0`: gerador funcional inicial.

## Prazzu Plus — saneamento de monetização

Exportação em planilha (`spreadsheet_export`) é Plus; geração individual e PDF permanecem Essenciais.
A autorização usa exclusivamente o gate central `tool.feature` no modo monetizado.
