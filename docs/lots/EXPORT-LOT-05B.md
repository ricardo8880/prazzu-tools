# Lote 5B — Ferramentas 29–32

## Escopo
Migração das ferramentas 29–32 para PDF e Excel reais pelo Core compartilhado.

## Ferramentas
- Conversor Fiscal de XML
- Emissor de Recibos
- Calculadora de Simples Nacional
- Calculadora de Férias

## Alterações
- PDF gerado pelo contrato `PdfExporter` e implementação Dompdf.
- Excel `.xlsx` gerado pelo contrato `SpreadsheetExporter` e implementação PhpSpreadsheet.
- Remoção de `BrowserPrintExporter` dos módulos alterados.
- Substituição do falso Excel `.xls` do Conversor XML por `.xlsx` real.
- Preservação dos formatos CSV e JSON existentes como opções adicionais.
- Tela e exportações reutilizam a mesma validação, ação e resultado de domínio.

## Continuidade
As 32 ferramentas oficiais estão cobertas pelos lotes de exportação. Uma auditoria global posterior pode remover qualquer legado residual fora dos módulos migrados e executar o conjunto completo de verificações de release.

## Validações executadas
- Sintaxe PHP dos quatro controllers e quatro arquivos de rotas: sem erros.
- Registro das novas rotas PDF/XLSX: confirmado com `php artisan route:list`.
- Busca por `BrowserPrintExporter`, `window.print()` e componente de impressão nos quatro módulos: sem ocorrências.
- Testes funcionais não executados neste ambiente porque faltam as extensões PHP `dom`, `mbstring` e `xmlwriter` exigidas pelo PHPUnit.
