@extends('layouts.app')

@section('title', 'Comparação salva — Comparador Tributário')

@section('content')
<x-tools.page title="Comparação salva" description="Resultado preservado com a versão da ferramenta e da regra." icon="clock-history" slug="comparador-tributario">
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a class="btn btn-outline-secondary" href="{{ route('tools.comparador-tributario.history.index') }}">Voltar ao histórico</a>
        <form method="post" action="{{ route('tools.comparador-tributario.history.repeat', $run->id) }}">@csrf<button class="btn btn-primary" type="submit">Reutilizar cenário</button></form>
        <form method="post" action="{{ route('tools.comparador-tributario.history.destroy', $run->id) }}">@csrf @method('delete')<button class="btn btn-outline-danger" type="submit">Excluir</button></form>
    </div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-body">
        <div class="row g-3"><div class="col-md-4"><div class="small text-body-secondary">Executado em</div><strong>{{ $run->finishedAt->format('d/m/Y H:i') }}</strong></div><div class="col-md-4"><div class="small text-body-secondary">Ferramenta</div><strong>{{ $run->toolVersion }}</strong></div><div class="col-md-4"><div class="small text-body-secondary">Regra</div><strong>{{ $run->ruleVersion }}</strong></div></div>
    </div></div>

    @php($result = $run->result)
    <div class="card border-success shadow-sm mb-4"><div class="card-body"><div class="small text-success fw-semibold">Menor ônus estimado</div><div class="h2 mb-0">{{ $result['winner'] ?? 'Sem comparação suficiente' }}</div></div></div>
    <div class="row g-3">@foreach(($result['ranking'] ?? []) as $item)<div class="col-md-4"><div class="card h-100"><div class="card-body"><span class="badge text-bg-light border">{{ $item['position'] }}º</span><h2 class="h5 mt-2">{{ $item['regime'] }}</h2><div>Mensal: <strong>{{ $item['monthly_tax'] }}</strong></div><div>Anual: <strong>{{ $item['annual_tax'] }}</strong></div></div></div></div>@endforeach</div>
</x-tools.page>
@endsection
