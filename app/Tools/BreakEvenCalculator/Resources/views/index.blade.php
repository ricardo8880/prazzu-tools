@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Dados do produto ou serviço">
        <form method="POST" action="{{ route('tools.ponto-de-equilibrio.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-4"><x-tools.form.money name="fixed_costs" label="Custos fixos do período" :value="old('fixed_costs', '0,00')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="sale_price" label="Preço de venda unitário" :value="old('sale_price')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="variable_cost" label="Custo variável unitário" :value="old('variable_cost', '0,00')" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular ponto de equilíbrio</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Ponto de equilíbrio">
            <div class="row g-3">@foreach ($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>@endforeach</tbody></table>
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
