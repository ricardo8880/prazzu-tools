@php
    $result = $result ?? [];
@endphp
<div class="row g-3 mb-4">
    @foreach(['Total cost'=>'total_cost','Selling price'=>'sale_price','Gross profit'=>'gross_profit','Net profit'=>'net_profit','Net margin'=>'margin','Markup'=>'markup','Markup multiplier'=>'markup_multiplier'] as $label=>$key)
        <div class="col-6"><div class="border rounded p-3 h-100"><small class="text-body-secondary d-block">{{ $label }}</small><strong>{{ $result[$key] ?? '—' }}</strong></div></div>
    @endforeach
</div>
<table class="table table-sm"><thead><tr><th>Deduction</th><th class="text-end">Amount</th></tr></thead><tbody>
    <tr><td>Taxes</td><td class="text-end">{{ $result['taxes_amount'] ?? '—' }}</td></tr><tr><td>Commission</td><td class="text-end">{{ $result['commission_amount'] ?? '—' }}</td></tr><tr><td>Card fees</td><td class="text-end">{{ $result['card_fees_amount'] ?? '—' }}</td></tr><tr><td>Marketplace</td><td class="text-end">{{ $result['marketplace_fees_amount'] ?? '—' }}</td></tr>
</tbody></table>
<p class="small text-body-secondary mb-0">Management estimate based on the information entered. Rule {{ $result['rule_version'] ?? '—' }}.</p>
