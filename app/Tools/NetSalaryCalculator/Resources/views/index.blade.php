@extends('layouts.app')

@section('title', 'Calculadora de Salário Líquido 2026')
@section('meta_description', 'Calcule o salário líquido CLT em 2026 com INSS progressivo, IRRF, dependentes, pensão e descontos, com memória de cálculo transparente.')

@section('content')
<x-tools.page
    title="Calculadora de Salário Líquido"
    description="Calcule o valor líquido mensal de um empregado CLT com as tabelas de INSS e IRRF vigentes em 2026."
    icon="bi-cash-coin"
    slug="calculadora-salario-liquido"
>
    @auth
        <div class="mb-3"><a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-salario-liquido.history.index') }}"><i class="bi bi-clock-history me-1"></i>Histórico Plus</a></div>
    @endauth
    <div class="alert alert-info d-flex gap-2" role="alert">
        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
        <div>
            <strong>Escopo do cálculo.</strong> Esta versão cobre salário mensal CLT regular em um único vínculo. Férias, 13º, rescisão, múltiplos vínculos e regimes previdenciários especiais possuem tratamento próprio.
        </div>
    </div>

    <form method="POST" action="{{ route('tools.calculadora-salario-liquido.calculate') }}" class="vstack gap-4">
        @csrf

        <x-tools.form-panel title="Dados essenciais" description="O resultado gratuito resolve o cálculo principal do salário líquido." badge="Essencial">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <x-tools.form.input name="competence" label="Competência" type="month" :value="old('competence', now()->format('Y-m'))" required help="As regras normativas disponíveis neste lote cobrem 2026." />
                </div>
                <div class="col-12 col-md-4">
                    <x-tools.form.money name="base_salary" label="Salário-base" :value="old('base_salary')" required help="Informe a remuneração fixa mensal." />
                </div>
                <div class="col-12 col-md-4">
                    <x-tools.form.input name="dependents" label="Dependentes para IRRF" type="number" min="0" max="99" :value="old('dependents', 0)" help="Quantidade de dependentes dedutíveis no IRRF mensal." />
                </div>
            </div>
        </x-tools.form-panel>

        <x-tools.form-panel title="Proventos e descontos adicionais" description="Use quando houver valores além do salário-base. Durante o lançamento, os recursos Plus permanecem disponíveis gratuitamente." badge="Prazzu Plus">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <x-tools.form.money name="taxable_additional_earnings" label="Proventos tributáveis adicionais" :value="old('taxable_additional_earnings')" help="Ex.: horas extras, comissões e adicionais que integrem as bases de INSS e IRRF. Informe apenas valores cuja incidência já tenha sido confirmada." />
                </div>
                <div class="col-12 col-md-6">
                    <x-tools.form.money name="non_taxable_earnings" label="Proventos não tributáveis" :value="old('non_taxable_earnings')" help="Ex.: reembolsos ou verbas que não integrem as bases, quando juridicamente aplicável." />
                </div>
                <div class="col-12 col-md-4">
                    <x-tools.form.money name="judicial_pension" label="Pensão alimentícia dedutível" :value="old('judicial_pension')" help="Informe somente valor legalmente dedutível no IRRF e efetivamente descontado." />
                </div>
                <div class="col-12 col-md-4">
                    <x-tools.form.money name="transport_discount" label="Desconto de vale-transporte" :value="old('transport_discount')" help="Informe o desconto já apurado pela folha." />
                </div>
                <div class="col-12 col-md-4">
                    <x-tools.form.money name="meal_discount" label="Desconto de alimentação/refeição" :value="old('meal_discount')" help="Informe o desconto já apurado pela folha." />
                </div>
                <div class="col-12 col-md-6">
                    <x-tools.form.money name="health_plan_discount" label="Desconto de plano de saúde" :value="old('health_plan_discount')" />
                </div>
                <div class="col-12 col-md-6">
                    <x-tools.form.money name="other_discounts" label="Outros descontos" :value="old('other_discounts')" help="Somente descontos que efetivamente reduzem o valor líquido." />
                </div>
            </div>
        </x-tools.form-panel>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="confirm_assumptions" name="confirm_assumptions" @checked(old('confirm_assumptions')) required>
            <label class="form-check-label" for="confirm_assumptions">
                Confirmo que os valores tributáveis/não tributáveis e os descontos informados já foram classificados corretamente para o caso analisado.
            </label>
        </div>

        <div>
            <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular salário líquido</button>
        </div>
    </form>

    @isset($result)
    <span data-analytics-result="main" hidden></span>
        @php
            $money = static fn (int $minor): string => \App\Core\Money\Money::fromMinor($minor)->formatPtBr();
            $details = $result->details;
        @endphp

        <div class="mt-5">
            <x-tools.result-panel title="Resultado" description="Estimativa transparente com INSS e IRRF calculados pelas regras versionadas de 2026.">
                <div class="row g-3 mb-4">
                    @foreach ($result->summary as $item)
                        <div class="col-12 col-md-6 col-xl">
                            <x-tools.result-metric :label="$item->label" :value="$item->value" :icon="$item->key === 'net' ? 'wallet2' : 'calculator'" />
                        </div>
                    @endforeach
                </div>

                @foreach ($result->warnings as $warning)
                    <div class="alert alert-warning">{{ $warning->message }}</div>
                @endforeach

                <div class="table-responsive mb-4">
                    <table class="table table-sm align-middle">
                        <tbody>
                            <tr><th>Remuneração tributável</th><td class="text-end">{{ $money($details['taxable_gross_minor']) }}</td></tr>
                            <tr><th>Base do INSS</th><td class="text-end">{{ $money($details['social_security_base_minor']) }}</td></tr>
                            <tr><th>INSS</th><td class="text-end">{{ $money($details['inss_minor']) }}</td></tr>
                            <tr><th>Método de dedução do IRRF</th><td class="text-end">{{ $details['irrf_deduction_method'] === 'simplified' ? 'Desconto simplificado mensal' : 'Deduções legais' }}</td></tr>
                            <tr><th>Base do IRRF</th><td class="text-end">{{ $money($details['irrf_base_minor']) }}</td></tr>
                            <tr><th>IRRF antes da redução de 2026</th><td class="text-end">{{ $money($details['irrf_before_reduction_minor']) }}</td></tr>
                            <tr><th>Redução mensal do IRRF</th><td class="text-end">- {{ $money($details['irrf_reduction_minor']) }}</td></tr>
                            <tr><th>IRRF final</th><td class="text-end">{{ $money($details['irrf_minor']) }}</td></tr>
                            <tr><th>Outros descontos informados</th><td class="text-end">{{ $money($details['user_discounts_minor']) }}</td></tr>
                            <tr class="table-light"><th>Salário líquido estimado</th><td class="text-end fw-bold">{{ $money($details['net_minor']) }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="h5">Memória de cálculo</h3>
                <div class="accordion mb-4" id="net-salary-memory">
                    @foreach ($result->calculationMemory->steps as $step)
                        <div class="accordion-item">
                            <h4 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#memory-{{ $loop->index }}" aria-expanded="false">
                                    {{ $step->label }}
                                </button>
                            </h4>
                            <div id="memory-{{ $loop->index }}" class="accordion-collapse collapse" data-bs-parent="#net-salary-memory">
                                <div class="accordion-body">
                                    <p class="mb-2"><strong>Fórmula:</strong> {{ $step->formula }}</p>
                                    <p class="small text-body-secondary mb-0">Resultado registrado: {{ (is_int($step->result) && (str_contains($step->key, 'inss') || in_array($step->key, ['irrf', 'net_salary', 'taxable_gross'], true))) ? $money((int) $step->result) : $step->result }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <h3 class="h5">Regras normativas utilizadas</h3>
                <ul class="small text-body-secondary">
                    @foreach ($result->calculationMemory->normativeRules as $rule)
                        <li>{{ $rule->identifier }} — versão {{ $rule->version }} — referência {{ $rule->referenceDate }}</li>
                    @endforeach
                </ul>

                @if (!empty($historySaved))
                    <div class="alert alert-success">Cálculo salvo no histórico da sua conta.</div>
                @endif

                <div class="d-flex gap-2 flex-wrap">@foreach(['pdf'=>'Baixar PDF','xlsx'=>'Baixar Excel (.xlsx)'] as $format=>$label)<form method="POST" action="{{ route('tools.calculadora-salario-liquido.export',$format) }}">@csrf @foreach($result->details['input'] as $n=>$v)<input type="hidden" name="{{ $n }}" value="{{ is_bool($v)?($v?1:0):$v }}">@endforeach<input type="hidden" name="confirm_assumptions" value="1"><button class="btn btn-outline-primary" type="submit" data-testid="download-{{ $format }}">{{ $label }}</button></form>@endforeach</div>
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>
@endsection
