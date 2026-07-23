# Recibo para impressão e PDF

- **Tipo:** Documento de saída / impressão
- **Implementação principal:** `app/Tools/ReceiptIssuer/Resources/views/pdf/receipt.blade.php`
- **Status:** ativo em rascunho

## Objetivo

Apresentar o recibo emitido em formato A4, limpo e adequado para impressão ou salvamento como PDF pelo navegador.

## Como funciona

A view é renderizada dentro do exportador compartilhado `BrowserPrintExporter`. O controller recalcula o recibo a partir de uma requisição validada e fornece somente os dados finais do documento.

## Conteúdo

- número e valor do recibo;
- identificação do pagador;
- valor numérico e por extenso;
- descrição do pagamento;
- local e data de emissão;
- assinatura e documento do recebedor;
- identificador único do recibo.

## Dependências

- `resources/views/exports/browser-print.blade.php`;
- `App\Core\Export\Data\PrintableDocument`;
- dados finais produzidos pelo domínio do Emissor de Recibos.

## Regras de manutenção

- manter o documento legível em A4 e impressão monocromática;
- não incluir controles de gestão, histórico ou persistência;
- não duplicar a estrutura geral de impressão mantida no Core;
- escapar todos os valores através do Blade;
- atualizar este documento quando o conteúdo do recibo mudar.

## Validação mínima após alterações

- confirmar renderização com e sem documentos das partes;
- verificar texto longo de descrição;
- testar impressão e salvamento em PDF;
- executar os testes de feature do módulo.
