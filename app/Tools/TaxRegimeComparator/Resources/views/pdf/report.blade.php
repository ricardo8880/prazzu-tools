<section>
    <p><strong>Data de referência:</strong> {{ $result['reference_date'] }}</p>
    <p><strong>Atividade:</strong> {{ $input['business_activity'] }}</p>
    <h2>Resumo</h2>
    <table><tbody>
        <tr><th>Menor ônus estimado</th><td>{{ $result['winner'] ?? 'Sem comparação suficiente' }}</td></tr>
        <tr><th>Economia mensal</th><td>{{ $result['monthly_savings'] ?? '—' }}</td></tr>
        <tr><th>Economia anual</th><td>{{ $result['annual_savings'] ?? '—' }}</td></tr>
        <tr><th>Versão das regras</th><td>{{ $result['rule_version'] }}</td></tr>
    </tbody></table>

    <h2>Ranking</h2>
    <table><thead><tr><th>Posição</th><th>Regime</th><th>Mensal</th><th>Anual</th></tr></thead><tbody>
        @foreach($result['ranking'] as $item)<tr><td>{{ $item['position'] }}</td><td>{{ $item['regime'] }}</td><td>{{ $item['monthly_tax'] }}</td><td>{{ $item['annual_tax'] }}</td></tr>@endforeach
    </tbody></table>

    @if($result['warnings'])<h2>Alertas</h2><ul>@foreach($result['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul>@endif
    <p><small>Relatório orientativo. Não substitui revisão contábil e tributária profissional.</small></p>
</section>
