<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 18mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 16px; }
        h2 { font-size: 13px; margin: 18px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #bbb; padding: 5px; text-align: left; vertical-align: top; }
        th { background: #eee; width: 38%; }
        .section-row th { width: auto; background: #f5f5f5; }
        .level-1 { padding-left: 18px; }
        .level-2 { padding-left: 30px; }
        .level-3 { padding-left: 42px; }
    </style>
</head>
<body>
@php
    $toRows = static function (array $values, int $level = 0) use (&$toRows): array {
        $rows = [];

        foreach ($values as $key => $value) {
            $label = is_string($key)
                ? str($key)->replace('_', ' ')->headline()->toString()
                : 'Item '.((int) $key + 1);

            if (is_array($value)) {
                $rows[] = ['label' => $label, 'value' => '', 'level' => $level];
                $rows = [...$rows, ...$toRows($value, $level + 1)];
                continue;
            }

            if (is_bool($value)) {
                $value = $value ? 'Sim' : 'Não';
            } elseif ($value === null) {
                $value = '';
            } elseif (! is_scalar($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
            }

            $rows[] = ['label' => $label, 'value' => (string) $value, 'level' => $level];
        }

        return $rows;
    };

    // Compatibilidade com controladores antigos, que enviavam `input` e `result`
    // diretamente, e com o fluxo novo, que envia `inputRows` e `resultRows`.
    $inputRows = $inputRows ?? $toRows(is_array($input ?? null) ? $input : []);
    $resultRows = $resultRows ?? $toRows(is_array($result ?? null) ? $result : []);
@endphp
<h1>{{ $title }}</h1>

@if ($inputRows !== [])
<h2>Dados informados</h2>
<table>
@foreach ($inputRows as $row)
    @if ($row['value'] === '')
        <tr class="section-row"><th colspan="2" class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th></tr>
    @else
        <tr><th class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th><td>{{ $row['value'] }}</td></tr>
    @endif
@endforeach
</table>
@endif

<h2>Resultado e informações para conferência</h2>
<table>
@foreach ($resultRows as $row)
    @if ($row['value'] === '')
        <tr class="section-row"><th colspan="2" class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th></tr>
    @else
        <tr><th class="level-{{ min(3, $row['level']) }}">{{ $row['label'] }}</th><td>{{ $row['value'] }}</td></tr>
    @endif
@endforeach
</table>
</body>
</html>
