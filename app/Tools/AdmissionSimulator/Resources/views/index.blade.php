@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug"><x-tools.form-panel title="Custos da contratação"><form method="POST" action="{{route('tools.simulador-admissao.calculate')}}" class="row g-3">@csrf
@foreach(['salary'=>'Salário','benefits'=>'Benefícios mensais','exam'=>'Exame admissional','recruitment'=>'Recrutamento','equipment'=>'Equipamentos','training'=>'Treinamento'] as $n=>$l)<div class="col-md-4"><x-tools.form.money :name="$n" :label="$l" :value="old($n,$n==='salary'?null:'0,00')" required/></div>@endforeach<div class="col-md-4"><x-tools.form.input name="monthly_burden" label="Encargos e provisões mensais" type="number" step="0.000001" suffix="%" :value="old('monthly_burden','40')" required/></div><div class="col-12"><button class="btn btn-primary">Simular admissão</button></div></form></x-tools.form-panel>
@isset($result)
    <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Custo da admissão"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value"/></div>@endforeach</div><h3 class="h5 mt-4">Checklist</h3><ul class="list-group">@foreach($result->details['checklist'] as $item)<li class="list-group-item"><input class="form-check-input me-2" type="checkbox"> {{$item}}</li>@endforeach</ul><x-tools.print-button class="mt-3">Imprimir / salvar PDF</x-tools.print-button></x-tools.result-panel>@endisset</x-tools.page>
@endsection
