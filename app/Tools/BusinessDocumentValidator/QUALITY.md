# Qualidade — Validador Inteligente de CNPJ, CPF e IE

## Perfil

- Natureza: validação determinística com consulta cadastral opcional.
- Dependência normativa: baixa, concentrada nas estratégias de Inscrição Estadual.
- Dados pessoais: documentos e conteúdo importado podem identificar pessoas/empresas.
- Integração externa: opcional; validade matemática local não depende do provedor.
- Persistência: histórico guarda somente metadados e totais definidos pela política do módulo.
- Resultado: informacional.

## Evidência verificada

- [x] CPF e CNPJ válidos possuem regressões unitárias com formatação conhecida.
- [x] Dígitos incorretos, sequências repetidas e tamanho incompatível são rejeitados por testes.
- [x] Estratégias de IE conhecidas possuem regressões para SP, MG, RJ e PR.
- [x] UF não suportada é reportada como não suportada, sem tentativa de adivinhar regra estadual.
- [x] Indisponibilidade de consulta externa é separada da validade matemática local pelo domínio.
- [x] Fluxo de consulta cadastral utiliza contrato de provedor e possui teste com substituto determinístico.
- [x] Histórico evita persistir o conteúdo integral do lote e declara `file_name` como sensível.
- [x] Processamento em lote é produtividade Plus; validações individuais essenciais permanecem completas.
- [x] Casos dourados são derivados dos testes executáveis existentes e não usam referência placeholder.
