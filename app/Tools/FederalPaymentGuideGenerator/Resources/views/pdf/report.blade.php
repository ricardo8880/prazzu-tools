<h2>Resumo orientativo</h2>
<table>
    <tr><th>Guia e código</th><td>{{ $result['guide']['type'] ?? '—' }} {{ $result['guide']['code'] ?? '—' }} — {{ $result['guide']['description'] ?? '—' }}</td></tr>
    <tr><th>Vencimento</th><td>{{ $result['dates']['due_date'] ?? '—' }}</td></tr>
    <tr><th>Pagamento previsto</th><td>{{ $result['dates']['payment_date'] ?? '—' }}</td></tr>
    <tr><th>Principal</th><td>{{ $result['amounts']['principal'] ?? '—' }}</td></tr>
    <tr><th>Multa</th><td>{{ $result['amounts']['penalty'] ?? '—' }} ({{ $result['calculation']['penalty_percent'] ?? '0' }}%)</td></tr>
    <tr><th>Juros</th><td>{{ $result['amounts']['interest'] ?? '—' }} ({{ $result['calculation']['interest_percent'] ?? '0' }}%)</td></tr>
    <tr><th>Total estimado</th><td><strong>{{ $result['amounts']['total'] ?? '—' }}</strong></td></tr>
    <tr><th>Dias corridos de atraso</th><td>{{ $result['calculation']['calendar_days_late'] ?? 0 }}</td></tr>
</table>
<h2>Alertas obrigatórios</h2>
<ul>@foreach(($result['warnings'] ?? []) as $warning)<li>{{ $warning }}</li>@endforeach</ul>
<p><strong>Importante:</strong> este relatório não é uma guia oficial, não transmite informações e não substitui a conferência no sistema oficial.</p>
