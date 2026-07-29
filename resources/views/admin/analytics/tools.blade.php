@extends('layouts.app')
@section('title','Ferramentas | Analytics | Prazzu Tools')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex gap-2 mb-2"><span class="badge text-bg-primary">Analytics de Produto</span><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard executivo</a></div>
            <h1 class="h2 mb-1">Analytics das Ferramentas</h1>
            <p class="text-body-secondary mb-0">Jornada declarada, conclusão, abandono, erros, tempos e comparações.</p>
        </div>
    </div>

    @include('admin.analytics.partials.page-guide', ['page' => 'tools'])

    <form method="get" class="card border-0 shadow-sm mb-4">
        <div class="card-body"><div class="row g-3 align-items-end">
            <div class="col-md-2"><label class="form-label small">Período</label><select class="form-select" name="period">@foreach(['today'=>'Hoje','yesterday'=>'Ontem','7'=>'7 dias','30'=>'30 dias','90'=>'90 dias'] as $v=>$l)<option value="{{ $v }}" @selected($selected_period===$v)>{{ $l }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label small">Ferramenta</label><select class="form-select" name="tool"><option value="">Todas</option>@foreach($filter_options['tools'] as $item)<option value="{{ $item['slug'] }}" @selected(($filters['tool'] ?? '') === $item['slug'])>{{ $item['name'] }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small">Categoria</label><select class="form-select" name="category"><option value="">Todas</option>@foreach($filter_options['categories'] as $item)<option @selected(($filters['category'] ?? '') === $item)>{{ $item }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small">Origem</label><select class="form-select" name="source"><option value="">Todas</option>@foreach($filter_options['sources'] as $item)<option @selected(($filters['source'] ?? '') === $item)>{{ $item }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small">Dispositivo</label><select class="form-select" name="device_type"><option value="">Todos</option>@foreach($filter_options['devices'] as $item)<option @selected(($filters['device_type'] ?? '') === $item)>{{ $item }}</option>@endforeach</select></div>
            <div class="col-md-1"><button class="btn btn-primary w-100" type="submit">Aplicar</button></div>
        </div></div>
    </form>

    <div class="row g-3 mb-4">
        @foreach([['Ferramentas',$summary['tools']],['Aberturas',$summary['opens']],['Resultados',$summary['results']],['Cálculos',$summary['calculations']],['Abandonos',$summary['abandonments']],['Erros',$summary['errors']],['Exportações',$summary['exports']],['Partilhas',$summary['shares']]] as [$label,$value])
            <div class="col-6 col-lg-3 col-xxl"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-body-secondary">{{ $label }}</div><div class="h4 mt-2 mb-0">{{ number_format($value,0,',','.') }}</div></div></div></div>
        @endforeach
    </div>

    @if($alerts->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-transparent fw-semibold">Alertas automáticos</div><div class="list-group list-group-flush">@foreach($alerts as $alert)<div class="list-group-item"><span class="badge text-bg-{{ $alert->severity }} me-2">Alerta</span><strong>{{ $alert->tool }}</strong> — {{ $alert->message }}</div>@endforeach</div></div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">Desempenho por ferramenta</div>
        <div class="table-responsive"><table class="table table-sm align-middle mb-0">
            <thead><tr><th>Ferramenta</th><th class="text-end">Aberturas</th><th class="text-end">Tendência</th><th class="text-end">Conclusão</th><th class="text-end">Período anterior</th><th class="text-end">Abandono</th><th class="text-end">Erros</th><th class="text-end">Média cálculo</th><th class="text-end">Mediana</th><th class="text-end">P95</th></tr></thead>
            <tbody>@forelse($tools as $row)<tr>
                <td><a class="fw-semibold text-decoration-none" href="{{ route('admin.analytics.tools.show',['tool'=>$row->slug,'period'=>$selected_period]) }}">{{ $row->name }}</a><div class="small text-body-secondary">{{ $row->slug }}</div></td>
                <td class="text-end">{{ $row->opens }}</td><td class="text-end">{{ $row->opens_trend === null ? 'novo' : number_format($row->opens_trend,1,',','.').'%' }}</td>
                <td class="text-end"><span class="badge text-bg-success">{{ number_format($row->completion_rate,1,',','.') }}%</span></td><td class="text-end">{{ number_format($row->previous_completion_rate,1,',','.') }}%</td>
                <td class="text-end"><span class="badge text-bg-{{ $row->abandonment_rate > 40 ? 'danger':'secondary' }}">{{ number_format($row->abandonment_rate,1,',','.') }}%</span></td>
                <td class="text-end">{{ $row->errors }}</td><td class="text-end">{{ number_format($row->average_calculation_seconds,1,',','.') }}s</td><td class="text-end">{{ number_format($row->median_calculation_seconds,1,',','.') }}s</td><td class="text-end">{{ number_format($row->p95_calculation_seconds,1,',','.') }}s</td>
            </tr>@empty<tr><td colspan="10" class="text-center text-body-secondary py-4">Sem dados.</td></tr>@endforelse</tbody>
        </table></div>
    </div>

    <div class="row g-4 mb-4">
        @foreach([['Mais utilizadas',$rankings['most_opened'],'opens',false],['Maior conclusão',$rankings['highest_completion'],'completion_rate',true],['Maior abandono',$rankings['highest_abandonment'],'abandonment_rate',true],['Mais erros',$rankings['most_errors'],'errors',false]] as [$title,$rows,$field,$percent])
        <div class="col-lg-3"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><ul class="list-group list-group-flush">@forelse($rows as $row)<li class="list-group-item d-flex justify-content-between gap-2"><span>{{ $row->name }}</span><strong>{{ number_format($row->$field,$percent?1:0,',','.') }}{{ $percent?'%':'' }}</strong></li>@empty<li class="list-group-item text-body-secondary">Sem dados.</li>@endforelse</ul></div></div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-xl-7"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Campos mais problemáticos</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Ferramenta</th><th>Campo</th><th>Etapa</th><th class="text-end">Erros</th><th class="text-end">Abandonos</th></tr></thead><tbody>@forelse($problem_fields as $row)<tr><td>{{ $row->tool }}</td><td><code>{{ $row->field }}</code></td><td>{{ $row->step }}</td><td class="text-end">{{ $row->errors }}</td><td class="text-end">{{ $row->abandonments }}</td></tr>@empty<tr><td colspan="5" class="text-center text-body-secondary py-4">Sem ocorrências.</td></tr>@endforelse</tbody></table></div></div></div>
        <div class="col-xl-5"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Etapas com maior abandono</div><ul class="list-group list-group-flush">@forelse($dropoff_steps as $row)<li class="list-group-item d-flex justify-content-between"><span><code>{{ $row->step }}</code></span><strong>{{ $row->total }} ({{ number_format($row->percentage,1,',','.') }}%)</strong></li>@empty<li class="list-group-item text-body-secondary">Sem abandonos.</li>@endforelse</ul></div></div>
    </div>
</div>
@endsection
