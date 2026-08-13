@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">

    <x-tools.form-panel title="Dados do produto ou serviço">
        <form method="POST" action="{{ route('tools.ponto-de-equilibrio.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-4"><x-tools.form.money name="fixed_costs" label="Custos fixos do período" :value="old('fixed_costs')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="sale_price" label="Preço de venda unitário" :value="old('sale_price')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="variable_cost" label="Custo variável unitário" :value="old('variable_cost')" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular ponto de equilíbrio</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Ponto de equilíbrio">
            <div class="row g-3">@foreach ($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>@endforeach</tbody></table>
        <x-tools.export-buttons :pdf-route="route('tools.ponto-de-equilibrio.export.pdf')" :excel-route="route('tools.ponto-de-equilibrio.export.excel')" :input="$calculationInput ?? []" />
        </x-tools.result-panel>
        <x-tools.form-panel class="mt-4" title="Prazzu Plus — comparação de cenário" description="Teste mudanças de preço e custos."><form method="POST" action="{{ route('tools.ponto-de-equilibrio.scenarios') }}" class="row g-3">@csrf @foreach (($calculationInput ?? []) as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach <div class="col-12"><x-tools.form.input name="scenario_name" label="Nome do cenário" value="Alternativo" /></div><div class="col-md-4"><x-tools.form.input name="fixed_cost_change_rate" label="Custos fixos" type="number" step="0.01" value="0" suffix="%" required /></div><div class="col-md-4"><x-tools.form.input name="sale_price_change_rate" label="Preço" type="number" step="0.01" value="5" suffix="%" required /></div><div class="col-md-4"><x-tools.form.input name="variable_cost_change_rate" label="Custo variável" type="number" step="0.01" value="0" suffix="%" required /></div><div class="col-12"><button class="btn btn-outline-primary">Comparar cenário</button></div></form></x-tools.form-panel>
    @endisset
    @isset($breakEvenScenarios)<x-tools.result-panel class="mt-4" title="Comparação de ponto de equilíbrio">@foreach($breakEvenScenarios as $scenario)<h3 class="h5 mt-3">{{ $scenario['name'] }}</h3><div class="row g-3">@foreach($scenario['result']->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" /></div>@endforeach</div>@endforeach</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
