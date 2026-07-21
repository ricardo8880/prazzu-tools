@extends('layouts.app')
@section('title', 'Tempo Real | Analytics | Prazzu Tools')
@section('content')
@php
    $cards = [
        'online_users' => ['Usuários online', 'bi-broadcast-pin'],
        'open_pages' => ['Páginas abertas', 'bi-window-stack'],
        'open_tools' => ['Ferramentas em uso', 'bi-calculator'],
        'events_30m' => ['Eventos em 30 min', 'bi-lightning-charge'],
        'conversions_30m' => ['Conversões em 30 min', 'bi-bullseye'],
        'registrations_30m' => ['Cadastros em 30 min', 'bi-person-plus'],
        'exports_30m' => ['Exportações em 30 min', 'bi-download'],
        'identified_users' => ['Usuários identificados', 'bi-person-check'],
    ];
@endphp
<div class="container-fluid py-4" data-realtime-dashboard data-endpoint="{{ route('admin.analytics.realtime.data') }}">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge text-bg-danger"><span class="spinner-grow spinner-grow-sm me-1" aria-hidden="true"></span>Ao vivo</span>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.acquisition') }}">Aquisição</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.audience') }}">Público</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.tools') }}">Ferramentas</a>
            </div>
            <h1 class="h2 mb-1">Tempo real</h1>
            <p class="text-body-secondary mb-0">Atividade dos últimos {{ $activity_window_minutes }} minutos. Online considera atividade nos últimos {{ $online_window_minutes }} minutos.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small text-body-secondary">Atualizado às <strong data-generated-at>{{ $generated_at->format('H:i:s') }}</strong></span>
            <button class="btn btn-outline-primary btn-sm" type="button" data-refresh><i class="bi bi-arrow-clockwise me-1"></i>Atualizar</button>
        </div>
    </div>

    @include('admin.analytics.partials.page-guide', ['page' => 'realtime'])
    <div class="alert alert-warning d-none" role="alert" data-error><i class="bi bi-exclamation-triangle me-2"></i>Não foi possível atualizar os dados agora.</div>

    <div class="row g-3 mb-4">
        @foreach($cards as $key => [$label, $icon])
            <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="d-flex justify-content-between gap-3"><div><div class="small text-body-secondary">{{ $label }}</div><div class="h3 mb-0" data-summary="{{ $key }}">{{ number_format($summary[$key], 0, ',', '.') }}</div></div><span class="rounded-3 bg-primary-subtle text-primary p-3 align-self-start"><i class="bi {{ $icon }}"></i></span></div></div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent d-flex justify-content-between"><strong>Eventos recentes</strong><span class="badge text-bg-light border">até 50</span></div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Horário</th><th>Evento</th><th>Canal</th><th>Página / ferramenta</th><th>Origem</th></tr></thead><tbody data-events>@include('admin.analytics.partials.realtime-events', ['events' => $events])</tbody></table></div></div></div>
        <div class="col-xl-5"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Mapa de atividade</div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Local</th><th class="text-end">Online</th></tr></thead><tbody data-locations>@include('admin.analytics.partials.realtime-locations', ['locations' => $locations])</tbody></table></div><div class="card-footer bg-transparent small text-body-secondary">Localização agregada conforme consentimento e dados disponíveis.</div></div></div>
    </div>

    <div class="row g-4">
        @foreach([['Páginas abertas', 'pages', $pages], ['Ferramentas em uso', 'tools', $tools], ['Origens online', 'sources', $sources]] as [$title, $target, $rows])
            <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">{{ $title }}</div><ul class="list-group list-group-flush" data-list="{{ $target }}">@include('admin.analytics.partials.realtime-list', ['rows' => $rows])</ul></div></div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const root = document.querySelector('[data-realtime-dashboard]');
    if (!root) return;
    const number = new Intl.NumberFormat('pt-BR');
    const escape = (value) => String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
    const listHtml = rows => rows.length ? rows.map(row => `<li class="list-group-item d-flex justify-content-between gap-3"><span class="text-truncate" title="${escape(row.label)}">${escape(row.label || 'Não identificado')}</span><span class="badge text-bg-light border">${number.format(row.total)}</span></li>`).join('') : '<li class="list-group-item text-body-secondary">Sem atividade agora.</li>';
    const locationHtml = rows => rows.length ? rows.map(row => `<tr><td>${escape([row.city, row.region, row.country].filter(v => v && v !== 'unknown').join(' · ') || 'Não identificado')}</td><td class="text-end fw-semibold">${number.format(row.total)}</td></tr>`).join('') : '<tr><td colspan="2" class="text-body-secondary">Sem localização disponível.</td></tr>';
    const eventHtml = rows => rows.length ? rows.map(row => `<tr><td class="text-nowrap">${escape(row.occurred_at_label)}</td><td><code>${escape(row.event_name)}</code></td><td><span class="badge text-bg-light border">${escape(row.channel)}</span></td><td class="text-truncate" style="max-width:260px">${escape(row.subject_slug || row.path || '—')}</td><td>${escape(row.source || '—')}</td></tr>`).join('') : '<tr><td colspan="5" class="text-center text-body-secondary py-4">Nenhum evento recente.</td></tr>';
    let loading = false;
    async function refresh() {
        if (loading || document.hidden) return;
        loading = true;
        root.querySelector('[data-refresh]')?.setAttribute('disabled', 'disabled');
        try {
            const response = await fetch(root.dataset.endpoint, {headers: {'Accept':'application/json'}, credentials:'same-origin', cache:'no-store'});
            if (!response.ok) throw new Error('Falha ao atualizar');
            const data = await response.json();
            Object.entries(data.summary).forEach(([key, value]) => { const el = root.querySelector(`[data-summary="${key}"]`); if (el) el.textContent = number.format(value); });
            root.querySelector('[data-generated-at]').textContent = new Date(data.generated_at).toLocaleTimeString('pt-BR');
            ['pages','tools','sources'].forEach(key => root.querySelector(`[data-list="${key}"]`).innerHTML = listHtml(data[key]));
            root.querySelector('[data-locations]').innerHTML = locationHtml(data.locations);
            root.querySelector('[data-events]').innerHTML = eventHtml(data.events);
            root.querySelector('[data-error]').classList.add('d-none');
        } catch (_) { root.querySelector('[data-error]').classList.remove('d-none'); }
        finally { loading = false; root.querySelector('[data-refresh]')?.removeAttribute('disabled'); }
    }
    root.querySelector('[data-refresh]')?.addEventListener('click', refresh);
    setInterval(refresh, 5000);
})();
</script>
@endpush
