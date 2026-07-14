<div class="summary-grid">
    @foreach ([
        'Total' => $result['summary']['total'] ?? 0,
        'Válidos' => $result['summary']['valid'] ?? 0,
        'Inválidos' => $result['summary']['invalid'] ?? 0,
        'Duplicados' => $result['summary']['duplicates'] ?? 0,
        'Com inconsistências' => $result['summary']['with_inconsistencies'] ?? 0,
    ] as $label => $value)
        <div class="summary-card"><span>{{ $label }}</span><strong>{{ $value }}</strong></div>
    @endforeach
</div>

<table>
    <thead><tr><th>Linha</th><th>Documento</th><th>Tipo</th><th>Status</th><th>Observações</th></tr></thead>
    <tbody>
    @foreach (($result['rows'] ?? []) as $row)
        @php($issues = collect($row['inconsistencies'] ?? [])->whereIn('severity', ['error', 'warning']))
        <tr>
            <td>{{ $row['line'] }}</td>
            <td>{{ $row['formatted_document'] ?: $row['document'] }}</td>
            <td>{{ $row['type'] }}</td>
            <td>{{ $row['valid'] ? 'Válido' : 'Inválido' }}{{ $row['duplicate'] ? ' · Duplicado' : '' }}</td>
            <td>{{ $issues->pluck('message')->implode(' | ') ?: ($row['message'] ?? 'Sem inconsistências') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
