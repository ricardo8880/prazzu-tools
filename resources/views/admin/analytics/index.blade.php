@extends('layouts.app')

@section('title', 'Dashboard Executivo | Analytics | Prazzu Tools')

@section('content')
@php
    $metricCards = [
        'visitors' => ['Visitantes únicos', 'bi-people', 'number'],
        'sessions' => ['Sessões', 'bi-window-stack', 'number'],
        'page_views' => ['Páginas visitadas', 'bi-eye', 'number'],
        'users' => ['Usuários identificados', 'bi-person-badge', 'number'],
        'average_session_seconds' => ['Tempo médio', 'bi-stopwatch', 'duration'],
        'bounce_rate' => ['Bounce rate', 'bi-box-arrow-right', 'percent'],
        'conversions' => ['Conversões', 'bi-bullseye', 'number'],
        'registrations' => ['Cadastros', 'bi-person-plus', 'number'],
        'subscriptions' => ['Assinaturas', 'bi-stars', 'number'],
        'estimated_revenue_cents' => ['Receita estimada', 'bi-cash-coin', 'money'],
    ];

    $formatMetric = static function (int|float $value, string $format): string {
        return match ($format) {
            'percent' => number_format($value, 1, ',', '.').'%',
            'money' => 'R$ '.number_format($value / 100, 2, ',', '.'),
            'duration' => $value >= 3600
                ? sprintf('%dh %02dmin', intdiv((int) $value, 3600), intdiv(((int) $value) % 3600, 60))
                : sprintf('%dmin %02ds', intdiv((int) $value, 60), ((int) $value) % 60),
            default => number_format($value, 0, ',', '.'),
        };
    };
@endphp

<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row align-items-xl-start justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2"><span class="badge text-bg-primary">Analytics 2.0</span>
                <a class="badge text-bg-danger text-decoration-none" href="{{ route('admin.analytics.realtime') }}"><i class="bi bi-broadcast-pin me-1"></i>Tempo real</a><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.tools') }}">Ferramentas</a><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.acquisition') }}">Aquisição</a><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.campaigns') }}">Campanhas</a><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.audience') }}">Público</a><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.seo') }}">SEO</a><a class="badge text-bg-warning text-decoration-none" href="{{ route('admin.analytics.insights') }}"><i class="bi bi-lightbulb me-1"></i>Insights</a><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.funnels') }}">Funis</a><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.reports') }}">Relatórios</a></div>
            <h1 class="h2 mb-1">Dashboard executivo</h1>
            <p class="text-body-secondary mb-0">Visão geral da aquisição, audiência e conversão da plataforma.</p>
        </div>

        <form method="get" class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-sm-auto">
                        <label class="form-label small" for="period">Período</label>
                        <select class="form-select" id="period" name="period" data-period-select>
                            @foreach(['today' => 'Hoje', 'yesterday' => 'Ontem', '7' => 'Últimos 7 dias', '30' => 'Últimos 30 dias', '90' => 'Últimos 90 dias', 'custom' => 'Personalizado'] as $value => $label)
                                <option value="{{ $value }}" @selected($selected_period === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-auto {{ $selected_period === 'custom' ? '' : 'd-none' }}" data-custom-period>
                        <label class="form-label small" for="start">De</label>
                        <input class="form-control" id="start" name="start" type="date" value="{{ request('start', $period->start->toDateString()) }}">
                    </div>
                    <div class="col-sm-auto {{ $selected_period === 'custom' ? '' : 'd-none' }}" data-custom-period>
                        <label class="form-label small" for="end">Até</label>
                        <input class="form-control" id="end" name="end" type="date" value="{{ request('end', $period->end->toDateString()) }}">
                    </div>
                    <div class="col-sm-auto">
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Aplicar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="badge text-bg-light border">Período atual: {{ $period->label() }}</span>
        <span class="badge text-bg-light border">Comparação: {{ $previous_period->label() }}</span>
    </div>

    @include('admin.analytics.partials.page-guide', ['page' => 'dashboard'])
    <div class="row g-3 mb-4">
        @foreach([
            ['hoje', $visitor_snapshots['today']],
            ['ontem', $visitor_snapshots['yesterday']],
            ['nos últimos 7 dias', $visitor_snapshots['last_7_days']],
            ['nos últimos 30 dias', $visitor_snapshots['last_30_days']],
        ] as [$label, $value])
            <div class="col-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="small text-body-secondary">Visitantes {{ $label }}</div>
                        <div class="h3 mb-0">{{ number_format($value, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        @foreach($metricCards as $key => [$label, $icon, $format])
            @php($metric = $metrics[$key])
            <div class="col-sm-6 col-xl-4 col-xxl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="small text-body-secondary mb-1">{{ $label }}</div>
                                <div class="h3 mb-1">{{ $formatMetric($metric['value'], $format) }}</div>
                            </div>
                            <span class="rounded-3 bg-primary-subtle text-primary p-3"><i class="bi {{ $icon }} fs-5"></i></span>
                        </div>
                        <div class="small mt-2">
                            @if($metric['change'] === null)
                                <span class="text-body-secondary"><i class="bi bi-dash-circle me-1"></i>Sem base comparável</span>
                            @elseif($metric['change'] > 0)
                                <span class="text-success"><i class="bi bi-arrow-up-right me-1"></i>{{ number_format($metric['change'], 1, ',', '.') }}%</span>
                            @elseif($metric['change'] < 0)
                                <span class="text-danger"><i class="bi bi-arrow-down-right me-1"></i>{{ number_format(abs($metric['change']), 1, ',', '.') }}%</span>
                            @else
                                <span class="text-body-secondary"><i class="bi bi-dash me-1"></i>Sem alteração</span>
                            @endif
                            <span class="text-body-secondary ms-1">vs. período anterior</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex flex-column flex-md-row justify-content-between gap-2">
            <div>
                <div class="fw-semibold">Evolução diária</div>
                <div class="small text-body-secondary">Visualizações, sessões e conversões no período selecionado.</div>
            </div>
            <div class="d-flex flex-wrap gap-3 small">
                <span><span class="badge text-bg-primary">&nbsp;</span> Visualizações</span>
                <span><span class="badge text-bg-info">&nbsp;</span> Sessões</span>
                <span><span class="badge text-bg-success">&nbsp;</span> Conversões</span>
            </div>
        </div>
        <div class="card-body">
            @php($chartMaximum = max(1, (int) $daily->max(fn ($day) => max($day->page_views, $day->sessions, $day->conversions))))
            <div class="d-flex align-items-end gap-2 overflow-auto" style="min-height: 260px">
                @foreach($daily as $day)
                    <div class="text-center flex-grow-1" style="min-width: {{ $period->days() > 31 ? '24px' : '52px' }}">
                        <div class="d-flex align-items-end justify-content-center gap-1" style="height: 190px">
                            <div class="bg-primary rounded-top" title="{{ $day->page_views }} visualizações" style="height: {{ max(3, (int) round(($day->page_views / $chartMaximum) * 180)) }}px; width: 28%"></div>
                            <div class="bg-info rounded-top" title="{{ $day->sessions }} sessões" style="height: {{ max(3, (int) round(($day->sessions / $chartMaximum) * 180)) }}px; width: 28%"></div>
                            <div class="bg-success rounded-top" title="{{ $day->conversions }} conversões" style="height: {{ max(3, (int) round(($day->conversions / $chartMaximum) * 180)) }}px; width: 28%"></div>
                        </div>
                        @if($period->days() <= 31 || $loop->first || $loop->last || $loop->iteration % 7 === 0)
                            <div class="small text-body-secondary mt-2 text-nowrap">{{ \Carbon\CarbonImmutable::parse($day->date)->format('d/m') }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">Principais origens</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Origem</th><th class="text-end">Visitantes</th><th class="text-end">Sessões</th></tr></thead>
                        <tbody>
                        @forelse($top_sources as $source)
                            <tr>
                                <td><span class="badge text-bg-light border">{{ $source->source }}</span></td>
                                <td class="text-end">{{ number_format($source->visitors, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($source->sessions, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-body-secondary py-4">Ainda não há dados de origem.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">Páginas mais visitadas</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Página</th><th class="text-end">Visitantes</th><th class="text-end">Views</th></tr></thead>
                        <tbody>
                        @forelse($top_pages as $page)
                            <tr>
                                <td class="text-truncate" style="max-width: 330px" title="{{ $page->path }}">{{ $page->path }}</td>
                                <td class="text-end">{{ number_format($page->visitors, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($page->views, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-body-secondary py-4">Ainda não há visualizações.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">Eventos recentes do período</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Evento</th><th>Canal</th><th>Página</th><th>Origem</th><th class="text-end">Horário</th></tr></thead>
                <tbody>
                @forelse($recent_events as $event)
                    <tr>
                        <td><code>{{ $event->event_name }}</code></td>
                        <td><span class="badge text-bg-light border">{{ $event->channel }}</span></td>
                        <td class="text-truncate" style="max-width: 320px">{{ $event->path ?: '—' }}</td>
                        <td>{{ $event->source ?: '—' }}</td>
                        <td class="text-end text-nowrap">{{ $event->occurred_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-body-secondary py-4">Nenhum evento registrado no período.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('[data-period-select]')?.addEventListener('change', function () {
        document.querySelectorAll('[data-custom-period]').forEach(function (element) {
            element.classList.toggle('d-none', this.value !== 'custom');
        }, this);
    });
</script>
@endpush
