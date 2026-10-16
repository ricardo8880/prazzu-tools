@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">

    <x-tools.form-panel title="Product or service inputs">
        <form method="POST" action="{{ route('tools.break-even-calculator.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-4"><x-tools.form.money name="fixed_costs" label="Fixed costs for the period" :value="old('fixed_costs')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="sale_price" label="Unit selling price" :value="old('sale_price')" placeholder="e.g., 100.00" data-e2e-value="100.00" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="variable_cost" label="Unit variable cost" :value="old('variable_cost')" placeholder="e.g., 60.00" data-e2e-value="60.00" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate break-even</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Break-even point">
            <div class="row g-3">@foreach ($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            @foreach($result->warnings as $warning)
                <div class="alert {{ $warning->level->value === 'danger' ? 'alert-danger' : ($warning->level->value === 'warning' ? 'alert-warning' : 'alert-info') }} mt-3" role="status">@if($warning->title)<strong>{{ $warning->title }}.</strong> @endif{{ $warning->message }}</div>
            @endforeach
            <h3 class="h5 mt-4">Calculation details</h3>
            <div class="table-responsive"><table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? '$'.number_format($step->result / 100, 2, '.', ',') : $step->result }}</td></tr>@endforeach</tbody></table></div>
        <x-tools.export-buttons :pdf-route="route('tools.break-even-calculator.export.pdf')" :excel-route="route('tools.break-even-calculator.export.excel')" :input="$calculationInput ?? []" />
        </x-tools.result-panel>
        <x-tools.form-panel class="mt-4" title="Prazzu Plus — scenario comparison" description="Test changes in price and costs."><form method="POST" action="{{ route('tools.break-even-calculator.scenarios') }}" class="row g-3">@csrf @foreach (($calculationInput ?? []) as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach <div class="col-12"><x-tools.form.input name="scenario_name" label="Scenario name" value="Alternative" /></div><div class="col-md-4"><x-tools.form.input name="fixed_cost_change_rate" label="Fixed costs" type="number" step="0.01" value="0" suffix="%" required /></div><div class="col-md-4"><x-tools.form.input name="sale_price_change_rate" label="Price" type="number" step="0.01" value="5" suffix="%" required /></div><div class="col-md-4"><x-tools.form.input name="variable_cost_change_rate" label="Variable cost" type="number" step="0.01" value="0" suffix="%" required /></div><div class="col-12"><button class="btn btn-outline-primary">Compare scenario</button></div></form></x-tools.form-panel>
    @endisset
    @isset($breakEvenScenarios)<x-tools.result-panel class="mt-4" title="Break-even comparison">@foreach($breakEvenScenarios as $scenario)<h3 class="h5 mt-3">{{ $scenario['name'] }}</h3><div class="row g-3">@foreach($scenario['result']->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" /></div>@endforeach</div>@endforeach</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
