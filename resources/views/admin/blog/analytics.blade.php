@extends('layouts.app')
@section('title', 'Analytics do blog | Prazzu Tools')
@section('content')
@php
$formatDuration = static fn (int $seconds): string => $seconds >= 3600 ? sprintf('%dh %02dmin', intdiv($seconds, 3600), intdiv($seconds % 3600, 60)) : sprintf('%dmin %02ds', intdiv($seconds, 60), $seconds % 60);
@endphp
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div><span class="badge text-bg-primary mb-2">Analytics 2.0</span><h1 class="h2 mb-1">Analytics do blog</h1><p class="text-body-secondary mb-0">Desempenho editorial, engajamento e conversão dos artigos.</p></div>
        <div class="d-flex flex-column flex-md-row gap-2 align-items-md-start">
            <a class="btn btn-outline-secondary" href="{{ route('admin.analytics.index') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard executivo</a><a class="btn btn-outline-primary" href="{{ route('admin.analytics.seo') }}"><i class="bi bi-search me-1"></i>SEO Analytics</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.blog.posts.index') }}">Postagens</a>
        </div>
    </div>

    <form method="get" class="card border-0 shadow-sm mb-4"><div class="card-body"><div class="row g-2 align-items-end">
        <div class="col-sm-auto"><label class="form-label small" for="period">Período</label><select class="form-select" id="period" name="period" data-period-select>@foreach(['today'=>'Hoje','yesterday'=>'Ontem','7'=>'Últimos 7 dias','30'=>'Últimos 30 dias','90'=>'Últimos 90 dias','custom'=>'Personalizado'] as $value=>$label)<option value="{{ $value }}" @selected($selected_period === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-sm-auto {{ $selected_period === 'custom' ? '' : 'd-none' }}" data-custom-period><label class="form-label small" for="start">De</label><input class="form-control" id="start" name="start" type="date" value="{{ request('start', $period->start->toDateString()) }}"></div>
        <div class="col-sm-auto {{ $selected_period === 'custom' ? '' : 'd-none' }}" data-custom-period><label class="form-label small" for="end">Até</label><input class="form-control" id="end" name="end" type="date" value="{{ request('end', $period->end->toDateString()) }}"></div>
        <div class="col-sm-auto"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Aplicar</button></div>
    </div></div></form>

    <div class="row g-3 mb-4">
        @foreach([
            ['Visualizações',$totals['views'],'bi-eye'],['Visitantes únicos',$totals['unique_visitors'],'bi-people'],['Leituras concluídas',$totals['reading_completions'],'bi-check2-circle'],['Cliques em ferramentas',$totals['tool_clicks'],'bi-tools'],['CTR',number_format($totals['ctr'],1,',','.').'%', 'bi-cursor'],['Cadastros',$totals['registrations'],'bi-person-plus'],['Assinaturas',$totals['subscriptions'],'bi-stars'],['Compartilhamentos',$totals['shares'],'bi-share']
        ] as [$label,$value,$icon])
        <div class="col-6 col-lg-3"><div class="card h-100 border-0 shadow-sm"><div class="card-body d-flex justify-content-between gap-2"><div><div class="small text-body-secondary">{{ $label }}</div><div class="h3 mb-0">{{ is_numeric($value) ? number_format($value,0,',','.') : $value }}</div></div><span class="text-primary bg-primary-subtle rounded-3 p-3 align-self-start"><i class="bi {{ $icon }}"></i></span></div></div></div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-transparent"><strong>Evolução diária</strong><div class="small text-body-secondary">Visualizações, visitantes e cliques em ferramentas.</div></div><div class="card-body">
        @php($maximum=max(1,(int)$daily->max(fn($day)=>max($day->views,$day->visitors,$day->tool_clicks))))
        <div class="d-flex align-items-end gap-2 overflow-auto" style="min-height:230px">@foreach($daily as $day)<div class="text-center flex-grow-1" style="min-width:{{ $period->days()>31?'24px':'48px' }}"><div class="d-flex align-items-end justify-content-center gap-1" style="height:170px"><div class="bg-primary rounded-top" title="{{ $day->views }} visualizações" style="height:{{ max(3,round($day->views/$maximum*160)) }}px;width:28%"></div><div class="bg-info rounded-top" title="{{ $day->visitors }} visitantes" style="height:{{ max(3,round($day->visitors/$maximum*160)) }}px;width:28%"></div><div class="bg-success rounded-top" title="{{ $day->tool_clicks }} cliques" style="height:{{ max(3,round($day->tool_clicks/$maximum*160)) }}px;width:28%"></div></div>@if($period->days()<=31 || $loop->first || $loop->last || $loop->iteration%7===0)<small class="text-body-secondary">{{ \Carbon\CarbonImmutable::parse($day->date)->format('d/m') }}</small>@endif</div>@endforeach</div>
    </div></div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-transparent d-flex justify-content-between"><strong>Desempenho por artigo</strong><span class="badge text-bg-light border">{{ $posts->count() }} artigos</span></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Artigo</th><th class="text-end">Views</th><th class="text-end">Tempo médio</th><th class="text-end">Leitura</th><th class="text-end">CTR</th><th class="text-end">Conversão</th><th></th></tr></thead><tbody>
        @forelse($posts as $item)<tr><td><div class="fw-semibold">{{ $item->title }}</div><small class="text-body-secondary">{{ $item->category }} · {{ $item->author ?: 'Sem autor' }}</small></td><td class="text-end">{{ number_format($item->views,0,',','.') }}</td><td class="text-end">{{ $formatDuration($item->average_time_seconds) }}</td><td class="text-end">{{ number_format($item->reading_rate,1,',','.') }}%</td><td class="text-end">{{ number_format($item->ctr,1,',','.') }}%</td><td class="text-end">{{ number_format($item->conversion_rate,1,',','.') }}%</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.blog.analytics.posts.show',$item->id) }}">Detalhes</a></td></tr>@empty<tr><td colspan="7" class="text-center py-4 text-body-secondary">Nenhum artigo encontrado.</td></tr>@endforelse
    </tbody></table></div></div>

    <div class="row g-4 mb-4">
        @foreach([['Mais lidos','most_read','views','Views'],['Mais compartilhados','most_shared','shares','Compart.'],['Maior tempo médio','longest_reading','average_time_seconds','Tempo'],['Maior conversão','highest_conversion','conversion_rate','Conversão'],['Maior abandono','highest_abandonment','abandonment_rate','Abandono']] as [$title,$key,$field,$label])
        <div class="col-lg-6 col-xxl-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><div class="list-group list-group-flush">@forelse($rankings[$key] as $item)<a href="{{ route('admin.blog.analytics.posts.show',$item->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between gap-3"><span class="text-truncate">{{ $item->title }}</span><strong class="text-nowrap">@if($field==='average_time_seconds'){{ $formatDuration($item->$field) }}@elseif(in_array($field,['conversion_rate','abandonment_rate'])){{ number_format($item->$field,1,',','.') }}%@else{{ number_format($item->$field,0,',','.') }}@endif</strong></a>@empty<div class="p-3 text-body-secondary">Sem dados.</div>@endforelse</div></div></div>
        @endforeach
    </div>

    <div class="row g-4">
        @foreach([['Categorias',$categories],['Autores',$authors],['Tags',$tags]] as [$title,$items])<div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Nome</th><th class="text-end">Artigos</th><th class="text-end">Views</th></tr></thead><tbody>@forelse($items->take(15) as $item)<tr><td>{{ $item->name }}</td><td class="text-end">{{ $item->posts }}</td><td class="text-end fw-semibold">{{ number_format($item->views,0,',','.') }}</td></tr>@empty<tr><td colspan="3" class="text-center text-body-secondary py-3">Sem dados.</td></tr>@endforelse</tbody></table></div></div></div>@endforeach
    </div>

    @if($rankings['never_accessed']->isNotEmpty() || $rankings['needs_update']->isNotEmpty())<div class="row g-4 mt-1"><div class="col-lg-6"><div class="card border-warning-subtle h-100"><div class="card-header bg-warning-subtle fw-semibold">Nunca acessados</div><div class="list-group list-group-flush">@forelse($rankings['never_accessed'] as $item)<a class="list-group-item list-group-item-action" href="{{ route('admin.blog.analytics.posts.show',$item->id) }}">{{ $item->title }}</a>@empty<div class="p-3 text-body-secondary">Todos os artigos receberam acesso.</div>@endforelse</div></div></div><div class="col-lg-6"><div class="card border-warning-subtle h-100"><div class="card-header bg-warning-subtle fw-semibold">Precisam de atualização</div><div class="list-group list-group-flush">@forelse($rankings['needs_update'] as $item)<a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('admin.blog.posts.edit',$item->id) }}"><span>{{ $item->title }}</span><small>{{ optional($item->updated_at)->format('d/m/Y') }}</small></a>@empty<div class="p-3 text-body-secondary">Nenhum artigo antigo.</div>@endforelse</div></div></div></div>@endif
</div>
@endsection
@push('scripts')<script>document.querySelector('[data-period-select]')?.addEventListener('change',function(){document.querySelectorAll('[data-custom-period]').forEach(element=>element.classList.toggle('d-none',this.value!=='custom'));});</script>@endpush
