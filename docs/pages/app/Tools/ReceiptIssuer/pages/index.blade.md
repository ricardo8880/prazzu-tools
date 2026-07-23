# Emissor de Recibos — Prazzu Tools

- **Tipo:** Página de ferramenta
- **Implementação principal:** `app/Tools/ReceiptIssuer/Resources/views/index.blade.php`
- **Status:** ativa em rascunho

## Objetivo

Permitir o preenchimento, validação, emissão e revisão de um recibo completo, com valor por extenso, identificação única e exportação dedicada para PDF.

## Como funciona

O formulário envia os dados para a rota de emissão. Após a validação e o processamento pelo domínio da ferramenta, a página apresenta uma revisão completa. A partir dessa revisão, o usuário pode imprimir a página ou abrir o documento dedicado de exportação em uma nova guia.

## Estados

- formulário inicial sem resultado;
- erros de validação junto ao formulário;
- revisão do recibo emitido;
- ações de impressão da página e exportação dedicada.

## Dependências

- layout compartilhado `layouts.app`;
- componentes compartilhados de introdução, níveis de recursos e impressão;
- rotas `tools.emissor-de-recibos.index`, `issue` e `export.pdf`;
- fluxo de domínio do módulo `ReceiptIssuer`.

## Regras de manutenção

- preservar a solução Essencial completa sem exigir autenticação;
- não persistir dados nesta etapa;
- reenviar os dados validados para gerar o documento dedicado, evitando confiar em HTML do navegador;
- priorizar Bootstrap e componentes compartilhados;
- atualizar este documento ao alterar campos, estados ou ações.

## Validação mínima após alterações

- validar formulário completo e documentos opcionais;
- confirmar revisão do recibo e valor por extenso;
- confirmar abertura da exportação dedicada;
- executar os testes do módulo.
