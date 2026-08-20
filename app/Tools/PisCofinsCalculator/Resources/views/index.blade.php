@extends('layouts.app')

@section('title', 'Calculadora PIS e COFINS')
@section('meta_description', 'Calcule PIS/Pasep e Cofins nos regimes cumulativo e não cumulativo, com créditos, múltiplas operações, comparação e memória de cálculo.')

@section('content')
<x-tools.page title="Calculadora PIS e COFINS" description="Apure PIS/Pasep e Cofins em 2026 com alíquotas gerais, créditos no não cumulativo e memória transparente." icon="bi-percent" slug="calculadora-pis-cofins">
    <div class="alert alert-info">
        <strong>Escopo da versão 2026.</strong> A ferramenta usa as alíquotas gerais dos regimes cumulativo e não cumulativo. Operações monofásicas, alíquota zero, suspensão, substituição, importação, benefícios ou regimes setoriais devem ser analisados separadamente.
    </div>

    <x-tools.validation-summary />
    @auth
        <div class="mb-3"><a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-pis-cofins.history.index') }}"><i class="bi bi-clock-history me-1"></i>Histórico Plus</a></div>
    @endauth

    <form method="POST" action="{{ route('tools.calculadora-pis-cofins.calculate') }}" class="vstack gap-4">
        @csrf
        <x-tools.form-panel title="Apuração da competência" description="O Essencial resolve um caso individual completo. Informe a base já revisada conforme o tratamento fiscal aplicável." badge="Essencial">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="period">Competência</label>
                    <input class="form-control" type="month" id="period" name="period" min="2026-01" max="2026-12" value="{{ old('period', now()->format('Y-m')) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label" for="regime">Regime de apuração</label>
                    <select class="form-select" id="regime" name="regime" required>
                        <option value="cumulative" @selected(old('regime','cumulative') === 'cumulative')>Cumulativo — PIS 0,65% + Cofins 3%</option>
                        <option value="non_cumulative" @selected(old('regime') === 'non_cumulative')>Não cumulativo — PIS 1,65% + Cofins 7,6%</option>
                    </select>
                </div>
                <div class="col-md-6"><x-tools.form.money name="taxable_revenue" label="Base tributável da receita" :value="old('taxable_revenue')" data-e2e-value="10.000,00" required help="Informe a base já após exclusões e ajustes aplicáveis ao caso." /></div>
                @if($plusEnabled ?? true)<div class="col-md-6"><x-tools.form.money name="credit_base" label="Base total elegível a créditos" :value="old('credit_base')" required help="Usada no regime não cumulativo. No cumulativo, mantenha zero se não estiver comparando cenários." /></div>@else<input type="hidden" name="credit_base" value="0">@endif
                <div class="col-md-6"><x-tools.form.money name="pis_withheld" label="PIS retido/compensável confirmado" :value="old('pis_withheld')" required /></div>
                <div class="col-md-6"><x-tools.form.money name="cofins_withheld" label="Cofins retida/compensável confirmada" :value="old('cofins_withheld')" required /></div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-disclosure title="Operações e análise avançada" description="Abra para comparar regimes ou adicionar outras operações da competência." badge="Prazzu Plus" :open="$errors->hasAny(['compare_regimes', 'operations.*'])">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="compare_regimes" value="1" id="compare_regimes" @checked(old('compare_regimes'))>
                <label class="form-check-label" for="compare_regimes">Exibir comparação cumulativo × não cumulativo</label>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Operação adicional</th><th>Base tributável</th><th>Base elegível a crédito</th></tr></thead>
                    <tbody>
                        @for($i = 0; $i < 3; $i++)
                            <tr>
                                <td><input class="form-control" name="operations[{{ $i }}][description]" value="{{ old("operations.$i.description") }}" placeholder="Ex.: prestação de serviço"></td>
                                <td><input class="form-control" name="operations[{{ $i }}][revenue]" value="{{ old("operations.$i.revenue") }}" inputmode="decimal"></td>
                                <td><input class="form-control" name="operations[{{ $i }}][credit_base]" value="{{ old("operations.$i.credit_base") }}" inputmode="decimal"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="form-text">As linhas adicionais são somadas à base principal. Informe crédito apenas quando a operação/aquisição efetivamente gerar crédito no regime não cumulativo.</div>
        </x-tools.form-disclosure>
        @endif

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="confirm_scope" value="1" id="confirm_scope" required @checked(old('confirm_scope'))>
            <label class="form-check-label" for="confirm_scope">Confirmo que revisei a base tributável, o regime e a elegibilidade dos créditos/retenções informados.</label>
        </div>

        <div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular PIS e Cofins</button></div>
    </form>

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        @php($money = static fn (int $minor) => \App\Core\Money\Money::fromMinor($minor)->formatPtBr())
        @php($selected = $result->details['selected'])
        <div class="mt-5">
            <x-tools.result-panel title="PIS e Cofins apurados" description="Apuração estimada conforme as bases e premissas confirmadas no formulário." eyebrow="Apuração concluída">
                <div class="row g-3 mb-4">
                    @foreach($result->summary as $item)
                        <div class="col-12 col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="percent" /></div>
                    @endforeach
                </div>

                <x-tools.result-insight
                    :title="'No regime '.$selected['label'].', o total estimado a recolher é '.$money($selected['total_payable_minor'])"
                    :description="'A apuração parte de '.$money($result->details['revenue_minor']).' de base tributável e considera os créditos e retenções/compensações que você informou.'"
                    :items="[
                        'Débitos antes de créditos e retenções: '.$money($selected['pis_debit_minor'] + $selected['cofins_debit_minor']).'.',
                        $selected['credits_total_minor'] > 0 ? 'Créditos considerados: '.$money($selected['credits_total_minor']).'.' : 'Nenhuma base de crédito reduziu esta apuração.',
                        ($selected['pis_withheld_minor'] + $selected['cofins_withheld_minor']) > 0 ? 'Retenções/compensações informadas: '.$money($selected['pis_withheld_minor'] + $selected['cofins_withheld_minor']).'.' : 'Nenhuma retenção/compensação foi informada.',
                        $result->details['compare_regimes'] ? 'A comparação entre regimes é apenas matemática e não define o enquadramento jurídico aplicável.' : '',
                    ]"
                    icon="bi-percent"
                />

                @foreach($result->warnings as $warning)<div class="alert alert-warning">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Apuração — {{ $selected['label'] }}</h3>
                <div class="table-responsive"><table class="table table-sm align-middle"><tbody>
                    <tr><th>Base tributável total</th><td class="text-end">{{ $money($result->details['revenue_minor']) }}</td></tr>
                    <tr><th>Débito PIS ({{ $selected['pis_rate'] }}%)</th><td class="text-end">{{ $money($selected['pis_debit_minor']) }}</td></tr>
                    <tr><th>Débito Cofins ({{ $selected['cofins_rate'] }}%)</th><td class="text-end">{{ $money($selected['cofins_debit_minor']) }}</td></tr>
                    <tr><th>Crédito PIS</th><td class="text-end">- {{ $money($selected['pis_credit_minor']) }}</td></tr>
                    <tr><th>Crédito Cofins</th><td class="text-end">- {{ $money($selected['cofins_credit_minor']) }}</td></tr>
                    <tr><th>PIS retido/compensável</th><td class="text-end">- {{ $money($selected['pis_withheld_minor']) }}</td></tr>
                    <tr><th>Cofins retida/compensável</th><td class="text-end">- {{ $money($selected['cofins_withheld_minor']) }}</td></tr>
                    <tr class="table-primary"><th>Total a recolher</th><td class="text-end fw-bold">{{ $money($selected['total_payable_minor']) }}</td></tr>
                </tbody></table></div>

                @if($selected['pis_credit_balance_minor'] > 0 || $selected['cofins_credit_balance_minor'] > 0)
                    <div class="alert alert-success">Saldo credor apurado antes de retenções: PIS {{ $money($selected['pis_credit_balance_minor']) }} e Cofins {{ $money($selected['cofins_credit_balance_minor']) }}. A utilização/ressarcimento depende da natureza e das regras do crédito.</div>
                @endif

                @if($result->details['compare_regimes'])
                    @php($comparison = $result->details['comparison'])
                    <h3 class="h5 mt-4">Comparação cumulativo × não cumulativo <span class="badge text-bg-primary">Plus</span></h3>
                    <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Regime</th><th class="text-end">PIS + Cofins antes de retenções</th><th class="text-end">Total a recolher</th></tr></thead><tbody>
                        <tr><td>Cumulativo</td><td class="text-end">{{ $money($comparison['cumulative']['contribution_before_withholding_minor']) }}</td><td class="text-end">{{ $money($comparison['cumulative']['total_payable_minor']) }}</td></tr>
                        <tr><td>Não cumulativo</td><td class="text-end">{{ $money($comparison['non_cumulative']['contribution_before_withholding_minor']) }}</td><td class="text-end">{{ $money($comparison['non_cumulative']['total_payable_minor']) }}</td></tr>
                    </tbody></table></div>
                    <div class="alert alert-secondary mb-4">Diferença antes das retenções: <strong>{{ $money(abs($comparison['difference_minor'])) }}</strong>. Esta comparação é matemática e não determina qual regime jurídico a empresa pode adotar.</div>
                @endif

                @if(count($result->details['operations']) > 0)
                    <h3 class="h5 mt-4">Operações adicionais <span class="badge text-bg-primary">Plus</span></h3>
                    <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Operação</th><th class="text-end">Base tributável</th><th class="text-end">Base de crédito</th></tr></thead><tbody>
                        @foreach($result->details['operations'] as $operation)<tr><td>{{ $operation['description'] }}</td><td class="text-end">{{ $money($operation['revenue_minor']) }}</td><td class="text-end">{{ $money($operation['credit_base_minor']) }}</td></tr>@endforeach
                    </tbody></table></div>
                @endif

                <h3 class="h5 mt-4">Memória de cálculo</h3>
                <div class="accordion mb-4" id="pis-cofins-memory" data-essential-transparency="memory">
                        @foreach($result->calculationMemory->steps as $step)
                            <div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pis-cofins-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="pis-cofins-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}</div></div></div>
                        @endforeach
                </div>

                @if(!empty($historySaved))<div class="alert alert-success">Cálculo salvo no histórico da sua conta.</div>@endif

                @php($exportInput = array_merge($calculationInput ?? [], ['confirm_scope' => 1]))
                <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions">
                    <a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.calculadora-pis-cofins.export', array_merge(['format'=>'pdf'],$exportInput)) }}">Exportar PDF</a>
                    <a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.calculadora-pis-cofins.export', array_merge(['format'=>'xlsx'],$exportInput)) }}">Baixar Excel</a>
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
