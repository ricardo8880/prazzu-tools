@extends('layouts.app')

@section('title', 'Calculadora de Parcelamento Tributário — Prazzu Tools')
@section('meta_description', 'Simule parcelas de uma dívida tributária, encargos, custo final e compare cenários de entrada e prazo.')

@section('content')
<x-tools.page title="Calculadora de Parcelamento Tributário" description="Estime o parcelamento de uma dívida usando a taxa de encargos que se aplica ao seu caso." icon="calendar2-check" slug="calculadora-parcelamento-tributario">
    <x-tools.validation-summary />

    <form data-testid="tool-form-panel" method="post" action="{{ route('tools.calculadora-parcelamento-tributario.calculate') }}" class="d-grid gap-4" data-analytics-form="main">
        @csrf
        <x-tools.form-panel title="Dívida e parcelamento" description="Informe os parâmetros do cenário principal. A versão Essencial já calcula as parcelas aproximadas e o custo final.">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="debt_amount">Dívida</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('debt_amount') is-invalid @enderror" id="debt_amount" name="debt_amount" value="{{ old('debt_amount') }}" inputmode="decimal" placeholder="0,00" required></div>
                    @error('debt_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="installments">Parcelas</label>
                    <div class="input-group"><input class="form-control @error('installments') is-invalid @enderror" id="installments" name="installments" type="number" min="1" max="240" value="{{ old('installments') }}" data-e2e-value="12" required placeholder="Ex.: 18"><span class="input-group-text">meses</span></div>
                    @error('installments')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="monthly_charge">Encargos mensais</label>
                    <div class="input-group"><input class="form-control @error('monthly_charge') is-invalid @enderror" id="monthly_charge" name="monthly_charge" value="{{ old('monthly_charge') }}" data-e2e-value="1,15" inputmode="decimal" required placeholder="Ex.: 1,15"><span class="input-group-text">% a.m.</span></div>
                    @error('monthly_charge')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="form-text">Use a taxa efetivamente informada pelo programa/órgão. Digite 0 se não houver encargo mensal.</div>
                </div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-panel title="Entrada e comparação de cenários" description="Compare alternativas usando a mesma dívida. Campos de cenário em branco são ignorados." badge="Prazzu Plus">
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="entry_amount">Entrada do cenário principal</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="entry_amount" name="entry_amount" value="{{ old('entry_amount') }}" inputmode="decimal"></div>
                    @error('entry_amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Cenário</th><th>Entrada</th><th>Parcelas</th><th>Encargos mensais</th></tr></thead>
                    <tbody>
                        @for($i = 0; $i < 3; $i++)
                            <tr>
                                <td><input class="form-control" name="scenarios[{{ $i }}][name]" value="{{ old("scenarios.$i.name") }}" placeholder="Ex.: Prazo maior"></td>
                                <td><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" name="scenarios[{{ $i }}][entry_amount]" value="{{ old("scenarios.$i.entry_amount") }}" inputmode="decimal"></div></td>
                                <td><input class="form-control" name="scenarios[{{ $i }}][installments]" type="number" min="1" max="240" value="{{ old("scenarios.$i.installments") }}" placeholder="Ex.: 36"></td>
                                <td><div class="input-group"><input class="form-control" name="scenarios[{{ $i }}][monthly_charge]" value="{{ old("scenarios.$i.monthly_charge") }}" inputmode="decimal" placeholder="Ex.: 1,00"><span class="input-group-text">%</span></div></td>
                            </tr>
                            @error("scenarios.$i.entry_amount")<tr><td colspan="4" class="text-danger small">{{ $message }}</td></tr>@enderror
                        @endfor
                    </tbody>
                </table>
            </div>
        </x-tools.form-panel>
        @endif

        <div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular parcelamento</button></div>
    </form>

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        @php($money = static fn(int $minor) => \App\Core\Money\Money::fromMinor($minor)->formatPtBr())
        @php($main = $result->details['scenarios'][0])
        <div class="mt-5">
            <x-tools.result-panel title="Resultado do parcelamento" description="Estimativa do cenário principal com amortização constante e encargos sobre o saldo devedor.">
                <div class="row g-3 mb-4">
                    @foreach($result->summary as $item)<div class="col-12 col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="calculator" /></div>@endforeach
                </div>
                @foreach($result->warnings as $warning)<div class="alert alert-info">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Resumo do cenário principal</h3>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4"><x-tools.result-metric label="Primeira parcela" :value="$money($main['first_installment_minor'])" icon="arrow-up-circle" /></div>
                    <div class="col-12 col-md-4"><x-tools.result-metric label="Última parcela" :value="$money($main['last_installment_minor'])" icon="arrow-down-circle" /></div>
                    <div class="col-12 col-md-4"><x-tools.result-metric label="Entrada" :value="$money($main['entry_minor'])" icon="cash-stack" /></div>
                </div>

                @if(count($result->details['scenarios']) > 1)
                    <h3 class="h5 mt-4">Comparação de cenários <span class="badge text-bg-primary">Plus</span></h3>
                    <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Cenário</th><th class="text-end">Entrada</th><th class="text-end">Prazo</th><th class="text-end">Taxa</th><th class="text-end">Parcela média</th><th class="text-end">Encargos</th><th class="text-end">Custo final</th></tr></thead><tbody>
                        @foreach($result->details['comparison'] as $scenario)<tr><td class="fw-semibold">{{ $scenario['name'] }}</td><td class="text-end">{{ $money($scenario['entry_minor']) }}</td><td class="text-end">{{ $scenario['installments'] }}x</td><td class="text-end">{{ $scenario['monthly_charge'] }}% a.m.</td><td class="text-end">{{ $money($scenario['average_installment_minor']) }}</td><td class="text-end">{{ $money($scenario['total_charges_minor']) }}</td><td class="text-end fw-semibold">{{ $money($scenario['final_cost_minor']) }}</td></tr>@endforeach
                    </tbody></table></div>
                @endif

                <h3 class="h5 mt-4">Evolução do saldo e cronograma <span class="badge text-bg-primary">Plus</span></h3>
                <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Mês</th><th class="text-end">Saldo inicial</th><th class="text-end">Amortização</th><th class="text-end">Encargos</th><th class="text-end">Parcela</th><th class="text-end">Saldo final</th></tr></thead><tbody>
                    @foreach($main['schedule'] as $row)<tr><td>{{ $row['month'] }}</td><td class="text-end">{{ $money($row['opening_balance_minor']) }}</td><td class="text-end">{{ $money($row['amortization_minor']) }}</td><td class="text-end">{{ $money($row['charge_minor']) }}</td><td class="text-end fw-semibold">{{ $money($row['payment_minor']) }}</td><td class="text-end">{{ $money($row['closing_balance_minor']) }}</td></tr>@endforeach
                </tbody></table></div>

                <h3 class="h5 mt-4">Memória de cálculo</h3>
                <div class="accordion mb-4" id="parcelamento-memory">@foreach($result->calculationMemory->steps as $step)<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#parcelamento-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="parcelamento-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}@if($step->roundingPolicy)<div class="small text-body-secondary mt-2">{{ $step->roundingPolicy }}</div>@endif</div></div></div>@endforeach</div>

                @php($exportInput = $calculationInput ?? [])
                <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions">
                    <a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.calculadora-parcelamento-tributario.export', array_merge(['format' => 'pdf'], $exportInput)) }}">Exportar relatório PDF</a>
                    <a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.calculadora-parcelamento-tributario.export', array_merge(['format' => 'xlsx'], $exportInput)) }}">Baixar cronograma XLSX</a>
                </div>
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>
@endsection
