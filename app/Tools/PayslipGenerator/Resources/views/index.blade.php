@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug"><x-tools.form-panel title="Dados do holerite"><form method="POST" action="{{route('tools.gerador-holerite.calculate')}}" class="row g-3">@csrf
@foreach(['name'=>'Funcionário','document'=>'CPF/documento','employer'=>'Empregador'] as $n=>$l)<div class="col-md-4"><x-tools.form.input :name="$n" :label="$l" :value="old($n)" required/></div>@endforeach<div class="col-md-4"><x-tools.form.input name="competence" label="Competência" type="month" :value="old('competence')" required/></div>@foreach(['salary'=>'Salário','other_earnings'=>'Outros proventos','inss'=>'INSS','irrf'=>'IRRF','other_deductions'=>'Outros descontos'] as $n=>$l)<div class="col-md-4"><x-tools.form.money :name="$n" :label="$l" :value="old($n,$n==='salary'?null:'0,00')" required/></div>@endforeach<div class="col-12"><button class="btn btn-primary">Gerar holerite</button></div></form></x-tools.form-panel>
@isset($result)@php($d=$result->details['document'])<x-tools.result-panel title="Holerite"><div class="card"><div class="card-body"><div class="d-flex justify-content-between"><strong>{{$d['employer']}}</strong><span>Competência {{$d['competence']}}</span></div><p>{{$d['employee']}} — {{$d['document']}}</p><table class="table"><tbody>@foreach($d['items'] as $l=>$v)<tr><th>{{$l}}</th><td class="text-end">{{$v}}</td></tr>@endforeach</tbody><tfoot><tr><th>Líquido</th><th class="text-end">{{$d['net']}}</th></tr></tfoot></table></div></div><x-tools.print-button class="mt-3">Imprimir / salvar PDF</x-tools.print-button></x-tools.result-panel>@endisset</x-tools.page>
@endsection
