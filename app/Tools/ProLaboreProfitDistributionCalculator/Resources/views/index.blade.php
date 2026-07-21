@extends('layouts.app')

@section('title', 'Calculadora de Pró-Labore e Distribuição de Lucros — Prazzu Tools')
@section('meta_description', 'Calcule pró-labore, INSS, IRRF, lucro disponível e distribuição de lucros com memória transparente.')

@section('content')
@auth
<div class="container pt-3"><a class="btn btn-outline-primary" href="{{ route('tools.calculadora-pro-labore-distribuicao-lucros.history.index') }}"><i class="bi bi-clock-history me-1"></i>Histórico</a></div>
@endauth
@if(session('history_message'))<div class="container pt-3"><div class="alert alert-info mb-0">{{ session('history_message') }}</div></div>@endif
<x-tools.page
    :title="$tool->name"
    :description="$tool->description"
    icon="cash-coin"
    :slug="$tool->slug"
>
    <x-tools.form-panel
        title="Dados da simulação Essencial"
        description="Este lote calcula um sócio, uma competência de 2026 e uma distribuição proporcional."
        heading-id="calculation-data-title"
    >
        <form id="essential-form" method="POST" action="{{ route('tools.calculadora-pro-labore-distribuicao-lucros.calculate') }}" class="row g-3">
            @csrf

            <div class="col-12"><h3 class="h5 mb-0">Contexto e sócio</h3></div>
            <div class="col-12 col-md-4">
                <x-tools.form.input name="competence" label="Competência" type="month" :value="old('competence', '2026-01')" min="2026-01" max="2026-12" required />
            </div>
            <div class="col-12 col-md-8">
                <x-tools.form.select
                    name="company_regime"
                    label="Enquadramento empresarial"
                    :options="[
                        'simples_outside_annex_iv' => 'Simples Nacional fora do Anexo IV',
                        'simples_annex_iv' => 'Simples Nacional — Anexo IV',
                        'presumed_profit' => 'Lucro Presumido',
                        'actual_profit' => 'Lucro Real',
                    ]"
                    placeholder="Selecione"
                    required
                />
            </div>
            <div class="col-12 col-md-6">
                <x-tools.form.input name="partner_label" label="Identificação temporária do sócio" :value="old('partner_label')" maxlength="80" help="Não informe CPF. Este rótulo não é necessário ao cálculo." />
            </div>
            <div class="col-12 col-md-3">
                <x-tools.form.input name="ownership_percentage" label="Participação" value="100" suffix="%" readonly required />
            </div>
            <div class="col-12 col-md-3">
                <x-tools.form.input name="dependents" label="Dependentes" type="number" min="0" max="99" :value="old('dependents', 0)" />
            </div>

            <div class="col-12 mt-4"><h3 class="h5 mb-0">Pró-labore</h3></div>
            <div class="col-12 col-md-6">
                <x-tools.form.money name="gross_pro_labore" label="Pró-labore bruto" :value="old('gross_pro_labore')" placeholder="5.000,00" required />
            </div>
            <div class="col-12 col-md-6">
                <x-tools.form.money name="other_official_social_security" label="Contribuição oficial em outros vínculos" :value="old('other_official_social_security', '0,00')" help="Use somente valores oficiais da mesma competência para controle do teto." />
            </div>

            <div class="col-12 mt-4"><h3 class="h5 mb-0">Lucro e distribuição</h3></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="accounting_profit" label="Lucro contábil do período" :value="old('accounting_profit')" required /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="accumulated_losses" label="Prejuízos acumulados" :value="old('accumulated_losses', '0,00')" /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="reserves_and_unavailable_amounts" label="Reservas e valores indisponíveis" :value="old('reserves_and_unavailable_amounts', '0,00')" /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="adjustments" label="Ajustes (+ ou -)" :value="old('adjustments', '0,00')" /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="prior_distributions" label="Antecipações já distribuídas" :value="old('prior_distributions', '0,00')" /></div>
            <div class="col-12 col-md-4"><x-tools.form.money name="intended_distribution" label="Distribuição pretendida" :value="old('intended_distribution')" required /></div>

            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm_assumptions" @checked(old('confirm_assumptions')) required>
                    <label class="form-check-label" for="confirm_assumptions">Confirmo o regime informado, a existência de suporte contábil para o lucro e que o caso não se enquadra nas situações não suportadas descritas pela ferramenta.</label>
                </div>
                @error('confirm_assumptions')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular retirada</button></div>
        </form>
    </x-tools.form-panel>

    @isset($result)
        @php
            $money = static fn (int $minor): string => 'R$ '.number_format($minor / 100, 2, ',', '.');
            $pro = $result->details['pro_labore'];
            $profit = $result->details['profit_distribution'];
        @endphp

        <x-tools.result-panel class="mt-4" title="Resultado consolidado" description="Valores da competência informada para o sócio simulado.">
            <div class="row g-3">
                @foreach ($result->summary as $item)
                    <div class="col-12 col-md-6 col-xl-4"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="cash-stack" /></div>
                @endforeach
            </div>
        </x-tools.result-panel>

        <x-tools.result-panel class="mt-4" title="Pró-labore e retenções">
            <div class="table-responsive"><table class="table align-middle mb-0"><tbody>
                <tr><th>Pró-labore bruto</th><td class="text-end">{{ $money($pro['gross_minor']) }}</td></tr>
                <tr><th>Base previdenciária</th><td class="text-end">{{ $money($pro['social_security_base_minor']) }}</td></tr>
                <tr><th>INSS retido</th><td class="text-end">{{ $money($pro['inss_withheld_minor']) }}</td></tr>
                <tr><th>Base do IRRF</th><td class="text-end">{{ $money($pro['irrf_base_minor']) }}</td></tr>
                <tr><th>IRRF</th><td class="text-end">{{ $money($pro['irrf_withheld_minor']) }}</td></tr>
                <tr><th>Pró-labore líquido</th><td class="text-end fw-semibold">{{ $money($pro['net_minor']) }}</td></tr>
                <tr><th>Contribuição patronal</th><td class="text-end">{{ $money($pro['employer_contribution_minor']) }}</td></tr>
                <tr><th>Custo total da empresa</th><td class="text-end fw-semibold">{{ $money($pro['company_total_cost_minor']) }}</td></tr>
            </tbody></table></div>
        </x-tools.result-panel>

        <x-tools.result-panel class="mt-4" title="Lucro disponível e distribuição">
            <div class="table-responsive"><table class="table align-middle mb-0"><tbody>
                <tr><th>Lucro contábil informado</th><td class="text-end">{{ $money($profit['accounting_profit_minor']) }}</td></tr>
                <tr><th>Lucro máximo disponível</th><td class="text-end">{{ $money($profit['maximum_available_profit_minor']) }}</td></tr>
                <tr><th>Lucro distribuído</th><td class="text-end fw-semibold">{{ $money($profit['distributed_amount_minor']) }}</td></tr>
                <tr><th>Saldo não distribuído</th><td class="text-end">{{ $money($profit['undistributed_balance_minor']) }}</td></tr>
            </tbody></table></div>
            @foreach ($result->details['warnings'] as $warning)<div class="alert alert-warning mt-3 mb-0" role="alert">{{ $warning }}</div>@endforeach
        </x-tools.result-panel>

        <div class="d-flex flex-wrap gap-2 mt-4">
            @foreach (['pdf' => 'PDF', 'csv' => 'CSV', 'json' => 'JSON'] as $format => $label)
                <form method="POST" action="{{ route('tools.calculadora-pro-labore-distribuicao-lucros.export', $format) }}" @if($format === 'pdf') target="_blank" @endif>
                    @csrf
                    @foreach($result->details['input'] as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
                    <input type="hidden" name="confirm_assumptions" value="1">
                    <button class="btn btn-outline-secondary" type="submit">Exportar {{ $label }}</button>
                </form>
            @endforeach
        </div>
        @if(!empty($historySaved))<div class="alert alert-success mt-3">Simulação salva no histórico da sua conta.</div>@endif

        <x-tools.result-panel class="mt-4" title="Premissas e rastreabilidade">
            <p class="mb-2">Competência: <strong>{{ $result->details['input']['competence'] }}</strong>. Método de dedução do IRRF: <strong>{{ $pro['irrf_deduction_method'] === 'simplified' ? 'desconto simplificado' : 'deduções legais' }}</strong>.</p>
            <p class="text-body-secondary mb-0">A simulação não substitui escrituração, folha de pagamento, obrigações acessórias ou revisão profissional. As versões normativas utilizadas permanecem registradas no resultado técnico.</p>
        </x-tools.result-panel>
    @endisset
    @include('tools-calculadora-pro-labore-distribuicao-lucros::partials-scenarios')
</x-tools.page>
@endsection
