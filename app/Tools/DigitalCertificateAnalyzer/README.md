# Analisador de Certificado Digital A1

## Descrição

### Problema resolvido

Permite abrir localmente no servidor de aplicação, durante uma única requisição, um certificado A1 PKCS#12 (`.pfx`/`.p12`) já existente e conferir dados essenciais antes de utilizá-lo em outras rotinas.

A ferramenta **não emite certificado ICP-Brasil**, não substitui uma Autoridade Certificadora, não opera token/cartão A3 e não mantém um cadastro de certificados.

## Funcionalidades

### Experiência Essencial

- abre PKCS#12 protegido por senha;
- mostra titular, organização e emissor;
- mostra início e fim do período de validade e dias restantes;
- identifica CPF/CNPJ somente quando um documento válido aparece nos campos lidos;
- classifica o tipo como provável e-CPF/e-CNPJ apenas nessa situação;
- alerta certificado vencido, ainda não válido ou a até 30 dias do vencimento;
- deixa explícito que validade temporal não equivale a validação de cadeia ou revogação.

## Prazzu Plus

`technical_report` acrescenta diagnóstico técnico (serial, algoritmo, chave pública, fingerprint SHA-256 e SAN quando exposto pelo OpenSSL) e relatório PDF sem incluir senha ou chave privada.

## Regras

### Segurança e privacidade

- limite de upload: 5 MB;
- extensões aceitas: `.pfx` e `.p12`;
- nenhuma persistência ou histórico;
- a senha não é enviada a analytics, não é colocada no resultado e não é reapresentada;
- a chave privada nunca é incluída em `CalculationInput::toArray()`, resultado ou exportação;
- o arquivo temporário de upload é consumido somente durante a requisição;
- erros de OpenSSL são drenados e não são exibidos ao usuário.

## Limites técnicos da versão 1.0

A análise confirma que o PKCS#12 pode ser aberto e lê os metadados do certificado X.509. Não consulta OCSP/CRL, não comprova cadeia ICP-Brasil, não autentica em SEFAZ e não assina documentos. Essas capacidades exigem desenho técnico e de segurança separado.

## Dependências

- PHP com extensão OpenSSL.
- Contratos e componentes compartilhados do Core.
- Bootstrap e infraestrutura de exportação PDF já existente.

## Integração com a plataforma

- Slug: `analisador-certificado-digital-a1`
- Vertical: `contabilidade`
- Histórico: desabilitado
- Persistência: desabilitada
- Exportação: PDF
- Compartilhamento: desabilitado
- Dados sensíveis: arquivo do certificado e senha, tratados sem persistência

## Histórico de versões

| Versão | Estado | Alterações |
| --- | --- | --- |
| 1.0.0 | Beta | Leitura PKCS#12, identidade, validade, diagnóstico técnico e relatório PDF. |
