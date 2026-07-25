@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug"><x-tools.form-panel title="Dados da declaração"><form method="POST" action="{{route('tools.declaracao-rendimentos.calculate')}}" class="row g-3">@csrf
@foreach(['name'=>'Beneficiário','document'=>'CPF/documento','payer'=>'Fonte pagadora'] as $n=>$l)<div class="col-md-4"><x-tools.form.input :name="$n" :label="$l" :value="old($n)" required/></div>@endforeach<div class="col-md-3"><x-tools.form.input name="year" label="Ano-calendário" type="number" :value="old('year',date('Y')-1)" required/></div>@foreach(['gross'=>'Rendimentos brutos','inss'=>'INSS','irrf'=>'IRRF','other_deductions'=>'Outras deduções'] as $n=>$l)<div class="col-md-3"><x-tools.form.money :name="$n" :label="$l" :value="old($n,$n==='gross'?null:'0,00')" required/></div>@endforeach<div class="col-12"><button class="btn btn-primary">Gerar declaração</button></div></form></x-tools.form-panel>
@isset($result)@php($d=$result->details['document'])<x-tools.result-panel title="Declaração de Rendimentos"><div class="card"><div class="card-body"><h3 class="text-center">{{$d['title']}}</h3><p class="lh-lg">{{$d['declaration']}}</p><p>Declaramos que <strong>{{$d['beneficiary']}}</strong>, documento {{$d['document']}}, recebeu de {{$d['payer']}} no ano-calendário {{$d['year']}} rendimentos brutos de {{$d['gross']}}.</p><table class="table"><tr><th>INSS</th><td>{{$d['inss']}}</td></tr><tr><th>IRRF</th><td>{{$d['irrf']}}</td></tr><tr><th>Outras deduções</th><td>{{$d['other']}}</td></tr><tr><th>Líquido</th><td>{{$d['net']}}</td></tr></table></div></div><div class="alert alert-warning mt-4"><strong>Antes de usar:</strong> {{implode(' ', $d['notice']['limitations'])}}</div><x-tools.print-button class="mt-3">Imprimir / salvar PDF</x-tools.print-button></x-tools.result-panel>@endisset</x-tools.page>
@endsection
