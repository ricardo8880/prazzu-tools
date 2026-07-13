@extends('layouts.app')

@section('title', 'Calculadora de Margem e Markup — Prazzu Tools')
@section('meta_description', 'Calcule preço de venda, lucro, margem e markup.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-margem-markup">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li><li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li><li class="breadcrumb-item active">Calculadora de Margem e Markup</li></ol></nav>
    <header class="prazzu-tool-intro"><span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-percent"></i></span><div class="flex-grow-1"><span class="prazzu-badge prazzu-badge--green">Grátis</span><h1>Calculadora de Margem e Markup</h1><p>Calcule o preço de venda necessário para atingir a margem desejada.</p></div></header>

    <section class="prazzu-tool-workspace text-start" aria-labelledby="tool-workspace-title">
        <h2 id="tool-workspace-title">Dados do cálculo</h2>
        <form method="post" action="{{ route('tools.calculadora-margem-markup.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6"><label class="form-label" for="reference_date">Data de referência</label><input class="form-control" id="reference_date" type="date" name="reference_date" value="{{ old('reference_date', date('Y-m-d')) }}" required></div>
            <div class="col-12 col-md-6"><label class="form-label" for="base_cost">Custo base</label><input class="form-control" id="base_cost" name="base_cost" value="{{ old('base_cost') }}" placeholder="1.000,00" inputmode="decimal" required></div>
            <div class="col-12 col-md-6"><label class="form-label" for="additional_costs">Custos adicionais</label><input class="form-control" id="additional_costs" name="additional_costs" value="{{ old('additional_costs', '0,00') }}" placeholder="100,00" inputmode="decimal"></div>
            <div class="col-12 col-md-6"><label class="form-label" for="desired_margin">Margem desejada (%)</label><input class="form-control" id="desired_margin" type="number" step="0.000001" min="0" max="99.999999" name="desired_margin" value="{{ old('desired_margin') }}" placeholder="30" required></div>
            @if ($errors->any())<div class="col-12"><div class="alert alert-danger mb-0"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
            <div class="col-12"><button class="btn btn-primary prazzu-btn-primary" type="submit">Calcular</button></div>
        </form>
    </section>

    @if (session('calculation_result'))
        @php($result = session('calculation_result'))
        <section class="mt-4" aria-labelledby="calculation-result-title">
            <h2 id="calculation-result-title" class="prazzu-section-title">Resultado</h2>
            <div class="row g-3">
                @foreach (['total_cost' => 'Custo total', 'sale_price' => 'Preço de venda', 'profit' => 'Lucro', 'margin' => 'Margem', 'markup' => 'Markup'] as $key => $label)
                    <div class="col-12 col-md-6 col-xl-4"><div class="prazzu-related-tool h-100"><span><small>{{ $label }}</small><strong>{{ $result[$key] }}</strong></span></div></div>
                @endforeach
            </div>
            <form method="post" action="{{ route('tools.calculadora-margem-markup.export') }}" class="mt-3">@csrf
                @foreach (['reference_date','base_cost','additional_costs','desired_margin'] as $field)<input type="hidden" name="{{ $field }}" value="{{ old($field) }}">@endforeach
                <button class="btn prazzu-btn-outline" type="submit"><i class="bi bi-download"></i> Exportar CSV</button>
            </form>
            <p class="text-body-secondary mt-3 mb-0"><small>Regra {{ $result['rule_version'] }}. Resultado estimativo para apoio gerencial.</small></p>
        </section>
    @endif
</div>
@endsection
