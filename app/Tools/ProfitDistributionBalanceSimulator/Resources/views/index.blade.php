@extends('layouts.app')
@section('title','Distribuição de Lucros: Balanço × sem Balanço — Prazzu Tools')
@section('meta_description','Compare quanto pode ser distribuído com balanço, sem balanço e simulação gerencial de escrituração.')
@section('content')
<x-tools.page title="Distribuição de Lucros: Balanço × sem Balanço" description="Compare os dois cenários e, no Plus, simule a formação do lucro contábil e o planejamento acumulado." icon="diagram-3" slug="simulador-distribuicao-lucros-balanco">
    <x-tools.validation-summary />

    <form data-testid="tool-form-panel" method="post" action="{{ route('tools.simulador-distribuicao-lucros-balanco.calculate') }}" class="d-grid gap-4" data-analytics-form="main">
        @csrf
        <x-tools.form-panel title="Comparação Essencial" description="Informe os parâmetros contábeis do período; a ferramenta não escolhe automaticamente percentuais legais por atividade.">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Faturamento do período</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="annual_revenue" value="{{ old('annual_revenue','240.000,00') }}" required></div></div>
                <div class="col-md-6"><label class="form-label">Lucro contábil apurado</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="accounting_profit" value="{{ old('accounting_profit','72.000,00') }}" required></div><div class="form-text">Usado no Essencial e como referência quando a simulação de escrituração não estiver marcada.</div></div>
                <div class="col-md-6"><label class="form-label">Percentual de referência sem balanço</label><div class="input-group"><input class="form-control" name="reference_margin" value="{{ old('reference_margin','32') }}" required><span class="input-group-text">%</span></div></div>
                <div class="col-md-6"><label class="form-label">Tributos sobre a receita no período</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="taxes_on_revenue" value="{{ old('taxes_on_revenue','14.400,00') }}" required></div></div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-panel title="Escrituração e planejamento" description="Plus — simule a formação do lucro contábil, pró-labore, despesas, distribuições acumuladas e planejamento de até 24 meses.">
            <div class="row g-3">
                <div class="col-12"><div class="form-check"><input type="hidden" name="simulate_bookkeeping" value="0"><input class="form-check-input" type="checkbox" id="simulate_bookkeeping" name="simulate_bookkeeping" value="1" @checked(old('simulate_bookkeeping'))><label class="form-check-label" for="simulate_bookkeeping">Simular escrituração/balanço a partir dos componentes abaixo</label></div></div>
                <div class="col-md-4"><label class="form-label">Despesas operacionais anuais</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="operating_expenses" value="{{ old('operating_expenses','60.000,00') }}"></div></div>
                <div class="col-md-4"><label class="form-label">Outras despesas anuais</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="other_expenses" value="{{ old('other_expenses','0') }}"></div></div>
                <div class="col-md-4"><label class="form-label">Distribuições anteriores</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="prior_distributions" value="{{ old('prior_distributions','0') }}"></div></div>
                <div class="col-md-4"><label class="form-label">Pró-labore mensal</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="monthly_pro_labore" value="{{ old('monthly_pro_labore','3.000,00') }}"></div></div>
                <div class="col-md-4"><label class="form-label">Crescimento mensal</label><div class="input-group"><input class="form-control" name="monthly_growth_rate" value="{{ old('monthly_growth_rate','0') }}"><span class="input-group-text">%</span></div></div>
                <div class="col-md-4"><label class="form-label">Meses de planejamento</label><input class="form-control" type="number" name="planning_months" min="1" max="24" value="{{ old('planning_months',12) }}"></div>
            </div>
        </x-tools.form-panel>
        @endif

        <div><button class="btn btn-primary" type="submit">Comparar distribuição</button></div>
    </form>

    @isset($result)
        @php($money=fn(int $m)=>\App\Core\Money\Money::fromMinor($m)->formatPtBr())
        @php($d=$result->details)
        <div data-analytics-result="main" data-testid="tool-result" class="mt-5">
            <x-tools.result-panel title="Comparação estimada">
                <div class="row g-3 mb-4">@foreach($result->summary as $item)<div class="col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="pie-chart" /></div>@endforeach</div>
                @foreach($result->warnings as $warning)<div class="alert alert-light border small">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Escrituração/balanço simulado <span class="badge text-bg-primary">Plus</span></h3>
                <div class="table-responsive"><table class="table table-sm"><tbody>
                    <tr><th>Modo</th><td class="text-end">{{ $d['bookkeeping']['enabled'] ? 'Simulação pelos componentes' : 'Lucro contábil informado' }}</td></tr>
                    <tr><th>Receita</th><td class="text-end">{{ $money($d['bookkeeping']['revenue_minor']) }}</td></tr>
                    <tr><th>Tributos</th><td class="text-end">{{ $money($d['bookkeeping']['taxes_minor']) }}</td></tr>
                    <tr><th>Pró-labore anual</th><td class="text-end">{{ $money($d['bookkeeping']['annual_pro_labore_minor']) }}</td></tr>
                    <tr><th>Despesas operacionais</th><td class="text-end">{{ $money($d['bookkeeping']['operating_expenses_minor']) }}</td></tr>
                    <tr><th>Outras despesas</th><td class="text-end">{{ $money($d['bookkeeping']['other_expenses_minor']) }}</td></tr>
                    <tr class="table-light"><th>Lucro contábil usado</th><td class="text-end fw-semibold">{{ $money($d['bookkeeping']['accounting_profit_used_minor']) }}</td></tr>
                    <tr><th>Lucros retidos após distribuições anteriores</th><td class="text-end">{{ $money($d['bookkeeping']['retained_earnings_after_distributions_minor']) }}</td></tr>
                </tbody></table></div>

                <h3 class="h5 mt-4">Planejamento anual <span class="badge text-bg-primary">Plus</span></h3>
                <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Ano</th><th class="text-end">Receita</th><th class="text-end">Lucro</th><th class="text-end">Tributos</th><th class="text-end">Pró-labore</th><th class="text-end">Capacidade com balanço</th><th class="text-end">Capacidade sem balanço</th></tr></thead><tbody>
                    @foreach($d['planning']['annual_rows'] as $r)<tr><td>{{ $r['year'] }}</td><td class="text-end">{{ $money($r['revenue_minor']) }}</td><td class="text-end">{{ $money($r['accounting_profit_minor']) }}</td><td class="text-end">{{ $money($r['taxes_minor']) }}</td><td class="text-end">{{ $money($r['pro_labore_minor']) }}</td><td class="text-end fw-semibold">{{ $money($r['with_balance_capacity_minor']) }}</td><td class="text-end">{{ $money($r['without_balance_capacity_minor']) }}</td></tr>@endforeach
                </tbody></table></div>

                <h3 class="h5 mt-4">Distribuição acumulada <span class="badge text-bg-primary">Plus</span></h3>
                <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Mês</th><th class="text-end">Receita</th><th class="text-end">Lucro contábil</th><th class="text-end">Sem balanço</th><th class="text-end">Pró-labore</th><th class="text-end">Acum. com balanço</th><th class="text-end">Acum. sem balanço</th></tr></thead><tbody>
                    @foreach($d['planning']['rows'] as $r)<tr><td>{{ $r['month'] }}</td><td class="text-end">{{ $money($r['revenue_minor']) }}</td><td class="text-end">{{ $money($r['accounting_profit_minor']) }}</td><td class="text-end">{{ $money($r['without_balance_capacity_minor']) }}</td><td class="text-end">{{ $money($r['pro_labore_minor']) }}</td><td class="text-end fw-semibold">{{ $money($r['cum_with_balance_minor']) }}</td><td class="text-end">{{ $money($r['cum_without_balance_minor']) }}</td></tr>@endforeach
                </tbody></table></div>

                @php($exportInput=$calculationInput??[])
                <div class="d-flex gap-2 flex-wrap mt-4" data-testid="download-actions"><a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.simulador-distribuicao-lucros-balanco.export',array_merge(['format'=>'pdf'],$exportInput)) }}">Exportar PDF</a><a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.simulador-distribuicao-lucros-balanco.export',array_merge(['format'=>'xlsx'],$exportInput)) }}">Baixar XLSX</a></div>
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>
@endsection
