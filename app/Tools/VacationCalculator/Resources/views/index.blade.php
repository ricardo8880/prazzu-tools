@extends('layouts.app')

@section('title', 'Calculadora de Férias — Prazzu Tools')
@section('meta_description', 'Calcule férias, terço constitucional, abono pecuniário, médias remuneratórias, descontos informados e prazos do período aquisitivo e concessivo.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-ferias">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calculadora de Férias</li>
        </ol>
    </nav>

    <x-tools.intro icon="calendar2-check" tone="blue" title="Calculadora de Férias" description="Estime os dias de direito, a remuneração das férias, o terço constitucional, o abono pecuniário e os principais prazos." badge="Grátis" />

    <x-tool-feature-tiers slug="calculadora-ferias" />

    <div class="d-flex flex-wrap gap-2 mb-3">
        <a class="btn btn-outline-primary" href="{{ route('tools.calculadora-ferias.planner') }}"><i class="bi bi-people me-1"></i>Planejamento em equipe <span class="badge text-bg-primary ms-1">Plus</span></a>
        @auth<a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-ferias.history.index') }}"><i class="bi bi-clock-history me-1"></i>Histórico</a>@endauth
    </div>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    @if($historySaved ?? session('history_saved', false))<div class="alert alert-success">Cálculo salvo no seu histórico Plus.</div>@endif

    <div class="alert alert-info d-flex gap-2 align-items-start" role="alert">
        <i class="bi bi-info-circle-fill mt-1" aria-hidden="true"></i>
        <div><strong>Estimativa transparente.</strong> O cálculo considera as médias e os descontos informados, mas ainda não apura automaticamente INSS e IRRF. Confira o resultado com o responsável trabalhista antes do pagamento.</div>
    </div>

    <section class="prazzu-form-panel" aria-labelledby="vacation-form-title">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h2 id="vacation-form-title" class="prazzu-section-title mb-1">Dados das férias</h2>
                <p class="text-body-secondary mb-0">Informe o período aquisitivo, a remuneração e as condições do descanso.</p>
            </div>
            <span class="badge text-bg-light border text-body-secondary align-self-start">Campos com * são obrigatórios</span>
        </div>

        <form method="post" action="{{ route('tools.calculadora-ferias.calculate') }}" class="row g-3" novalidate>
            @csrf

            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="monthly_salary">Salário mensal *</label>
                <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('monthly_salary') is-invalid @enderror" id="monthly_salary" name="monthly_salary" value="{{ old('monthly_salary') }}" placeholder="3.000,00" inputmode="decimal" required>@error('monthly_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="acquisition_start_date">Início do período aquisitivo *</label>
                <input class="form-control @error('acquisition_start_date') is-invalid @enderror" id="acquisition_start_date" type="date" name="acquisition_start_date" value="{{ old('acquisition_start_date') }}" required>
                @error('acquisition_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="vacation_start_date">Início das férias *</label>
                <input class="form-control @error('vacation_start_date') is-invalid @enderror" id="vacation_start_date" type="date" name="vacation_start_date" value="{{ old('vacation_start_date') }}" required>
                @error('vacation_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="unjustified_absences">Faltas injustificadas</label>
                <input class="form-control @error('unjustified_absences') is-invalid @enderror" id="unjustified_absences" type="number" min="0" max="365" name="unjustified_absences" value="{{ old('unjustified_absences', 0) }}">
                @error('unjustified_absences')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">As faixas legais podem reduzir os dias de direito.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="convert_one_third_to_cash" name="convert_one_third_to_cash" value="1" @checked(old('convert_one_third_to_cash'))>
                    <label class="form-check-label" for="convert_one_third_to_cash">Converter 1/3 em abono pecuniário</label>
                </div>
            </div>

            <div class="col-12"><hr class="my-2"><h3 class="h6 mb-0">Médias e adicionais mensais</h3></div>
            @foreach ([
                'commission_average' => ['Média de comissões', '0,00'],
                'overtime_average' => ['Média de horas extras', '0,00'],
                'recurring_additions' => ['Outros adicionais habituais', '0,00'],
                'other_deductions' => ['Outros descontos informados', '0,00'],
            ] as $field => [$label, $placeholder])
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $placeholder) }}" inputmode="decimal">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
            @endforeach

            @if ($errors->any())
                <div class="col-12"><div class="alert alert-danger mb-0" role="alert"><strong>Revise os dados informados.</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
            @endif

            <div class="col-12 d-flex flex-column flex-sm-row gap-2 pt-2">
                <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-calculator me-1" aria-hidden="true"></i>Calcular férias</button>
                <button class="btn btn-outline-secondary" type="reset"><i class="bi bi-eraser me-1" aria-hidden="true"></i>Limpar formulário</button>
            </div>
        </form>
    </section>

    @php($result = $calculationResult ?? session('calculation_result'))
    @if ($result)
        <div class="d-flex flex-wrap gap-2 mb-3">
            <form method="post" action="{{ route('tools.calculadora-ferias.export', 'csv') }}">@csrf @foreach(old() as $key=>$value) @if(!is_array($value) && $key !== '_token')<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach<button class="btn btn-sm btn-outline-secondary">Exportar CSV <span class="badge text-bg-primary">Plus</span></button></form>
            <form method="post" action="{{ route('tools.calculadora-ferias.export', 'json') }}">@csrf @foreach(old() as $key=>$value) @if(!is_array($value) && $key !== '_token')<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach<button class="btn btn-sm btn-outline-secondary">Exportar JSON</button></form>
            <form method="post" action="{{ route('tools.calculadora-ferias.export', 'pdf') }}">@csrf @foreach(old() as $key=>$value) @if(!is_array($value) && $key !== '_token')<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif @endforeach<button class="btn btn-sm btn-outline-secondary">Relatório PDF</button></form>
        </div>
        @php($summary = collect($result['summary'])->keyBy('key'))
        @php($remuneration = $result['details']['remuneration'])
        @php($periods = $result['details']['periods'])
        <section class="prazzu-form-panel mt-4" aria-labelledby="vacation-result-title">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div><span class="badge text-bg-success mb-2">Estimativa calculada</span><h2 id="vacation-result-title" class="prazzu-section-title mb-1">Resultado das férias</h2><p class="text-body-secondary mb-0">Memória baseada na remuneração e nas condições informadas.</p></div>
                <div class="text-lg-end"><small class="text-body-secondary d-block">Total líquido estimado</small><strong class="fs-3 text-success">{{ $summary['net_total']['value'] }}</strong></div>
            </div>

            <div class="row g-3 mb-4">
                @foreach ([
                    ['Dias de direito', $summary['entitled_days']['value']],
                    ['Dias de descanso', $summary['leave_days']['value']],
                    ['Dias de abono', $summary['cash_allowance_days']['value']],
                    ['Total bruto', $summary['gross_total']['value']],
                ] as [$label, $value])
                    <div class="col-12 col-md-6 col-lg-3"><div class="border rounded-3 p-3 h-100"><small class="text-body-secondary d-block">{{ $label }}</small><strong class="fs-5">{{ $value }}</strong></div></div>
                @endforeach
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-7">
                    <h3 class="h6">Memória de valores</h3>
                    <div class="table-responsive"><table class="table align-middle mb-0"><tbody>
                        <tr><th>Remuneração-base</th><td class="text-end">{{ \App\Core\Money\Money::fromMinor($remuneration['base_minor'])->formatPtBr() }}</td></tr>
                        <tr><th>Férias dos dias de descanso</th><td class="text-end">{{ \App\Core\Money\Money::fromMinor($remuneration['vacation_minor'])->formatPtBr() }}</td></tr>
                        <tr><th>Terço constitucional sobre o descanso</th><td class="text-end">{{ \App\Core\Money\Money::fromMinor($remuneration['vacation_third_minor'])->formatPtBr() }}</td></tr>
                        <tr><th>Abono pecuniário</th><td class="text-end">{{ \App\Core\Money\Money::fromMinor($remuneration['cash_allowance_minor'])->formatPtBr() }}</td></tr>
                        <tr><th>Terço sobre o abono</th><td class="text-end">{{ \App\Core\Money\Money::fromMinor($remuneration['cash_allowance_third_minor'])->formatPtBr() }}</td></tr>
                        <tr><th>Descontos informados</th><td class="text-end">- {{ \App\Core\Money\Money::fromMinor($remuneration['other_deductions_minor'])->formatPtBr() }}</td></tr>
                    </tbody></table></div>
                </div>
                <div class="col-12 col-lg-5">
                    <h3 class="h6">Prazos</h3>
                    <dl class="row mb-0">
                        <dt class="col-7">Fim do período aquisitivo</dt><dd class="col-5 text-end">{{ \Carbon\CarbonImmutable::parse($periods['acquisition_end_date'])->format('d/m/Y') }}</dd>
                        <dt class="col-7">Limite do período concessivo</dt><dd class="col-5 text-end">{{ \Carbon\CarbonImmutable::parse($periods['concession_deadline'])->format('d/m/Y') }}</dd>
                        <dt class="col-7">Prazo estimado para pagamento</dt><dd class="col-5 text-end">{{ \Carbon\CarbonImmutable::parse($periods['payment_deadline'])->format('d/m/Y') }}</dd>
                    </dl>
                </div>
            </div>

            @if (! empty($result['warnings']))
                <div class="mt-4">
                    @foreach ($result['warnings'] as $warning)
                        <div class="alert alert-warning d-flex gap-2 align-items-start mb-2" role="alert"><i class="bi bi-exclamation-triangle-fill mt-1" aria-hidden="true"></i><div>{{ $warning['message'] }}</div></div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    <section class="mt-4" aria-labelledby="vacation-guidance-title">
        <h2 id="vacation-guidance-title" class="prazzu-section-title">Como interpretar</h2>
        <div class="row g-3">
            <div class="col-12 col-md-4"><div class="prazzu-form-panel h-100"><h3 class="h6">Dias de direito</h3><p class="text-body-secondary mb-0">As faltas injustificadas podem reduzir o período de 30 para 24, 18 ou 12 dias, ou eliminar o direito no período.</p></div></div>
            <div class="col-12 col-md-4"><div class="prazzu-form-panel h-100"><h3 class="h6">Abono pecuniário</h3><p class="text-body-secondary mb-0">Quando marcado, um terço dos dias de direito é convertido em pagamento, e o restante permanece como descanso.</p></div></div>
            <div class="col-12 col-md-4"><div class="prazzu-form-panel h-100"><h3 class="h6">Resultado estimado</h3><p class="text-body-secondary mb-0">Médias e descontos dependem dos dados fornecidos. Tributos automáticos e casos especiais serão ampliados em etapas posteriores.</p></div></div>
        </div>
    </section>
</div>
@endsection
