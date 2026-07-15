@extends('layouts.app')

@section('title', 'Estimativa de Honorários Contábeis — Prazzu Tools')

@section('content')
<div class="prazzu-page tool-page">
    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-share"></i></span>
        <div>
            <span class="badge text-bg-light border mb-2">Resultado compartilhado</span>
            <h1>Estimativa de Honorários Contábeis</h1>
            <p>Simulação gerada em {{ $calculation->created_at?->format('d/m/Y H:i') }}.</p>
        </div>
    </header>

    <section class="prazzu-tool-workspace text-start">
        <div class="row g-3 mb-4">
            @foreach ([
                ['Honorário mínimo', data_get($calculation->result, 'minimum_fee')],
                ['Honorário recomendado', data_get($calculation->result, 'recommended_fee')],
                ['Referência superior', data_get($calculation->result, 'upper_reference_fee')],
            ] as [$label, $value])
                <div class="col-12 col-md-4"><div class="card h-100 border-0 bg-body-tertiary"><div class="card-body"><div class="small text-body-secondary">{{ $label }}</div><div class="fs-4 fw-bold">{{ $value }}</div></div></div></div>
            @endforeach
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card h-100"><div class="card-header fw-semibold">Cenário analisado</div><div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Faturamento mensal</dt><dd class="col-6 text-end">R$ {{ data_get($calculation->input, 'monthly_revenue') }}</dd>
                        <dt class="col-6">Funcionários</dt><dd class="col-6 text-end">{{ data_get($calculation->input, 'employees') }}</dd>
                        <dt class="col-6">Sócios</dt><dd class="col-6 text-end">{{ data_get($calculation->input, 'partners') }}</dd>
                        <dt class="col-6">Regime</dt><dd class="col-6 text-end">{{ data_get($calculation->input, 'tax_regime') }}</dd>
                        <dt class="col-6">Complexidade</dt><dd class="col-6 text-end">{{ data_get($calculation->result, 'complexity_level') }} ({{ data_get($calculation->result, 'complexity_score') }}/100)</dd>
                    </dl>
                </div></div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card h-100"><div class="card-header fw-semibold">Observação</div><div class="card-body">
                    <p class="mb-0">Esta estimativa é gerencial. O valor final deve considerar escopo contratado, custos internos, região, riscos e posicionamento do escritório.</p>
                </div></div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4 d-print-none">
            <button class="btn btn-outline-secondary" type="button" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir ou salvar em PDF</button>
            <a class="btn btn-primary" href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">Criar minha estimativa</a>
        </div>
    </section>
</div>
@endsection
