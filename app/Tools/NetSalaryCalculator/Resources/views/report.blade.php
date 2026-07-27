@php
    $money = static fn (int $minor): string => \App\Core\Money\Money::fromMinor($minor)->formatPtBr();
    $details = $result->details;
@endphp

<table class="table table-sm">
    <tbody>
        @foreach ($result->summary as $item)
            <tr><th>{{ $item->label }}</th><td class="text-end">{{ $item->value }}</td></tr>
        @endforeach
    </tbody>
</table>

<h2 class="h5 mt-4">Detalhamento</h2>
<table class="table table-sm">
    <tbody>
        <tr><th>Remuneração tributável</th><td>{{ $money($details['taxable_gross_minor']) }}</td></tr>
        <tr><th>Base do INSS</th><td>{{ $money($details['social_security_base_minor']) }}</td></tr>
        <tr><th>INSS</th><td>{{ $money($details['inss_minor']) }}</td></tr>
        <tr><th>Base do IRRF</th><td>{{ $money($details['irrf_base_minor']) }}</td></tr>
        <tr><th>Redução do IRRF</th><td>{{ $money($details['irrf_reduction_minor']) }}</td></tr>
        <tr><th>IRRF final</th><td>{{ $money($details['irrf_minor']) }}</td></tr>
        <tr><th>Outros descontos</th><td>{{ $money($details['user_discounts_minor']) }}</td></tr>
        <tr><th>Salário líquido</th><td><strong>{{ $money($details['net_minor']) }}</strong></td></tr>
    </tbody>
</table>

<p class="small text-muted">Estimativa para salário mensal CLT regular em um único vínculo. Confira incidências específicas com o responsável pela folha.</p>
