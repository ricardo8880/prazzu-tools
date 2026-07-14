<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $document->title }}</title>
    <style>
        @page { size: A4; margin: 14mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #212529; font: 12px/1.45 Arial, sans-serif; background: #f8f9fa; }
        .print-toolbar { max-width: 210mm; margin: 16px auto; padding: 12px; display: flex; gap: 8px; justify-content: flex-end; }
        .print-button { border: 1px solid #6c757d; border-radius: 6px; padding: 9px 14px; background: #fff; color: #212529; cursor: pointer; font-weight: 700; }
        .print-button--primary { color: #fff; background: #0d6efd; border-color: #0d6efd; }
        .print-sheet { width: 210mm; min-height: 297mm; margin: 0 auto 24px; padding: 14mm; background: #fff; box-shadow: 0 2px 14px rgba(0,0,0,.12); }
        .print-header { display: flex; justify-content: space-between; gap: 24px; padding-bottom: 14px; border-bottom: 3px solid #212529; }
        .print-header h1 { margin: 0 0 4px; font-size: 24px; }
        .print-header p { margin: 4px 0; }
        .print-muted { color: #6c757d; }
        .print-content h2 { margin: 22px 0 8px; font-size: 15px; border-bottom: 2px solid #212529; padding-bottom: 5px; }
        .print-content p { margin: 4px 0; }
        .print-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .print-card { border: 1px solid #dee2e6; border-radius: 6px; padding: 9px; }
        .print-card span { display: block; color: #6c757d; font-size: 10px; text-transform: uppercase; }
        .print-card strong { font-size: 14px; }
        .print-content table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .print-content th, .print-content td { border-bottom: 1px solid #dee2e6; padding: 6px 5px; text-align: left; vertical-align: top; }
        .print-content th { background: #f1f3f5; }
        .print-right { text-align: right !important; }
        .print-summary-row td { font-weight: 700; border-top: 2px solid #212529; }
        .print-warning { border: 1px solid #ffc107; background: #fff3cd; padding: 9px; margin-top: 12px; }
        .print-footer { margin-top: 22px; padding-top: 10px; border-top: 1px solid #adb5bd; color: #6c757d; font-size: 10px; }
        .print-content ul { margin: 6px 0 0 18px; padding: 0; }
        @media print {
            body { background: #fff; }
            .print-toolbar { display: none; }
            .print-sheet { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
        }
        @media (max-width: 760px) {
            .print-sheet { width: 100%; min-height: 0; padding: 20px; }
            .print-header { display: block; }
            .print-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="print-toolbar" aria-label="Ações do relatório">
    <button class="print-button" type="button" onclick="history.back()">{{ $document->backLabel }}</button>
    <button class="print-button print-button--primary" type="button" onclick="window.print()">{{ $document->printLabel }}</button>
</div>

<main class="print-sheet">
    <header class="print-header">
        <div>
            <p class="print-muted">{{ $document->applicationName }}</p>
            <h1>{{ $document->title }}</h1>
            @if ($document->subtitle)
                <p>{{ $document->subtitle }}</p>
            @endif
            @if ($document->generatedAt)
                <p class="print-muted">Gerado em {{ $document->generatedAt }}</p>
            @endif
        </div>

        @if ($document->summaryValue)
            <div class="print-right">
                @if ($document->summaryLabel)
                    <span class="print-muted">{{ $document->summaryLabel }}</span>
                @endif
                <strong style="display:block;font-size:22px;white-space:nowrap">{{ $document->summaryValue }}</strong>
            </div>
        @endif
    </header>

    <div class="print-content">
        @include($document->contentView, $document->data)
    </div>
</main>
</body>
</html>
