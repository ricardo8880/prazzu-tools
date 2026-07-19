@extends('layouts.app')
@section('title', 'Funis | Analytics | Prazzu Tools')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge text-bg-primary">Analytics 2.0</span>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.acquisition') }}">Aquisição</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.audience') }}">Público</a>
            </div>
            <h1 class="h2 mb-1">Funis</h1>
            <p class="text-body-secondary mb-0">Conversão sequencial entre eventos, com funis padrão e personalizados.</p>
        </div>
        <button class="btn btn-outline-primary align-self-start" data-bs-toggle="modal" data-bs-target="#newFunnel"><i class="bi bi-plus-lg me-1"></i>Novo funil</button>
    </div>

    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><strong>Não foi possível salvar.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form method="get" class="card border-0 shadow-sm mb-4"><div class="card-body"><div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label" for="funnel">Funil</label><select class="form-select" id="funnel" name="funnel">@foreach($funnels as $funnel)<option value="{{ $funnel->key }}" @selected($selected_funnel?->key === $funnel->key)>{{ $funnel->name }}{{ $funnel->custom ? ' · personalizado' : '' }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><label class="form-label" for="period">Período</label><select class="form-select" id="period" name="period">@foreach(['today'=>'Hoje','yesterday'=>'Ontem','7'=>'7 dias','30'=>'30 dias','90'=>'90 dias'] as $value=>$label)<option value="{{ $value }}" @selected($selected_period===$value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><label class="form-label" for="source">Origem</label><select class="form-select" id="source" name="source"><option value="">Todas</option>@foreach($sources as $source)<option value="{{ $source }}" @selected($filters['source']===$source)>{{ ucfirst($source) }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><label class="form-label" for="device_type">Dispositivo</label><select class="form-select" id="device_type" name="device_type"><option value="">Todos</option>@foreach(['desktop'=>'Desktop','mobile'=>'Mobile','tablet'=>'Tablet'] as $value=>$label)<option value="{{ $value }}" @selected($filters['device_type']===$value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Aplicar</button></div>
    </div></div></form>

    @if($selected_funnel && $result)
    <div class="card border-0 shadow-sm mb-4"><div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
        <div><div class="d-flex align-items-center gap-2"><h2 class="h4 mb-0">{{ $selected_funnel->name }}</h2>@if($selected_funnel->custom)<span class="badge text-bg-secondary">Personalizado</span>@endif</div><p class="text-body-secondary mb-0 mt-1">{{ $selected_funnel->description }}</p><small class="text-body-secondary">Identidade: {{ ['visitor'=>'visitante','session'=>'sessão','user'=>'usuário'][$selected_funnel->identity_type] ?? $selected_funnel->identity_type }}</small></div>
        @if($selected_funnel->custom)<form method="post" action="{{ route('admin.analytics.funnels.destroy', $selected_funnel->id) }}" onsubmit="return confirm('Remover este funil?')">@csrf @method('delete')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Remover</button></form>@endif
    </div></div>

    @include('admin.analytics.partials.page-guide', ['page' => 'funnels'])
    <div class="row g-3 mb-4">
        @foreach([['Entradas',$result['entrants'],'bi-box-arrow-in-right'],['Conclusões',$result['completed'],'bi-check2-circle'],['Conversão',number_format($result['conversion_rate'],1,',','.').' %','bi-graph-up-arrow'],['Identidades analisadas',$result['identities'],'bi-people']] as [$label,$value,$icon])
        <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="d-flex justify-content-between"><span class="small text-body-secondary">{{ $label }}</span><i class="bi {{ $icon }} text-primary"></i></div><div class="h4 mt-2 mb-0">{{ $value }}</div></div></div></div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm"><div class="card-header bg-transparent fw-semibold">Etapas do funil</div><div class="card-body">
        @forelse($result['steps'] as $index => $step)
        <div class="row align-items-center g-3 {{ !$loop->last ? 'mb-4' : '' }}">
            <div class="col-md-3"><div class="d-flex align-items-center gap-2"><span class="badge rounded-pill text-bg-primary">{{ $index + 1 }}</span><strong>{{ $step->name }}</strong></div><small class="text-body-secondary">{{ implode(', ', $step->events) }}</small></div>
            <div class="col-md-6"><div class="progress" style="height: 28px" role="progressbar" aria-valuenow="{{ $step->overall_rate }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $step->overall_rate }}%">{{ number_format($step->overall_rate,1,',','.') }}%</div></div></div>
            <div class="col-4 col-md-1 text-md-end"><strong>{{ $step->total }}</strong><div class="small text-body-secondary">pessoas</div></div>
            <div class="col-4 col-md-1 text-md-end"><strong>{{ number_format($step->step_rate,1,',','.') }}%</strong><div class="small text-body-secondary">da etapa</div></div>
            <div class="col-4 col-md-1 text-md-end"><strong>{{ $step->dropoff }}</strong><div class="small text-body-secondary">abandono</div></div>
        </div>
        @empty<p class="text-body-secondary mb-0">Este funil não possui etapas.</p>@endforelse
    </div></div>
    @else<div class="alert alert-info">Crie um funil para começar a análise.</div>@endif

    <div class="card border-0 shadow-sm mt-4"><div class="card-header bg-transparent fw-semibold">Comparação entre funis</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Funil</th><th class="text-end">Etapas</th><th class="text-end">Entradas</th><th class="text-end">Conclusões</th><th class="text-end">Conversão</th></tr></thead><tbody>@forelse($funnel_comparison as $row)<tr><td><a class="text-decoration-none fw-semibold" href="{{ request()->fullUrlWithQuery(['funnel' => $row->key]) }}">{{ $row->name }}</a>@if($row->custom) <span class="badge text-bg-secondary">Personalizado</span>@endif</td><td class="text-end">{{ $row->steps_count }}</td><td class="text-end">{{ $row->entrants }}</td><td class="text-end">{{ $row->completed }}</td><td class="text-end"><span class="badge {{ $row->conversion_rate > 0 ? 'text-bg-success' : 'text-bg-light border' }}">{{ number_format($row->conversion_rate,1,',','.') }}%</span></td></tr>@empty<tr><td colspan="5" class="text-body-secondary">Nenhum funil disponível.</td></tr>@endforelse</tbody></table></div></div>
</div>

<div class="modal fade" id="newFunnel" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg"><form method="post" action="{{ route('admin.analytics.funnels.store') }}" class="modal-content">@csrf
<div class="modal-header"><h2 class="modal-title fs-5">Novo funil personalizado</h2><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body"><div class="row g-3"><div class="col-md-8"><label class="form-label" for="name">Nome</label><input class="form-control" id="name" name="name" maxlength="120" required value="{{ old('name') }}"></div><div class="col-md-4"><label class="form-label" for="identity_type">Identidade</label><select class="form-select" id="identity_type" name="identity_type"><option value="visitor">Visitante</option><option value="session">Sessão</option><option value="user">Usuário</option></select></div><div class="col-12"><label class="form-label" for="description">Descrição</label><input class="form-control" id="description" name="description" maxlength="500" value="{{ old('description') }}"></div><div class="col-12"><label class="form-label" for="steps">Etapas</label><textarea class="form-control font-monospace" id="steps" name="steps" rows="7" required placeholder="Acessou artigo|blog.post.viewed,blog.reading.started&#10;Abriu ferramenta|tool.opened,blog.tool.clicked&#10;Concluiu cálculo|tool.calculation.completed&#10;Assinou Plus|subscription.started">{{ old('steps') }}</textarea><div class="form-text">Uma etapa por linha, no formato <code>Nome|evento,segundo.evento</code>. A ordem das linhas define a sequência.</div></div></div></div>
<div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary" type="submit">Criar funil</button></div></form></div></div>
@if($errors->any())<script>document.addEventListener('DOMContentLoaded',()=>bootstrap.Modal.getOrCreateInstance(document.getElementById('newFunnel')).show())</script>@endif
@endsection
