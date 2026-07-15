@extends('layouts.app')

@section('title', 'Aquisição | Analytics | Prazzu Tools')

@section('content')
@php
    $mediumLabels = [
        'organic' => 'Busca orgânica', 'ai' => 'Inteligência artificial', 'social' => 'Redes sociais',
        'referral' => 'Referências', 'campaign' => 'Campanhas', 'email' => 'Newsletter',
        'qr' => 'QR Code', 'none' => 'Direto', 'unknown' => 'Não identificado',
    ];
@endphp
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge text-bg-primary">Analytics 2.0</span>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.funnels') }}">Funis</a>
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard executivo</a>
            </div>
            <h1 class="h2 mb-1">Aquisição</h1>
            <p class="text-body-secondary mb-0">Origens, campanhas, atribuição e jornada até a conversão.</p>
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
                    <div class="col-sm-auto"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-1"></i>Aplicar</button></div>
                </div>
            </div>
        </form>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
    @endif

    <div class="row g-3 mb-4">
        @foreach([
            ['Sessões', $summary['sessions'], 'bi-window-stack'], ['Visitantes', $summary['visitors'], 'bi-people'],
            ['Origens', $summary['sources'], 'bi-signpost-split'], ['Campanhas', $summary['campaigns'], 'bi-megaphone'],
            ['Conversões', $summary['conversions'], 'bi-bullseye'], ['Taxa de conversão', number_format($summary['conversion_rate'], 1, ',', '.').'%', 'bi-graph-up-arrow'],
        ] as [$label, $value, $icon])
            <div class="col-6 col-xl-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="d-flex justify-content-between gap-2"><span class="small text-body-secondary">{{ $label }}</span><i class="bi {{ $icon }} text-primary"></i></div>
                    <div class="h4 mb-0 mt-2">{{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}</div>
                </div></div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">Canais de aquisição</div>
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>Canal</th><th class="text-end">Sessões</th><th class="text-end">Conversão</th></tr></thead>
                    <tbody>@forelse($channels as $channel)<tr>
                        <td><span class="badge text-bg-light border">{{ $mediumLabels[$channel->medium] ?? ucfirst($channel->medium) }}</span></td>
                        <td class="text-end">{{ number_format($channel->sessions, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>{{ number_format($channel->conversion_rate, 1, ',', '.') }}%</strong><div class="small text-body-secondary">{{ $channel->conversions }} eventos</div></td>
                    </tr>@empty<tr><td colspan="3" class="text-center text-body-secondary py-4">Sem dados no período.</td></tr>@endforelse</tbody>
                </table></div>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">Origens</div>
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>Origem</th><th>Canal</th><th class="text-end">Visitantes</th><th class="text-end">Sessões</th><th class="text-end">Conversão</th></tr></thead>
                    <tbody>@forelse($sources as $source)<tr>
                        <td class="fw-semibold">{{ $source->source }}</td><td>{{ $mediumLabels[$source->medium] ?? $source->medium }}</td>
                        <td class="text-end">{{ number_format($source->visitors, 0, ',', '.') }}</td><td class="text-end">{{ number_format($source->sessions, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($source->conversion_rate, 1, ',', '.') }}%</td>
                    </tr>@empty<tr><td colspan="5" class="text-center text-body-secondary py-4">Sem origens registradas.</td></tr>@endforelse</tbody>
                </table></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">Campanhas e UTMs</div>
        <div class="table-responsive"><table class="table align-middle mb-0">
            <thead><tr><th>Campanha</th><th>Origem</th><th>Meio</th><th class="text-end">Sessões</th><th class="text-end">Visitantes</th><th class="text-end">Conversão</th></tr></thead>
            <tbody>@forelse($campaigns as $campaign)<tr><td class="fw-semibold">{{ $campaign->campaign }}</td><td>{{ $campaign->source }}</td><td>{{ $campaign->medium }}</td><td class="text-end">{{ $campaign->sessions }}</td><td class="text-end">{{ $campaign->visitors }}</td><td class="text-end">{{ number_format($campaign->conversion_rate, 1, ',', '.') }}%</td></tr>
            @empty<tr><td colspan="6" class="text-center text-body-secondary py-4">Nenhuma campanha identificada.</td></tr>@endforelse</tbody>
        </table></div>
    </div>

    <div class="row g-4 mb-4">
        @foreach([
            ['Primeira origem', $first_touch, 'Visitantes'], ['Última origem', $last_touch, 'Visitantes'],
            ['Origem da conversão', $conversion_sources, 'Conversões'], ['Origem do cadastro', $registration_sources, 'Cadastros'],
            ['Origem da assinatura', $subscription_sources, 'Assinaturas'], ['Origem da exportação', $export_sources, 'Exportações'],
        ] as [$title, $rows, $totalLabel])
            <div class="col-md-6 col-xl-4"><div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold">{{ $title }}</div>
                <ul class="list-group list-group-flush">@forelse($rows->take(8) as $row)
                    <li class="list-group-item d-flex justify-content-between align-items-center"><span><strong>{{ $row->source }}</strong><small class="d-block text-body-secondary">{{ $mediumLabels[$row->medium] ?? $row->medium }}</small></span><span class="badge text-bg-primary rounded-pill">{{ $row->visitors ?? $row->total }}</span></li>
                @empty<li class="list-group-item text-center text-body-secondary py-4">Sem dados.</li>@endforelse</ul>
            </div></div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent"><div class="fw-semibold">Funis por origem</div><div class="small text-body-secondary">Visitantes únicos que avançaram em cada etapa durante o período.</div></div>
        <div class="card-body">
            <div class="accordion" id="acquisitionFunnels">
                @forelse($funnels as $funnel)
                    <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#funnel-{{ $loop->index }}"><strong>{{ $funnel['source'] }}</strong><span class="ms-2 badge text-bg-light border">{{ $mediumLabels[$funnel['medium']] ?? $funnel['medium'] }}</span></button></h2>
                    <div id="funnel-{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#acquisitionFunnels"><div class="accordion-body">
                        <div class="row g-3">@foreach($funnel['steps'] as $step)<div class="col-sm-6 col-lg"><div class="border rounded-3 p-3 h-100"><div class="small text-body-secondary">{{ $step['label'] }}</div><div class="h4 mb-1">{{ $step['visitors'] }}</div><div class="progress" role="progressbar" aria-valuenow="{{ $step['step_rate'] }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ min(100, $step['step_rate']) }}%">{{ number_format($step['step_rate'], 1, ',', '.') }}%</div></div></div></div>@endforeach</div>
                    </div></div></div>
                @empty<p class="text-center text-body-secondary mb-0">Não há dados suficientes para montar funis.</p>@endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('[data-period-select]')?.addEventListener('change', function () {
    document.querySelectorAll('[data-custom-period]').forEach((element) => element.classList.toggle('d-none', this.value !== 'custom'));
});
</script>
@endpush
