@extends('layouts.app')

@section('title', 'Validador Inteligente de CNPJ, CPF e IE — Prazzu Tools')
@section('meta_description', 'Valide CPF e CNPJ gratuitamente, com detecção automática e diagnóstico dos dígitos verificadores.')

@section('content')
<div class="prazzu-page tool-page" data-tool="validador-de-cnpj">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Validador Inteligente de CNPJ, CPF e IE</li>
        </ol>
    </nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--green">
            <i class="bi bi-shield-check" aria-hidden="true"></i>
        </span>
        <div class="flex-grow-1">
            <span class="prazzu-badge prazzu-badge--green">Grátis</span>
            <h1>Validador Inteligente de CNPJ, CPF e IE</h1>
            <p>Valide CPF e CNPJ localmente, sem depender de consulta externa, e entenda com clareza o resultado.</p>
        </div>
    </header>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <section class="prazzu-form-panel" aria-labelledby="individual-validation-title">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
                    <div>
                        <h2 id="individual-validation-title" class="prazzu-section-title mb-1">Validação individual</h2>
                        <p class="text-body-secondary mb-0">Informe um CPF ou CNPJ com ou sem máscara.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-light border align-self-start">Validação local</span>
                </div>

                <form method="post" action="{{ route('tools.validador-de-cnpj.validate') }}" class="row g-3" novalidate>
                    @csrf
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="document_type">Tipo de documento</label>
                        <select class="form-select @error('document_type') is-invalid @enderror" id="document_type" name="document_type" required>
                            <option value="automatic" @selected(old('document_type', 'automatic') === 'automatic')>Detectar automaticamente</option>
                            <option value="cpf" @selected(old('document_type') === 'cpf')>CPF</option>
                            <option value="cnpj" @selected(old('document_type') === 'cnpj')>CNPJ</option>
                        </select>
                        @error('document_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 col-md-7">
                        <label class="form-label" for="document_number">Número do documento</label>
                        <input
                            class="form-control @error('document_number') is-invalid @enderror"
                            id="document_number"
                            name="document_number"
                            value="{{ old('document_number') }}"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="000.000.000-00 ou 00.000.000/0000-00"
                            maxlength="30"
                            required
                        >
                        @error('document_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">A validação matemática não consulta nem armazena dados cadastrais.</div>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2">
                        <button class="btn btn-primary prazzu-btn-primary" type="submit">
                            <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                            Validar documento
                        </button>
                        <a class="btn btn-outline-secondary" href="{{ route('tools.validador-de-cnpj.index') }}">Limpar</a>
                    </div>
                </form>
            </section>


            <section class="prazzu-form-panel mt-4" aria-labelledby="state-registration-title">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
                    <div>
                        <h2 id="state-registration-title" class="prazzu-section-title mb-1">Validação de Inscrição Estadual</h2>
                        <p class="text-body-secondary mb-0">Selecione a UF para uma confirmação precisa ou busque estados candidatos.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-light border align-self-start">13 UFs suportadas</span>
                </div>

                <form method="post" action="{{ route('tools.validador-de-cnpj.validate-state-registration') }}" class="row g-3" novalidate>
                    @csrf
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="state">UF</label>
                        <select class="form-select @error('state') is-invalid @enderror" id="state" name="state" required>
                            <option value="AUTO" @selected(old('state', 'AUTO') === 'AUTO')>Detectar estados candidatos</option>
                            @foreach ([
                                'CE' => 'Ceará', 'ES' => 'Espírito Santo', 'MA' => 'Maranhão', 'MG' => 'Minas Gerais',
                                'PA' => 'Pará', 'PB' => 'Paraíba', 'PE' => 'Pernambuco', 'PR' => 'Paraná',
                                'RJ' => 'Rio de Janeiro', 'RS' => 'Rio Grande do Sul', 'SC' => 'Santa Catarina',
                                'SE' => 'Sergipe', 'SP' => 'São Paulo'
                            ] as $uf => $name)
                                <option value="{{ $uf }}" @selected(old('state') === $uf)>{{ $uf }} — {{ $name }}</option>
                            @endforeach
                        </select>
                        @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-7">
                        <label class="form-label" for="state_registration">Inscrição Estadual</label>
                        <input
                            class="form-control @error('state_registration') is-invalid @enderror"
                            id="state_registration"
                            name="state_registration"
                            value="{{ old('state_registration') }}"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="Informe a IE com ou sem pontuação"
                            maxlength="30"
                            required
                        >
                        @error('state_registration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <button class="btn btn-primary prazzu-btn-primary" type="submit">
                            <i class="bi bi-patch-check me-1" aria-hidden="true"></i>
                            Validar IE
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="form-text">A detecção automática mostra candidatos; a seleção da UF é a forma mais segura de confirmar a regra aplicável.</div>
                    </div>
                </form>
            </section>

            @if (session('state_registration_result'))
                @php($ie = session('state_registration_result'))
                @php($ieClass = $ie['valid'] ? 'success' : ($ie['supported'] ? 'warning' : 'secondary'))
                <section class="mt-4" aria-labelledby="state-registration-result-title">
                    <div class="card border-{{ $ieClass }} shadow-sm">
                        <div class="card-header bg-{{ $ieClass }}-subtle border-{{ $ieClass }}-subtle">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <div>
                                    <h2 id="state-registration-result-title" class="h5 mb-1">
                                        <i class="bi bi-{{ $ie['valid'] ? 'check-circle-fill text-success' : 'exclamation-circle-fill text-warning' }} me-1" aria-hidden="true"></i>
                                        {{ $ie['valid'] ? 'Inscrição Estadual válida' : 'Inscrição Estadual não confirmada' }}
                                    </h2>
                                    <p class="mb-0 text-body-secondary">{{ $ie['message'] }}</p>
                                </div>
                                <span class="badge text-bg-{{ $ieClass }} align-self-start">{{ $ie['state_label'] }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-{{ $ie['candidate_states'] ? '3' : '0' }}">
                                <dt class="col-sm-4">Número normalizado</dt>
                                <dd class="col-sm-8"><code>{{ $ie['formatted'] ?: $ie['normalized'] }}</code></dd>
                                <dt class="col-sm-4">UF analisada</dt>
                                <dd class="col-sm-8 mb-0">{{ $ie['state'] ? $ie['state'].' — '.$ie['state_label'] : $ie['state_label'] }}</dd>
                            </dl>
                            @if ($ie['candidate_states'])
                                <h3 class="h6">Estados candidatos</h3>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($ie['candidate_states'] as $candidate)
                                        <span class="badge text-bg-light border">{{ $candidate }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            <section class="prazzu-form-panel mt-4" aria-labelledby="company-lookup-title">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
                    <div>
                        <h2 id="company-lookup-title" class="prazzu-section-title mb-1">Consulta cadastral de CNPJ</h2>
                        <p class="text-body-secondary mb-0">Consulte dados públicos da empresa sem confundir disponibilidade externa com validade matemática.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-primary align-self-start">Consulta externa</span>
                </div>

                <form method="post" action="{{ route('tools.validador-de-cnpj.lookup-company') }}" class="row g-3" novalidate>
                    @csrf
                    <div class="col-12 col-lg-8">
                        <label class="form-label" for="cnpj">CNPJ</label>
                        <input
                            class="form-control @error('cnpj') is-invalid @enderror"
                            id="cnpj"
                            name="cnpj"
                            value="{{ old('cnpj') }}"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="00.000.000/0000-00"
                            maxlength="30"
                            required
                        >
                        @error('cnpj')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-4 d-flex align-items-end">
                        <button class="btn btn-primary prazzu-btn-primary w-100" type="submit">
                            <i class="bi bi-search me-1" aria-hidden="true"></i>
                            Consultar CNPJ
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="form-text">A consulta só é enviada ao provedor quando o CNPJ passa na validação matemática local.</div>
                    </div>
                </form>
            </section>

            @if (session('registry_lookup_result'))
                @php($lookup = session('registry_lookup_result'))
                @php($lookupClass = $lookup['status'] === 'found' ? 'success' : ($lookup['status'] === 'not_found' ? 'warning' : 'secondary'))
                <section class="mt-4" aria-labelledby="registry-result-title">
                    <div class="card border-{{ $lookupClass }} shadow-sm">
                        <div class="card-header bg-{{ $lookupClass }}-subtle border-{{ $lookupClass }}-subtle">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                <div>
                                    <h2 id="registry-result-title" class="h5 mb-1">{{ $lookup['status_label'] }}</h2>
                                    <p class="mb-0 text-body-secondary">{{ $lookup['message'] }}</p>
                                </div>
                                <span class="badge text-bg-{{ $lookupClass }} align-self-start">Dados cadastrais</span>
                            </div>
                        </div>

                        @if ($lookup['company'])
                            @php($company = $lookup['company'])
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-12 col-lg-7">
                                        <h3 class="h6 text-uppercase text-body-secondary">Identificação</h3>
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4">Razão social</dt><dd class="col-sm-8">{{ $company['legal_name'] }}</dd>
                                            <dt class="col-sm-4">Nome fantasia</dt><dd class="col-sm-8">{{ $company['trade_name'] ?: 'Não informado' }}</dd>
                                            <dt class="col-sm-4">Situação</dt><dd class="col-sm-8"><span class="badge text-bg-light border">{{ $company['registration_status'] ?: 'Não informada' }}</span></dd>
                                            <dt class="col-sm-4">Matriz/filial</dt><dd class="col-sm-8">{{ $company['branch_type'] ?: 'Não informado' }}</dd>
                                            <dt class="col-sm-4">Abertura</dt><dd class="col-sm-8">{{ $company['opening_date'] ?: 'Não informada' }}</dd>
                                            <dt class="col-sm-4">Natureza jurídica</dt><dd class="col-sm-8 mb-0">{{ $company['legal_nature'] ?: 'Não informada' }}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-12 col-lg-5">
                                        <h3 class="h6 text-uppercase text-body-secondary">Endereço cadastral</h3>
                                        <address class="mb-0">
                                            {{ $company['address']['street'] ?: 'Logradouro não informado' }}{{ $company['address']['number'] ? ', '.$company['address']['number'] : '' }}<br>
                                            @if ($company['address']['complement']){{ $company['address']['complement'] }}<br>@endif
                                            {{ $company['address']['district'] ?: 'Bairro não informado' }}<br>
                                            {{ $company['address']['city'] ?: 'Município não informado' }} / {{ $company['address']['state'] ?: 'UF' }}<br>
                                            CEP: {{ $company['address']['postal_code'] ?: 'Não informado' }}
                                        </address>
                                    </div>
                                </div>

                                <hr>

                                <h3 class="h6 text-uppercase text-body-secondary">Atividades econômicas</h3>
                                @if ($company['primary_activity'])
                                    <div class="alert alert-light border mb-3">
                                        <strong>Principal:</strong>
                                        {{ $company['primary_activity']['code'] }} — {{ $company['primary_activity']['description'] }}
                                    </div>
                                @endif

                                @if ($company['secondary_activities'])
                                    <div class="accordion" id="secondary-activities">
                                        <div class="accordion-item">
                                            <h4 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secondary-activities-list" aria-expanded="false" aria-controls="secondary-activities-list">
                                                    Atividades secundárias ({{ count($company['secondary_activities']) }})
                                                </button>
                                            </h4>
                                            <div id="secondary-activities-list" class="accordion-collapse collapse" data-bs-parent="#secondary-activities">
                                                <div class="accordion-body p-0">
                                                    <ul class="list-group list-group-flush">
                                                        @foreach ($company['secondary_activities'] as $activity)
                                                            <li class="list-group-item">{{ $activity['code'] }} — {{ $activity['description'] }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <p class="small text-body-secondary mt-3 mb-0">
                                    Fonte: {{ $company['source'] }}. Consulta realizada em {{ \Illuminate\Support\Carbon::parse($company['consulted_at'])->format('d/m/Y H:i:s') }}.
                                </p>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            <section class="prazzu-form-panel mt-4" aria-labelledby="consistency-analysis-title">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
                    <div>
                        <h2 id="consistency-analysis-title" class="prazzu-section-title mb-1">Análise inteligente de inconsistências</h2>
                        <p class="text-body-secondary mb-0">Compare os dados informados com a consulta cadastral e com as regras locais disponíveis.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-success align-self-start">Análise auditável</span>
                </div>

                <form method="post" action="{{ route('tools.validador-de-cnpj.analyze-consistency') }}" class="row g-3" novalidate>
                    @csrf
                    <div class="col-12 col-lg-6">
                        <label class="form-label" for="analysis_cnpj">CNPJ *</label>
                        <input class="form-control @error('analysis_cnpj') is-invalid @enderror" id="analysis_cnpj" name="analysis_cnpj" value="{{ old('analysis_cnpj') }}" inputmode="numeric" autocomplete="off" placeholder="00.000.000/0000-00" maxlength="30" required>
                        @error('analysis_cnpj')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label" for="legal_name">Razão social informada</label>
                        <input class="form-control @error('legal_name') is-invalid @enderror" id="legal_name" name="legal_name" value="{{ old('legal_name') }}" maxlength="255" placeholder="Ex.: Empresa Exemplo Ltda">
                        @error('legal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label" for="trade_name">Nome fantasia informado</label>
                        <input class="form-control @error('trade_name') is-invalid @enderror" id="trade_name" name="trade_name" value="{{ old('trade_name') }}" maxlength="255" placeholder="Ex.: Exemplo">
                        @error('trade_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label" for="analysis_state">UF</label>
                        <select class="form-select @error('analysis_state') is-invalid @enderror" id="analysis_state" name="analysis_state">
                            <option value="">Selecione</option>
                            @foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" @selected(old('analysis_state') === $uf)>{{ $uf }}</option>
                            @endforeach
                        </select>
                        @error('analysis_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-8 col-lg-4">
                        <label class="form-label" for="city">Município informado</label>
                        <input class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" maxlength="150" placeholder="Ex.: São Paulo">
                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label" for="analysis_state_registration">Inscrição Estadual informada</label>
                        <input class="form-control @error('analysis_state_registration') is-invalid @enderror" id="analysis_state_registration" name="analysis_state_registration" value="{{ old('analysis_state_registration') }}" maxlength="30" inputmode="numeric" placeholder="Selecione a UF para validar a IE">
                        @error('analysis_state_registration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <button class="btn btn-primary prazzu-btn-primary" type="submit">
                            <i class="bi bi-clipboard2-pulse me-1" aria-hidden="true"></i>
                            Analisar inconsistências
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="form-text">Campos além do CNPJ são opcionais. Quanto mais dados forem preenchidos, mais cruzamentos poderão ser realizados.</div>
                    </div>
                </form>
            </section>

            @if (session('consistency_analysis_result'))
                @php($analysis = session('consistency_analysis_result'))
                @php($analysisClass = $analysis['summary']['errors'] > 0 ? 'danger' : ($analysis['summary']['warnings'] > 0 ? 'warning' : 'success'))
                <section class="mt-4" aria-labelledby="consistency-analysis-result-title">
                    <div class="card border-{{ $analysisClass }} shadow-sm">
                        <div class="card-header bg-{{ $analysisClass }}-subtle border-{{ $analysisClass }}-subtle">
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                <div>
                                    <h2 id="consistency-analysis-result-title" class="h5 mb-1">Resultado da análise de inconsistências</h2>
                                    <p class="mb-0 text-body-secondary">{{ $analysis['message'] }}</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-self-start">
                                    <span class="badge text-bg-danger">{{ $analysis['summary']['errors'] }} erro(s)</span>
                                    <span class="badge text-bg-warning">{{ $analysis['summary']['warnings'] }} alerta(s)</span>
                                    <span class="badge text-bg-info">{{ $analysis['summary']['information'] }} informação(ões)</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="vstack gap-3">
                                @foreach ($analysis['inconsistencies'] as $item)
                                    @php($itemClass = $item['severity'] === 'error' ? 'danger' : ($item['severity'] === 'warning' ? 'warning' : 'info'))
                                    <article class="alert alert-{{ $itemClass }} mb-0" aria-label="{{ $item['severity_label'] }}: {{ $item['title'] }}">
                                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-2">
                                            <h3 class="h6 mb-0">{{ $item['title'] }}</h3>
                                            <span class="badge text-bg-{{ $itemClass }} align-self-start">{{ $item['severity_label'] }}</span>
                                        </div>
                                        <p class="mb-2">{{ $item['message'] }}</p>
                                        @if ($item['informed_value'] !== null || $item['registry_value'] !== null)
                                            <dl class="row small mb-2">
                                                @if ($item['informed_value'] !== null)
                                                    <dt class="col-sm-4">Valor informado</dt><dd class="col-sm-8">{{ $item['informed_value'] }}</dd>
                                                @endif
                                                @if ($item['registry_value'] !== null)
                                                    <dt class="col-sm-4">Valor cadastral</dt><dd class="col-sm-8">{{ $item['registry_value'] }}</dd>
                                                @endif
                                            </dl>
                                        @endif
                                        <p class="small mb-0"><strong>Recomendação:</strong> {{ $item['recommendation'] }}</p>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if (session('validation_result'))
                @php($result = session('validation_result'))
                <section class="mt-4" aria-labelledby="validation-result-title">
                    <div class="card border-{{ $result['valid'] ? 'success' : 'danger' }} shadow-sm">
                        <div class="card-header bg-{{ $result['valid'] ? 'success' : 'danger' }}-subtle border-{{ $result['valid'] ? 'success' : 'danger' }}-subtle">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                                <div>
                                    <h2 id="validation-result-title" class="h5 mb-1">
                                        <i class="bi bi-{{ $result['valid'] ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} me-1" aria-hidden="true"></i>
                                        {{ $result['type_label'] }} {{ $result['valid'] ? 'válido' : 'inválido' }}
                                    </h2>
                                    <p class="mb-0 text-body-secondary">Resultado da verificação matemática do documento.</p>
                                </div>
                                <span class="badge text-bg-{{ $result['valid'] ? 'success' : 'danger' }} align-self-start align-self-md-center">
                                    {{ $result['valid'] ? 'Aprovado' : 'Reprovado' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-4">
                                <dt class="col-sm-4">Tipo identificado</dt>
                                <dd class="col-sm-8">{{ $result['type_label'] }}</dd>
                                <dt class="col-sm-4">Documento formatado</dt>
                                <dd class="col-sm-8 mb-0"><code>{{ $result['formatted'] !== '' ? $result['formatted'] : 'Não informado' }}</code></dd>
                            </dl>

                            <h3 class="h6">Diagnóstico</h3>
                            <ul class="list-group list-group-flush border rounded">
                                @foreach ($result['messages'] as $message)
                                    <li class="list-group-item d-flex gap-2 align-items-start">
                                        <i class="bi bi-info-circle text-primary mt-1" aria-hidden="true"></i>
                                        <span>{{ $message }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </section>
            @endif
            <section class="prazzu-form-panel mt-4" aria-labelledby="batch-import-title">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
                    <div>
                        <h2 id="batch-import-title" class="prazzu-section-title mb-1">Validação e consulta em lote</h2>
                        <p class="text-body-secondary mb-0">Importe CSV ou Excel, confira as colunas e processe até 500 registros por arquivo.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-warning align-self-start">Prazzu Plus</span>
                </div>

                @error('batch_file')<div class="alert alert-danger">{{ $message }}</div>@enderror
                @error('batch_import')<div class="alert alert-danger">{{ $message }}</div>@enderror

                <form method="post" action="{{ route('tools.validador-de-cnpj.batch.preview') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-12 col-lg-8">
                        <label class="form-label" for="batch_file">Planilha *</label>
                        <input class="form-control @error('batch_file') is-invalid @enderror" id="batch_file" name="batch_file" type="file" accept=".csv,.txt,.xlsx" required>
                        <div class="form-text">Primeira linha como cabeçalho. Máximo de 5 MB e 500 linhas. Na planilha Excel, a primeira aba será utilizada.</div>
                    </div>
                    <div class="col-12 col-lg-4 d-flex align-items-end">
                        <button class="btn btn-primary prazzu-btn-primary w-100" type="submit">
                            <i class="bi bi-file-earmark-spreadsheet me-1" aria-hidden="true"></i>
                            Importar e pré-visualizar
                        </button>
                    </div>
                </form>

                @if (session('batch_import_preview'))
                    @php($preview = session('batch_import_preview'))
                    <div class="card border-0 bg-body-tertiary mt-4">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                <div>
                                    <h3 class="h6 mb-1">{{ $preview['file_name'] }}</h3>
                                    <p class="small text-body-secondary mb-0">{{ $preview['format'] }} · {{ $preview['total_rows'] }} linha(s) encontrada(s)</p>
                                </div>
                                <span class="badge text-bg-success align-self-start">Arquivo lido</span>
                            </div>

                            <div class="table-responsive border rounded mb-4">
                                <table class="table table-sm table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            @foreach ($preview['headers'] as $header)<th scope="col">{{ $header }}</th>@endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($preview['preview_rows'] as $row)
                                            <tr>@foreach ($preview['headers'] as $header)<td>{{ $row[$header] ?? '—' }}</td>@endforeach</tr>
                                        @empty
                                            <tr><td colspan="{{ max(1, count($preview['headers'])) }}" class="text-center text-body-secondary py-3">Nenhuma linha de dados encontrada.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <h3 class="h6">Mapeamento das colunas</h3>
                            <form method="post" action="{{ route('tools.validador-de-cnpj.batch.process') }}" class="row g-3">
                                @csrf
                                <input type="hidden" name="import_token" value="{{ $preview['token'] }}">
                                @php($mapping = $preview['suggested_mapping'] ?? [])
                                @foreach ([
                                    'document_column' => ['CPF ou CNPJ *', true],
                                    'legal_name_column' => ['Razão social', false],
                                    'trade_name_column' => ['Nome fantasia', false],
                                    'state_column' => ['UF', false],
                                    'city_column' => ['Município', false],
                                    'state_registration_column' => ['Inscrição Estadual', false],
                                ] as $field => [$label, $required])
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                        <select class="form-select @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" @required($required)>
                                            <option value="">{{ $required ? 'Selecione uma coluna' : 'Não importar' }}</option>
                                            @foreach ($preview['headers'] as $header)
                                                <option value="{{ $header }}" @selected(old($field, $mapping[$field] ?? '') === $header)>{{ $header }}</option>
                                            @endforeach
                                        </select>
                                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach

                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" id="consult_registry" name="consult_registry" type="checkbox" value="1" @checked(old('consult_registry'))>
                                        <label class="form-check-label" for="consult_registry">Consultar situação cadastral dos CNPJs válidos</label>
                                    </div>
                                    <div class="form-text">Para proteger o provedor externo, são realizadas no máximo 50 consultas cadastrais por processamento. Todos os documentos continuam sendo validados localmente.</div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success" type="submit">
                                        <i class="bi bi-play-circle me-1" aria-hidden="true"></i>
                                        Processar {{ $preview['total_rows'] }} registro(s)
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </section>

            @if (session('batch_validation_result'))
                @php($batch = session('batch_validation_result'))
                <section class="mt-4" aria-labelledby="batch-result-title">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h2 id="batch-result-title" class="h5 mb-1">Resultado do processamento em lote</h2>
                            <p class="text-body-secondary mb-0">Validação concluída para {{ $batch['summary']['total'] }} registro(s).</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-4">
                                @foreach ([
                                    ['Válidos', $batch['summary']['valid'], 'success'],
                                    ['Inválidos', $batch['summary']['invalid'], 'danger'],
                                    ['Duplicados', $batch['summary']['duplicates'], 'warning'],
                                    ['Com inconsistências', $batch['summary']['with_inconsistencies'], 'info'],
                                ] as [$label, $value, $context])
                                    <div class="col-6 col-lg-3"><div class="border rounded p-3 h-100"><div class="small text-body-secondary">{{ $label }}</div><div class="fs-4 fw-semibold text-{{ $context }}">{{ $value }}</div></div></div>
                                @endforeach
                            </div>

                            @if ($batch['summary']['registry_consulted'] > 0)
                                <div class="alert alert-light border">{{ $batch['summary']['registry_consulted'] }} consulta(s) cadastral(is) realizada(s); {{ $batch['summary']['registry_unavailable'] }} indisponível(is).</div>
                            @endif

                            @if (session('history_saved'))
                                <div class="alert alert-success"><i class="bi bi-clock-history me-1" aria-hidden="true"></i>Resumo salvo no seu histórico, sem armazenar documentos ou dados cadastrais.</div>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Exportações do resultado">
                                <a class="btn btn-outline-success" href="{{ route('tools.validador-de-cnpj.batch.export', ['format' => 'excel']) }}"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
                                <a class="btn btn-outline-primary" href="{{ route('tools.validador-de-cnpj.batch.export', ['format' => 'csv']) }}"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
                                <a class="btn btn-outline-warning" href="{{ route('tools.validador-de-cnpj.batch.export', ['format' => 'excel', 'only_issues' => 1]) }}"><i class="bi bi-exclamation-triangle me-1"></i>Somente problemas</a>
                                <a class="btn btn-outline-secondary" target="_blank" rel="noopener" href="{{ route('tools.validador-de-cnpj.batch.print') }}"><i class="bi bi-printer me-1"></i>Imprimir / PDF</a>
                            </div>

                            <div class="table-responsive border rounded">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light"><tr><th>Linha</th><th>Documento</th><th>Tipo</th><th>Validação</th><th>Cadastro</th><th>Alertas</th></tr></thead>
                                    <tbody>
                                        @foreach ($batch['rows'] as $row)
                                            <tr>
                                                <td>{{ $row['line'] }}</td>
                                                <td><code>{{ $row['formatted_document'] ?: $row['document'] }}</code>@if($row['duplicate'])<span class="badge text-bg-warning ms-1">Duplicado</span>@endif</td>
                                                <td>{{ $row['type'] }}</td>
                                                <td><span class="badge text-bg-{{ $row['valid'] ? 'success' : 'danger' }}">{{ $row['valid'] ? 'Válido' : 'Inválido' }}</span><div class="small text-body-secondary mt-1">{{ $row['message'] }}</div></td>
                                                <td>
                                                    @if ($row['registry_status'] === 'found')
                                                        <span class="badge text-bg-success">Localizado</span><div class="small mt-1">{{ $row['company']['legal_name'] ?? '' }}</div>
                                                    @elseif ($row['registry_status'])
                                                        <span class="badge text-bg-secondary">{{ $row['registry_message'] }}</span>
                                                    @else
                                                        <span class="text-body-secondary">Não consultado</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($row['inconsistencies'])
                                                        <div class="vstack gap-1">@foreach ($row['inconsistencies'] as $issue)<span class="badge text-bg-{{ $issue['severity'] === 'error' ? 'danger' : ($issue['severity'] === 'warning' ? 'warning' : 'info') }} text-wrap">{{ $issue['title'] }}</span>@endforeach</div>
                                                    @else
                                                        <span class="text-body-secondary">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        </div>

        <div class="col-12 col-xl-4">
            <aside class="card border-0 shadow-sm" aria-labelledby="validation-info-title">
                <div class="card-body p-4">
                    <h2 id="validation-info-title" class="h5 mb-3">O que já está disponível</h2>
                    <div class="vstack gap-3">
                        <div class="d-flex gap-3">
                            <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
                            <div><strong>CPF</strong><div class="small text-body-secondary">Tamanho, repetição e dígitos verificadores.</div></div>
                        </div>
                        <div class="d-flex gap-3">
                            <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
                            <div><strong>CNPJ</strong><div class="small text-body-secondary">Tamanho, repetição e dígitos verificadores.</div></div>
                        </div>
                        <div class="d-flex gap-3">
                            <i class="bi bi-magic text-primary" aria-hidden="true"></i>
                            <div><strong>Detecção automática</strong><div class="small text-body-secondary">Identificação pelo número de dígitos.</div></div>
                        </div>
                        <div class="d-flex gap-3">
                            <i class="bi bi-lock-fill text-secondary" aria-hidden="true"></i>
                            <div><strong>Inscrição Estadual</strong><div class="small text-body-secondary">Regras estaduais para 13 UFs, com busca de candidatos.</div></div>
                        </div>
                        <div class="d-flex gap-3">
                            <i class="bi bi-lock-fill text-secondary" aria-hidden="true"></i>
                            <div><strong>Análise de inconsistências</strong><div class="small text-body-secondary">Cruzamento auditável de cadastro, endereço, situação e IE.</div></div>
                        </div>
                    </div>
                </div>
            </aside>

            @auth
                <aside class="card border-0 shadow-sm mt-4" aria-labelledby="recent-history-title">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 id="recent-history-title" class="h5 mb-0">Histórico recente</h2>
                            <a class="small" href="{{ route('tools.validador-de-cnpj.history.index') }}">Ver tudo</a>
                        </div>
                        @forelse ($recentHistory as $run)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="fw-semibold">{{ data_get($run->input_payload, 'file_name', 'Importação') }}</div>
                                <div class="small text-body-secondary">{{ $run->finished_at?->format('d/m/Y H:i') }} · {{ data_get($run->result_payload, 'summary.total', 0) }} registros</div>
                            </div>
                        @empty
                            <p class="small text-body-secondary mb-0">Nenhum processamento salvo ainda.</p>
                        @endforelse
                    </div>
                </aside>
            @endauth

            <div class="alert alert-warning mt-4 mb-0" role="note">
                <strong>Importante:</strong> um documento matematicamente válido pode não estar ativo ou regular. A consulta cadastral depende de um provedor externo e pode ficar temporariamente indisponível sem afetar a validação matemática.
            </div>
        </div>
    </div>
</div>
@endsection
