@extends('layouts.app')

@section('title', 'Calculadora de Honorários Contábeis — Prazzu Tools')
@section('meta_description', 'Estime honorários contábeis de acordo com o porte, regime tributário e complexidade da empresa.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-de-honorarios-contabeis">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calculadora de Honorários Contábeis</li>
        </ol>
    </nav>

    <x-tools.intro icon="calculator" title="Calculadora de Honorários Contábeis" description="Estruture uma precificação coerente considerando o perfil e a complexidade de cada cliente." badge="Grátis" />

    <x-tool-feature-tiers slug="calculadora-de-honorarios-contabeis" />

    @include('tools-calculadora-de-honorarios-contabeis::partials.navigation')

    @if ($successMessage ?? session('success'))
        <div class="alert alert-success" role="alert">{{ $successMessage ?? session('success') }}</div>
    @endif

    <x-tools.validation-summary class="mb-4" />

    @if ($taxSnapshotIntegration)
        <div class="alert alert-primary d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" role="status">
            <div>
                <div class="fw-semibold"><i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i> Dados disponíveis do Simples Nacional</div>
                <div class="small">Faturamento mensal: R$ {{ $taxSnapshotIntegration->data['monthly_revenue'] }} · Anexo {{ $taxSnapshotIntegration->data['annex'] }}</div>
            </div>
            <button class="btn btn-primary btn-sm flex-shrink-0" type="button" data-apply-tax-snapshot>Usar estes dados</button>
        </div>
    @endif

    <section class="prazzu-tool-workspace text-start" aria-labelledby="pricing-data-title">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
            <div>
                <h2 id="pricing-data-title" class="mb-1">Dados para precificação</h2>
                <p class="text-body-secondary mb-0">Informe o cenário mensal do cliente. Campos com <span class="text-danger">*</span> são obrigatórios.</p>
            </div>
            <span class="badge text-bg-light border align-self-start align-self-lg-center">Regra 1.0.0</span>
        </div>

        <form class="row g-3" method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.calculate') }}">
            @csrf

            <div class="col-12 col-lg-6">
                <label class="form-label" for="monthly_revenue">Faturamento mensal <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input class="form-control @error('monthly_revenue') is-invalid @enderror" id="monthly_revenue" name="monthly_revenue" value="{{ old('monthly_revenue') }}" placeholder="100.000,00" inputmode="decimal" required>
                    @error('monthly_revenue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label" for="employees">Funcionários <span class="text-danger">*</span></label>
                <input class="form-control @error('employees') is-invalid @enderror" id="employees" name="employees" type="number" min="0" value="{{ old('employees', 0) }}" required>
                @error('employees')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label" for="partners">Sócios ou titulares <span class="text-danger">*</span></label>
                <input class="form-control @error('partners') is-invalid @enderror" id="partners" name="partners" type="number" min="1" value="{{ old('partners', 1) }}" required>
                @error('partners')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="tax_regime">Regime tributário <span class="text-danger">*</span></label>
                <select class="form-select @error('tax_regime') is-invalid @enderror" id="tax_regime" name="tax_regime" required>
                    <option value="">Selecione o regime</option>
                    <option value="mei" @selected(old('tax_regime') === 'mei')>MEI</option>
                    <option value="simples_nacional" @selected(old('tax_regime') === 'simples_nacional')>Simples Nacional</option>
                    <option value="lucro_presumido" @selected(old('tax_regime') === 'lucro_presumido')>Lucro Presumido</option>
                    <option value="lucro_real" @selected(old('tax_regime') === 'lucro_real')>Lucro Real</option>
                </select>
                @error('tax_regime')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="business_segment">Segmento <span class="text-danger">*</span></label>
                <select class="form-select @error('business_segment') is-invalid @enderror" id="business_segment" name="business_segment" required>
                    <option value="">Selecione o segmento</option>
                    @foreach (['services' => 'Prestação de serviços', 'commerce' => 'Comércio', 'industry' => 'Indústria', 'construction' => 'Construção civil', 'healthcare' => 'Saúde', 'digital_business' => 'Negócios digitais', 'other' => 'Outro segmento'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('business_segment') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('business_segment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="monthly_invoices">Notas fiscais por mês <span class="text-danger">*</span></label>
                <input class="form-control @error('monthly_invoices') is-invalid @enderror" id="monthly_invoices" name="monthly_invoices" type="number" min="0" value="{{ old('monthly_invoices', 0) }}" required>
                <div class="form-text">Considere notas de entrada, saída e serviços.</div>
                @error('monthly_invoices')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="monthly_bank_transactions">Movimentações financeiras por mês <span class="text-danger">*</span></label>
                <input class="form-control @error('monthly_bank_transactions') is-invalid @enderror" id="monthly_bank_transactions" name="monthly_bank_transactions" type="number" min="0" value="{{ old('monthly_bank_transactions', 0) }}" required>
                <div class="form-text">Use uma média de lançamentos bancários e financeiros.</div>
                @error('monthly_bank_transactions')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label" for="complexity">Complexidade operacional <span class="text-danger">*</span></label>
                <select class="form-select @error('complexity') is-invalid @enderror" id="complexity" name="complexity" required>
                    <option value="">Selecione a complexidade</option>
                    <option value="low" @selected(old('complexity') === 'low')>Baixa — rotina padronizada</option>
                    <option value="medium" @selected(old('complexity') === 'medium')>Média — exceções recorrentes</option>
                    <option value="high" @selected(old('complexity') === 'high')>Alta — operação diversificada</option>
                    <option value="very_high" @selected(old('complexity') === 'very_high')>Muito alta — controles especiais</option>
                </select>
                @error('complexity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0" role="alert">
                        <strong>Revise os campos informados.</strong>
                    </div>
                </div>
            @endif

            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                <button class="btn btn-primary prazzu-btn-primary" type="submit">
                    <i class="bi bi-calculator me-1" aria-hidden="true"></i> Calcular honorários
                </button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">
                    <i class="bi bi-eraser me-1" aria-hidden="true"></i> Limpar formulário
                </a>
            </div>
        </form>
    </section>

    @php($result = $calculationResult ?? session('calculation_result'))
    @if ($result)
        @php($complexityVariant = match ($result['complexity_level']) { 'Baixa' => 'success', 'Média' => 'warning', 'Alta', 'Muito alta' => 'danger', default => 'secondary' })

        <section class="mt-4" aria-labelledby="calculation-result-title">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-3">
                <div>
                    <h2 id="calculation-result-title" class="prazzu-section-title mb-1">Resultado da precificação</h2>
                    <p class="text-body-secondary mb-0">Use os indicadores abaixo para estruturar uma proposta comercial sustentável.</p>
                </div>
                <span class="badge text-bg-{{ $complexityVariant }} fs-6 align-self-start align-self-lg-center">
                    Complexidade {{ $result['complexity_level'] }} · {{ $result['complexity_score'] }}/100
                </span>
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <span class="text-body-secondary">Honorário mínimo</span>
                                <span class="badge text-bg-light border"><i class="bi bi-shield-check me-1"></i>Piso técnico</span>
                            </div>
                            <div class="display-6 fw-bold mt-3">{{ $result['minimum_fee'] }}</div>
                            <p class="text-body-secondary small mt-2 mb-0">Valor estimado para cobrir a operação sem margem comercial adicional.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card h-100 border-primary shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <span class="text-body-secondary">Honorário recomendado</span>
                                <span class="badge text-bg-primary"><i class="bi bi-stars me-1"></i>Principal</span>
                            </div>
                            <div class="display-6 fw-bold text-primary mt-3">{{ $result['recommended_fee'] }}</div>
                            <p class="text-body-secondary small mt-2 mb-0">Inclui margem operacional de 15% para sustentar atendimento, gestão e imprevistos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <span class="text-body-secondary">Referência superior</span>
                                <span class="badge text-bg-light border"><i class="bi bi-graph-up-arrow me-1"></i>Valor agregado</span>
                            </div>
                            <div class="display-6 fw-bold mt-3">{{ $result['upper_reference_fee'] }}</div>
                            <p class="text-body-secondary small mt-2 mb-0">Faixa para propostas com maior disponibilidade, consultoria e especialização.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-xl-7">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-body d-flex flex-column flex-sm-row justify-content-between gap-2">
                            <div>
                                <h3 class="h5 mb-1">Composição do custo-base</h3>
                                <p class="text-body-secondary small mb-0">Participação de cada componente antes dos fatores multiplicadores.</p>
                            </div>
                            <span class="badge text-bg-light border align-self-start">Regra {{ $result['rule_version'] }}</span>
                        </div>
                        <div class="card-body">
                            <div class="vstack gap-4">
                                @foreach ($result['breakdown'] as $item)
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                            <span>{{ $item['label'] }}</span>
                                            <div class="text-end">
                                                <strong class="d-block">{{ $item['value'] }}</strong>
                                                <small class="text-body-secondary">{{ $item['percentage'] }}%</small>
                                            </div>
                                        </div>
                                        <div class="progress" role="progressbar" aria-label="{{ $item['label'] }}" aria-valuenow="{{ $item['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="height: 8px;">
                                            <div class="progress-bar" style="width: {{ $item['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-5">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-body">
                            <h3 class="h5 mb-1">Leitura da complexidade</h3>
                            <p class="text-body-secondary small mb-0">Pontuação consolidada do cenário informado.</p>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-end justify-content-between gap-3 mb-2">
                                <div>
                                    <span class="text-body-secondary small">Índice geral</span>
                                    <div class="display-5 fw-bold">{{ $result['complexity_score'] }}<span class="fs-5 text-body-secondary">/100</span></div>
                                </div>
                                <span class="badge text-bg-{{ $complexityVariant }} fs-6">{{ $result['complexity_level'] }}</span>
                            </div>

                            <div class="progress mb-4" role="progressbar" aria-label="Índice de complexidade" aria-valuenow="{{ $result['complexity_score'] }}" aria-valuemin="0" aria-valuemax="100" style="height: 12px;">
                                <div class="progress-bar bg-{{ $complexityVariant }}" style="width: {{ $result['complexity_score'] }}%"></div>
                            </div>

                            <h4 class="h6">Fatores aplicados</h4>
                            <div class="list-group list-group-flush border rounded">
                                @foreach ($result['applied_factors'] as $factor)
                                    <div class="list-group-item d-flex justify-content-between gap-3">
                                        <span>{{ $factor['label'] }}</span>
                                        <strong>{{ $factor['percentage'] > 0 ? '+' : '' }}{{ number_format($factor['percentage'] / 100, 0, ',', '.') }}%</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-body">
                    <h3 class="h5 mb-1">Recomendações para a proposta</h3>
                    <p class="text-body-secondary small mb-0">Pontos de atenção gerados a partir do perfil informado.</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($result['recommendations'] as $recommendation)
                            <div class="col-12 col-md-6">
                                <div class="border rounded p-3 h-100 d-flex gap-3">
                                    <span class="fs-4 text-primary" aria-hidden="true"><i class="bi {{ $recommendation['icon'] }}"></i></span>
                                    <div>
                                        <h4 class="h6 mb-1">{{ $recommendation['title'] }}</h4>
                                        <p class="small text-body-secondary mb-0">{{ $recommendation['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="alert alert-warning d-flex gap-2 align-items-start mt-3 mb-0" role="note">
                <i class="bi bi-exclamation-triangle-fill mt-1" aria-hidden="true"></i>
                <div><strong>Referência gerencial:</strong> ajuste o preço conforme custos internos, escopo contratado, região, responsabilidade técnica, prazo de atendimento e posicionamento do escritório.</div>
            </div>


            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-body d-flex flex-column flex-lg-row justify-content-between gap-2">
                    <div>
                        <h3 class="h5 mb-1">Gerar proposta comercial</h3>
                        <p class="text-body-secondary small mb-0">Use o honorário recomendado e transforme o cálculo em uma apresentação pronta para o cliente.</p>
                    </div>
                    <span class="badge text-bg-success align-self-start align-self-lg-center"><i class="bi bi-file-earmark-text me-1"></i>Pronta para impressão</span>
                </div>
                <div class="card-body">
                    <form class="row g-3" method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.proposal') }}">
                        @csrf

                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="client_company">Empresa cliente <span class="text-danger">*</span></label>
                            <input class="form-control @error('client_company') is-invalid @enderror" id="client_company" name="client_company" value="{{ old('client_company') }}" maxlength="150" required>
                            @error('client_company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="client_document">CNPJ ou CPF</label>
                            <input class="form-control @error('client_document') is-invalid @enderror" id="client_document" name="client_document" value="{{ old('client_document') }}" maxlength="30">
                            @error('client_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-lg-3">
                            <label class="form-label" for="contact_name">Responsável <span class="text-danger">*</span></label>
                            <input class="form-control @error('contact_name') is-invalid @enderror" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" maxlength="120" required>
                            @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="accounting_firm">Nome do escritório <span class="text-danger">*</span></label>
                            <input class="form-control @error('accounting_firm') is-invalid @enderror" id="accounting_firm" name="accounting_firm" value="{{ old('accounting_firm') }}" maxlength="150" required>
                            @error('accounting_firm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="monthly_fee">Honorário mensal <span class="text-danger">*</span></label>
                            <input class="form-control @error('monthly_fee') is-invalid @enderror" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', str_replace(['R$ ', '.'], ['', ''], $result['recommended_fee'])) }}" inputmode="decimal" required>
                            @error('monthly_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="setup_fee">Taxa de implantação</label>
                            <input class="form-control @error('setup_fee') is-invalid @enderror" id="setup_fee" name="setup_fee" value="{{ old('setup_fee', '0,00') }}" inputmode="decimal">
                            @error('setup_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="due_day">Dia do vencimento <span class="text-danger">*</span></label>
                            <select class="form-select @error('due_day') is-invalid @enderror" id="due_day" name="due_day" required>
                                @foreach ([5, 10, 15, 20, 25, 28] as $day)
                                    <option value="{{ $day }}" @selected((int) old('due_day', 10) === $day)>Dia {{ $day }}</option>
                                @endforeach
                            </select>
                            @error('due_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="validity_days">Validade da proposta <span class="text-danger">*</span></label>
                            <select class="form-select @error('validity_days') is-invalid @enderror" id="validity_days" name="validity_days" required>
                                @foreach ([7, 15, 30, 45, 60] as $days)
                                    <option value="{{ $days }}" @selected((int) old('validity_days', 15) === $days)>{{ $days }} dias</option>
                                @endforeach
                            </select>
                            @error('validity_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <fieldset>
                                <legend class="form-label">Serviços incluídos <span class="text-danger">*</span></legend>
                                <div class="row g-2">
                                    @foreach (['accounting' => 'Escrituração contábil e demonstrações', 'tax' => 'Apuração fiscal e obrigações acessórias', 'payroll' => 'Folha de pagamento e rotinas trabalhistas', 'corporate' => 'Rotinas societárias recorrentes', 'advisory' => 'Consultoria e acompanhamento gerencial', 'financial' => 'BPO financeiro e conciliações'] as $value => $label)
                                        <div class="col-12 col-md-6 col-xl-4">
                                            <div class="form-check border rounded p-3 ps-5 h-100">
                                                <input class="form-check-input" id="service_{{ $value }}" name="services[]" type="checkbox" value="{{ $value }}" @checked(in_array($value, old('services', ['accounting', 'tax', 'payroll']), true))>
                                                <label class="form-check-label" for="service_{{ $value }}">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('services')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                                @error('services.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            </fieldset>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="notes">Observações comerciais</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="2000" placeholder="Ex.: atendimento remoto, reunião mensal e prazo para envio dos documentos.">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="bi bi-file-earmark-text me-1"></i>Gerar proposta comercial
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="mt-4" aria-labelledby="contract-generator-title">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex gap-3 align-items-start mb-4">
                        <span class="badge text-bg-primary rounded-pill p-3"><i class="bi bi-file-earmark-lock"></i></span>
                        <div>
                            <h3 id="contract-generator-title" class="h5 mb-1">Gerar contrato de prestação de serviços</h3>
                            <p class="text-body-secondary mb-0">Transforme os dados da negociação em um modelo contratual pronto para revisão e impressão.</p>
                        </div>
                    </div>

                    <form class="row g-3" method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.contract') }}">
                        @csrf
                        <div class="col-12"><h4 class="h6 border-bottom pb-2">Partes do contrato</h4></div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="contract_client_company">Empresa contratante <span class="text-danger">*</span></label>
                            <input class="form-control @error('client_company') is-invalid @enderror" id="contract_client_company" name="client_company" value="{{ old('client_company') }}" maxlength="150" required>
                            @error('client_company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="contract_client_document">CNPJ/CPF</label>
                            <input class="form-control @error('client_document') is-invalid @enderror" id="contract_client_document" name="client_document" value="{{ old('client_document') }}" maxlength="30">
                            @error('client_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="contract_client_representative">Representante <span class="text-danger">*</span></label>
                            <input class="form-control @error('client_representative') is-invalid @enderror" id="contract_client_representative" name="client_representative" value="{{ old('client_representative') }}" maxlength="120" required>
                            @error('client_representative')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label" for="contract_accounting_firm">Escritório contratado <span class="text-danger">*</span></label>
                            <input class="form-control @error('accounting_firm') is-invalid @enderror" id="contract_accounting_firm" name="accounting_firm" value="{{ old('accounting_firm') }}" maxlength="150" required>
                            @error('accounting_firm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="contract_accounting_firm_document">CNPJ/CPF</label>
                            <input class="form-control @error('accounting_firm_document') is-invalid @enderror" id="contract_accounting_firm_document" name="accounting_firm_document" value="{{ old('accounting_firm_document') }}" maxlength="30">
                            @error('accounting_firm_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label" for="contract_accounting_representative">Representante <span class="text-danger">*</span></label>
                            <input class="form-control @error('accounting_representative') is-invalid @enderror" id="contract_accounting_representative" name="accounting_representative" value="{{ old('accounting_representative') }}" maxlength="120" required>
                            @error('accounting_representative')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12"><h4 class="h6 border-bottom pb-2 mt-2">Condições comerciais</h4></div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label" for="contract_monthly_fee">Honorário mensal <span class="text-danger">*</span></label>
                            <input class="form-control @error('monthly_fee') is-invalid @enderror" id="contract_monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', str_replace(['R$ ', '.'], ['', ''], $result['recommended_fee'])) }}" inputmode="decimal" required>
                            @error('monthly_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label" for="contract_due_day">Vencimento <span class="text-danger">*</span></label>
                            <select class="form-select @error('due_day') is-invalid @enderror" id="contract_due_day" name="due_day" required>
                                @foreach ([5, 10, 15, 20, 25, 28] as $day)<option value="{{ $day }}" @selected((int) old('due_day', 10) === $day)>Dia {{ $day }}</option>@endforeach
                            </select>
                            @error('due_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label" for="contract_start_date">Início da vigência <span class="text-danger">*</span></label>
                            <input class="form-control @error('start_date') is-invalid @enderror" id="contract_start_date" name="start_date" type="date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <label class="form-label" for="contract_duration_months">Vigência <span class="text-danger">*</span></label>
                            <select class="form-select @error('duration_months') is-invalid @enderror" id="contract_duration_months" name="duration_months" required>
                                @foreach ([6, 12, 24, 36] as $months)<option value="{{ $months }}" @selected((int) old('duration_months', 12) === $months)>{{ $months }} meses</option>@endforeach
                            </select>
                            @error('duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="contract_adjustment_index">Índice de reajuste <span class="text-danger">*</span></label>
                            <select class="form-select @error('adjustment_index') is-invalid @enderror" id="contract_adjustment_index" name="adjustment_index" required>
                                @foreach (['IPCA', 'INPC', 'IGP-M', 'Percentual acordado'] as $index)<option value="{{ $index }}" @selected(old('adjustment_index', 'IPCA') === $index)>{{ $index }}</option>@endforeach
                            </select>
                            @error('adjustment_index')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="contract_late_fee_percent">Multa por atraso <span class="text-danger">*</span></label>
                            <div class="input-group"><input class="form-control @error('late_fee_percent') is-invalid @enderror" id="contract_late_fee_percent" name="late_fee_percent" type="number" min="0" max="10" value="{{ old('late_fee_percent', 2) }}" required><span class="input-group-text">%</span>@error('late_fee_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="contract_termination_notice_days">Aviso para rescisão <span class="text-danger">*</span></label>
                            <select class="form-select @error('termination_notice_days') is-invalid @enderror" id="contract_termination_notice_days" name="termination_notice_days" required>
                                @foreach ([15, 30, 45, 60, 90] as $days)<option value="{{ $days }}" @selected((int) old('termination_notice_days', 30) === $days)>{{ $days }} dias</option>@endforeach
                            </select>
                            @error('termination_notice_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <fieldset>
                                <legend class="form-label">Serviços contratados <span class="text-danger">*</span></legend>
                                <div class="row g-2">
                                    @foreach (['accounting' => 'Escrituração contábil e demonstrações', 'tax' => 'Apuração fiscal e obrigações acessórias', 'payroll' => 'Folha de pagamento e rotinas trabalhistas', 'corporate' => 'Rotinas societárias recorrentes', 'advisory' => 'Consultoria e acompanhamento gerencial', 'financial' => 'BPO financeiro e conciliações'] as $value => $label)
                                        <div class="col-12 col-md-6 col-xl-4"><div class="form-check border rounded p-3 ps-5 h-100"><input class="form-check-input" id="contract_service_{{ $value }}" name="services[]" type="checkbox" value="{{ $value }}" @checked(in_array($value, old('services', ['accounting', 'tax', 'payroll']), true))><label class="form-check-label" for="contract_service_{{ $value }}">{{ $label }}</label></div></div>
                                    @endforeach
                                </div>
                                @error('services')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            </fieldset>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch border rounded p-3 ps-5 h-100">
                                <input class="form-check-input" id="includes_lgpd" name="includes_lgpd" type="checkbox" value="1" @checked(old('includes_lgpd', true))>
                                <label class="form-check-label fw-semibold" for="includes_lgpd">Incluir cláusula de LGPD</label>
                                <div class="small text-body-secondary">Prevê finalidade, segurança e cooperação no tratamento de dados.</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-check form-switch border rounded p-3 ps-5 h-100">
                                <input class="form-check-input" id="includes_confidentiality" name="includes_confidentiality" type="checkbox" value="1" @checked(old('includes_confidentiality', true))>
                                <label class="form-check-label fw-semibold" for="includes_confidentiality">Incluir cláusula de confidencialidade</label>
                                <div class="small text-body-secondary">Protege informações comerciais, financeiras e cadastrais.</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="additional_terms">Condições adicionais</label>
                            <textarea class="form-control @error('additional_terms') is-invalid @enderror" id="additional_terms" name="additional_terms" rows="3" maxlength="3000" placeholder="Ex.: atendimento presencial mensal, canais oficiais e regras para serviços extraordinários.">{{ old('additional_terms') }}</textarea>
                            @error('additional_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-file-earmark-lock me-1"></i>Gerar contrato</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    @endif
</div>
@if ($taxSnapshotIntegration)
    <script>
        document.querySelector('[data-apply-tax-snapshot]')?.addEventListener('click', () => {
            document.getElementById('monthly_revenue').value = @json($taxSnapshotIntegration->data['monthly_revenue']);
            document.getElementById('tax_regime').value = 'simples_nacional';
            document.getElementById('monthly_revenue').focus();
        });
    </script>
@endif

@endsection
