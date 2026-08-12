@extends('layouts.app')

@section('title','Calculadora de Retenções na Nota Fiscal')
@section('meta_description','Estime IRRF, INSS, ISS, PIS, Cofins e CSLL de notas fiscais com parâmetros configuráveis, memória de cálculo e relatório.')

@section('content')
<x-tools.page title="Calculadora de Retenções na Nota Fiscal" description="Estime retenções da nota com parâmetros revisáveis e veja quanto fica retido e o líquido estimado." icon="bi-receipt-cutoff" slug="calculadora-retencoes-nota-fiscal">
    <div class="alert alert-info"><strong>Ferramenta paramétrica.</strong> Marque somente tributos que você já confirmou como aplicáveis ao caso. Tipo do serviço, tomador, prestador, município, regime tributário, dispensas e exceções podem alterar a retenção.</div>
    <x-tools.validation-summary />

    <form method="POST" action="{{ route('tools.calculadora-retencoes-nota-fiscal.calculate') }}" class="vstack gap-4">
        @csrf
        <x-tools.form-panel title="Nota ou serviço" description="O Essencial resolve uma nota individual e mostra cada retenção com memória de cálculo." badge="Essencial">
            <div class="row g-3 mb-4">
                <div class="col-md-3"><label class="form-label" for="competence">Competência</label><input class="form-control" type="month" id="competence" name="competence" min="2026-01" max="2026-12" value="{{ old('competence','2026-08') }}" required></div>
                <div class="col-md-3"><label class="form-label" for="invoice_number">Número da NF</label><input class="form-control" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="Opcional"></div>
                <div class="col-md-6"><label class="form-label" for="service_description">Serviço / natureza da operação</label><input class="form-control" id="service_description" name="service_description" value="{{ old('service_description') }}" data-e2e-value="Serviço de consultoria" required></div>
                <div class="col-md-4"><x-tools.form.money name="gross_value" label="Valor bruto da nota" :value="old('gross_value')" data-e2e-value="10.000,00" required /></div>
            </div>

            <h3 class="h6 mb-3">Retenções a estimar</h3>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Aplicar</th><th>Tributo</th><th style="max-width:180px">Alíquota (%)</th><th>Referência de uso</th></tr></thead>
                    <tbody>
                        @foreach([
                            ['irrf','IRRF','1.5','Retenção de IR quando aplicável ao serviço/pagamento.'],
                            ['inss','INSS','11','Hipóteses previdenciárias, como cessão de mão de obra/empreitada.'],
                            ['iss','ISS','2','Confirme legislação e responsabilidade do município competente.'],
                            ['pis','PIS/Pasep','0.65','Contribuições sociais retidas nas hipóteses aplicáveis.'],
                            ['cofins','Cofins','3','Contribuições sociais retidas nas hipóteses aplicáveis.'],
                            ['csll','CSLL','1','Contribuições sociais retidas nas hipóteses aplicáveis.'],
                        ] as [$key,$label,$rate,$help])
                            <tr>
                                <td><div class="form-check"><input class="form-check-input" type="checkbox" name="apply_{{ $key }}" value="1" id="apply_{{ $key }}" @checked(old('apply_'.$key, in_array($key,['irrf','pis','cofins','csll'],true)))><label class="visually-hidden" for="apply_{{ $key }}">Aplicar {{ $label }}</label></div></td>
                                <td class="fw-semibold">{{ $label }}</td>
                                <td><input class="form-control" name="{{ $key }}_rate" id="{{ $key }}_rate" type="number" step="0.000001" min="0" max="100" value="{{ old($key.'_rate',$rate) }}" required></td>
                                <td class="small text-body-secondary">{{ $help }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-panel title="Regras avançadas e conferência em lote" description="Ajuste a base de cada tributo e inclua outras notas/serviços com as mesmas regras." badge="Prazzu Plus">
            <h3 class="h6 mb-3">Percentual da nota que compõe a base</h3>
            <div class="row g-3 mb-4">
                @foreach([['irrf','IRRF'],['inss','INSS'],['iss','ISS'],['pis','PIS/Pasep'],['cofins','Cofins'],['csll','CSLL']] as [$key,$label])
                    <div class="col-6 col-md-4 col-xl-2"><label class="form-label" for="{{ $key }}_base_percent">{{ $label }}</label><div class="input-group"><input class="form-control" id="{{ $key }}_base_percent" name="{{ $key }}_base_percent" type="number" step="0.000001" min="0" max="100" value="{{ old($key.'_base_percent','100') }}" required><span class="input-group-text">%</span></div></div>
                @endforeach
            </div>
            <div class="form-text mb-4">Mantenha 100% quando toda a nota compõe a base. Reduza somente quando houver fundamento para exclusão, dedução ou base específica.</div>

            <h3 class="h6">Notas ou serviços adicionais</h3>
            <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Descrição</th><th>Valor bruto</th></tr></thead><tbody>
                @for($i=0;$i<5;$i++)
                    <tr><td><input class="form-control" name="notes[{{ $i }}][description]" value="{{ old("notes.$i.description") }}" placeholder="Ex.: Nota adicional {{ $i+1 }}"></td><td><input class="form-control" name="notes[{{ $i }}][value]" value="{{ old("notes.$i.value",'0') }}" inputmode="decimal"></td></tr>
                @endfor
            </tbody></table></div>
            <div class="form-text">As notas adicionais usam as mesmas incidências, alíquotas e percentuais de base definidos acima. O relatório discrimina cada nota separadamente.</div>
        </x-tools.form-panel>
        @endif

        <div class="form-check"><input class="form-check-input" type="checkbox" name="confirm_scope" value="1" id="confirm_scope" required @checked(old('confirm_scope'))><label class="form-check-label" for="confirm_scope">Confirmo que revisei a aplicabilidade, as bases, as alíquotas e eventuais dispensas/exceções de cada retenção informada.</label></div>
        <div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular retenções</button></div>
    </form>

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        @php($money=static fn(int $minor)=>\App\Core\Money\Money::fromMinor($minor)->formatPtBr())
        <div class="mt-5"><x-tools.result-panel title="Resultado das retenções" description="Estimativa baseada exclusivamente nos parâmetros que você confirmou.">
            <div class="row g-3 mb-4">@foreach($result->summary as $item)<div class="col-12 col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="receipt" /></div>@endforeach</div>
            @foreach($result->warnings as $warning)<div class="alert alert-warning">{{ $warning->message }}</div>@endforeach

            <h3 class="h5 mt-4">Conferência por tributo</h3>
            <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Tributo</th><th>Status</th><th class="text-end">Base</th><th class="text-end">Alíquota</th><th class="text-end">Retido</th></tr></thead><tbody>
                @foreach($result->details['taxes'] as $tax)<tr><td class="fw-semibold">{{ $tax['label'] }}</td><td>{!! $tax['enabled'] ? '<span class="badge text-bg-success">Aplicado</span>' : '<span class="badge text-bg-secondary">Não aplicado</span>' !!}</td><td class="text-end">{{ $money($tax['base_minor']) }} <small class="text-body-secondary">({{ $tax['base_percent'] }}%)</small></td><td class="text-end">{{ $tax['rate'] }}%</td><td class="text-end fw-semibold">{{ $money($tax['withheld_minor']) }}</td></tr>@endforeach
            </tbody></table></div>

            <h3 class="h5 mt-4">Notas e serviços @if(count($result->details['notes'])>1)<span class="badge text-bg-primary">Plus</span>@endif</h3>
            <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Descrição</th><th>NF</th><th class="text-end">Bruto</th><th class="text-end">Retenções</th><th class="text-end">Líquido</th></tr></thead><tbody>@foreach($result->details['notes'] as $note)<tr><td>{{ $note['description'] }}</td><td>{{ $note['invoice_number'] ?: '—' }}</td><td class="text-end">{{ $money($note['gross_minor']) }}</td><td class="text-end">{{ $money($note['withheld_minor']) }}</td><td class="text-end fw-semibold">{{ $money($note['net_minor']) }}</td></tr>@endforeach</tbody></table></div>

            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <div class="accordion mb-4" id="retencoes-memory">@foreach($result->calculationMemory->steps as $step)<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#retencoes-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="retencoes-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}@if($step->roundingPolicy)<div class="small text-body-secondary mt-2">{{ $step->roundingPolicy }}</div>@endif</div></div></div>@endforeach</div>

            @if(!empty($historySaved))<div class="alert alert-success">Cálculo salvo no histórico da sua conta.</div>@endif
            @php($exportInput=array_merge($calculationInput??[],['confirm_scope'=>1]))
            <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions"><a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.calculadora-retencoes-nota-fiscal.export',array_merge(['format'=>'pdf'],$exportInput)) }}">Exportar relatório PDF</a><a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.calculadora-retencoes-nota-fiscal.export',array_merge(['format'=>'xlsx'],$exportInput)) }}">Baixar planilha</a></div>
        </x-tools.result-panel></div>
    @endisset
</x-tools.page>
@endsection
