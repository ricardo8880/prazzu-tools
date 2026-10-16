<h2>Final pay assumptions</h2>
<div class="print-grid">
    <div class="print-card"><span>Separation date</span><strong>{{ isset($input['termination_date']) ? \Carbon\CarbonImmutable::parse($input['termination_date'])->format('M j, Y') : '—' }}</strong></div>
    <div class="print-card"><span>Separation type</span><strong>{{ ucfirst(str_replace('_', ' ', $result['separation_type'] ?? '—')) }}</strong></div>
    <div class="print-card"><span>Hourly rate</span><strong>{{ isset($input['hourly_rate']) ? '$'.number_format((float)$input['hourly_rate'], 2, '.', ',') : '—' }}</strong></div>
</div>
<h2>Final pay</h2>
<table><thead><tr><th>Item</th><th class="print-right">Amount</th></tr></thead><tbody>
@foreach ([
    'Regular pay due' => 'regular_pay_due',
    'PTO payout' => 'pto_payout',
    'Severance pay' => 'severance_pay',
    'Bonus / commission due' => 'bonus_commission_due',
    'Other pay' => 'other_pay',
    'Gross final pay' => 'gross_final_pay',
    'Federal withholding entered' => 'federal_withholding',
    'State/local withholding entered' => 'state_local_withholding',
    'Other deductions' => 'other_deductions',
    'Total deductions' => 'total_deductions',
    'Estimated net final pay' => 'net_final_pay',
] as $label => $key)<tr><td>{{ $label }}</td><td class="print-right">{{ $result[$key] ?? '—' }}</td></tr>@endforeach
</tbody></table>
@if (! empty($result['warnings']))<div class="print-warning"><strong>Important</strong><ul>@foreach ($result['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul></div>@endif
<div class="print-footer"><p>Rule version {{ $result['rule_version'] ?? '—' }}.</p><p>Planning estimate only. Final-pay timing, PTO payout, severance, deductions, and wage rules can vary by state, contract, and employer policy.</p></div>
