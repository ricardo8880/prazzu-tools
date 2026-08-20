@extends('layouts.app')

@section('title', 'Calculadora de IRPJ e CSLL — Lucro Presumido')
@section('meta_description', 'Calcule IRPJ, adicional de IRPJ e CSLL no Lucro Presumido com apuração mensal ou trimestral, múltiplas atividades, cenários e memória fiscal.')

@section('content')
<x-tools.page
    title="Calculadora de IRPJ e CSLL — Lucro Presumido"
    description="Apure bases presumidas, IRPJ, adicional e CSLL, com modo mensal ou trimestral e comparação de cenários."
    icon="bi-building-check"
    slug="calculadora-irpj-csll-lucro-presumido"
>
    <div class="alert alert-info">
        <strong>Escopo 2026.</strong> Esta versão atende pessoas jurídicas em geral no Lucro Presumido. Em 2026, a LC 224/2025 acrescenta 10% aos percentuais de presunção sobre a parcela de receita acima do limite aplicável. Instituições financeiras e situações com regime específico não estão cobertas.
    </div>

    <x-tools.validation-summary />
    @auth
        <div class="mb-3"><a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-irpj-csll-lucro-presumido.history.index') }}"><i class="bi bi-clock-history me-1"></i>Histórico Plus</a></div>
    @endauth

    <form method="POST" action="{{ route('tools.calculadora-irpj-csll-lucro-presumido.calculate') }}" class="vstack gap-4">
        @csrf
        <x-tools.form-panel title="Receita do período" description="Preencha as atividades existentes no período. A apuração trimestral resolve o caso Essencial; o modo mensal e os cenários fazem parte do Plus." badge="Essencial + Plus">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="periodicity">Periodicidade</label>
                    <select class="form-select" name="periodicity" id="periodicity">
                        <option value="quarterly" @selected(old('periodicity','quarterly') === 'quarterly')>Trimestral</option>
                        @if($plusEnabled ?? true)<option value="monthly" @selected(old('periodicity') === 'monthly')>Mensal — Plus</option>@endif
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="quarter">Trimestre de 2026</label>
                    <select class="form-select" name="quarter" id="quarter">
                        @foreach([1 => '1º trimestre', 2 => '2º trimestre', 3 => '3º trimestre', 4 => '4º trimestre'] as $value => $label)
                            <option value="{{ $value }}" @selected((int) old('quarter', 3) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @if($plusEnabled ?? true)
                <div class="col-md-4">
                    <label class="form-label" for="month">Mês de 2026 — Plus</label>
                    <select class="form-select" name="month" id="month"><option value="">Selecione no modo mensal</option>@foreach(range(1,12) as $m)<option value="{{ $m }}" @selected((int) old('month') === $m)>{{ str_pad((string)$m,2,'0',STR_PAD_LEFT) }}/2026</option>@endforeach</select>
                </div>
                @endif

                <div class="col-md-6"><x-tools.form.money name="commerce_revenue" label="Comércio / indústria / carga / hospitalar qualificado" :value="old('commerce_revenue')" data-e2e-value="5.000,00" required help="Presunção-base: IRPJ 8% e CSLL 12%. Use serviço hospitalar apenas se os requisitos legais forem atendidos." /></div>
                <div class="col-md-6"><x-tools.form.money name="fuel_revenue" label="Revenda de combustíveis elegível" :value="old('fuel_revenue')" required help="Presunção-base: IRPJ 1,6% e CSLL 12%." /></div>
                <div class="col-md-6"><x-tools.form.money name="passenger_transport_revenue" label="Transporte de passageiros" :value="old('passenger_transport_revenue')" required help="Presunção-base: IRPJ 16% e CSLL 12%." /></div>
                <div class="col-md-6"><x-tools.form.money name="services_revenue" label="Serviços em geral / intermediação / locação" :value="old('services_revenue')" required help="Presunção-base: IRPJ 32% e CSLL 32%." /></div>
                <div class="col-12"><x-tools.form.money name="other_taxable_additions" label="Adições tributáveis integralmente" :value="old('other_taxable_additions')" required help="Ex.: receitas financeiras, ganhos de capital e outros valores que devam ser somados integralmente às bases. Não entram no limite da receita sujeita aos coeficientes de presunção." /></div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-disclosure title="Ajustes do ano e créditos" description="Abra se houver acumulados anteriores ou créditos/retenções compensáveis." badge="Prazzu Plus" :open="$errors->hasAny(['prior_irpj_presumption_revenue', 'prior_csll_presumption_revenue', 'irpj_credits', 'csll_credits'])">
            <div class="row g-3">
                <div class="col-md-6"><x-tools.form.money name="prior_irpj_presumption_revenue" label="Receita anterior no ano — limite IRPJ" :value="old('prior_irpj_presumption_revenue')" required help="Receita bruta dos trimestres anteriores de 2026 sujeita a coeficientes de presunção. Não inclua receitas financeiras/ganhos adicionados integralmente." /></div>
                <div class="col-md-6"><x-tools.form.money name="prior_csll_presumption_revenue" label="Receita anterior desde o 2º tri — limite CSLL" :value="old('prior_csll_presumption_revenue')" required help="Para Q3/Q4, informe somente a receita sujeita à presunção desde o 2º trimestre. Em Q1/Q2, normalmente é zero." /></div>
                <div class="col-md-6"><x-tools.form.money name="irpj_credits" label="Créditos/retenções de IRPJ compensáveis" :value="old('irpj_credits')" required help="Informe somente valores cuja compensação no período tenha sido confirmada." /></div>
                <div class="col-md-6"><x-tools.form.money name="csll_credits" label="Créditos/retenções de CSLL compensáveis" :value="old('csll_credits')" required help="Informe somente valores cuja compensação no período tenha sido confirmada." /></div>
            </div>
        </x-tools.form-disclosure>
        @endif


        @if($plusEnabled ?? true)
        <x-tools.form-disclosure title="Comparação de cenários" description="Abra para comparar até três alternativas no mesmo período." badge="Prazzu Plus" :open="$errors->hasAny(['scenarios.*'])">
            @foreach([0,1] as $scenarioIndex)
                <div class="border rounded p-3 mb-3"><div class="row g-3">
                    <div class="col-12"><label class="form-label">Nome do cenário {{ $scenarioIndex + 2 }}</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][name]" value="{{ old('scenarios.'.$scenarioIndex.'.name', 'Cenário '.($scenarioIndex + 2)) }}"></div>
                    <div class="col-md-3"><x-tools.form.money :name="'scenarios['.$scenarioIndex.'][commerce_revenue]'" label="Comércio/indústria" :value="old('scenarios.'.$scenarioIndex.'.commerce_revenue')" /></div>
                    <div class="col-md-3"><x-tools.form.money :name="'scenarios['.$scenarioIndex.'][fuel_revenue]'" label="Combustíveis" :value="old('scenarios.'.$scenarioIndex.'.fuel_revenue')" /></div>
                    <div class="col-md-3"><x-tools.form.money :name="'scenarios['.$scenarioIndex.'][passenger_transport_revenue]'" label="Transporte passageiros" :value="old('scenarios.'.$scenarioIndex.'.passenger_transport_revenue')" /></div>
                    <div class="col-md-3"><x-tools.form.money :name="'scenarios['.$scenarioIndex.'][services_revenue]'" label="Serviços em geral" :value="old('scenarios.'.$scenarioIndex.'.services_revenue')" /></div>
                    <div class="col-12"><x-tools.form.money :name="'scenarios['.$scenarioIndex.'][other_taxable_additions]'" label="Adições integrais" :value="old('scenarios.'.$scenarioIndex.'.other_taxable_additions')" /></div>
                </div></div>
            @endforeach
        </x-tools.form-disclosure>
        @endif
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="confirm_scope" value="1" id="confirm_scope" required @checked(old('confirm_scope'))>
            <label class="form-check-label" for="confirm_scope">Confirmo que revisei o enquadramento das atividades, os percentuais de presunção, as adições integrais e os créditos aplicáveis ao caso.</label>
        </div>

        <div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular IRPJ e CSLL</button></div>
    </form>

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        @php($money = static fn (int $minor) => \App\Core\Money\Money::fromMinor($minor)->formatPtBr())
        <div class="mt-5">
            <x-tools.result-panel :title="$result- eyebrow="Apuração concluída">details['periodicity'] === 'monthly' ? 'IRPJ e CSLL apurados no mês' : 'IRPJ e CSLL apurados no trimestre'" description="Estimativa conforme os dados e enquadramentos confirmados no formulário.">
                <div class="row g-3 mb-4">
                    @foreach($result->summary as $item)
                        <div class="col-12 col-md-6 col-xl"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="calculator" /></div>
                    @endforeach
                </div>

                @php
                    $incomeTaxShareHundredths = $result->details['total_revenue_minor'] > 0
                        ? intdiv(($result->details['total_due_minor'] * 10_000) + intdiv($result->details['total_revenue_minor'], 2), $result->details['total_revenue_minor'])
                        : 0;
                    $incomeTaxShare = number_format($incomeTaxShareHundredths / 100, 2, ',', '.');
                @endphp
                <x-tools.result-insight
                    :title="'IRPJ + CSLL estimados em '.$money($result->details['total_due_minor'])"
                    :description="'Neste cenário, o total de IRPJ e CSLL equivale a '.$incomeTaxShare.'% da receita bruta sujeita à presunção informada. Esse percentual não representa a carga tributária total da empresa.'"
                    :items="[
                        'IRPJ a pagar: '.$money($result->details['irpj_due_minor']).'.',
                        'CSLL a pagar: '.$money($result->details['csll_due_minor']).'.',
                        $result->details['irpj_additional_minor'] > 0 ? 'Houve adicional de IRPJ de 10% no valor de '.$money($result->details['irpj_additional_minor']).'.' : 'Não houve adicional de IRPJ de 10% neste cenário.',
                        ($result->details['irpj_credits_minor'] + $result->details['csll_credits_minor']) > 0 ? 'Créditos informados reduziram a apuração em até '.$money($result->details['irpj_credits_minor'] + $result->details['csll_credits_minor']).', respeitado o limite de saldo zero.' : 'Nenhum crédito foi informado para reduzir IRPJ ou CSLL.',
                    ]"
                    icon="bi-calculator"
                />

                @foreach($result->warnings as $warning)<div class="alert alert-warning">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Apuração</h3>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <tbody>
                            <tr><th>Receita bruta sujeita à presunção</th><td class="text-end">{{ $money($result->details['total_revenue_minor']) }}</td></tr>
                            <tr><th>Faixa normal disponível — IRPJ</th><td class="text-end">{{ $money($result->details['irpj_normal_allowance_minor']) }}</td></tr>
                            <tr><th>Base presumida IRPJ</th><td class="text-end">{{ $money($result->details['irpj_presumed_base_minor']) }}</td></tr>
                            <tr><th>Adições integrais</th><td class="text-end">{{ $money($result->details['other_taxable_additions_minor']) }}</td></tr>
                            <tr><th>IRPJ 15%</th><td class="text-end">{{ $money($result->details['irpj_main_minor']) }}</td></tr>
                            <tr><th>Adicional IRPJ 10%</th><td class="text-end">{{ $money($result->details['irpj_additional_minor']) }}</td></tr>
                            <tr><th>Créditos IRPJ</th><td class="text-end">- {{ $money($result->details['irpj_credits_minor']) }}</td></tr>
                            <tr class="table-light"><th>IRPJ a pagar</th><td class="text-end fw-bold">{{ $money($result->details['irpj_due_minor']) }}</td></tr>
                            <tr><th>Faixa normal disponível — CSLL</th><td class="text-end">{{ $money($result->details['csll_normal_allowance_minor']) }}</td></tr>
                            <tr><th>Base presumida CSLL</th><td class="text-end">{{ $money($result->details['csll_presumed_base_minor']) }}</td></tr>
                            <tr><th>CSLL antes dos créditos</th><td class="text-end">{{ $money($result->details['csll_before_credits_minor']) }}</td></tr>
                            <tr><th>Créditos CSLL</th><td class="text-end">- {{ $money($result->details['csll_credits_minor']) }}</td></tr>
                            <tr class="table-light"><th>CSLL a pagar</th><td class="text-end fw-bold">{{ $money($result->details['csll_due_minor']) }}</td></tr>
                            <tr class="table-primary"><th>Total IRPJ + CSLL</th><td class="text-end fw-bold">{{ $money($result->details['total_due_minor']) }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="h5 mt-4">Detalhamento por atividade</h3>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Atividade</th><th class="text-end">Receita</th><th class="text-end">Base IRPJ</th><th class="text-end">Base CSLL</th></tr></thead>
                        <tbody>
                            @foreach($result->details['activities'] as $activity)
                                <tr><td>{{ $activity['label'] }}</td><td class="text-end">{{ $money($activity['revenue_minor']) }}</td><td class="text-end">{{ $money($activity['irpj_base_minor']) }}</td><td class="text-end">{{ $money($activity['csll_base_minor']) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                @if(!empty($result->details['scenario_comparison']))
                    <h3 class="h5 mt-4">Comparação de cenários <span class="badge text-bg-primary">Plus</span></h3>
                    <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Cenário</th><th class="text-end">Receita</th><th class="text-end">IRPJ</th><th class="text-end">CSLL</th><th class="text-end">Total</th><th class="text-end">Diferença</th></tr></thead><tbody>
                    @foreach($result->details['scenario_comparison'] as $scenario)<tr><td>{{ $scenario['name'] }}</td><td class="text-end">{{ $money($scenario['total_revenue_minor']) }}</td><td class="text-end">{{ $money($scenario['irpj_due_minor']) }}</td><td class="text-end">{{ $money($scenario['csll_due_minor']) }}</td><td class="text-end fw-semibold">{{ $money($scenario['total_due_minor']) }}</td><td class="text-end">{{ $money($scenario['difference_from_main_minor']) }}</td></tr>@endforeach
                    </tbody></table></div>
                @endif
                <h3 class="h5 mt-4">Memória de cálculo</h3>
                <div class="accordion mb-4" id="presumed-profit-memory">
                    @foreach($result->calculationMemory->steps as $step)
                        <div class="accordion-item">
                            <h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#presumed-memory-{{ $loop->index }}">{{ $step->label }}</button></h4>
                            <div id="presumed-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}</div></div>
                        </div>
                    @endforeach
                </div>

                @if(!empty($historySaved))<div class="alert alert-success">Cálculo salvo no histórico da sua conta.</div>@endif

                @php($exportInput = array_merge($calculationInput ?? [], ['confirm_scope' => 1]))
                <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions">
                    <a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.calculadora-irpj-csll-lucro-presumido.export', array_merge(['format' => 'pdf'], $exportInput)) }}">Exportar PDF</a>
                    <a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.calculadora-irpj-csll-lucro-presumido.export', array_merge(['format' => 'xlsx'], $exportInput)) }}">Baixar Excel</a>
                </div>

            <x-tools.normative-trust
                :rules="$result->calculationMemory?->normativeRules ?? []"
                :assumptions="$result->calculationMemory?->assumptions ?? []"
                :is-estimate="$result->calculationMemory?->isEstimate ?? false"
            />
</x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>
@endsection
