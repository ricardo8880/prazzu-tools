@extends('layouts.app')

@section('title', 'Comparador Tributário — Prazzu Tools')
@section('meta_description', 'Compare estimativas do Simples Nacional, Lucro Presumido e Lucro Real com premissas transparentes.')

@section('content')
<x-tools.page
    title="Comparador Tributário"
    description="Compare estimativas tributárias entre Simples Nacional, Lucro Presumido e Lucro Real para apoiar uma análise profissional."
    icon="arrow-left-right"
    slug="comparador-tributario"
>
    @if (session('history_message'))
        <div class="alert alert-success">{{ session('history_message') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('tools.comparador-tributario.history.index') }}">
            <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Histórico
        </a>
    </div>

    <div class="alert alert-warning d-flex gap-2" role="note">
        <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
        <div><strong>Estimativa orientativa.</strong> O resultado não substitui diagnóstico contábil, escrituração, segregação de receitas ou análise de benefícios fiscais.</div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Revise os dados informados.</div>
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="post" action="{{ route('tools.comparador-tributario.compare') }}" class="card border-0 shadow-sm">
        @csrf
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-4">
                <div>
                    <h2 class="h4 mb-1">Dados do cenário</h2>
                    <p class="text-body-secondary mb-0">Use valores médios mensais e acumulados coerentes com a data de referência.</p>
                </div>
                <span class="badge text-bg-light border align-self-start">Versão estimativa</span>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="reference_date">Data de referência</label>
                    <input class="form-control @error('reference_date') is-invalid @enderror" id="reference_date" name="reference_date" type="date" value="{{ old('reference_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label" for="business_activity">Atividade predominante</label>
                    <select class="form-select @error('business_activity') is-invalid @enderror" id="business_activity" name="business_activity" required>
                        <option value="">Selecione</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->value }}" @selected(old('business_activity') === $activity->value)>{{ $activity->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="monthly_revenue">Faturamento mensal</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_revenue" name="monthly_revenue" inputmode="decimal" value="{{ old('monthly_revenue') }}" placeholder="100.000,00" required></div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="revenue_last_twelve_months">Receita dos últimos 12 meses</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="revenue_last_twelve_months" name="revenue_last_twelve_months" inputmode="decimal" value="{{ old('revenue_last_twelve_months') }}" placeholder="1.200.000,00" required></div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="payroll_last_twelve_months">Folha dos últimos 12 meses</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="payroll_last_twelve_months" name="payroll_last_twelve_months" inputmode="decimal" value="{{ old('payroll_last_twelve_months') }}" placeholder="300.000,00" required></div>
                    <div class="form-text">Usada no Fator R quando aplicável.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="monthly_operating_costs">Custos operacionais mensais</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_operating_costs" name="monthly_operating_costs" inputmode="decimal" value="{{ old('monthly_operating_costs', '0,00') }}" required></div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="monthly_deductible_expenses">Despesas dedutíveis mensais</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_deductible_expenses" name="monthly_deductible_expenses" inputmode="decimal" value="{{ old('monthly_deductible_expenses', '0,00') }}" required></div>
                </div>
            </div>

            <div class="accordion mt-4" id="advanced-fields">
                <div class="accordion-item">
                    <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#advanced-fields-panel">Dados avançados para maior cobertura</button></h3>
                    <div id="advanced-fields-panel" class="accordion-collapse collapse" data-bs-parent="#advanced-fields">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="monthly_pis_cofins_credit_base">Base mensal elegível a créditos de PIS/Cofins</label>
                                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_pis_cofins_credit_base" name="monthly_pis_cofins_credit_base" inputmode="decimal" value="{{ old('monthly_pis_cofins_credit_base') }}"></div>
                                    <div class="form-text">Necessária para estimar o Lucro Real.</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="indirect_tax_rate">Alíquota efetiva de ISS, ICMS ou ICMS/IPI</label>
                                    <div class="input-group"><input class="form-control" id="indirect_tax_rate" name="indirect_tax_rate" inputmode="decimal" value="{{ old('indirect_tax_rate') }}" placeholder="5,00"><span class="input-group-text">%</span></div>
                                </div>
                                <div class="col-12 col-md-3"><label class="form-label" for="state">UF</label><input class="form-control text-uppercase" id="state" name="state" maxlength="2" value="{{ old('state') }}" placeholder="SP"></div>
                                <div class="col-12 col-md-9"><label class="form-label" for="municipality">Município</label><input class="form-control" id="municipality" name="municipality" maxlength="120" value="{{ old('municipality') }}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid d-md-flex justify-content-md-end mt-4">
                <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2" aria-hidden="true"></i>Comparar regimes</button>
            </div>
        </div>
    </form>

    @if (session('comparison_result'))
        @php($result = session('comparison_result'))
        <section class="mt-5" aria-labelledby="comparison-result-title">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                <div><h2 id="comparison-result-title" class="h3 mb-1">Resultado da comparação</h2><p class="text-body-secondary mb-0">Referência {{ $result['reference_date'] }} · regras {{ $result['rule_version'] }}</p></div>
                <div class="d-flex flex-wrap gap-2 align-self-lg-start">
                    <span class="badge text-bg-light border align-self-center">{{ $result['comparable_regime_count'] }} regimes comparáveis</span>
                    @foreach (['csv' => 'CSV', 'json' => 'JSON'] as $format => $label)
                        <form method="post" action="{{ route('tools.comparador-tributario.export', $format) }}">@csrf
                            @foreach(old() as $key => $value) @if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach
                            <button class="btn btn-outline-primary btn-sm" type="submit">Exportar {{ $label }}</button>
                        </form>
                    @endforeach
                    <form method="post" action="{{ route('tools.comparador-tributario.report') }}">@csrf
                        @foreach(old() as $key => $value) @if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach
                        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-file-earmark-pdf me-1"></i>Relatório</button>
                    </form>
                </div>
            </div>

            @if ($result['winner'])
                <div class="card border-success shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-6"><div class="text-uppercase small text-success fw-semibold">Menor ônus estimado</div><div class="display-6 fw-semibold">{{ $result['winner'] }}</div></div>
                            <div class="col-6 col-lg-3"><div class="text-body-secondary small">Economia mensal</div><div class="h4 mb-0">{{ $result['monthly_savings'] }}</div></div>
                            <div class="col-6 col-lg-3"><div class="text-body-secondary small">Economia anual</div><div class="h4 mb-0">{{ $result['annual_savings'] }}</div></div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">Não há pelo menos dois regimes comparáveis para declarar uma alternativa de menor ônus.</div>
            @endif

            <div class="row g-3">
                @foreach ($result['ranking'] as $item)
                    <div class="col-12 col-xl-4">
                        <article class="card h-100 shadow-sm {{ $item['position'] === 1 ? 'border-success' : 'border-0' }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-3"><div><span class="badge {{ $item['position'] === 1 ? 'text-bg-success' : 'text-bg-light border' }} mb-2">{{ $item['position'] }}º lugar</span><h3 class="h4 mb-0">{{ $item['regime'] }}</h3></div></div>
                                <dl class="row mb-3"><dt class="col-7 text-body-secondary fw-normal">Mensal</dt><dd class="col-5 text-end fw-semibold">{{ $item['monthly_tax'] }}</dd><dt class="col-7 text-body-secondary fw-normal">Anual</dt><dd class="col-5 text-end fw-semibold">{{ $item['annual_tax'] }}</dd>@if($item['position'] > 1)<dt class="col-7 text-body-secondary fw-normal">Diferença anual</dt><dd class="col-5 text-end text-danger">+ {{ $item['annual_difference'] }}</dd>@endif</dl>
                                <div class="accordion mt-auto" id="details-{{ $item['position'] }}"><div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#details-panel-{{ $item['position'] }}">Ver composição e premissas</button></h4><div id="details-panel-{{ $item['position'] }}" class="accordion-collapse collapse"><div class="accordion-body p-0"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Tributo</th><th class="text-end">Mensal</th></tr></thead><tbody>@foreach($item['taxes'] as $tax)<tr><td>{{ $tax['name'] }}</td><td class="text-end">{{ $tax['monthly_amount'] }}</td></tr>@endforeach</tbody></table></div>@if($item['assumptions'])<div class="p-3 border-top"><div class="fw-semibold small mb-1">Premissas</div><ul class="small mb-0 ps-3">@foreach($item['assumptions'] as $assumption)<li>{{ $assumption }}</li>@endforeach</ul></div>@endif @if($item['warnings'])<div class="p-3 border-top bg-warning-subtle"><div class="fw-semibold small mb-1">Alertas</div><ul class="small mb-0 ps-3">@foreach($item['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul></div>@endif</div></div></div></div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            @if ($result['unavailable'])
                <div class="card border-0 shadow-sm mt-4"><div class="card-body"><h3 class="h5">Regimes não comparáveis neste cenário</h3><div class="vstack gap-2">@foreach($result['unavailable'] as $item)<div class="alert alert-secondary mb-0"><div class="fw-semibold">{{ $item['regime'] }}</div>@foreach($item['warnings'] as $warning)<div>{{ $warning }}</div>@endforeach</div>@endforeach</div></div></div>
            @endif
        </section>
    @endif
</x-tools.page>
@endsection
