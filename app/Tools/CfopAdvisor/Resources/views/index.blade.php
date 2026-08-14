@extends('layouts.app')
@section('title','Consultor e Validador de CFOP — Prazzu Tools')
@section('meta_description','Valide CFOP e entenda direção, abrangência e descrição fiscal com referência ao CONFAZ.')
@section('content')
<x-tools.page title="Consultor e Validador de CFOP" description="Valide a estrutura do CFOP e entenda rapidamente o sentido fiscal do código. A descrição exata deve ser conferida na tabela vigente do CONFAZ." icon="search" slug="consultor-validador-cfop">
<x-tools.validation-summary />
<form method="post" action="{{ route('tools.consultor-validador-cfop.calculate') }}" class="d-grid gap-4" data-testid="tool-form-panel">@csrf
<x-tools.form-panel title="CFOP" description="Informe os quatro dígitos com ou sem ponto."><div><label class="form-label" for="cfop">Código CFOP</label><input class="form-control" id="cfop" name="cfop" value="{{ old('cfop') }}" placeholder="Ex.: 5.102" data-e2e-value="5102" required></div></x-tools.form-panel><div><button class="btn btn-primary" type="submit">Analisar CFOP</button></div></form>
@isset($result)<div class="mt-5" data-testid="tool-result"><x-tools.result-panel title="Diagnóstico do CFOP"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-4"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="check-circle" /></div>@endforeach</div>@if(($catalogDetailsAllowed ?? false) && $result->details['description'])<div class="alert alert-success mt-4 mb-0"><strong>Descrição:</strong> {{ $result->details['description'] }}</div>@endif @foreach($result->warnings as $warning)<div class="alert alert-warning mt-3 mb-0">{{ $warning->message }}</div>@endforeach<div class="small text-body-secondary mt-3">Fonte normativa: {{ $result->details['official_source'] }}.</div></x-tools.result-panel></div>@endisset
</x-tools.page>@endsection
