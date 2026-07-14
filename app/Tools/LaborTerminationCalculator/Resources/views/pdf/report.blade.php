<h2>Dados considerados</h2>
<div class="print-grid">
    <div class="print-card"><span>Admissão</span><strong>{{ isset($input['admission_date']) ? \Carbon\CarbonImmutable::parse($input['admission_date'])->format('d/m/Y') : '—' }}</strong></div>
    <div class="print-card"><span>Desligamento</span><strong>{{ isset($input['termination_date']) ? \Carbon\CarbonImmutable::parse($input['termination_date'])->format('d/m/Y') : '—' }}</strong></div>
    <div class="print-card"><span>Projeção do contrato</span><strong>{{ $result['projected_termination_date'] ?? '—' }}</strong></div>
    <div class="print-card"><span>Salário mensal</span><strong>{{ $result['monthly_salary'] ?? ($input['monthly_salary'] ?? '—') }}</strong></div>
    <div class="print-card"><span>Remuneração-base</span><strong>{{ $result['salary_base'] ?? '—' }}</strong></div>
    <div class="print-card"><span>Tipo de contrato</span><strong>{{ ($result['is_domestic'] ?? false) ? 'Empregado doméstico' : match ($result['contract_type'] ?? '') { 'indefinite' => 'Prazo indeterminado', 'fixed_term' => 'Prazo determinado', 'experience' => 'Experiência', default => '—' } }}</strong></div>
</div>

<h2>Verbas rescisórias</h2>
<table>
    <thead><tr><th>Item</th><th class="print-right">Valor</th></tr></thead>
    <tbody>
    @foreach ([
        'Saldo de salário' => 'salary_balance', 'Férias vencidas' => 'overdue_vacation', '1/3 sobre férias vencidas' => 'overdue_vacation_third',
        'Férias proporcionais' => 'proportional_vacation', '1/3 sobre férias proporcionais' => 'proportional_vacation_third',
        '13º proporcional' => 'proportional_thirteenth_salary', 'Aviso-prévio indenizado' => 'notice_pay',
        'Indenização do art. 479' => 'article_479_indemnity', 'Indenizações adicionais' => 'extraordinary_indemnities',
    ] as $label => $key)
        <tr><td>{{ $label }}</td><td class="print-right">{{ $result[$key] ?? '—' }}</td></tr>
    @endforeach
        <tr class="print-summary-row"><td>Total bruto</td><td class="print-right">{{ $result['gross_total'] ?? '—' }}</td></tr>
    </tbody>
</table>

<h2>Descontos</h2>
<table>
    <thead><tr><th>Item</th><th class="print-right">Valor</th></tr></thead>
    <tbody>
    @foreach ([
        'INSS sobre salário' => 'inss_salary', 'INSS sobre 13º' => 'inss_thirteenth', 'IRRF sobre salário' => 'irrf_salary',
        'IRRF sobre 13º' => 'irrf_thirteenth', 'Aviso não cumprido' => 'notice_discount', 'Art. 480' => 'article_480_discount',
        'Outros descontos' => 'other_discounts',
    ] as $label => $key)
        <tr><td>{{ $label }}</td><td class="print-right">{{ $result[$key] ?? '—' }}</td></tr>
    @endforeach
        <tr class="print-summary-row"><td>Total de descontos</td><td class="print-right">{{ $result['total_discounts'] ?? '—' }}</td></tr>
        <tr class="print-summary-row"><td>Valor líquido estimado</td><td class="print-right">{{ $result['net_total'] ?? '—' }}</td></tr>
    </tbody>
</table>

<h2>FGTS e indenização</h2>
<div class="print-grid">
    <div class="print-card"><span>FGTS rescisório</span><strong>{{ $result['fgts_termination_deposit'] ?? '—' }}</strong></div>
    <div class="print-card"><span>{{ ($result['is_domestic'] ?? false) ? 'Reserva indenizatória' : 'Multa rescisória' }}</span><strong>{{ $result['fgts_penalty'] ?? '—' }}</strong></div>
    <div class="print-card"><span>Disponível estimado</span><strong>{{ $result['estimated_fgts_available'] ?? '—' }}</strong></div>
</div>

@if (! empty($result['warnings']))
    <div class="print-warning"><strong>Atenção</strong><ul>@foreach ($result['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul></div>
@endif

<div class="print-footer">
    <p>Regra {{ $result['rule_version'] ?? '—' }} · Tabela tributária {{ $result['tax_table_version'] ?? '—' }}.</p>
    <p>Estimativa informativa. O relatório não substitui o TRCT, documentos oficiais nem a conferência por profissional qualificado. Valores de FGTS são apresentados separadamente do pagamento líquido da empresa.</p>
    <p>Referências gerais: Constituição Federal, CLT, Lei nº 8.036/1990, Lei nº 12.506/2011 e Lei Complementar nº 150/2015.</p>
</div>
