@extends('layouts.app')

@section('title', 'Simulador MEI → Microempresa — Prazzu Tools')
@section('meta_description', 'Compare faturamento atual e projetado com o teto do MEI e simule custos de uma migração para Microempresa.')

@section('content')
<x-tools.page title="Simulador MEI → Microempresa" description="Veja quando o faturamento projetado pressiona a permanência no MEI e simule o peso econômico da migração." icon="arrow-up-right-square" slug="simulador-mei-microempresa">
    <x-tools.validation-summary />

    <form data-testid="tool-form-panel" method="post" action="{{ route('tools.simulador-mei-microempresa.calculate') }}" class="d-grid gap-4" data-analytics-form="main">
        @csrf
        <x-tools.form-panel title="Faturamento atual e projetado" description="A versão Essencial compara sua projeção anual com o teto de referência do MEI e mostra a faixa de impacto do excesso.">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="current_annual_revenue">Faturamento anual atual</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input data-testid="field-current-revenue" class="form-control @error('current_annual_revenue') is-invalid @enderror" id="current_annual_revenue" name="current_annual_revenue" value="{{ old('current_annual_revenue') }}" placeholder="Ex.: 68.000,00" inputmode="decimal" required></div>
                    <div class="form-text">Use a receita bruta anual acumulada/estimada do cenário atual.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="projected_annual_revenue">Faturamento anual projetado</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input data-testid="field-projected-revenue" class="form-control @error('projected_annual_revenue') is-invalid @enderror" id="projected_annual_revenue" name="projected_annual_revenue" value="{{ old('projected_annual_revenue') }}" placeholder="Ex.: 92.000,00" inputmode="decimal" required></div>
                    <div class="form-text">Informe quanto espera faturar no ano completo.</div>
                </div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-panel title="Projeção econômica" description="Plus — parâmetros para estimar impostos, custos empresariais, evolução anual e o faturamento em que o custo adicional da migração fica abaixo da meta escolhida.">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="me_effective_tax_rate">Alíquota efetiva estimada da ME</label>
                    <div class="input-group"><input class="form-control" id="me_effective_tax_rate" name="me_effective_tax_rate" value="{{ old('me_effective_tax_rate') }}" placeholder="Ex.: 7,50" inputmode="decimal"><span class="input-group-text">%</span></div>
                    <div class="form-text">Informe a alíquota efetiva estimada para o seu caso. Não inferimos anexo/CNAE.</div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="monthly_accounting_cost">Custo contábil mensal</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_accounting_cost" name="monthly_accounting_cost" value="{{ old('monthly_accounting_cost') }}" placeholder="Ex.: 650,00" inputmode="decimal"></div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="monthly_other_cost">Outros custos empresariais mensais</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_other_cost" name="monthly_other_cost" value="{{ old('monthly_other_cost') }}" placeholder="Ex.: 300,00" inputmode="decimal"></div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="monthly_mei_cost">Custo mensal atual do MEI</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="monthly_mei_cost" name="monthly_mei_cost" value="{{ old('monthly_mei_cost') }}" placeholder="Ex.: 80,00" inputmode="decimal"></div>
                    <div class="form-text">Informe DAS e outros custos mensais que deseja usar como referência de comparação.</div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="annual_growth_rate">Crescimento anual projetado</label>
                    <div class="input-group"><input class="form-control" id="annual_growth_rate" name="annual_growth_rate" value="{{ old('annual_growth_rate') }}" placeholder="Ex.: 12" inputmode="decimal"><span class="input-group-text">%</span></div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="projection_years">Anos de projeção</label>
                    <input class="form-control" id="projection_years" name="projection_years" type="number" min="1" max="10" value="{{ old('projection_years') }}" placeholder="Ex.: 2">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="target_fixed_cost_burden">Meta de peso adicional da migração</label>
                    <div class="input-group"><input class="form-control" id="target_fixed_cost_burden" name="target_fixed_cost_burden" value="{{ old('target_fixed_cost_burden') }}" placeholder="Ex.: 8" inputmode="decimal"><span class="input-group-text">%</span></div>
                    <div class="form-text">Usada para encontrar o faturamento em que o custo adicional estimado da ME, comparado ao custo atual do MEI informado, cai para essa parcela da receita.</div>
                </div>
            </div>
        </x-tools.form-panel>
        @endif

        <div><button class="btn btn-primary" type="submit">Simular migração</button></div>
    </form>

    @isset($result)
        @php($money = fn (int $minor) => \App\Core\Money\Money::fromMinor($minor)->formatPtBr())
        @php($d = $result->details)
        <div data-analytics-result="main" data-testid="tool-result">
            <x-tools.result-panel title="Impacto estimado da saída do MEI">
                <div class="row g-3 mb-4">
                    @foreach($result->summary as $item)<div class="col-12 col-md-6 col-xl-3"><div class="border rounded p-3 h-100"><div class="small text-body-secondary">{{ $item->label }}</div><div class="fw-semibold fs-5">{{ $item->value }}</div></div></div>@endforeach
                </div>

                <div class="alert {{ $d['band'] === 'within_limit' ? 'alert-success' : ($d['band'] === 'excess_up_to_20' ? 'alert-warning' : 'alert-danger') }}">
                    <strong>{{ $d['band_label'] }}.</strong> {{ $d['impact_text'] }}
                    <div class="small mt-2">Sua projeção equivale a {{ $d['projected_percent_of_limit'] }}% do teto de referência de 2026.</div>
                </div>

                @foreach($result->warnings as $warning)<div class="alert alert-light border small">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Projeção anual <span class="badge text-bg-primary">Plus</span></h3>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6"><div class="border rounded p-3"><div class="small text-body-secondary">Custos fixos anuais informados</div><div class="fs-5 fw-semibold">{{ $money($d['plus']['annual_fixed_costs_minor']) }}</div></div></div>
                    <div class="col-12 col-md-6"><div class="border rounded p-3"><div class="small text-body-secondary">Ponto em que a migração pesa menos — meta {{ $d['plus']['target_fixed_cost_burden'] }}%</div><div class="fs-5 fw-semibold">@if($d['plus']['migration_point_reached']) {{ $money($d['plus']['migration_less_weight_revenue_minor'] ?? 0) }}/ano @else Não atingível com estas premissas @endif</div><div class="small text-body-secondary">Compara o custo anual atual do MEI informado com impostos e custos da ME. Não é um limite jurídico.</div></div></div>
                </div>

                <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Ano</th><th class="text-end">Faturamento</th><th class="text-end">Teto MEI ref.</th><th class="text-end">Impostos estimados</th><th class="text-end">Custos fixos</th><th class="text-end">Custo total ME</th><th class="text-end">Custo adicional</th><th class="text-end">Peso adicional</th></tr></thead><tbody>
                    @foreach($d['plus']['projection'] as $row)<tr><td>{{ $row['year'] }}</td><td class="text-end">{{ $money($row['revenue_minor']) }}</td><td class="text-end">{{ $money($row['mei_limit_minor']) }}</td><td class="text-end">{{ $money($row['estimated_taxes_minor']) }}</td><td class="text-end">{{ $money($row['fixed_costs_minor']) }}</td><td class="text-end fw-semibold">{{ $money($row['total_business_cost_minor']) }}</td><td class="text-end">{{ $money($row['incremental_migration_cost_minor']) }}</td><td class="text-end">{{ $row['incremental_migration_burden_percent'] }}%</td></tr>@endforeach
                </tbody></table></div>

                <h3 class="h5 mt-4">Memória de cálculo</h3>
                <div class="accordion mb-4" id="mei-memory">@foreach($result->calculationMemory->steps as $step)<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mei-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="mei-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}@if($step->roundingPolicy)<div class="small text-body-secondary mt-2">{{ $step->roundingPolicy }}</div>@endif</div></div></div>@endforeach</div>

                @php($exportInput = $calculationInput ?? [])
                <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions">
                    <a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.simulador-mei-microempresa.export', array_merge(['format' => 'pdf'], $exportInput)) }}">Exportar relatório PDF</a>
                    <a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.simulador-mei-microempresa.export', array_merge(['format' => 'xlsx'], $exportInput)) }}">Baixar projeção XLSX</a>
                </div>
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>
@endsection
