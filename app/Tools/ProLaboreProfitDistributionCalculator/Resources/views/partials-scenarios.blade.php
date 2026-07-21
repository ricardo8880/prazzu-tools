@php
    $defaultScenario = static fn (string $name, string $grossA, string $grossB): array => [
        'name' => $name,
        'periods' => [[
            'competence' => '2026-01',
            'company_regime' => 'simples_outside_annex_iv',
            'accounting_profit' => '20000,00',
            'accumulated_losses' => '0,00',
            'reserves_and_unavailable_amounts' => '0,00',
            'adjustments' => '0,00',
            'prior_distributions' => '0,00',
            'intended_distribution' => '10000,00',
            'partners' => [
                ['label' => 'Sócio A', 'ownership_percentage' => '60', 'gross_pro_labore' => $grossA, 'dependents' => 0, 'other_official_social_security' => '0,00'],
                ['label' => 'Sócio B', 'ownership_percentage' => '40', 'gross_pro_labore' => $grossB, 'dependents' => 0, 'other_official_social_security' => '0,00'],
            ],
        ]],
    ];
    $scenarios = old('scenarios', [
        $defaultScenario('Cenário atual', '5000,00', '3000,00'),
        $defaultScenario('Cenário alternativo', '6500,00', '4000,00'),
    ]);
@endphp

<x-tools.form-panel
    class="mt-4"
    title="Simulações avançadas"
    description="Compare de dois a quatro cenários, com até doze competências e dez sócios por competência. Os dados permanecem temporários."
    heading-id="advanced-simulation-title"
>
    <form method="POST" action="{{ route('tools.calculadora-pro-labore-distribuicao-lucros.simulate') }}" id="advanced-simulation-form">
        @csrf
        <div id="simulation-scenarios">
            @foreach ($scenarios as $scenarioIndex => $scenario)
                <section class="card mb-4 scenario-card" data-scenario>
                    <div class="card-header d-flex gap-2 align-items-center justify-content-between">
                        <input class="form-control fw-semibold scenario-name" style="max-width: 30rem" name="scenarios[{{ $scenarioIndex }}][name]" value="{{ $scenario['name'] }}" maxlength="60" required>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-scenario">Remover cenário</button>
                    </div>
                    <div class="card-body periods-container">
                        @foreach ($scenario['periods'] as $periodIndex => $period)
                            <section class="border rounded p-3 mb-3 period-card" data-period>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="h6 mb-0">Competência</h4>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-period">Remover competência</button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-3"><label class="form-label">Competência</label><input type="month" class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][competence]" value="{{ $period['competence'] }}" min="2026-01" max="2026-12" required></div>
                                    <div class="col-md-5"><label class="form-label">Regime</label><select class="form-select" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][company_regime]" required>
                                        @foreach (['simples_outside_annex_iv'=>'Simples fora do Anexo IV','simples_annex_iv'=>'Simples — Anexo IV','presumed_profit'=>'Lucro Presumido','actual_profit'=>'Lucro Real'] as $value => $label)<option value="{{ $value }}" @selected($period['company_regime'] === $value)>{{ $label }}</option>@endforeach
                                    </select></div>
                                    <div class="col-md-4"><label class="form-label">Lucro contábil</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][accounting_profit]" value="{{ $period['accounting_profit'] }}" required></div>
                                    @foreach (['accumulated_losses'=>'Prejuízos acumulados','reserves_and_unavailable_amounts'=>'Reservas/indisponíveis','adjustments'=>'Ajustes','prior_distributions'=>'Antecipações','intended_distribution'=>'Distribuição pretendida'] as $field => $label)
                                        <div class="col-md-{{ $field === 'intended_distribution' ? '4' : '2' }}"><label class="form-label">{{ $label }}</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][{{ $field }}]" value="{{ $period[$field] ?? '0,00' }}" required></div>
                                    @endforeach
                                </div>
                                <h5 class="h6 mt-4">Sócios</h5>
                                <div class="partners-container">
                                    @foreach ($period['partners'] as $partnerIndex => $partner)
                                        <div class="row g-2 align-items-end mb-2 partner-row" data-partner>
                                            <div class="col-md-3"><label class="form-label">Rótulo</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][partners][{{ $partnerIndex }}][label]" value="{{ $partner['label'] }}"></div>
                                            <div class="col-md-2"><label class="form-label">Participação %</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][partners][{{ $partnerIndex }}][ownership_percentage]" value="{{ $partner['ownership_percentage'] }}" required></div>
                                            <div class="col-md-2"><label class="form-label">Pró-labore</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][partners][{{ $partnerIndex }}][gross_pro_labore]" value="{{ $partner['gross_pro_labore'] }}" required></div>
                                            <div class="col-md-2"><label class="form-label">Dependentes</label><input type="number" min="0" max="99" class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][partners][{{ $partnerIndex }}][dependents]" value="{{ $partner['dependents'] ?? 0 }}"></div>
                                            <div class="col-md-2"><label class="form-label">INSS outros vínculos</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][periods][{{ $periodIndex }}][partners][{{ $partnerIndex }}][other_official_social_security]" value="{{ $partner['other_official_social_security'] ?? '0,00' }}"></div>
                                            <div class="col-md-1"><button type="button" class="btn btn-outline-danger remove-partner" aria-label="Remover sócio">×</button></div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm add-partner">Adicionar sócio</button>
                            </section>
                        @endforeach
                    </div>
                    <div class="card-footer"><button type="button" class="btn btn-outline-secondary btn-sm add-period">Adicionar competência</button></div>
                </section>
            @endforeach
        </div>
        @error('scenarios')<div class="alert alert-danger">{{ $message }}</div>@enderror
        <div class="d-flex gap-2 mb-3"><button type="button" class="btn btn-outline-primary" id="add-scenario">Duplicar último cenário</button></div>
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="confirm_simulation_assumptions" value="1" id="confirm_simulation_assumptions" required><label class="form-check-label" for="confirm_simulation_assumptions">Confirmo os regimes, participações societárias e suporte contábil dos lucros informados.</label></div>
        <button class="btn btn-primary" type="submit">Comparar cenários</button>
    </form>
</x-tools.form-panel>

@isset($simulationResult)
    @php($money = static fn (int $minor): string => 'R$ '.number_format($minor / 100, 2, ',', '.'))
    <x-tools.result-panel class="mt-4" title="Comparação de cenários" description="Diferenças calculadas contra o primeiro cenário.">
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Cenário</th><th class="text-end">Total recebido</th><th class="text-end">Custo empresa</th><th class="text-end">Δ recebido</th><th class="text-end">Δ custo</th></tr></thead><tbody>
            @foreach ($simulationResult['comparison'] as $item)<tr><th>{{ $item['name'] }}</th><td class="text-end">{{ $money($item['total_received_minor']) }}</td><td class="text-end">{{ $money($item['company_cost_minor']) }}</td><td class="text-end">{{ $money($item['difference_received_minor']) }}</td><td class="text-end">{{ $money($item['difference_company_cost_minor']) }}</td></tr>@endforeach
        </tbody></table></div>
    </x-tools.result-panel>
    @foreach ($simulationResult['scenarios'] as $scenario)
        <x-tools.result-panel class="mt-4" :title="$scenario['name']" description="Consolidação por competência e sócio.">
            @foreach ($scenario['periods'] as $period)
                <h4 class="h6 mt-3">{{ $period['competence'] }}</h4>
                <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Sócio</th><th class="text-end">Pró-labore líquido</th><th class="text-end">Lucro</th><th class="text-end">Total recebido</th><th class="text-end">Custo empresa</th></tr></thead><tbody>
                    @foreach ($period['partners'] as $partner)<tr><td>{{ $partner['label'] }} ({{ $partner['ownership_percentage'] }}%)</td><td class="text-end">{{ $money($partner['net_pro_labore_minor']) }}</td><td class="text-end">{{ $money($partner['profit_distribution_minor']) }}</td><td class="text-end fw-semibold">{{ $money($partner['total_received_minor']) }}</td><td class="text-end">{{ $money($partner['company_cost_minor']) }}</td></tr>@endforeach
                </tbody></table></div>
            @endforeach
        </x-tools.result-panel>
    @endforeach
@endisset

@push('scripts')
<script>
(() => {
 const root=document.getElementById('simulation-scenarios'); if(!root)return;
 const reindex=()=>{[...root.querySelectorAll('[data-scenario]')].forEach((s,si)=>{[...s.querySelectorAll('[data-period]')].forEach((p,pi)=>{[...p.querySelectorAll('[data-partner]')].forEach((r,ri)=>{r.querySelectorAll('[name]').forEach(i=>i.name=i.name.replace(/scenarios\[\d+\]\[periods\]\[\d+\]\[partners\]\[\d+\]/,`scenarios[${si}][periods][${pi}][partners][${ri}]`));});p.querySelectorAll(':scope > .row [name]').forEach(i=>i.name=i.name.replace(/scenarios\[\d+\]\[periods\]\[\d+\]/,`scenarios[${si}][periods][${pi}]`));});s.querySelector('.scenario-name').name=`scenarios[${si}][name]`;});};
 root.addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;if(b.classList.contains('add-partner')){const c=b.previousElementSibling,r=c.lastElementChild.cloneNode(true);r.querySelectorAll('input').forEach(i=>{if(i.name.includes('[label]'))i.value='Novo sócio';else if(i.name.includes('[ownership_percentage]'))i.value='0';else i.value=i.type==='number'?'0':'0,00';});c.append(r);}if(b.classList.contains('remove-partner')&&b.closest('.partners-container').children.length>1)b.closest('[data-partner]').remove();if(b.classList.contains('add-period')){const c=b.closest('[data-scenario]').querySelector('.periods-container'),p=c.lastElementChild.cloneNode(true);c.append(p);}if(b.classList.contains('remove-period')&&b.closest('.periods-container').children.length>1)b.closest('[data-period]').remove();if(b.classList.contains('remove-scenario')&&root.children.length>2)b.closest('[data-scenario]').remove();reindex();});
 document.getElementById('add-scenario').addEventListener('click',()=>{if(root.children.length>=4)return;const s=root.lastElementChild.cloneNode(true);s.querySelector('.scenario-name').value='Novo cenário';root.append(s);reindex();});
 reindex();
})();
</script>
@endpush
