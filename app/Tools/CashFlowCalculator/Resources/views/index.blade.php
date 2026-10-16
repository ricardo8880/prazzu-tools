@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">

    <x-tools.form-panel title="Period forecast" description="Enter actual or forecast values for one month.">
        <form method="POST" action="{{ route('tools.cash-flow-calculator.calculate') }}" class="row g-3">
            @csrf
            @foreach ([
                'opening_balance' => 'Saldo inicial',
                'sales_receipts' => 'Recebimentos de vendas',
                'other_inflows' => 'Outras entradas',
                'operating_payments' => 'Pagamentos operacionais',
                'tax_payments' => 'Tributos pagos',
                'investments' => 'Investimentos',
                'financing_payments' => 'Parcelas de financiamentos',
                'other_outflows' => 'Outras saídas',
            ] as $name => $label)
                <div class="col-12 col-md-6"><x-tools.form.money :name="$name" :label="$label" :value="old($name)" required /></div>
            @endforeach
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate cash flow</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Cash flow forecast">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            @foreach($result->warnings as $warning)
                <div class="alert {{ $warning->level->value === 'danger' ? 'alert-danger' : ($warning->level->value === 'warning' ? 'alert-warning' : 'alert-info') }} mt-3" role="status">@if($warning->title)<strong>{{ $warning->title }}.</strong> @endif{{ $warning->message }}</div>
            @endforeach
            <h3 class="h5 mt-4">Calculation details</h3>
            <div class="table-responsive"><table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? '$'.number_format($step->result / 100, 2, '.', ',') : $step->result }}</td></tr>@endforeach</tbody></table></div>
        <x-tools.export-buttons :pdf-route="route('tools.cash-flow-calculator.export.pdf')" :excel-route="route('tools.cash-flow-calculator.export.excel')" :input="$calculationInput ?? []" /></x-tools.result-panel>
        <x-tools.form-panel class="mt-4" title="Prazzu Plus — cash flow scenarios" description="Compare base, conservative, and optimistic scenarios."><form method="POST" action="{{ route('tools.cash-flow-calculator.scenarios') }}" class="row g-3">@csrf @foreach (($calculationInput ?? []) as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach <div class="col-md-6"><x-tools.form.input name="inflow_change_rate" label="Change in inflows" type="number" min="0" max="90" step="0.01" value="10" suffix="%" required /></div><div class="col-md-6"><x-tools.form.input name="outflow_change_rate" label="Change in outflows" type="number" min="0" max="90" step="0.01" value="10" suffix="%" required /></div><div class="col-12"><button class="btn btn-outline-primary">Compare scenarios</button></div></form></x-tools.form-panel>
    @endisset
    @isset($cashFlowScenarios)<x-tools.result-panel class="mt-4" title="Cash flow scenarios">@foreach($cashFlowScenarios as $scenario)<h3 class="h5 mt-3">{{ $scenario['name'] }}</h3><div class="row g-3">@foreach($scenario['result']->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" /></div>@endforeach</div>@endforeach</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
