@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Acumulados dos 12 meses anteriores">
        <form method="POST" action="{{ route('tools.simulador-fator-r.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6">
                <x-tools.form.money name="payroll_12" label="Folha de salários e encargos (FS12)" help="Remunerações e pró-labore pagos, CPP e FGTS efetivamente recolhidos." :value="old('payroll_12', '0,00')" required />
            </div>
            <div class="col-12 col-md-6">
                <x-tools.form.money name="revenue_12" label="Receita bruta acumulada (RBT12)" help="Mercados interno e externo nos 12 meses anteriores ao período de apuração." :value="old('revenue_12', '0,00')" required />
            </div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular Fator R</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Resultado do Fator R">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <div class="table-responsive"><table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>@endforeach</tbody></table></div>
            <div class="alert alert-warning mt-3 mb-0">O enquadramento depende de a atividade estar sujeita ao Fator R. Confira atividade, segregação de receitas e dados do PGDAS-D.</div>
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
