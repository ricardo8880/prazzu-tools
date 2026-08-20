@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.form-panel title="Remuneração e enquadramento"><form method="POST" action="{{route('tools.encargos-trabalhistas.calculate')}}" class="row g-3">@csrf
<div class="col-md-6"><x-tools.form.money name="salary" label="Salário mensal" :value="old('salary')" required/></div>
<div class="col-md-6"><x-tools.form.money name="benefits" label="Benefícios mensais" :value="old('benefits')" required/></div>
<div class="col-md-4"><x-tools.form.select name="regime" label="Regime" :options="['general'=>'Regime geral','simples_annex_iv'=>'Simples — Anexo IV','simples_other'=>'Simples — demais anexos']" :value="old('regime','general')" required/></div>
<div class="col-md-4"><x-tools.form.input name="rat" label="RAT ajustado" type="number" step="0.000001" suffix="%" :value="old('rat')" data-e2e-value="1.2" required placeholder="Ex.: 1,20" /></div>
<div class="col-md-4"><x-tools.form.input name="third_parties" label="Terceiros" type="number" step="0.000001" suffix="%" :value="old('third_parties')" data-e2e-value="5.8" required placeholder="Ex.: 5,80" /></div>
<div class="col-12"><button class="btn btn-primary">Calcular encargos</button></div></form></x-tools.form-panel>
@isset($result)
    <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Custo provisionado"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description"/></div>@endforeach</div>
<h3 class="h5 mt-4">Memória de cálculo</h3><div class="table-responsive"><table class="table table-sm"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>@endforeach</tbody></table></div>
<div class="alert alert-warning mb-0">Estimativa mensal. Confirme incidências, benefícios, CCT, FPAS, FAP e particularidades da folha.</div><x-tools.export-buttons :pdf-route="route('tools.encargos-trabalhistas.export.pdf')" :excel-route="route('tools.encargos-trabalhistas.export.excel')" :input="$calculationInput ?? []" />
            <x-tools.normative-trust
                :rules="$result->calculationMemory?->normativeRules ?? []"
                :assumptions="$result->calculationMemory?->assumptions ?? []"
                :is-estimate="$result->calculationMemory?->isEstimate ?? false"
            />
</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
