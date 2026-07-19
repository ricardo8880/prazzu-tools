@extends('layouts.app')

@section('title', 'Calculadora de Simples Nacional — Prazzu Tools')
@section('meta_description', 'Calcule anexo, faixa, Fator R, alíquota efetiva e DAS estimado do Simples Nacional.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-simples-nacional">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calculadora de Simples Nacional</li>
        </ol>
    </nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-calculator"></i></span>
        <div class="flex-grow-1">
            <span class="prazzu-badge prazzu-badge--green">Grátis</span>
            <h1>Calculadora de Simples Nacional</h1>
            <p>Calcule a faixa, a alíquota efetiva e o DAS estimado, com enquadramento automático pelo Fator R quando necessário.</p>
        </div>
    </header>

    <x-tool-feature-tiers slug="calculadora-simples-nacional" />

    <section class="prazzu-tool-workspace text-start" aria-labelledby="calculation-data-title">
        <div class="mb-4">
            <h2 id="calculation-data-title" class="mb-1">Dados do cálculo</h2>
            <p class="text-body-secondary mb-0">Informe os valores da empresa para estimar o anexo, a alíquota efetiva e o DAS mensal.</p>
        </div>

        <form method="post" action="{{ route('tools.calculadora-simples-nacional.calculate') }}" class="row g-3">
            @csrf

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="use_factor_r" name="use_factor_r" value="1" @checked(old('use_factor_r'))>
                    <label class="form-check-label fw-semibold" for="use_factor_r">A atividade está sujeita ao Fator R</label>
                </div>
                <div class="form-text">Ao ativar, o sistema define automaticamente entre os Anexos III e V.</div>
            </div>

            <div class="col-12 col-lg-6" id="annex-field">
                <label class="form-label" for="annex">Anexo</label>
                <select class="form-select" id="annex" name="annex">
                    <option value="">Selecione</option>
                    @foreach ($annexes as $annex)
                        <option value="{{ $annex->value }}" @selected(old('annex') === $annex->value)>{{ $annex->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-lg-6">
                <label class="form-label" for="rbt12">Receita bruta acumulada nos últimos 12 meses (RBT12)</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input class="form-control" id="rbt12" name="rbt12" value="{{ old('rbt12') }}" placeholder="180.000,00" inputmode="decimal" required>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label class="form-label" for="monthly_revenue">Faturamento do mês</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input class="form-control" id="monthly_revenue" name="monthly_revenue" value="{{ old('monthly_revenue') }}" placeholder="15.000,00" inputmode="decimal" required>
                </div>
            </div>

            <div class="col-12 col-lg-6" id="payroll-field">
                <label class="form-label" for="payroll_12">Folha de salários dos últimos 12 meses</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input class="form-control" id="payroll_12" name="payroll_12" value="{{ old('payroll_12') }}" placeholder="50.400,00" inputmode="decimal">
                </div>
                <div class="form-text">Inclua salários, pró-labore e encargos considerados pela regra do Fator R.</div>
            </div>

            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0" role="alert">
                        <div class="fw-semibold mb-1">Revise os dados informados:</div>
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif

            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-calculator me-1" aria-hidden="true"></i> Calcular DAS</button>
                <button class="btn btn-outline-secondary" type="reset"><i class="bi bi-eraser me-1" aria-hidden="true"></i> Limpar formulário</button>
            </div>
        </form>
    </section>

    @if (session('calculation_result'))
        @php($result = session('calculation_result'))
        @php($factorR = session('factor_r_result'))

        <section class="mt-4" aria-labelledby="calculation-result-title">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <h2 id="calculation-result-title" class="prazzu-section-title mb-0">Resultado estimado</h2>
                <span class="badge text-bg-primary">{{ $result['annex_label'] }}</span>
            </div>

            @if ($factorR)
                <div class="alert {{ $factorR['applicable_annex'] === 'III' ? 'alert-success' : 'alert-warning' }}" role="status">
                    <div class="d-flex gap-2">
                        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                        <div><strong>Fator R: {{ $factorR['factor_r'] }}</strong><br>{{ $factorR['explanation'] }}</div>
                    </div>
                </div>
            @endif

            <div class="row g-3">
                @foreach ([
                    'estimated_das' => ['DAS estimado', 'bi-receipt'],
                    'effective_rate' => ['Alíquota efetiva', 'bi-percent'],
                    'bracket' => ['Faixa', 'bi-bar-chart-steps'],
                    'nominal_rate' => ['Alíquota nominal', 'bi-percent'],
                    'deduction' => ['Parcela a deduzir', 'bi-dash-circle'],
                    'monthly_revenue' => ['Faturamento do mês', 'bi-cash-stack'],
                ] as $key => [$label, $icon])
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="prazzu-related-tool h-100">
                            <i class="bi {{ $icon }}" aria-hidden="true"></i>
                            <span><small>{{ $label }}</small><strong>{{ $result[$key] }}</strong></span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h3 class="h5">Memória do cálculo</h3>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">RBT12</dt><dd class="col-sm-8">{{ $result['rbt12'] }}</dd>
                        <dt class="col-sm-4">Intervalo da faixa</dt><dd class="col-sm-8">{{ $result['bracket_from'] }} até {{ $result['bracket_until'] }}</dd>
                        <dt class="col-sm-4">Fórmula da alíquota efetiva</dt><dd class="col-sm-8">{{ $result['formula'] }}</dd>
                        <dt class="col-sm-4">Cálculo do DAS</dt><dd class="col-sm-8">Faturamento do mês × alíquota efetiva</dd>
                    </dl>
                </div>
            </div>

            <div class="alert alert-light border mt-3 mb-0">
                <small><strong>Importante:</strong> resultado estimativo com base nos valores informados. Regra {{ $result['rule_version'] }}, vigente desde {{ $result['rule_valid_from'] }}.</small>
            </div>
        </section>
    @endif


    <section class="mt-5" aria-labelledby="plus-resources-title">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <span class="badge text-bg-warning mb-2">Plus</span>
                <h2 id="plus-resources-title" class="prazzu-section-title mb-1">Simulações e acompanhamento</h2>
                <p class="text-secondary mb-0">Recursos avançados liberados nesta fase e preparados para o controle de assinatura futuro.</p>
            </div>
        </div>

        @if (session('history_success'))
            <div class="alert alert-success" role="status">{{ session('history_success') }}</div>
        @endif

        @if (collect($plusAccess)->every(static fn (bool $allowed): bool => $allowed))
            <div class="alert alert-info d-flex gap-2" role="status">
                <i class="bi bi-unlock-fill" aria-hidden="true"></i>
                <div><strong>Recursos Plus disponíveis.</strong> Sua conta pode utilizar todos os recursos avançados desta ferramenta.</div>
            </div>
        @endif

        <div class="accordion" id="simples-plus-accordion">
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#scenario-panel">
                        <i class="bi bi-sliders me-2"></i> Simular e comparar cenários
                    </button>
                </h3>
                <div id="scenario-panel" class="accordion-collapse collapse show" data-bs-parent="#simples-plus-accordion">
                    <div class="accordion-body">
                        <form method="post" action="{{ route('tools.calculadora-simples-nacional.plus.compare-scenarios') }}" class="row g-3">
                            @csrf
                            @for ($i = 0; $i < 2; $i++)
                                <div class="col-12 col-xl-6">
                                    <div class="card h-100 border">
                                        <div class="card-body row g-3">
                                            <div class="col-12"><label class="form-label">Nome do cenário</label><input class="form-control" name="scenarios[{{ $i }}][name]" value="{{ old("scenarios.$i.name", 'Cenário '.($i + 1)) }}" required></div>
                                            <div class="col-md-4"><label class="form-label">Anexo</label><select class="form-select" name="scenarios[{{ $i }}][annex]" required>@foreach($annexes as $annex)<option value="{{ $annex->value }}" @selected(old("scenarios.$i.annex", $i === 0 ? 'III' : 'V') === $annex->value)>{{ $annex->label() }}</option>@endforeach</select></div>
                                            <div class="col-md-4"><label class="form-label">RBT12</label><input class="form-control" name="scenarios[{{ $i }}][rbt12]" value="{{ old("scenarios.$i.rbt12", old('rbt12')) }}" required></div>
                                            <div class="col-md-4"><label class="form-label">Faturamento</label><input class="form-control" name="scenarios[{{ $i }}][monthly_revenue]" value="{{ old("scenarios.$i.monthly_revenue", old('monthly_revenue')) }}" required></div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                            <div class="col-12"><button class="btn btn-primary" type="submit" @disabled(! ($plusAccess['compare_scenarios'] ?? false))><i class="bi bi-arrow-left-right me-1"></i> Comparar cenários</button></div>
                        </form>

                        @if(session('scenario_comparison'))
                            <div class="table-responsive mt-4"><table class="table table-striped align-middle mb-0"><thead><tr><th>Cenário</th><th>Anexo</th><th>Faixa</th><th>Alíquota efetiva</th><th>DAS</th></tr></thead><tbody>@foreach(session('scenario_comparison.scenarios') as $scenario)<tr class="{{ $scenario['best'] ? 'table-success' : '' }}"><td><strong>{{ $scenario['name'] }}</strong> @if($scenario['best'])<span class="badge text-bg-success ms-1">Menor DAS</span>@endif</td><td>{{ $scenario['annex_label'] }}</td><td>{{ $scenario['bracket'] }}</td><td>{{ $scenario['effective_rate'] }}</td><td>{{ $scenario['estimated_das'] }}</td></tr>@endforeach</tbody></table></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#annex-panel"><i class="bi bi-columns-gap me-2"></i> Comparar anexos</button></h3>
                <div id="annex-panel" class="accordion-collapse collapse" data-bs-parent="#simples-plus-accordion"><div class="accordion-body">
                    <form method="post" action="{{ route('tools.calculadora-simples-nacional.plus.compare-annexes') }}" class="row g-3">@csrf
                        <div class="col-12"><label class="form-label d-block">Anexos</label><div class="d-flex flex-wrap gap-3">@foreach($annexes as $annex)<div class="form-check"><input class="form-check-input" type="checkbox" name="annexes[]" value="{{ $annex->value }}" id="compare-annex-{{ $annex->value }}" @checked(in_array($annex->value, old('annexes', ['III','V']), true))><label class="form-check-label" for="compare-annex-{{ $annex->value }}">{{ $annex->label() }}</label></div>@endforeach</div></div>
                        <div class="col-md-5"><label class="form-label">RBT12</label><input class="form-control" name="rbt12" value="{{ old('rbt12') }}" required></div>
                        <div class="col-md-5"><label class="form-label">Faturamento do mês</label><input class="form-control" name="monthly_revenue" value="{{ old('monthly_revenue') }}" required></div>
                        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit" @disabled(! ($plusAccess['compare_annexes'] ?? false))>Comparar</button></div>
                    </form>
                    @if(session('annex_comparison'))<div class="table-responsive mt-4"><table class="table table-hover align-middle mb-0"><thead><tr><th>Anexo</th><th>Faixa</th><th>Alíquota efetiva</th><th>DAS</th></tr></thead><tbody>@foreach(session('annex_comparison.scenarios') as $scenario)<tr class="{{ $scenario['best'] ? 'table-success' : '' }}"><td><strong>{{ $scenario['annex_label'] }}</strong></td><td>{{ $scenario['bracket'] }}</td><td>{{ $scenario['effective_rate'] }}</td><td>{{ $scenario['estimated_das'] }} @if($scenario['best'])<span class="badge text-bg-success ms-1">Melhor resultado</span>@endif</td></tr>@endforeach</tbody></table></div>@endif
                </div></div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#projection-panel"><i class="bi bi-graph-up-arrow me-2"></i> Projeção anual</button></h3>
                <div id="projection-panel" class="accordion-collapse collapse" data-bs-parent="#simples-plus-accordion"><div class="accordion-body">
                    <form method="post" action="{{ route('tools.calculadora-simples-nacional.plus.project') }}" class="row g-3">@csrf
                        <div class="col-md-3"><label class="form-label">Anexo</label><select class="form-select" name="annex" required>@foreach($annexes as $annex)<option value="{{ $annex->value }}" @selected(old('annex') === $annex->value)>{{ $annex->label() }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label">Faturamento mensal inicial</label><input class="form-control" name="monthly_revenue" value="{{ old('monthly_revenue') }}" required></div>
                        <div class="col-md-3"><label class="form-label">Crescimento mensal (%)</label><input class="form-control" type="number" step="0.01" name="monthly_growth" value="{{ old('monthly_growth', 0) }}" required></div>
                        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit" @disabled(! ($plusAccess['annual_projection'] ?? false))>Projetar</button></div>
                    </form>
                    @if(session('annual_projection'))@php($projection=session('annual_projection'))<div class="row g-3 mt-2"><div class="col-md-6"><div class="alert alert-primary mb-0"><small>Receita projetada</small><div class="fs-4 fw-bold">{{ $projection['total_revenue'] }}</div></div></div><div class="col-md-6"><div class="alert alert-warning mb-0"><small>DAS projetado</small><div class="fs-4 fw-bold">{{ $projection['total_das'] }}</div></div></div></div><div class="table-responsive mt-3"><table class="table table-sm table-striped"><thead><tr><th>Mês</th><th>Receita</th><th>RBT12 anualizado</th><th>Faixa</th><th>Alíquota</th><th>DAS</th></tr></thead><tbody>@foreach($projection['months'] as $month)<tr><td>{{ $month['month'] }}</td><td>{{ $month['monthly_revenue'] }}</td><td>{{ $month['rbt12'] }}</td><td>{{ $month['bracket'] }}</td><td>{{ $month['effective_rate'] }}</td><td>{{ $month['estimated_das'] }}</td></tr>@endforeach</tbody></table></div><div class="form-text">A projeção anualiza o faturamento de cada mês e aplica a taxa de crescimento informada.</div>@endif
                </div></div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#alerts-panel"><i class="bi bi-bell me-2"></i> Alertas e inteligência</button></h3>
                <div id="alerts-panel" class="accordion-collapse collapse" data-bs-parent="#simples-plus-accordion"><div class="accordion-body">
                    <form method="post" action="{{ route('tools.calculadora-simples-nacional.plus.alerts') }}" class="row g-3">@csrf
                        <div class="col-md-2"><label class="form-label">Anexo</label><select class="form-select" name="annex" required>@foreach($annexes as $annex)<option value="{{ $annex->value }}" @selected(old('annex') === $annex->value)>{{ $annex->label() }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label">RBT12</label><input class="form-control" name="rbt12" value="{{ old('rbt12') }}" required></div>
                        <div class="col-md-3"><label class="form-label">Faturamento mensal</label><input class="form-control" name="monthly_revenue" value="{{ old('monthly_revenue') }}" required></div>
                        <div class="col-md-2"><label class="form-label">Folha 12 meses</label><input class="form-control" name="payroll_12" value="{{ old('payroll_12') }}"></div>
                        <div class="col-md-2"><label class="form-label">Crescimento (%)</label><input class="form-control" type="number" min="0" max="100" step="0.01" name="monthly_growth" value="{{ old('monthly_growth', 0) }}"></div>
                        <div class="col-12"><button class="btn btn-primary" type="submit" @disabled(! ($plusAccess['alerts'] ?? false))><i class="bi bi-search me-1"></i> Analisar alertas</button></div>
                    </form>
                    @if(session('alerts_analysis'))
                        @php($analysis = session('alerts_analysis'))
                        <div class="row g-2 mt-3">
                            <div class="col-auto"><span class="badge text-bg-danger">Críticos: {{ $analysis['summary']['danger'] }}</span></div>
                            <div class="col-auto"><span class="badge text-bg-warning">Atenção: {{ $analysis['summary']['warning'] }}</span></div>
                            <div class="col-auto"><span class="badge text-bg-info">Informativos: {{ $analysis['summary']['info'] + $analysis['summary']['primary'] }}</span></div>
                        </div>
                        <div class="vstack gap-2 mt-3">
                            @foreach($analysis['alerts'] as $alert)
                                <div class="alert alert-{{ $alert['level'] }} mb-0" role="alert">
                                    <div class="fw-semibold">{{ $alert['title'] }}</div>
                                    <div>{{ $alert['message'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div></div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#history-panel"><i class="bi bi-clock-history me-2"></i> Histórico mensal</button></h3>
                <div id="history-panel" class="accordion-collapse collapse" data-bs-parent="#simples-plus-accordion"><div class="accordion-body">
                    @guest
                        <div class="alert alert-info mb-0" role="note"><i class="bi bi-person-plus me-1" aria-hidden="true"></i>Você pode usar todos os cálculos sem login. Crie uma conta gratuita apenas para salvar e consultar o histórico mensal.</div>
                    @endguest
                    @auth
                    <form method="post" action="{{ route('tools.calculadora-simples-nacional.plus.history.store') }}" class="row g-3">@csrf
                        <div class="col-md-3"><label class="form-label">Competência</label><input class="form-control" type="month" name="reference_month" value="{{ old('reference_month', now()->format('Y-m')) }}" required></div>
                        <div class="col-md-3"><label class="form-label">Anexo</label><select class="form-select" name="annex">@foreach($annexes as $annex)<option value="{{ $annex->value }}">{{ $annex->label() }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label">RBT12</label><input class="form-control" name="rbt12" value="{{ old('rbt12') }}" required></div>
                        <div class="col-md-3"><label class="form-label">Faturamento</label><input class="form-control" name="monthly_revenue" value="{{ old('monthly_revenue') }}" required></div>
                        <div class="col-12"><button class="btn btn-primary" type="submit" @disabled(! ($plusAccess['monthly_history'] ?? false))><i class="bi bi-save me-1"></i> Salvar cálculo</button></div>
                    </form>
                    <div class="table-responsive mt-4"><table class="table table-hover align-middle mb-0"><thead><tr><th>Competência</th><th>Anexo</th><th>Receita</th><th>Alíquota</th><th>DAS</th><th></th></tr></thead><tbody>@forelse($history as $item)<tr><td>{{ $item->referenceDate->format('m/Y') }}</td><td>Anexo {{ data_get($item->input, 'annex') }}</td><td>{{ data_get($item->result, 'monthly_revenue') }}</td><td>{{ data_get($item->result, 'effective_rate') }}</td><td>{{ data_get($item->result, 'estimated_das') }}</td><td class="text-end"><form method="post" action="{{ route('tools.calculadora-simples-nacional.plus.history.destroy', $item->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir" @disabled(! ($plusAccess['monthly_history'] ?? false))><i class="bi bi-trash"></i></button></form></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-4">Nenhum cálculo salvo ainda.</td></tr>@endforelse</tbody></table></div>
                    @endauth
                </div></div>
            </div>
        </div>
    </section>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const factorSwitch = document.getElementById('use_factor_r');
    const annexField = document.getElementById('annex-field');
    const annexSelect = document.getElementById('annex');
    const payrollField = document.getElementById('payroll-field');
    const payrollInput = document.getElementById('payroll_12');

    const syncFactorRFields = () => {
        const enabled = factorSwitch.checked;
        annexField.classList.toggle('d-none', enabled);
        payrollField.classList.toggle('d-none', !enabled);
        annexSelect.disabled = enabled;
        payrollInput.required = enabled;
    };

    factorSwitch.addEventListener('change', syncFactorRFields);
    syncFactorRFields();
});
</script>
@endsection
