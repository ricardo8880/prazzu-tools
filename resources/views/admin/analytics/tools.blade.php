@extends('layouts.app')
@section('title','Ferramentas | Analytics | Prazzu Tools')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex gap-2 mb-2"><span class="badge text-bg-primary">Analytics de Produto</span><a class="badge text-bg-light border text-decoration-none" href="{{ route('admin.analytics.index') }}">Dashboard executivo</a></div>
            <h1 class="h2 mb-1">Analytics das Ferramentas</h1>
            <p class="text-body-secondary mb-0">Descubra onde as sessões param, quantas pessoas identificáveis chegam ao resultado e quantas voltam para resolver novamente.</p>
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
        @foreach([
            ['Problemas resolvidos',$retention->problems_solved,'Resultados válidos concluídos no período.'],
            ['Pessoas que resolveram',$retention->solvers,'Usuários ou visitantes identificáveis com pelo menos um resultado.'],
            ['Voltaram e resolveram',$retention->returning_solvers,'Resolveram novamente em outro dia dentro do período.'],
            ['Taxa de retorno',number_format($retention->return_rate,1,',','.').'%','Percentual de pessoas que voltaram em outro dia e resolveram novamente.'],
        ] as [$label,$value,$help])
            <div class="col-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="small text-body-secondary">{{ $label }}</div><div class="h3 mt-2 mb-1">{{ is_numeric($value) ? number_format((float)$value,0,',','.') : $value }}</div><div class="small text-body-secondary">{{ $help }}</div></div></div></div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent"><div class="fw-semibold">Funil por sessões</div><div class="small text-body-secondary">Use este funil para localizar atrito na experiência. Uma mesma pessoa pode gerar mais de uma sessão.</div></div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="small text-body-secondary">1. Abriu</div><div class="h3 my-2">{{ number_format($session_funnel->opens,0,',','.') }}</div><div class="small">sessões</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="small text-body-secondary">2. Começou</div><div class="h3 my-2">{{ number_format($session_funnel->starts,0,',','.') }}</div><div class="small"><strong>{{ number_format($session_funnel->start_rate,1,',','.') }}%</strong> das sessões</div></div></div>
                        <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="small text-body-secondary">3. Viu resultado</div><div class="h3 my-2">{{ number_format($session_funnel->results,0,',','.') }}</div><div class="small"><strong>{{ number_format($session_funnel->result_after_start_rate,1,',','.') }}%</strong> das iniciadas</div></div></div>
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-3 small text-body-secondary">
                        <span>Conclusão: <strong class="text-body">{{ number_format($session_funnel->completion_rate,1,',','.') }}%</strong></span>
                        <span>Abandonos: <strong class="text-body">{{ number_format($session_funnel->abandonments,0,',','.') }}</strong> ({{ number_format($session_funnel->abandonment_rate,1,',','.') }}%)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent"><div class="fw-semibold">Funil por pessoas</div><div class="small text-body-secondary">Conta somente usuários ou visitantes com identidade persistente. Sessões sem identidade não entram aqui.</div></div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-4"><div class="small text-body-secondary">Abriram</div><div class="h4 my-2">{{ number_format($people_funnel->opens,0,',','.') }}</div></div>
                        <div class="col-4"><div class="small text-body-secondary">Começaram</div><div class="h4 my-2">{{ number_format($people_funnel->starts,0,',','.') }}</div></div>
                        <div class="col-4"><div class="small text-body-secondary">Resultado</div><div class="h4 my-2">{{ number_format($people_funnel->results,0,',','.') }}</div></div>
                    </div>
                    <div class="small text-body-secondary mt-3">Início: <strong class="text-body">{{ number_format($people_funnel->start_rate,1,',','.') }}%</strong> · Resultado após início: <strong class="text-body">{{ number_format($people_funnel->result_after_start_rate,1,',','.') }}%</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent"><div class="fw-semibold">Retenção de coorte</div><div class="small text-body-secondary">D1, D7 e D30 medem quem resolveu novamente exatamente 1, 7 ou 30 dias após o primeiro resultado observado no período. “—” significa que a coorte ainda não teve tempo para maturar.</div></div>
        <div class="card-body"><div class="row g-3">
            @foreach(['D1' => $retention->d1, 'D7' => $retention->d7, 'D30' => $retention->d30] as $label => $cohort)
                <div class="col-md-4"><div class="border rounded p-3 h-100"><div class="small text-body-secondary">{{ $label }}</div><div class="h3 my-2">{{ $cohort->rate === null ? '—' : number_format($cohort->rate,1,',','.').'%' }}</div><div class="small text-body-secondary">{{ $cohort->eligible }} pessoas elegíveis · {{ $cohort->returned }} retornaram</div></div></div>
            @endforeach
        </div></div>
    </div>

    @if($alerts->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-transparent fw-semibold">Onde investigar primeiro</div><div class="list-group list-group-flush">@foreach($alerts as $alert)<div class="list-group-item"><span class="badge text-bg-{{ $alert->severity }} me-2">Alerta</span><strong>{{ $alert->tool }}</strong> — {{ $alert->message }}</div>@endforeach</div></div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent"><div class="fw-semibold">Desempenho por ferramenta</div><div class="small text-body-secondary">Compare descoberta, início e entrega de resultado antes de decidir qualquer redesign.</div></div>
        <div class="table-responsive"><table class="table table-sm align-middle mb-0">
            <thead><tr><th>Ferramenta</th><th class="text-end">Aberturas</th><th class="text-end">Começaram</th><th class="text-end">Início</th><th class="text-end">Resultado</th><th class="text-end">Após início</th><th class="text-end">Abandono</th><th class="text-end">Erros</th><th class="text-end">Média</th></tr></thead>
            <tbody>@forelse($tools as $row)<tr>
                <td><a class="fw-semibold text-decoration-none" href="{{ route('admin.analytics.tools.show',['tool'=>$row->slug,'period'=>$selected_period]) }}">{{ $row->name }}</a><div class="small text-body-secondary">{{ $row->slug }}</div></td>
                <td class="text-end">{{ $row->session_opens }}<div class="small text-body-secondary">{{ $row->people_opens }} pessoas</div></td>
                <td class="text-end">{{ $row->session_starts }}<div class="small text-body-secondary">{{ $row->people_starts }} pessoas</div></td>
                <td class="text-end"><span class="badge text-bg-{{ $row->start_rate < 40 && $row->session_opens >= 10 ? 'warning' : 'secondary' }}">{{ number_format($row->start_rate,1,',','.') }}%</span></td>
                <td class="text-end">{{ $row->session_results }}<div class="small text-body-secondary">{{ $row->people_results }} pessoas</div></td>
                <td class="text-end"><span class="badge text-bg-{{ $row->result_after_start_rate < 40 && $row->session_starts >= 10 ? 'warning' : 'success' }}">{{ number_format($row->result_after_start_rate,1,',','.') }}%</span></td>
                <td class="text-end"><span class="badge text-bg-{{ $row->abandonment_rate > 40 ? 'danger':'secondary' }}">{{ number_format($row->abandonment_rate,1,',','.') }}%</span></td>
                <td class="text-end">{{ $row->errors }}</td>
                <td class="text-end">{{ number_format($row->average_calculation_seconds,1,',','.') }}s</td>
            </tr>@empty<tr><td colspan="9" class="text-center text-body-secondary py-4">Sem dados.</td></tr>@endforelse</tbody>
        </table></div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Campos mais problemáticos</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Ferramenta</th><th>Campo</th><th>Etapa</th><th class="text-end">Erros</th><th class="text-end">Abandonos</th></tr></thead><tbody>@forelse($problem_fields as $row)<tr><td>{{ $row->tool }}</td><td><code>{{ $row->field }}</code></td><td>{{ $row->step }}</td><td class="text-end">{{ $row->errors }}</td><td class="text-end">{{ $row->abandonments }}</td></tr>@empty<tr><td colspan="5" class="text-center text-body-secondary py-4">Sem ocorrências.</td></tr>@endforelse</tbody></table></div></div></div>
        <div class="col-xl-5"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-transparent fw-semibold">Etapas com maior abandono</div><ul class="list-group list-group-flush">@forelse($dropoff_steps as $row)<li class="list-group-item d-flex justify-content-between"><span><code>{{ $row->step }}</code></span><strong>{{ $row->total }} ({{ number_format($row->percentage,1,',','.') }}%)</strong></li>@empty<li class="list-group-item text-body-secondary">Sem abandonos.</li>@endforelse</ul></div></div>
    </div>
</div>
@endsection
