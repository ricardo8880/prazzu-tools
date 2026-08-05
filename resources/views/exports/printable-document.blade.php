<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 14mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #212529; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        header { padding-bottom: 10px; border-bottom: 2px solid #212529; }
        header h1 { margin: 0 0 4px; font-size: 18px; }
        .muted { color: #6c757d; }
        .summary { float: right; text-align: right; margin-top: -34px; }
        .summary strong { display: block; font-size: 16px; white-space: nowrap; }
        .print-content { clear: both; padding-top: 8px; }
        .print-content h2 { margin: 16px 0 6px; font-size: 12px; border-bottom: 1px solid #212529; padding-bottom: 4px; }
        .print-grid { width: 100%; }
        .print-card { display: inline-block; width: 31%; margin: 0 1% 6px 0; border: 1px solid #dee2e6; padding: 7px; vertical-align: top; }
        .print-card span { display: block; color: #6c757d; font-size: 8px; text-transform: uppercase; }
        .print-card strong { font-size: 11px; }
        .print-content table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .print-content th, .print-content td { border-bottom: 1px solid #dee2e6; padding: 5px 4px; text-align: left; vertical-align: top; }
        .print-content th { background: #f1f3f5; }
        .print-right { text-align: right !important; }
        .print-summary-row td { font-weight: bold; border-top: 2px solid #212529; }
        .print-warning { border: 1px solid #d39e00; background: #fff3cd; padding: 7px; margin-top: 10px; }
        .print-footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #adb5bd; color: #6c757d; font-size: 8px; }
        .alert { border: 1px solid #d39e00; background: #fff3cd; padding: 8px; }
        .print-action { display: none; }
        @media screen { .print-action { display: inline-block; margin: 0 0 12px; } }
    </style>
</head>
<body>
<button class="print-action" type="button" onclick="window.print()">Imprimir / Salvar como PDF</button>
<header>
    <div class="muted">Prazzu Tools</div>
    <h1>{{ $title }}</h1>
    @if (!empty($generatedAt))<div class="muted">Gerado em {{ $generatedAt }}</div>@endif
    @if (!empty($summaryValue))
        <div class="summary">
            @if (!empty($summaryLabel))<span class="muted">{{ $summaryLabel }}</span>@endif
            <strong>{{ $summaryValue }}</strong>
        </div>
    @endif
</header>
<div class="print-content">
    @include($contentView, $contentData ?? [])
</div>
</body>
</html>
