@extends('layouts.app')

@section('title', 'Relatórios — Analytics')

@section('content')
@php
    $selected = fn (string $key, mixed $value): bool => (string) ($filters[$key] ?? '') === (string) $value;
    $query = array_filter(request()->except('format'), fn ($value) => $value !== null && $value !== '');
    $eventCatalog = app(\App\Core\Analytics\Domain\Catalog\AnalyticsEventCatalog::class);
    $eventGroups = $eventCatalog->groupedOptions($dimensions['events']);
@endphp
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard</a>
                <span class="badge text-bg-primary">Relatórios</span>
            </div>
            <h1 class="h2 mb-1">Relatórios do Analytics</h1>
            <p class="text-body-secondary mb-0">Filtre, compare, exporte e programe relatórios recorrentes.</p>
        </div>
        <div class="d-flex flex-column gap-2 align-self-xl-start">
            <div class="small fw-semibold text-body-secondary">Relatório estratégico para análise por IA</div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="{{ route('admin.analytics.reports.export', $query + ['format' => 'package']) }}"><i class="bi bi-file-earmark-zip me-1"></i>Pacote completo</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.analytics.reports.export', $query + ['format' => 'package_summary']) }}"><i class="bi bi-file-zip me-1"></i>Pacote resumido</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.analytics.reports.export', $query + ['format' => 'markdown']) }}"><i class="bi bi-markdown me-1"></i>Markdown</a>
                <a class="btn btn-outline-primary" href="{{ route('admin.analytics.reports.export', $query + ['format' => 'json']) }}"><i class="bi bi-braces me-1"></i>JSON</a>
            </div>
            <div class="small text-body-secondary">O pacote completo inclui eventos brutos com todos os campos e CSVs especializados de todas as dimensões; o resumido contém apenas contexto, métricas, insights e dicionário.</div>
            <div class="small fw-semibold text-body-secondary mt-1">Exportação de dados brutos</div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.analytics.reports.export', $query + ['format' => 'csv']) }}"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
                <a class="btn btn-outline-success" href="{{ route('admin.analytics.reports.export', $query + ['format' => 'excel']) }}"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
                <a class="btn btn-outline-danger" href="{{ route('admin.analytics.reports.export', $query + ['format' => 'pdf']) }}"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            </div>
        </div>
    </div>

    @include('admin.analytics.partials.page-guide', ['page' => 'reports'])
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">Filtros avançados</div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3"><label class="form-label" for="period">Período</label><select class="form-select" id="period" name="period" data-period-select>@foreach(['today'=>'Hoje','yesterday'=>'Ontem','7'=>'7 dias','30'=>'30 dias','90'=>'90 dias','custom'=>'Personalizado'] as $value=>$label)<option value="{{ $value }}" @selected($selected_period===$value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-md-3 {{ $selected_period === 'custom' ? '' : 'd-none' }}" data-custom-period><label class="form-label" for="start">De</label><input class="form-control" type="date" id="start" name="start" value="{{ request('start', $period->start->toDateString()) }}"></div>
                <div class="col-md-3 {{ $selected_period === 'custom' ? '' : 'd-none' }}" data-custom-period><label class="form-label" for="end">Até</label><input class="form-control" type="date" id="end" name="end" value="{{ request('end', $period->end->toDateString()) }}"></div>
                @foreach([
                    'channel'=>['Canal',$dimensions['channels']], 'source'=>['Origem',$dimensions['sources']],
                    'category'=>['Categoria',$dimensions['categories']], 'author_id'=>['Autor',$dimensions['authors']],
                    'tool'=>['Ferramenta',$dimensions['tools']], 'device_type'=>['Dispositivo',$dimensions['devices']],
                    'operating_system'=>['Sistema operacional',$dimensions['operating_systems']], 'region'=>['Estado',$dimensions['regions']],
                    'city'=>['Cidade',$dimensions['cities']],
                ] as $name=>[$label,$options])
                    <div class="col-md-3"><label class="form-label" for="{{ $name }}">{{ $label }}</label><select class="form-select" id="{{ $name }}" name="{{ $name }}"><option value="">Todos</option>@foreach($options as $key=>$option)
                        @php
                            $value = $name === 'author_id' ? $key : $option;
                        @endphp
                        <option value="{{ $value }}" @selected($selected($name,$value))>{{ $option }}</option>@endforeach</select></div>
                @endforeach
                <div class="col-md-3">
                    <label class="form-label" for="event_name">Evento</label>
                    <select class="form-select" id="event_name" name="event_name">
                        <option value="">Todos</option>
                        @foreach($eventGroups as $category => $events)
                            <optgroup label="{{ $category }}">
                                @foreach($events as $event)
                                    <option value="{{ $event['value'] }}" title="{{ $event['description'] }}" @selected($selected('event_name', $event['value']))>{{ $event['label'] }} — {{ $event['technical_name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <div class="form-text">Nome amigável seguido do identificador técnico.</div>
                </div>
                <div class="col-md-3"><label class="form-label" for="user_id">ID do usuário</label><input class="form-control" id="user_id" name="user_id" type="number" min="1" value="{{ $filters['user_id'] ?? '' }}"></div>
                <div class="col-12 d-flex flex-wrap gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Aplicar</button><a class="btn btn-outline-secondary" href="{{ route('admin.analytics.reports') }}">Limpar</a></div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach(['events'=>'Eventos','visitors'=>'Visitantes','sessions'=>'Sessões','users'=>'Usuários','conversions'=>'Conversões'] as $key=>$label)
            @php
                $metric = $summary[$key];
            @endphp
            <div class="col-6 col-lg"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="small text-body-secondary">{{ $label }}</div><div class="h3 mb-1">{{ number_format($metric['value'],0,',','.') }}</div><div class="small {{ ($metric['change'] ?? 0) > 0 ? 'text-success' : (($metric['change'] ?? 0) < 0 ? 'text-danger' : 'text-body-secondary') }}">{{ $metric['change'] === null ? 'Sem base comparável' : number_format($metric['change'],1,',','.').'%' }} vs. anterior</div></div></div></div>
        @endforeach
    </div>

    @php
        $reportSections = [
            'Aquisição e campanhas' => [
                'Canais' => $complete_report['channel_breakdown'] ?? [],
                'Origens' => $complete_report['source_breakdown'] ?? [],
                'Mídias' => $complete_report['medium_breakdown'] ?? [],
                'Campanhas' => $complete_report['campaign_breakdown'] ?? [],
                'Referenciadores' => $complete_report['referrer_breakdown'] ?? [],
            ],
            'Conteúdo e produto' => [
                'Ferramentas' => $complete_report['tool_breakdown'] ?? [],
                'Páginas' => $complete_report['path_breakdown'] ?? [],
                'Tipos de objeto' => $complete_report['subject_type_breakdown'] ?? [],
                'Contextos de aquisição' => $complete_report['acquisition_context_breakdown'] ?? [],
            ],
            'Tecnologia' => [
                'Dispositivos' => $complete_report['device_breakdown'] ?? [],
                'Navegadores' => $complete_report['browser_breakdown'] ?? [],
                'Sistemas operacionais' => $complete_report['os_breakdown'] ?? [],
                'Idiomas' => $complete_report['language_breakdown'] ?? [],
            ],
            'Geografia' => [
                'Países' => $complete_report['country_breakdown'] ?? [],
                'Estados e regiões' => $complete_report['region_breakdown'] ?? [],
                'Cidades' => $complete_report['city_breakdown'] ?? [],
            ],
        ];
    @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex flex-column flex-lg-row justify-content-between gap-2">
            <div><div class="fw-semibold">Cobertura completa do relatório</div><div class="small text-body-secondary">A tela resume os dados em seções recolhíveis. O pacote completo exporta todas as tabelas abaixo, a série temporal, qualidade dos dados e os eventos brutos com todos os campos.</div></div>
            <span class="badge text-bg-success align-self-lg-center">{{ count($reportSections) }} grupos analíticos</span>
        </div>
        <div class="accordion accordion-flush" id="completeAnalyticsReport">
            @foreach($reportSections as $sectionTitle => $tables)
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#report-section-{{ $loop->index }}">{{ $sectionTitle }}</button></h2>
                    <div id="report-section-{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#completeAnalyticsReport">
                        <div class="accordion-body">
                            <div class="row g-4">
                                @foreach($tables as $tableTitle => $tableRows)
                                    <div class="col-xl-6">
                                        <h3 class="h6">{{ $tableTitle }}</h3>
                                        <div class="table-responsive border rounded-3" style="max-height: 360px">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead class="sticky-top"><tr><th>Dimensão</th><th class="text-end">Eventos</th><th class="text-end">Visitantes</th><th class="text-end">Conversões</th></tr></thead>
                                                <tbody>
                                                @forelse(array_slice($tableRows, 0, 100) as $item)
                                                    <tr><td class="text-break">{{ ($item['name'] ?? '') !== '' ? $item['name'] : 'Não informado' }}</td><td class="text-end">{{ number_format($item['events'] ?? 0,0,',','.') }}</td><td class="text-end">{{ number_format($item['visitors'] ?? 0,0,',','.') }}</td><td class="text-end">{{ number_format($item['conversions'] ?? 0,0,',','.') }}</td></tr>
                                                @empty
                                                    <tr><td colspan="4" class="text-center text-body-secondary py-3">Sem dados no período.</td></tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#report-quality">Qualidade e completude dos dados</button></h2>
                <div id="report-quality" class="accordion-collapse collapse" data-bs-parent="#completeAnalyticsReport"><div class="accordion-body">
                    <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Campo</th><th class="text-end">Preenchidos</th><th class="text-end">Ausentes</th><th class="text-end">Cobertura</th></tr></thead><tbody>
                    @foreach(($complete_report['data_quality']['fields'] ?? []) as $field => $quality)<tr><td><code>{{ $field }}</code></td><td class="text-end">{{ number_format($quality['present'],0,',','.') }}</td><td class="text-end">{{ number_format($quality['missing'],0,',','.') }}</td><td class="text-end">{{ number_format($quality['coverage_rate'],1,',','.') }}%</td></tr>@endforeach
                    </tbody></table></div>
                </div></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between"><span class="fw-semibold">Eventos encontrados</span><span class="badge text-bg-light border">{{ number_format($total_rows,0,',','.') }} registros</span></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Data/hora</th><th>Evento</th><th>Canal</th><th>Objeto</th><th>Origem</th><th>Dispositivo</th><th>Localização</th><th>Página</th></tr></thead><tbody>
        @forelse($rows as $row)
            @php
                $eventDefinition = $eventCatalog->describe($row->event_name);
            @endphp
            <tr><td class="text-nowrap">{{ $row->occurred_at?->format('d/m/Y H:i') }}</td><td><div class="fw-semibold" title="{{ $eventDefinition['description'] }} {{ $eventDefinition['business_meaning'] }}">{{ $eventDefinition['label'] }}</div><code class="small">{{ $row->event_name }}</code></td><td>{{ $row->channel ?: '—' }}</td><td>{{ $row->subject_slug ?: '—' }}</td><td>{{ $row->source ?: 'Direto' }}</td><td>{{ $row->device_type ?: '—' }}</td><td>{{ collect([$row->city,$row->region])->filter()->implode(' / ') ?: '—' }}</td><td class="text-truncate" style="max-width:280px" title="{{ $row->path }}">{{ $row->path ?: '—' }}</td></tr>@empty<tr><td colspan="8" class="text-center text-body-secondary py-5">Nenhum evento encontrado para os filtros.</td></tr>@endforelse
        </tbody></table></div>
        @if($total_rows > $rows->count())<div class="card-footer bg-transparent small text-body-secondary">A tela exibe os 100 registros mais recentes. As exportações incluem até {{ number_format(config('analytics.reports.export_limit',10000),0,',','.') }} registros.</div>@endif
    </div>

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Agendar relatório</div><div class="card-body">
                <form method="post" action="{{ route('admin.analytics.reports.schedules.store') }}" class="row g-3">@csrf
                    <div class="col-12"><label class="form-label" for="schedule-name">Nome</label><input class="form-control" id="schedule-name" name="name" required maxlength="120"></div>
                    <div class="col-md-4"><label class="form-label">Frequência</label><select class="form-select" name="frequency"><option value="daily">Diário</option><option value="weekly">Semanal</option><option value="monthly">Mensal</option></select></div>
                    <div class="col-md-4"><label class="form-label">Formato</label><select class="form-select" name="format"><option value="csv">CSV</option><option value="excel">Excel</option><option value="pdf">PDF</option></select></div>
                    <div class="col-md-4"><label class="form-label">Período</label><select class="form-select" name="period"><option value="7">7 dias</option><option value="30" selected>30 dias</option><option value="90">90 dias</option></select></div>
                    @foreach($filters as $name=>$value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
                    <div class="col-12"><div class="form-text mb-3">O agendamento usará os filtros atualmente aplicados.</div><button class="btn btn-primary" type="submit"><i class="bi bi-calendar-plus me-1"></i>Criar agendamento</button></div>
                </form>
            </div></div>
        </div>
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Relatórios agendados</div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Relatório</th><th>Próxima execução</th><th>Status</th><th class="text-end">Ações</th></tr></thead><tbody>
            @forelse($schedules as $schedule)<tr><td><div class="fw-semibold">{{ $schedule->name }}</div><div class="small text-body-secondary">{{ ucfirst($schedule->frequency) }} · {{ strtoupper($schedule->format) }}</div>@if($schedule->last_error)<div class="small text-danger">{{ $schedule->last_error }}</div>@endif</td><td>{{ $schedule->next_run_at?->format('d/m/Y H:i') }}</td><td><span class="badge {{ $schedule->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $schedule->is_active ? 'Ativo' : 'Pausado' }}</span></td><td><div class="d-flex justify-content-end gap-1">@if($schedule->last_file_path)<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.analytics.reports.schedules.download',$schedule) }}" title="Baixar último arquivo"><i class="bi bi-download"></i></a>@endif<form method="post" action="{{ route('admin.analytics.reports.schedules.toggle',$schedule) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-secondary" title="Ativar ou pausar"><i class="bi {{ $schedule->is_active ? 'bi-pause' : 'bi-play' }}"></i></button></form><form method="post" action="{{ route('admin.analytics.reports.schedules.destroy',$schedule) }}" onsubmit="return confirm('Remover este agendamento?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></div></td></tr>@empty<tr><td colspan="4" class="text-center text-body-secondary py-5">Nenhum relatório agendado.</td></tr>@endforelse
            </tbody></table></div></div>
        </div>
    </div>
</div>
<script>document.querySelector('[data-period-select]')?.addEventListener('change',e=>document.querySelectorAll('[data-custom-period]').forEach(el=>el.classList.toggle('d-none',e.target.value!=='custom')));</script>
@endsection
