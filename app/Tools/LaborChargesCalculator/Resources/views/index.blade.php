@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.form-panel title="Remuneração e enquadramento"><form method="POST" action="{{route('tools.encargos-trabalhistas.calculate')}}" class="row g-3">@csrf
<div class="col-md-6"><x-tools.form.money name="salary" label="Salário mensal" :value="old('salary')" required/></div>
<div class="col-md-6"><x-tools.form.money name="benefits" label="Benefícios mensais" :value="old('benefits','0,00')" required/></div>
<div class="col-md-4"><x-tools.form.select name="regime" label="Regime" :options="['general'=>'Regime geral','simples_annex_iv'=>'Simples — Anexo IV','simples_other'=>'Simples — demais anexos']" :value="old('regime','general')" required/></div>
<div class="col-md-4"><x-tools.form.input name="rat" label="RAT ajustado" type="number" step="0.000001" suffix="%" :value="old('rat','1')" required/></div>
<div class="col-md-4"><x-tools.form.input name="third_parties" label="Terceiros" type="number" step="0.000001" suffix="%" :value="old('third_parties','5.8')" required/></div>
<div class="col-12"><button class="btn btn-primary">Calcular encargos</button></div></form></x-tools.form-panel>
@isset($result)<x-tools.result-panel title="Custo provisionado"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description"/></div>@endforeach</div>
<h3 class="h5 mt-4">Memória de cálculo</h3><table class="table table-sm"><tbody>@foreach($result->details['memory'] as $formula=>$value)<tr><th>{{$formula}}</th><td class="text-end">{{$value}}</td></tr>@endforeach</tbody></table>
<div class="alert alert-warning mb-0">Estimativa mensal. Confirme incidências, benefícios, CCT, FPAS, FAP e particularidades da folha.</div></x-tools.result-panel>@endisset
</x-tools.page>
@endsection
