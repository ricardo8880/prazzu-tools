# Exportação universal — Lote 1

## Escopo

Este lote cria somente a infraestrutura transversal. Nenhuma das 32 ferramentas foi migrada ainda.

## Dependências oficiais

- PDF: `dompdf/dompdf`.
- Excel: `phpoffice/phpspreadsheet`.

## API compartilhada

- `PdfExporter::download(PdfDocument $document)`.
- `SpreadsheetExporter::download(SpreadsheetDocument $document)`.

As ferramentas devem fornecer conteúdo de resultado específico. Os exportadores não recebem páginas completas da plataforma.

## Legado temporário

`BrowserPrintExporter`, `PrintableDocument`, `print-page.js` e os métodos artesanais de Excel permanecem apenas para não quebrar ferramentas ainda não migradas. Eles estão proibidos para novos usos e serão removidos após a migração integral.

## Verificação do ambiente

O ambiente de análise não possuía Composer nem as extensões PHP `dom`, `mbstring`, `xmlwriter`, `zip` e SQLite, e também não possuía acesso DNS aos repositórios. Por isso, o `composer.lock` não pôde ser regenerado neste lote. O `composer.json` contém as dependências corretas; o próximo ambiente com acesso ao Composer deve executar a atualização dirigida antes da validação integrada.
