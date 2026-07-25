@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Faturamento e regra de comissão">
        <form method="POST" action="{{ route('tools.comissao-vendedores.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6"><x-tools.form.money name="sales" label="Faturamento do vendedor" :value="old('sales')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="rate" label="Comissão-base" type="number" step="0.000001" min="0" max="100" suffix="%" :value="old('rate')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.money name="goal" label="Meta de faturamento" help="Use zero para calcular sem meta." :value="old('goal', '0,00')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="goal_bonus_rate" label="Bônus ao atingir a meta" type="number" step="0.000001" min="0" max="100" suffix="%" :value="old('goal_bonus_rate', '0')" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular comissão</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
        <x-tools.result-panel title="Resultado da comissão">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <table class="table table-sm mb-0"><tbody>@foreach($result->details['memory'] as $formula => $value)<tr><th>{{ $formula }}</th><td class="text-end">{{ $value }}</td></tr>@endforeach</tbody></table>
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
