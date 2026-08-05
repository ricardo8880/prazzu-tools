<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 18mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 14px; }
        h2 { font-size: 13px; margin: 18px 0 7px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #bbb; padding: 6px; vertical-align: top; }
        th { background: #eee; text-align: left; }
        .value { text-align: right; white-space: nowrap; }
        .note { color: #444; font-size: 9px; }
        .section-row th { background: #f5f5f5; font-size: 10px; }
        .level-1 { padding-left: 18px; }
        .level-2 { padding-left: 30px; }
        .level-3 { padding-left: 42px; }
    </style>
</head>
<body>
<h1>{{ $title }}</h1>

<h2>Resumo</h2>
<table>
    <thead><tr><th>Indicador</th><th>Valor</th><th>Descrição</th></tr></thead>
    <tbody>
    @foreach ($summary as $item)
        <tr>
            <td>{{ $item['label'] ?? $item['key'] ?? '' }}</td>
            <td class="value">{{ $item['value'] ?? '' }}</td>
            <td>{{ $item['description'] ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@if ($inputRows !== [])
<h2>Dados informados</h2>
<table><tbody>
@foreach ($inputRows as $row)
    @if ($row['value'] === '')
        <tr class="section-row"><th colspan="2" class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th></tr>
    @else
        <tr>
            <th class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th>
            <td>{{ $row['value'] }}</td>
        </tr>
    @endif
@endforeach
</tbody></table>
@endif

@if ($detailRows !== [])
<h2>Detalhamento para conferência</h2>
<table><tbody>
@foreach ($detailRows as $row)
    @if ($row['value'] === '')
        <tr class="section-row"><th colspan="2" class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th></tr>
    @else
        <tr>
            <th class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th>
            <td>{{ $row['value'] }}</td>
        </tr>
    @endif
@endforeach
</tbody></table>
@endif

@if ($memoryRows !== [])
<h2>Memória de cálculo</h2>
<table>
<thead><tr><th>Etapa</th><th>Critério aplicado</th><th>Resultado</th></tr></thead>
<tbody>
@foreach ($memoryRows as $step)
<tr><td>{{ $step['label'] }}</td><td>{{ $step['formula'] }}</td><td class="value">{{ $step['result'] }}</td></tr>
@endforeach
</tbody>
</table>
@endif

@if (!empty($warnings))
<h2>Alertas e orientações</h2>
@foreach ($warnings as $warning)
<p class="note">{{ $warning['message'] ?? $warning['label'] ?? '' }}</p>
@endforeach
@endif
</body>
</html>
