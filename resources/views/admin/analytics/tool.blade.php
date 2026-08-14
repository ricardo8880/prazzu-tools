@extends('layouts.app')
@section('title',$tool['name'].' | Analytics')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
        <div><a class="small text-decoration-none" href="{{ route('admin.analytics.tools',['period'=>$selected_period]) }}"><i class="bi bi-arrow-left"></i> Ferramentas</a><h1 class="h2 mt-2 mb-1">{{ $tool['name'] }}</h1><p class="text-body-secondary mb-0">{{ $tool['description'] }}</p></div>
        <a class="btn btn-outline-primary align-self-start" href="{{ route($tool['route_name']) }}" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Abrir ferramenta</a>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Sessões que abriram',$metrics->session_opens],
            ['Pessoas que abriram',$metrics->people_opens],
            ['Sessões que começaram',$metrics->session_starts],
            ['Pessoas que começaram',$metrics->people_starts],
            ['Sessões com resultado',$metrics->session_results],
            ['Pessoas com resultado',$metrics->people_results],
            ['Resultado após início',number_format($metrics->result_after_start_rate,1,',','.').'%'],
            ['Tempo médio',number_format($metrics->average_calculation_seconds,1,',','.').'s'],
        ] as [$l,$v])
            <div class="col-6 col-md-4 col-xl"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-body-secondary">{{ $l }}</div><div class="h4 mt-2 mb-0">{{ $v }}</div></div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent"><div class="fw-semibold">Funil por sessões</div><div class="small text-body-secondary">Mostra atrito entre abertura, início e resultado nesta ferramenta.</div></div><div class="card-body"><div class="row g-3 text-center"><div class="col-4"><div class="small text-body-secondary">Abriu</div><div class="h3 my-1">{{ $session_funnel->opens }}</div></div><div class="col-4"><div class="small text-body-secondary">Começou</div><div class="h3 my-1">{{ $session_funnel->starts }}</div><div class="small">{{ number_format($session_funnel->start_rate,1,',','.') }}%</div></div><div class="col-4"><div class="small text-body-secondary">Resultado</div><div class="h3 my-1">{{ $session_funnel->results }}</div><div class="small">{{ number_format($session_funnel->result_after_start_rate,1,',','.') }}%</div></div></div></div></div></div>
        <div class="col-xl-6"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent"><div class="fw-semibold">Funil por pessoas</div><div class="small text-body-secondary">Somente identidades persistentes; uma pessoa pode atravessar mais de uma sessão.</div></div><div class="card-body"><div class="row g-3 text-center"><div class="col-4"><div class="small text-body-secondary">Abriu</div><div class="h3 my-1">{{ $people_funnel->opens }}</div></div><div class="col-4"><div class="small text-body-secondary">Começou</div><div class="h3 my-1">{{ $people_funnel->starts }}</div><div class="small">{{ number_format($people_funnel->start_rate,1,',','.') }}%</div></div><div class="col-4"><div class="small text-body-secondary">Resultado</div><div class="h3 my-1">{{ $people_funnel->results }}</div><div class="small">{{ number_format($people_funnel->result_after_start_rate,1,',','.') }}%</div></div></div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent"><div class="fw-semibold">Retorno e retenção desta ferramenta</div><div class="small text-body-secondary">Retorno geral exige outro resultado em outro dia. D1/D7/D30 usam o primeiro resultado observado no período como início da coorte.</div></div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-3"><div class="small text-body-secondary">Problemas resolvidos</div><div class="h4 mb-0">{{ $retention->problems_solved }}</div></div>
                <div class="col-6 col-md-3"><div class="small text-body-secondary">Pessoas que resolveram</div><div class="h4 mb-0">{{ $retention->solvers }}</div></div>
                <div class="col-6 col-md-3"><div class="small text-body-secondary">Voltaram em outro dia</div><div class="h4 mb-0">{{ $retention->returning_solvers }}</div></div>
                <div class="col-6 col-md-3"><div class="small text-body-secondary">Retorno geral</div><div class="h4 mb-0">{{ number_format($retention->return_rate,1,',','.') }}%</div></div>
            </div>
            <div class="row g-3">
                @foreach(['D1' => $retention->d1, 'D7' => $retention->d7, 'D30' => $retention->d30] as $label => $cohort)
                    <div class="col-md-4"><div class="border rounded p-3"><div class="small text-body-secondary">{{ $label }}</div><div class="h4 my-1">{{ $cohort->rate === null ? '—' : number_format($cohort->rate,1,',','.').'%' }}</div><div class="small text-body-secondary">{{ $cohort->eligible }} elegíveis · {{ $cohort->returned }} retornaram</div></div></div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4"><div class="col-xl-6"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Dispositivos</div><ul class="list-group list-group-flush">@forelse($devices as $r)<li class="list-group-item d-flex justify-content-between"><span>{{ ucfirst($r->label) }}</span><span class="badge text-bg-primary">{{ $r->total }}</span></li>@empty<li class="list-group-item text-body-secondary">Sem dados.</li>@endforelse</ul></div></div><div class="col-xl-6"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Origens</div><ul class="list-group list-group-flush">@forelse($sources as $r)<li class="list-group-item d-flex justify-content-between"><span>{{ $r->label }}</span><span class="badge text-bg-primary">{{ $r->total }}</span></li>@empty<li class="list-group-item text-body-secondary">Sem dados.</li>@endforelse</ul></div></div></div>

    <div class="card border-0 shadow-sm mt-4"><div class="card-header bg-transparent fw-semibold">Eventos recentes</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Evento</th><th>Origem</th><th>Dispositivo</th><th>Quando</th></tr></thead><tbody>@forelse($recent_events as $e)<tr><td><code>{{ $e->event_name }}</code></td><td>{{ $e->source ?: 'Direto' }}</td><td>{{ $e->device_type ?: '—' }}</td><td>{{ $e->occurred_at?->format('d/m/Y H:i') }}</td></tr>@empty<tr><td colspan="4" class="text-center text-body-secondary py-4">Sem eventos.</td></tr>@endforelse</tbody></table></div></div>
</div>
@endsection
