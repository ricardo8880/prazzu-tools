# Qualidade — Analisador de Certificado Digital A1

O README da raiz continua sendo a autoridade máxima.

## Perfil

- Natureza: validação
- Dependência normativa: nenhuma para o parser/diagnóstico temporal
- Dados pessoais: sensíveis
- Integração externa: nenhuma
- Persistência: temporária por requisição
- Resultado: informacional
- Exportação: PDF

## Segurança verificada

- [x] upload limitado a 5 MB e extensão `.pfx`/`.p12`;
- [x] senha obrigatória e nunca devolvida ao HTML;
- [x] arquivo, senha e chave privada ausentes do payload serializável do cálculo;
- [x] nenhuma política de histórico/persistência;
- [x] erros internos do OpenSSL não vazam para a resposta;
- [x] relatório recebe somente metadados sanitizados;
- [x] cenário E2E usa certificado autoassinado fictício e senha exclusiva de teste.

## Casos cobertos

- certificado PKCS#12 válido;
- senha incorreta;
- extensão não permitida;
- leitura de CNPJ válido no CN;
- diagnóstico técnico/fingerprint;
- contrato de acesso Plus do relatório técnico;
- fluxo browser válido e inválido pelo manifesto E2E.

## Limites assumidos

“Válido” significa **dentro do período X.509**. Revogação, cadeia de confiança e aceitação em serviço externo não são inferidas.
