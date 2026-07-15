@extends('layouts.app')

@section('title', 'Calculadora de Margem e Markup — Prazzu Tools')
@section('meta_description', 'Calcule preço de venda, lucro, margem e markup.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-margem-markup">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li><li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li><li class="breadcrumb-item active">Calculadora de Margem e Markup</li></ol></nav>
    <header class="prazzu-tool-intro"><span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-percent"></i></span><div class="flex-grow-1"><span class="prazzu-badge prazzu-badge--green">Grátis</span><h1>Calculadora de Margem e Markup</h1><p>Calcule o preço de venda necessário para atingir a margem desejada.</p></div></header>

    <section class="prazzu-tool-workspace text-start" aria-labelledby="tool-workspace-title">
        <div class="mb-4">
            <h2 id="tool-workspace-title" class="mb-1">Dados do cálculo</h2>
            <p class="text-body-secondary mb-0">Informe os custos e a margem desejada para estimar o preço de venda.</p>
        </div>
        <form method="post" action="{{ route('tools.calculadora-margem-markup.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6"><label class="form-label" for="reference_date">Data de referência</label><input class="form-control" id="reference_date" type="date" name="reference_date" value="{{ old('reference_date', date('Y-m-d')) }}" required></div>
            <div class="col-12 col-md-6"><label class="form-label" for="base_cost">Custo base</label><input class="form-control" id="base_cost" name="base_cost" value="{{ old('base_cost') }}" placeholder="1.000,00" inputmode="decimal" required></div>
            <div class="col-12 col-md-6"><label class="form-label" for="additional_costs">Custos adicionais</label><input class="form-control" id="additional_costs" name="additional_costs" value="{{ old('additional_costs', '0,00') }}" placeholder="100,00" inputmode="decimal"></div>
            <div class="col-12 col-md-6"><label class="form-label" for="desired_margin">Margem desejada (%)</label><input class="form-control" id="desired_margin" type="number" step="0.000001" min="0" max="99.999999" name="desired_margin" value="{{ old('desired_margin') }}" placeholder="30" required></div>
            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0" role="alert">
                        <div class="fw-semibold mb-1">Revise os dados informados:</div>
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif
            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-calculator me-1" aria-hidden="true"></i> Calcular margem e markup</button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-margem-markup.index') }}"><i class="bi bi-eraser me-1" aria-hidden="true"></i> Limpar formulário</a>
            </div>
        </form>
    </section>

    @if (session('calculation_result'))
        @php($result = session('calculation_result'))
        <section class="mt-4" aria-labelledby="calculation-result-title">
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-3">
                <div>
                    <h2 id="calculation-result-title" class="prazzu-section-title mb-1">Resultado do cálculo</h2>
                    <p class="text-body-secondary mb-0">Confira o preço sugerido e a composição de margem e markup.</p>
                </div>
                <span class="badge text-bg-success align-self-start align-self-md-center">Estimativa calculada</span>
            </div>
            <div class="row g-3">
                @foreach (['total_cost' => 'Custo total', 'sale_price' => 'Preço de venda', 'profit' => 'Lucro', 'margin' => 'Margem', 'markup' => 'Markup'] as $key => $label)
                    <div class="col-12 col-md-6 col-xl-4"><div class="prazzu-related-tool h-100"><span><small>{{ $label }}</small><strong>{{ $result[$key] }}</strong></span></div></div>
                @endforeach
            </div>
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mt-3">
                <form method="post" action="{{ route('tools.calculadora-margem-markup.export') }}">@csrf
                    @foreach (['reference_date','base_cost','additional_costs','desired_margin'] as $field)<input type="hidden" name="{{ $field }}" value="{{ old($field) }}">@endforeach
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-filetype-csv me-1" aria-hidden="true"></i> Exportar CSV</button>
                </form>
                <p class="text-body-secondary mb-0"><small>Regra {{ $result['rule_version'] }}. Resultado estimativo para apoio gerencial.</small></p>
            </div>
        </section>
    @endif
</div>
@endsection
