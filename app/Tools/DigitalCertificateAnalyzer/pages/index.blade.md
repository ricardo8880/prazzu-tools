# Analisador de Certificado Digital A1

## Objetivo

Permitir uma análise pontual de certificado digital A1 em arquivo `.pfx`/`.p12`, mostrando identidade, emissor e situação temporal sem transformar o Prazzu Tools em emissor ICP-Brasil ou sistema de gestão de certificados.

## Funcionamento

O usuário envia o PKCS#12 e informa a senha apenas para a requisição corrente. O módulo abre o contêiner com OpenSSL, lê o certificado público e devolve titular, emissor, período de validade, dias restantes e CPF/CNPJ quando identificável.

## Implementação principal

- View: `app/Tools/DigitalCertificateAnalyzer/Resources/views/index.blade.php`.
- Request: `Presentation/Requests/ExecuteToolRequest.php`.
- Domínio: `Domain/Services/Calculator.php`.
- Rotas: `Routes/web.php`.

## Conteúdos e estados

A página possui estado inicial, erro de upload/senha, certificado inválido ou ilegível, certificado ainda não válido, válido, próximo do vencimento e vencido. O resultado nunca reapresenta a senha ou a chave privada.

## Dependências e segurança

A análise usa OpenSSL disponível no ambiente PHP. Não existe persistência, histórico ou armazenamento do arquivo. A política de dados sensíveis marca `certificate_file` e `password` como campos redigidos. Emissão ICP-Brasil, A3/token, assinatura digital e autenticação em serviços externos permanecem fora desta versão.

## Prazzu Plus

`technical_report` acrescenta diagnóstico técnico e relatório PDF. O Essencial continua resolvendo a verificação principal de identidade e validade.

## Regras de manutenção

Nunca registrar senha, bytes PKCS#12 ou chave privada em histórico, logs, Analytics ou exportação. Qualquer futura integração de assinatura ou SEFAZ precisa de lote próprio, modelo explícito de confiança e revisão de segurança.

## Validação mínima

Validar upload `.pfx/.p12`, senha correta/incorreta, estados de validade, ausência de persistência, fixture fictícia, rota GET/POST, exportação Plus, manifesto, catálogo, Analytics, E2E e arquitetura.
