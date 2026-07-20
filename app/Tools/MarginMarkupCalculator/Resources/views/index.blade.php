@extends('layouts.app')

@section('title', 'Calculadora de Margem, Markup e Formação de Preço — Prazzu Tools')
@section('meta_description', 'Calcule preço de venda, margem, markup, impostos, comissões, taxas e lucro.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-margem-markup">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active">Margem, Markup e Formação de Preço</li>
        </ol>
    </nav>

    <x-tools.intro icon="percent" title="Calculadora de Margem, Markup e Formação de Preço" description="Monte um preço de venda considerando custos, despesas, impostos, comissões e taxas." badge="Grátis">
        <x-slot:actions>
            @auth
                <a class="btn btn-outline-primary" href="{{ route('tools.calculadora-margem-markup.history.index') }}"><i class="bi bi-clock-history me-1"></i>Histórico</a>
            @endauth
        </x-slot:actions>
    </x-tools.intro>

    <x-tool-feature-tiers slug="calculadora-margem-markup" />

    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif

    <x-tools.validation-summary class="mb-4" />

    @if ($taxSnapshotIntegration)
        <div class="alert alert-primary d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" role="status">
            <div>
                <div class="fw-semibold"><i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i> Alíquota disponível do Simples Nacional</div>
                <div class="small">Alíquota efetiva calculada: {{ $taxSnapshotIntegration->data['effective_rate'] }}. Revise antes de usar na formação do preço.</div>
            </div>
            <button class="btn btn-primary btn-sm flex-shrink-0" type="button" data-apply-effective-rate>Usar como imposto</button>
        </div>
    @endif

    <section class="prazzu-tool-workspace text-start" aria-labelledby="tool-workspace-title">
        <div class="mb-4">
            <h2 id="tool-workspace-title" class="mb-1">Dados do cálculo</h2>
            <p class="text-body-secondary mb-0">Preencha somente os campos aplicáveis ao seu negócio.</p>
        </div>

        <form method="post" action="{{ route('tools.calculadora-margem-markup.calculate') }}" class="row g-3">
            @csrf

            <div class="col-12 col-md-4">
                <label class="form-label" for="reference_date">Data de referência</label>
                <input class="form-control" id="reference_date" type="date" name="reference_date" value="{{ old('reference_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="base_cost">Custo base</label>
                <input class="form-control" id="base_cost" name="base_cost" value="{{ old('base_cost') }}" placeholder="1.000,00" inputmode="decimal" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="additional_costs">Outros custos</label>
                <input class="form-control" id="additional_costs" name="additional_costs" value="{{ old('additional_costs', '0,00') }}" placeholder="0,00" inputmode="decimal">
            </div>

            <div class="col-12"><hr class="my-1"><h3 class="h5 mb-0">Despesas por unidade ou venda</h3></div>

            <div class="col-12 col-md-4">
                <label class="form-label" for="freight_cost">Frete</label>
                <input class="form-control" id="freight_cost" name="freight_cost" value="{{ old('freight_cost', '0,00') }}" placeholder="0,00" inputmode="decimal">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="packaging_cost">Embalagem</label>
                <input class="form-control" id="packaging_cost" name="packaging_cost" value="{{ old('packaging_cost', '0,00') }}" placeholder="0,00" inputmode="decimal">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="fixed_expenses">Despesas rateadas</label>
                <input class="form-control" id="fixed_expenses" name="fixed_expenses" value="{{ old('fixed_expenses', '0,00') }}" placeholder="0,00" inputmode="decimal">
                <div class="form-text">Ex.: aluguel, energia e mão de obra rateados por item.</div>
            </div>

            <div class="col-12"><hr class="my-1"><h3 class="h5 mb-0">Percentuais sobre a venda</h3></div>

            @foreach ([
                'desired_margin' => ['Margem líquida desejada (%)', '30'],
                'taxes_percentage' => ['Impostos (%)', '0'],
                'commission_percentage' => ['Comissão (%)', '0'],
                'card_fees_percentage' => ['Taxas de cartão (%)', '0'],
                'marketplace_fees_percentage' => ['Taxas de marketplace (%)', '0'],
            ] as $field => [$label, $placeholder])
                <div class="col-12 col-md-6 col-xl">
                    <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                    <input class="form-control" id="{{ $field }}" type="number" step="0.000001" min="0" max="99.999999" name="{{ $field }}" value="{{ old($field, $field === 'desired_margin' ? '' : '0') }}" placeholder="{{ $placeholder }}" {{ $field === 'desired_margin' ? 'required' : '' }}>
                </div>
            @endforeach

            <div class="col-12">
                <div class="alert alert-info mb-0" role="note">
                    <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                    A soma da margem, impostos, comissão e taxas precisa ser menor que 100%.
                </div>
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
                <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-calculator me-1"></i> Calcular preço</button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-margem-markup.index') }}"><i class="bi bi-eraser me-1"></i> Limpar</a>
            </div>
        </form>
    </section>

    @if (session('calculation_result'))
        @php($result = session('calculation_result'))
        <section class="mt-4" aria-labelledby="calculation-result-title">
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-3">
                <div>
                    <h2 id="calculation-result-title" class="prazzu-section-title mb-1">Resultado da formação de preço</h2>
                    <p class="text-body-secondary mb-0">Valores estimados com base nos dados informados.</p>
                </div>
                <span class="badge text-bg-success">Cálculo concluído</span>
            </div>

            <div class="row g-3 mb-3">
                @foreach ([
                    'sale_price' => 'Preço de venda sugerido',
                    'net_profit' => 'Lucro líquido estimado',
                    'total_cost' => 'Custo total',
                    'gross_profit' => 'Lucro bruto',
                    'margin' => 'Margem líquida',
                    'markup' => 'Markup sobre o custo',
                    'markup_multiplier' => 'Índice de markup',
                ] as $key => $label)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="prazzu-related-tool h-100"><span><small>{{ $label }}</small><strong>{{ $result[$key] }}</strong></span></div>
                    </div>
                @endforeach
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h5 card-title">Valores descontados da venda</h3>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <tbody>
                                <tr><th scope="row">Impostos</th><td class="text-end">{{ $result['taxes_amount'] }}</td></tr>
                                <tr><th scope="row">Comissão</th><td class="text-end">{{ $result['commission_amount'] }}</td></tr>
                                <tr><th scope="row">Taxas de cartão</th><td class="text-end">{{ $result['card_fees_amount'] }}</td></tr>
                                <tr><th scope="row">Taxas de marketplace</th><td class="text-end">{{ $result['marketplace_fees_amount'] }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                <form method="post" action="{{ route('tools.calculadora-margem-markup.export') }}">
                    @csrf
                    @foreach (['reference_date','base_cost','additional_costs','freight_cost','packaging_cost','fixed_expenses','desired_margin','taxes_percentage','commission_percentage','card_fees_percentage','marketplace_fees_percentage'] as $field)
                        <input type="hidden" name="{{ $field }}" value="{{ old($field) }}">
                    @endforeach
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-filetype-csv me-1"></i> Exportar CSV</button>
                    <button class="btn btn-outline-danger" type="submit" formaction="{{ route('tools.calculadora-margem-markup.export.pdf') }}" formtarget="_blank"><i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF</button>
                </form>
                <p class="text-body-secondary mb-0"><small>Regra {{ $result['rule_version'] }}. Estimativa para apoio gerencial.</small></p>
            </div>
        </section>
    @endif
    @include('tools-calculadora-margem-markup::partials.pricing-scenarios')
    @include('tools-calculadora-margem-markup::partials.product-import')
    @include('tools-calculadora-margem-markup::partials.batch-calculator')
</div>
@if ($taxSnapshotIntegration)
    <script>
        document.querySelector('[data-apply-effective-rate]')?.addEventListener('click', () => {
            const rate = String(@json($taxSnapshotIntegration->data['effective_rate']))
                .replace('%', '').replace('.', '').replace(',', '.').trim();
            document.getElementById('taxes_percentage').value = rate;
            document.getElementById('taxes_percentage').focus();
        });
    </script>
@endif

@endsection
