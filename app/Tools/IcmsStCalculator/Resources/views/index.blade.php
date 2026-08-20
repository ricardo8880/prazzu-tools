@extends('layouts.app')

@section('title','Calculadora de ICMS-ST')
@section('meta_description','Calcule ICMS-ST estimado por MVA, com MVA ajustada, FCP, operações interestaduais, múltiplos itens e relatório.')

@section('content')
<x-tools.page title="Calculadora de ICMS-ST" description="Estime ICMS-ST com parâmetros confirmados da operação e memória transparente do cálculo." icon="bi-box-seam" slug="calculadora-icms-st">
    <div class="alert alert-info"><strong>Ferramenta paramétrica.</strong> A incidência de ST, MVA, alíquotas, FCP, reduções e composição da base dependem da mercadoria, NCM/CEST, UF e legislação aplicável. Confirme os parâmetros antes de usar o resultado fiscalmente.</div>
    <x-tools.validation-summary />
    @auth
        <div class="mb-3"><a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-icms-st.history.index') }}"><i class="bi bi-clock-history me-1"></i>Histórico Plus</a></div>
    @endauth

    <form method="POST" action="{{ route('tools.calculadora-icms-st.calculate') }}" class="vstack gap-4">
        @csrf
        <x-tools.form-panel title="Operação principal" description="O Essencial resolve uma operação básica com MVA e alíquotas informadas." badge="Essencial">
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label" for="competence">Competência</label><input class="form-control" type="month" id="competence" name="competence" min="2026-01" max="2026-12" value="{{ old('competence', now()->format('Y-m')) }}" required></div>
                <div class="col-md-4"><label class="form-label" for="operation_type">Tipo de operação</label><select class="form-select" id="operation_type" name="operation_type" required><option value="internal" @selected(old('operation_type','internal')==='internal')>Interna</option>@if($plusEnabled ?? true)<option value="interstate" @selected(old('operation_type')==='interstate')>Interestadual · Plus</option>@endif</select></div>
                <div class="col-md-2"><label class="form-label" for="origin_uf">UF origem</label><select class="form-select" id="origin_uf" name="origin_uf" required>@foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)<option value="{{ $uf }}" @selected(old('origin_uf','SP')===$uf)>{{ $uf }}</option>@endforeach</select></div>
                <div class="col-md-2"><label class="form-label" for="destination_uf">UF destino</label><select class="form-select" id="destination_uf" name="destination_uf" required>@foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)<option value="{{ $uf }}" @selected(old('destination_uf','SP')===$uf)>{{ $uf }}</option>@endforeach</select></div>
                <div class="col-md-4"><x-tools.form.money name="merchandise_value" label="Valor da mercadoria" :value="old('merchandise_value')" data-e2e-value="1.000,00" required /></div>
                <div class="col-md-4"><x-tools.form.money name="freight" label="Frete que integra a base" :value="old('freight')" required /></div>
                <div class="col-md-4"><x-tools.form.money name="insurance" label="Seguro que integra a base" :value="old('insurance')" required /></div>
                <div class="col-md-4"><x-tools.form.money name="other_charges" label="Outras despesas da base" :value="old('other_charges')" required /></div>
                <div class="col-md-4"><x-tools.form.money name="ipi" label="IPI que integra a base" :value="old('ipi')" required /></div>
                <div class="col-md-4"><x-tools.form.money name="discount" label="Desconto incondicional" :value="old('discount')" required /></div>
                <div class="col-md-4"><label class="form-label" for="original_mva">MVA original (%)</label><input class="form-control" id="original_mva" name="original_mva" type="number" step="0.0001" min="0" max="500" value="{{ old('original_mva') }}" required placeholder="Ex.: 35"></div>
                <div class="col-md-4"><label class="form-label" for="internal_rate">Alíquota interna destino (%)</label><input class="form-control" id="internal_rate" name="internal_rate" type="number" step="0.0001" min="0.0001" max="99.9999" value="{{ old('internal_rate') }}" required placeholder="Ex.: 17"></div>
                <div class="col-md-4"><label class="form-label" for="own_icms_override">ICMS próprio destacado (opcional)</label><input class="form-control" id="own_icms_override" name="own_icms_override" inputmode="decimal" value="{{ old('own_icms_override') }}" placeholder="Em branco = cálculo automático"><div class="form-text">Se informado, substitui o ICMS próprio calculado pela alíquota da operação.</div></div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-disclosure title="Recursos avançados" description="Abra para MVA ajustada, FCP, alíquota interestadual ou múltiplos itens." badge="Prazzu Plus" :open="$errors->hasAny(['interstate_rate', 'fcp_rate', 'adjust_mva', 'items.*'])">
            <div class="row g-3 mb-3">
                <div class="col-md-4"><label class="form-label" for="interstate_rate">Alíquota interestadual (%)</label><input class="form-control" id="interstate_rate" name="interstate_rate" type="number" step="0.0001" min="0" max="99.9999" value="{{ old('interstate_rate') }}" placeholder="Ex.: 12"><div class="form-text">Usada somente quando a operação for interestadual.</div></div>
                <div class="col-md-4"><label class="form-label" for="fcp_rate">FCP-ST (%)</label><input class="form-control" id="fcp_rate" name="fcp_rate" type="number" step="0.0001" min="0" max="10" value="{{ old('fcp_rate') }}" placeholder="Ex.: 2"></div>
                <div class="col-md-4 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="adjust_mva" value="1" id="adjust_mva" @checked(old('adjust_mva'))><label class="form-check-label" for="adjust_mva">Calcular MVA ajustada na operação interestadual</label></div></div>
            </div>
            <h3 class="h6">Itens adicionais</h3>
            <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Descrição</th><th>Valor da mercadoria</th><th>MVA original (%)</th></tr></thead><tbody>
                @for($i=0;$i<4;$i++)<tr><td><input class="form-control" name="items[{{ $i }}][description]" value="{{ old("items.$i.description") }}" placeholder="Ex.: Item {{ $i+2 }}"></td><td><input class="form-control" name="items[{{ $i }}][merchandise_value]" value="{{ old("items.$i.merchandise_value") }}" inputmode="decimal"></td><td><input class="form-control" name="items[{{ $i }}][mva]" value="{{ old("items.$i.mva") }}" type="number" step="0.0001" min="0" max="500" placeholder="Usa MVA principal"></td></tr>@endfor
            </tbody></table></div>
            <div class="form-text">Os itens adicionais usam as mesmas alíquotas da operação. Frete, seguro, IPI e outras despesas do painel principal pertencem somente ao item principal.</div>
        </x-tools.form-disclosure>
        @endif

        <div class="form-check"><input class="form-check-input" type="checkbox" name="confirm_scope" value="1" id="confirm_scope" required @checked(old('confirm_scope'))><label class="form-check-label" for="confirm_scope">Confirmo que verifiquei NCM/CEST, sujeição à ST, MVA, alíquotas, FCP, benefícios e composição da base aplicáveis à operação.</label></div>
        <div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular ICMS-ST</button></div>
    </form>

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        @php($money=static fn(int $minor)=>\App\Core\Money\Money::fromMinor($minor)->formatPtBr())
        <div class="mt-5"><x-tools.result-panel title="ICMS-ST calculado" description="Estimativa conforme os parâmetros fiscais confirmados no formulário." eyebrow="Simulação concluída">
            <div class="row g-3 mb-4">@foreach($result->summary as $item)<div class="col-12 col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="receipt" /></div>@endforeach</div>
            <x-tools.result-insight
                :title="'ICMS-ST + FCP-ST estimados em '.$money($result->details['totals']['total_minor'])"
                :description="'A leitura usa os parâmetros fiscais que você confirmou para uma operação '.($result->details['operation_type'] === 'interstate' ? 'interestadual' : 'interna').'. O total não confirma, por si só, que a mercadoria esteja sujeita à substituição tributária.'"
                :items="[
                    'Base total de ICMS-ST formada na simulação: '.$money($result->details['totals']['st_base_minor']).'.',
                    'MVA utilizada no item principal: '.$result->details['used_mva'].'%.',
                    count($result->details['items']) > 1 ? count($result->details['items']).' itens foram consolidados neste resultado.' : 'O resultado corresponde ao item principal informado.',
                ]"
                icon="bi-receipt"
            />
            @foreach($result->warnings as $warning)<div class="alert alert-warning">{{ $warning->message }}</div>@endforeach

            <h3 class="h5 mt-4">Parâmetros utilizados</h3>
            <div class="table-responsive"><table class="table table-sm"><tbody><tr><th>Operação</th><td>{{ $result->details['operation_type']==='interstate'?'Interestadual':'Interna' }}</td></tr><tr><th>MVA original</th><td>{{ $result->details['original_mva'] }}%</td></tr><tr><th>MVA utilizada</th><td><strong>{{ $result->details['used_mva'] }}%</strong></td></tr><tr><th>Alíquota interna</th><td>{{ $result->details['internal_rate'] }}%</td></tr><tr><th>Alíquota própria/interestadual</th><td>{{ $result->details['interstate_rate'] }}%</td></tr><tr><th>FCP-ST</th><td>{{ $result->details['fcp_rate'] }}%</td></tr></tbody></table></div>

            <h3 class="h5 mt-4">Itens da operação @if(count($result->details['items'])>1)<span class="badge text-bg-primary">Plus</span>@endif</h3>
            <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Item</th><th class="text-end">Base operação</th><th class="text-end">MVA</th><th class="text-end">Base ST</th><th class="text-end">ICMS próprio</th><th class="text-end">ICMS-ST</th><th class="text-end">FCP-ST</th></tr></thead><tbody>@foreach($result->details['items'] as $item)<tr><td>{{ $item['description'] }}</td><td class="text-end">{{ $money($item['operation_base_minor']) }}</td><td class="text-end">{{ $item['mva'] }}%</td><td class="text-end">{{ $money($item['st_base_minor']) }}</td><td class="text-end">{{ $money($item['own_icms_minor']) }}</td><td class="text-end fw-semibold">{{ $money($item['icms_st_minor']) }}</td><td class="text-end">{{ $money($item['fcp_st_minor']) }}</td></tr>@endforeach</tbody></table></div>

            <h3 class="h5 mt-4">Memória de cálculo</h3><div class="accordion mb-4" id="icms-st-memory">@foreach($result->calculationMemory->steps as $step)<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#icms-st-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="icms-st-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}</div></div></div>@endforeach</div>
            @if(!empty($historySaved))<div class="alert alert-success">Cálculo salvo no histórico da sua conta.</div>@endif
            @php($exportInput=array_merge($calculationInput??[],['confirm_scope'=>1]))
            <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions"><a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.calculadora-icms-st.export',array_merge(['format'=>'pdf'],$exportInput)) }}">Exportar relatório PDF</a><a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.calculadora-icms-st.export',array_merge(['format'=>'xlsx'],$exportInput)) }}">Baixar planilha</a></div>

            <x-tools.normative-trust
                :rules="$result->calculationMemory?->normativeRules ?? []"
                :assumptions="$result->calculationMemory?->assumptions ?? []"
                :is-estimate="$result->calculationMemory?->isEstimate ?? false"
            />
</x-tools.result-panel></div>
    @endisset
</x-tools.page>
@endsection
