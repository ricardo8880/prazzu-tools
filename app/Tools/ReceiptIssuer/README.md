# Emissor de Recibos

## Descrição

### Problema resolvido

Gera um recibo completo e verificável a partir dos dados do pagamento, evitando textos improvisados, valores divergentes e documentos sem identificação.

## Escopo entregue até o lote 6

O lote 1 entregou o núcleo de domínio. O lote 2 conectou esse núcleo ao fluxo Essencial público: formulário, validação, montagem da entrada tipada, emissão e revisão completa em tela. O lote 3 adicionou a exportação dedicada para impressão e salvamento em PDF por meio da infraestrutura compartilhada do Core. O lote 4 adicionou histórico autenticado, reaproveitamento, exclusão e exportação de recibos salvos usando a persistência compartilhada do Core. O lote 5 adicionou perfis reutilizáveis de pagadores e recebedores, vinculados à conta e com documentos criptografados. O lote 6 adiciona geração em lote por CSV, com validação independente por linha e impressão consolidada.

## Funcionalidades

### Experiência Essencial

- preenchimento completo do recibo;
- validação dos dados principais;
- valor numérico e por extenso;
- identificação única e número público;
- revisão completa do recibo em tela;
- impressão básica da página;
- exportação dedicada para impressão ou salvamento em PDF;
- histórico autenticado com recuperação, exclusão e nova via.

### Prazzu Plus

- histórico e reaproveitamento;
- perfis reutilizáveis de pagadores e recebedores;
- geração em lote por CSV, com até 100 recibos e relatório de linhas inválidas;
- identidade visual personalizada.

O Plus adiciona produtividade e continuidade; não corrige nem completa o recibo Essencial.

## Dependências

- Core técnico: `Money`, `Cpf`, `Cnpj` e contratos padronizados de ferramenta.
- Nenhuma dependência de outro módulo de ferramenta.

## Regras de domínio

- o valor deve ser positivo e expresso com `App\Core\Money\Money`;
- CPF e CNPJ utilizam os value objects compartilhados do Core;
- o número público aceita até 40 caracteres alfanuméricos e os separadores `.`, `/`, `_` e `-`;
- a identificação interna é um UUID válido;
- nomes têm de 2 a 160 caracteres;
- a descrição tem de 3 a 1.000 caracteres;
- a cidade é opcional e limitada a 120 caracteres;
- valores por extenso aceitam BRL positivo até R$ 999.999.999,99.

## Integrações

- Contratos publicados: nenhum.
- Contratos aceitos: nenhum.

Não foram criados contratos artificiais. A ferramenta funciona isoladamente.

## Capacidades da plataforma

- Slug: `emissor-de-recibos`
- Estado: `draft`
- Histórico versionado: declarado para o futuro lote Plus
- Exportação: PDF
- Dados pessoais: documentos de pagador e recebedor com política criptografada

## Histórico de versões

| Versão | Estado | Alterações |
| --- | --- | --- |
| 0.1.0 | Draft | Estrutura oficial e núcleo de domínio do lote 1. |
| 0.2.0 | Draft | Formulário Essencial, validação, emissão e revisão em tela do lote 2. |
| 0.3.0 | Draft | Exportação dedicada para impressão e salvamento em PDF do lote 3. |
| 0.4.0 | Draft | Histórico autenticado, reaproveitamento e exportação de recibos salvos do lote 4. |
| 0.5.0 | Draft | Perfis reutilizáveis de pagadores e recebedores do lote 5. |
| 0.6.0 | Draft | Geração de recibos em lote por CSV do lote 6. |
