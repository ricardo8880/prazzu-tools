@extends('layouts.app')

@section('title', 'Simulador de Pró-Labore Ideal | Prazzu Tools')
@section('meta_description', 'Calcule retenções, valor líquido e custo empresarial do pró-labore com memória de cálculo.')
@section('canonical_url', route('tools.simulador-pro-labore-ideal.index'))

@section('content')
    <x-tools.page title="Simulador de Pró-Labore Ideal" description="Simule pró-labore, INSS, IRRF, valor líquido e custo empresarial com memória transparente." icon="bi-person-badge" slug="simulador-pro-labore-ideal">


        <form data-testid="tool-form-panel" method="post" action="{{ route('tools.simulador-pro-labore-ideal.calculate') }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label" for="competence">Competência</label>
                <input class="form-control" id="competence" name="competence" type="month" min="2026-01" max="2026-12" value="{{ old('competence', now()->format('Y-m')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="company_regime">Regime</label>
                <select class="form-select" id="company_regime" name="company_regime">
                    <option value="simples_outside_annex_iv" @selected(old('company_regime', 'simples_outside_annex_iv') === 'simples_outside_annex_iv')>Simples fora do Anexo IV</option>
                    <option value="simples_annex_iv" @selected(old('company_regime') === 'simples_annex_iv')>Simples Anexo IV</option>
                    <option value="presumed_profit" @selected(old('company_regime') === 'presumed_profit')>Lucro Presumido</option>
                    <option value="actual_profit" @selected(old('company_regime') === 'actual_profit')>Lucro Real</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="gross_pro_labore">Pró-labore bruto</label>
                <input class="form-control" id="gross_pro_labore" name="gross_pro_labore" value="{{ old('gross_pro_labore') }}" placeholder="Ex.: 5.500,00" required>
            </div>
            <div class="col-12">
                <x-tools.form-disclosure title="Deduções e contribuições adicionais" description="Abra somente se houver dependentes ou contribuição previdenciária oficial em outro vínculo." :open="$errors->hasAny(['dependents', 'other_official_social_security'])">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="dependents">Dependentes</label>
                            <input class="form-control" id="dependents" type="number" name="dependents" value="{{ old('dependents') }}" placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="other_official_social_security">Outras contribuições oficiais</label>
                            <input class="form-control" id="other_official_social_security" name="other_official_social_security" value="{{ old('other_official_social_security') }}" placeholder="0,00">
                        </div>
                    </div>
                </x-tools.form-disclosure>
            </div>
            <div class="col-12 form-check ms-2">
                <input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm" required>
                <label class="form-check-label" for="confirm">Confirmo que revisei competência, regime e premissas.</label>
            </div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular</button></div>
        </form>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">{{ $errors->first() }}</div>
        @endif

        @isset($result)
            <span data-analytics-result="main" hidden></span>
            @php($money = static fn (int $minor) => \App\Core\Money\Money::fromMinor($minor)->formatPtBr())
            <div class="mt-4">
                <x-tools.result-panel title="Pró-labore calculado" description="Veja quanto chega ao sócio e qual é o custo empresarial deste cenário." heading-id="pro-labore-result-title" eyebrow="Simulação concluída">
                    <div class="row g-3 mb-4">
                        @foreach ($result->summary as $item)
                            <div class="col-12 col-md-6 col-xl"><x-tools.result-metric :label="$item->label" :value="$item->value" :icon="$item->key === 'net' ? 'wallet2' : 'calculator'" /></div>
                        @endforeach
                    </div>
                    <x-tools.result-insight
                        :title="'O sócio recebe '.$money($result->details['net_minor']).' líquidos neste cenário'"
                        :description="'Sobre o pró-labore bruto de '.$money($result->details['gross_minor']).', foram calculadas retenções de INSS e IRRF. Para a empresa, o custo total estimado é '.$money($result->details['company_total_cost_minor']).'.'"
                        :items="[
                            'INSS retido do sócio: '.$money($result->details['inss_withheld_minor']).'.',
                            'IRRF retido: '.$money($result->details['irrf_withheld_minor']).'.',
                            $result->details['employer_contribution_minor'] > 0 ? 'Contribuição patronal considerada no custo da empresa: '.$money($result->details['employer_contribution_minor']).'.' : 'Neste regime informado, não foi acrescentada contribuição patronal ao cenário.',
                        ]"
                        icon="bi-person-badge"
                    />
                    <x-tools.export-buttons :pdf-route="route('tools.simulador-pro-labore-ideal.export.pdf')" :excel-route="route('tools.simulador-pro-labore-ideal.export.excel')" :input="$calculationInput ?? []" />
                    <x-tools.normative-trust :rules="$result->details['normative_rules'] ?? []" />
                </x-tools.result-panel>
            </div>
        @endisset
        <div class="card mt-4"><div class="card-body"><h2 class="h4">Prazzu Plus — cenários anuais</h2><form method="POST" action="{{ route('tools.simulador-pro-labore-ideal.scenarios') }}" class="row g-3">@csrf <div class="col-md-6"><label class="form-label">Regime</label><select class="form-select" name="company_regime"><option value="simples_outside_annex_iv">Simples fora do Anexo IV</option><option value="simples_annex_iv">Simples Anexo IV</option><option value="presumed_profit">Lucro Presumido</option><option value="actual_profit">Lucro Real</option></select></div><div class="col-md-3"><x-tools.form.input name="dependents" label="Dependentes" type="number" min="0" placeholder="0" /></div><div class="col-md-3"><x-tools.form.money name="other_official_social_security" label="Outras contribuições" placeholder="0,00" /></div><div class="col-12"><label class="form-label">Valores mensais</label><input class="form-control" name="scenario_values" value="{{ old('scenario_values') }}" placeholder="Digite os valores mensais" aria-describedby="scenario-values-help" required><div id="scenario-values-help" class="form-text">Para comparar mais de um valor, separe-os por ponto e vírgula (;).</div></div><div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="confirm_scenario_assumptions" value="1" id="confirm-scenarios" required><label class="form-check-label" for="confirm-scenarios">Confirmo as premissas dos cenários.</label></div><div class="col-12"><button class="btn btn-outline-primary">Comparar ano</button></div></form></div></div>
        @isset($proLaboreScenarios)<div class="card mt-4"><div class="card-body"><h2 class="h4">Comparação anual</h2><div class="table-responsive"><table class="table"><thead><tr><th>Cenário</th><th>Líquido anual</th><th>Custo empresarial anual</th></tr></thead><tbody>@foreach($proLaboreScenarios as $scenario)<tr><th>{{ $scenario['name'] }}</th><td>R$ {{ number_format($scenario['annual_net_minor']/100,2,',','.') }}</td><td>R$ {{ number_format($scenario['annual_company_cost_minor']/100,2,',','.') }}</td></tr>@endforeach</tbody></table></div></div></div>@endisset
    </x-tools.page>
@endsection
