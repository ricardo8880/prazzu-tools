@extends('layouts.app')
@section('title', 'Público | Analytics | Prazzu Tools')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge text-bg-primary">Analytics 2.0</span>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.funnels') }}">Funis</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.acquisition') }}">Aquisição</a>
            </div>
            <h1 class="h2 mb-1">Público</h1>
            <p class="text-body-secondary mb-0">Perfil agregado dos visitantes, dispositivos e distribuição geográfica.</p>
        </div>
        <form method="get" class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-auto"><label class="form-label small" for="period">Período</label><select class="form-select" id="period" name="period">@foreach(['today'=>'Hoje','yesterday'=>'Ontem','7'=>'7 dias','30'=>'30 dias','90'=>'90 dias'] as $value=>$label)<option value="{{ $value }}" @selected($selected_period===$value)>{{ $label }}</option>@endforeach</select></div>
                    <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Aplicar</button></div>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Visitantes',$summary['visitors'],'bi-people'],['Novos',$summary['new_visitors'],'bi-person-plus'],['Recorrentes',$summary['returning_visitors'],'bi-arrow-repeat'],['Taxa de retorno',number_format($summary['returning_rate'],1,',','.').' %','bi-percent'],['Sessões',$summary['sessions'],'bi-window'],['Usuários identificados',$summary['identified_users'],'bi-person-check']
        ] as [$label,$value,$icon])
            <div class="col-6 col-xl-2"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="d-flex justify-content-between"><span class="small text-body-secondary">{{ $label }}</span><i class="bi {{ $icon }} text-primary"></i></div><div class="h4 mt-2 mb-0">{{ is_numeric($value) ? number_format($value,0,',','.') : $value }}</div></div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        @foreach([['Dispositivos',$devices],['Navegadores',$browsers],['Sistemas operacionais',$operating_systems]] as [$title,$rows])
            <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><div class="card-body">@forelse($rows as $row)<div class="mb-3"><div class="d-flex justify-content-between small mb-1"><span>{{ ucfirst(str_replace('_',' ',$row->label)) }}</span><strong>{{ $row->total }} · {{ number_format($row->percentage,1,',','.') }}%</strong></div><div class="progress" role="progressbar" aria-valuenow="{{ $row->percentage }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $row->percentage }}%"></div></div></div>@empty<p class="text-body-secondary mb-0">Sem dados no período.</p>@endforelse</div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Evolução diária</div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Data</th><th class="text-end">Visitantes</th><th class="text-end">Novos</th><th class="text-end">Sessões</th></tr></thead><tbody>@foreach($daily as $row)<tr><td>{{ \Carbon\CarbonImmutable::parse($row->day)->format('d/m/Y') }}</td><td class="text-end">{{ $row->visitors }}</td><td class="text-end">{{ $row->new_visitors }}</td><td class="text-end">{{ $row->sessions }}</td></tr>@endforeach</tbody></table></div></div></div>
        <div class="col-xl-5"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Regiões do Brasil</div><div class="card-body">@php($maxRegion=max(1,(int)$brazil_regions->max('total')))@foreach($brazil_regions as $row)<div class="row align-items-center g-2 mb-3"><div class="col-4 small">{{ $row->label }}</div><div class="col"><div class="progress"><div class="progress-bar" style="width: {{ round($row->total/$maxRegion*100,1) }}%"></div></div></div><div class="col-auto fw-semibold">{{ $row->total }}</div></div>@endforeach<p class="small text-body-secondary mb-0">A região é calculada a partir da UF informada pela infraestrutura de geolocalização.</p></div></div></div>
    </div>

    <div class="row g-4 mb-4">
        @foreach([['Países',$countries,'label'],['Estados / regiões',$regions,'label'],['Cidades',$cities,'label']] as [$title,$rows,$field])
            <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Local</th><th class="text-end">Sessões</th><th class="text-end">Visitantes</th></tr></thead><tbody>@forelse($rows->take(12) as $row)<tr><td>{{ strtoupper($row->$field)==='UNKNOWN' ? 'Não identificado' : $row->$field }}</td><td class="text-end">{{ $row->total }}</td><td class="text-end">{{ $row->visitors }}</td></tr>@empty<tr><td colspan="3" class="text-body-secondary">Sem dados.</td></tr>@endforelse</tbody></table></div></div></div>
        @endforeach
    </div>

    <div class="row g-4">
        @foreach([['Idiomas',$languages],['Fusos horários',$timezones],['Resoluções de tela',$resolutions]] as [$title,$rows])
            <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><ul class="list-group list-group-flush">@forelse($rows as $row)<li class="list-group-item d-flex justify-content-between gap-3"><span class="text-truncate">{{ $row->label === 'unknown' ? 'Não identificado' : $row->label }}</span><strong>{{ $row->total }}</strong></li>@empty<li class="list-group-item text-body-secondary">Sem dados.</li>@endforelse</ul></div></div>
        @endforeach
    </div>
</div>
@endsection
