@php($result = $result ?? [])
<div class="row g-3 mb-4">
    @foreach(['Custo total'=>'total_cost','Preço de venda'=>'sale_price','Lucro bruto'=>'gross_profit','Lucro líquido'=>'net_profit','Margem líquida'=>'margin','Markup'=>'markup','Índice de markup'=>'markup_multiplier'] as $label=>$key)
        <div class="col-6"><div class="border rounded p-3 h-100"><small class="text-body-secondary d-block">{{ $label }}</small><strong>{{ $result[$key] ?? '—' }}</strong></div></div>
    @endforeach
</div>
<table class="table table-sm"><thead><tr><th>Dedução</th><th class="text-end">Valor</th></tr></thead><tbody>
    <tr><td>Impostos</td><td class="text-end">{{ $result['taxes_amount'] ?? '—' }}</td></tr><tr><td>Comissão</td><td class="text-end">{{ $result['commission_amount'] ?? '—' }}</td></tr><tr><td>Taxas de cartão</td><td class="text-end">{{ $result['card_fees_amount'] ?? '—' }}</td></tr><tr><td>Marketplace</td><td class="text-end">{{ $result['marketplace_fees_amount'] ?? '—' }}</td></tr>
</tbody></table>
<p class="small text-body-secondary mb-0">Estimativa gerencial baseada nos dados informados. Regra {{ $result['rule_version'] ?? '—' }}.</p>
