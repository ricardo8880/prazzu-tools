@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Dados do reajuste" description="Informe o percentual e, se previsto na convenção, um aumento fixo adicional.">
        <form method="POST" action="{{ route('tools.reajuste-salarial.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6"><x-tools.form.money name="current_salary" label="Salário atual" :value="old('current_salary')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="adjustment_rate" label="Percentual de reajuste" type="number" step="0.000001" min="0" max="100" suffix="%" :value="old('adjustment_rate')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.money name="fixed_addition" label="Aumento fixo adicional" :value="old('fixed_addition', '0,00')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="retroactive_months" label="Meses retroativos" type="number" min="0" max="60" :value="old('retroactive_months', 0)" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular reajuste</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Resultado do reajuste">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <div class="table-responsive"><table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>@endforeach</tbody></table></div>
            <div class="alert alert-info mt-3 mb-0">O cálculo não aplica pisos, tetos, compensações ou cláusulas específicas. Confira a convenção coletiva.</div>
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
