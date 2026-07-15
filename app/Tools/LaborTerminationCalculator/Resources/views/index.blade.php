@extends('layouts.app')

@section('title', 'Calculadora de Rescisão Trabalhista — Prazzu Tools')
@section('meta_description', 'Informe os dados do contrato para estimar saldo de salário, férias, 13º, aviso-prévio, FGTS e multa rescisória.')

@section('content')
<div class="prazzu-page tool-page" data-tool="calculadora-de-rescisao">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calculadora de Rescisão Trabalhista</li>
        </ol>
    </nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--pink"><i class="bi bi-briefcase"></i></span>
        <div class="flex-grow-1">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="prazzu-badge prazzu-badge--green">Grátis</span>
                <span class="badge text-bg-success">Ativa</span>
            </div>
            <h1>Calculadora de Rescisão Trabalhista</h1>
            <p>Informe os dados do vínculo para estimar as principais verbas de uma rescisão de contrato de trabalho.</p>
        </div>
        @auth
            <a class="btn btn-outline-primary align-self-start" href="{{ route('tools.calculadora-de-rescisao.history.index') }}">
                <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Histórico
            </a>
        @endauth
    </header>

    @if (session('history_message'))
        <div class="alert alert-success d-flex gap-2 align-items-start" role="status">
            <i class="bi bi-check-circle-fill mt-1" aria-hidden="true"></i>
            <div>{{ session('history_message') }}</div>
        </div>
    @endif

    @if (session('history_saved'))
        <div class="alert alert-success d-flex gap-2 align-items-start" role="status">
            <i class="bi bi-cloud-check-fill mt-1" aria-hidden="true"></i>
            <div>Este cálculo foi salvo automaticamente no seu histórico.</div>
        </div>
    @endif

    <div class="alert alert-info d-flex gap-2 align-items-start" role="alert">
        <i class="bi bi-info-circle-fill mt-1" aria-hidden="true"></i>
        <div>
            <strong>Casos especiais disponíveis.</strong>
            O cálculo considera remuneração variável, múltiplos períodos de férias, férias em dobro, contratos por prazo determinado e o regime de empregado doméstico. As tabelas tributárias utilizadas são as de 2026.
        </div>
    </div>


    @auth
        <section class="prazzu-form-panel mb-4" aria-labelledby="recent-history-title">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                <div>
                    <h2 id="recent-history-title" class="prazzu-section-title mb-1">Histórico recente</h2>
                    <p class="text-body-secondary mb-0">Seus cálculos autenticados são armazenados de forma criptografada por até 180 dias.</p>
                </div>
                <a class="btn btn-outline-primary" href="{{ route('tools.calculadora-de-rescisao.history.index') }}">
                    <i class="bi bi-clock-history me-1" aria-hidden="true"></i> Ver histórico completo
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Data</th><th>Tipo</th><th>Valor líquido</th><th class="text-end">Ações</th></tr></thead>
                    <tbody>
                    @forelse ($recentHistory as $run)
                        <tr>
                            <td>{{ $run->finished_at?->format('d/m/Y H:i') }}</td>
                            <td>{{ $run->result_payload['termination_type_label'] ?? 'Rescisão' }}</td>
                            <td class="fw-semibold">{{ $run->result_payload['net_total'] ?? '—' }}</td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-de-rescisao.history.show', $run) }}">Detalhes</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-body-secondary py-4">Nenhum cálculo salvo ainda.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <div class="alert alert-light border d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3" role="note">
            <span><i class="bi bi-clock-history me-1" aria-hidden="true"></i> Entre na sua conta para salvar e consultar o histórico dos cálculos.</span>
            @if (Route::has('login'))<a class="btn btn-sm btn-outline-primary" href="{{ route('login') }}">Entrar</a>@endif
        </div>
    @endauth

    <section class="prazzu-form-panel" aria-labelledby="termination-form-title">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <h2 id="termination-form-title" class="prazzu-section-title mb-1">Dados da rescisão</h2>
                <p class="text-body-secondary mb-0">Preencha as informações básicas do contrato e do desligamento.</p>
            </div>
            <span class="badge text-bg-light border text-body-secondary align-self-start">Campos com * são obrigatórios</span>
        </div>

        <form method="post" action="{{ route('tools.calculadora-de-rescisao.calculate') }}" class="row g-3" novalidate>
            @csrf

            <div class="col-12 col-lg-4">
                <label class="form-label" for="monthly_salary">Salário mensal *</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input
                        class="form-control @error('monthly_salary') is-invalid @enderror"
                        id="monthly_salary"
                        name="monthly_salary"
                        value="{{ old('monthly_salary') }}"
                        placeholder="3.000,00"
                        inputmode="decimal"
                        required
                    >
                    @error('monthly_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="admission_date">Data de admissão *</label>
                <input class="form-control @error('admission_date') is-invalid @enderror" id="admission_date" type="date" name="admission_date" value="{{ old('admission_date') }}" required>
                @error('admission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="termination_date">Data de desligamento *</label>
                <input class="form-control @error('termination_date') is-invalid @enderror" id="termination_date" type="date" name="termination_date" value="{{ old('termination_date') }}" required>
                @error('termination_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-lg-6">
                <label class="form-label" for="termination_type">Motivo da rescisão *</label>
                <select class="form-select @error('termination_type') is-invalid @enderror" id="termination_type" name="termination_type" required>
                    <option value="">Selecione</option>
                    @foreach ($terminationTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('termination_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('termination_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-lg-6">
                <label class="form-label" for="contract_type">Tipo de contrato *</label>
                <select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type" name="contract_type" required>
                    <option value="">Selecione</option>
                    @foreach ($contractTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('contract_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('contract_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-lg-8">
                <label class="form-label" for="notice_type">Aviso-prévio *</label>
                <select class="form-select @error('notice_type') is-invalid @enderror" id="notice_type" name="notice_type" required>
                    <option value="">Selecione</option>
                    @foreach ($noticeTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('notice_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('notice_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="days_worked_in_month">Dias trabalhados no mês *</label>
                <input class="form-control @error('days_worked_in_month') is-invalid @enderror" id="days_worked_in_month" type="number" min="0" max="31" name="days_worked_in_month" value="{{ old('days_worked_in_month') }}" placeholder="15" required>
                @error('days_worked_in_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12"><hr class="my-2"><h3 class="h6 mb-0">Casos especiais e remuneração variável</h3></div>

            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label" for="overdue_vacation_periods">Períodos de férias vencidas</label>
                <input class="form-control @error('overdue_vacation_periods') is-invalid @enderror" id="overdue_vacation_periods" type="number" min="0" max="3" name="overdue_vacation_periods" value="{{ old('overdue_vacation_periods', 0) }}">
                @error('overdue_vacation_periods')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label" for="double_vacation_periods">Desses, períodos em dobro</label>
                <input class="form-control @error('double_vacation_periods') is-invalid @enderror" id="double_vacation_periods" type="number" min="0" max="3" name="double_vacation_periods" value="{{ old('double_vacation_periods', 0) }}">
                @error('double_vacation_periods')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @foreach ([
                'commission_average' => 'Média mensal de comissões',
                'overtime_average' => 'Média mensal de horas extras',
                'recurring_additions' => 'Adicionais mensais recorrentes',
            ] as $field => $label)
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, '0,00') }}" inputmode="decimal">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
            @endforeach
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="contract_end_date">Fim previsto do contrato</label>
                <input class="form-control @error('contract_end_date') is-invalid @enderror" id="contract_end_date" type="date" name="contract_end_date" value="{{ old('contract_end_date') }}">
                @error('contract_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Obrigatório no término antecipado de contrato determinado ou de experiência.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="early_termination_initiative">Quem antecipou o término</label>
                <select class="form-select @error('early_termination_initiative') is-invalid @enderror" id="early_termination_initiative" name="early_termination_initiative">
                    <option value="">Não se aplica</option><option value="employer" @selected(old('early_termination_initiative') === 'employer')>Empregador</option><option value="employee" @selected(old('early_termination_initiative') === 'employee')>Empregado</option>
                </select>
                @error('early_termination_initiative')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="article_480_discount">Desconto informado do art. 480</label>
                <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('article_480_discount') is-invalid @enderror" id="article_480_discount" name="article_480_discount" value="{{ old('article_480_discount', '0,00') }}" inputmode="decimal">@error('article_480_discount')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="extraordinary_indemnities">Indenizações adicionais</label>
                <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('extraordinary_indemnities') is-invalid @enderror" id="extraordinary_indemnities" name="extraordinary_indemnities" value="{{ old('extraordinary_indemnities', '0,00') }}" inputmode="decimal">@error('extraordinary_indemnities')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="form-text">Estabilidade, norma coletiva ou ajuste extraordinário informado manualmente.</div>
            </div>

            <div class="col-12"><hr class="my-2"><h3 class="h6 mb-0">FGTS e descontos</h3></div>

            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="fgts_balance">Saldo atual do FGTS</label>
                <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('fgts_balance') is-invalid @enderror" id="fgts_balance" name="fgts_balance" value="{{ old('fgts_balance', '0,00') }}" inputmode="decimal" placeholder="12.000,00">@error('fgts_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="form-text">Use o saldo da conta vinculada deste contrato para estimar a multa.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="domestic_indemnity_reserve_balance">Reserva indenizatória doméstica</label>
                <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('domestic_indemnity_reserve_balance') is-invalid @enderror" id="domestic_indemnity_reserve_balance" name="domestic_indemnity_reserve_balance" value="{{ old('domestic_indemnity_reserve_balance', '0,00') }}" inputmode="decimal" placeholder="4.800,00">@error('domestic_indemnity_reserve_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="form-text">Somente para empregado doméstico: saldo dos depósitos mensais de 3,2%.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="other_discounts">Outros descontos</label>
                <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('other_discounts') is-invalid @enderror" id="other_discounts" name="other_discounts" value="{{ old('other_discounts', '0,00') }}" inputmode="decimal" placeholder="0,00">@error('other_discounts')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="form-text">Adiantamentos, faltas ou descontos autorizados.</div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label" for="dependents">Dependentes para IRRF</label>
                <input class="form-control @error('dependents') is-invalid @enderror" id="dependents" type="number" min="0" max="99" name="dependents" value="{{ old('dependents', 0) }}">
                @error('dependents')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0" role="alert">
                        <strong>Revise os dados informados.</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="col-12 d-flex flex-column flex-sm-row gap-2 pt-2">
                <button class="btn btn-primary prazzu-btn-primary" type="submit">
                    <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>
                    Calcular estimativa
                </button>
                <button class="btn btn-outline-secondary" type="reset">
                    <i class="bi bi-eraser me-1" aria-hidden="true"></i>
                    Limpar formulário
                </button>
            </div>
        </form>
    </section>

    @if (session('calculation_result'))
        @php($result = session('calculation_result'))
        <section class="prazzu-form-panel mt-4" aria-labelledby="termination-result-title">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <span class="badge text-bg-success mb-2">Estimativa calculada</span>
                    <h2 id="termination-result-title" class="prazzu-section-title mb-1">Resultado da rescisão</h2>
                    <p class="text-body-secondary mb-0">{{ $result['termination_type_label'] }} — aviso: {{ $result['notice_type_label'] }}. Valores de FGTS são exibidos separadamente do pagamento líquido.</p>
                </div>
                <div class="text-lg-end">
                    <small class="text-body-secondary d-block">Valor líquido estimado</small>
                    <strong class="fs-3 text-success">{{ $result['net_total'] }}</strong>
                    @if (session('calculation_input'))
                        <form method="post" action="{{ route('tools.calculadora-de-rescisao.export') }}" class="mt-2" target="_blank">
                            @csrf
                            @foreach (session('calculation_input') as $field => $value)
                                @if (! is_array($value) && $value !== null)
                                    <input type="hidden" name="{{ $field }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <button class="btn btn-outline-primary btn-sm" type="submit">
                                <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i> Exportar PDF
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block">Remuneração-base mensal</small>
                        <strong class="fs-5">{{ $result['salary_base'] }}</strong>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block">Avos de férias</small>
                        <strong class="fs-5">{{ $result['proportional_vacation_months'] }}/12</strong>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block">Avos de 13º</small>
                        <strong class="fs-5">{{ $result['proportional_thirteenth_months'] }}/12</strong>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block">Aviso-prévio considerado</small>
                        <strong class="fs-5">{{ $result['notice_days'] }} dia(s)</strong>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-body-secondary d-block">Data projetada do contrato</small>
                        <strong class="fs-5">{{ $result['projected_termination_date'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Verba</th>
                            <th scope="col">Base considerada</th>
                            <th scope="col" class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Saldo de salário</td>
                            <td>{{ $result['days_worked_in_month'] }} dia(s)</td>
                            <td class="text-end fw-semibold">{{ $result['salary_balance'] }}</td>
                        </tr>
                        <tr>
                            <td>Férias vencidas</td>
                            <td>{{ $result['overdue_vacation_periods'] }} período(s), sendo {{ $result['double_vacation_periods'] }} em dobro</td>
                            <td class="text-end fw-semibold">{{ $result['overdue_vacation'] }}</td>
                        </tr>
                        <tr>
                            <td>1/3 sobre férias vencidas</td>
                            <td>Adicional constitucional</td>
                            <td class="text-end fw-semibold">{{ $result['overdue_vacation_third'] }}</td>
                        </tr>
                        <tr>
                            <td>Férias proporcionais</td>
                            <td>{{ $result['proportional_vacation_months'] }}/12 do salário</td>
                            <td class="text-end fw-semibold">{{ $result['proportional_vacation'] }}</td>
                        </tr>
                        <tr>
                            <td>1/3 sobre férias proporcionais</td>
                            <td>Adicional constitucional</td>
                            <td class="text-end fw-semibold">{{ $result['proportional_vacation_third'] }}</td>
                        </tr>
                        <tr>
                            <td>13º salário proporcional</td>
                            <td>{{ $result['proportional_thirteenth_months'] }}/12 do salário</td>
                            <td class="text-end fw-semibold">{{ $result['proportional_thirteenth_salary'] }}</td>
                        </tr>
                        <tr>
                            <td>Aviso-prévio indenizado</td>
                            <td>{{ $result['notice_days'] }} dia(s); metade quando houver acordo</td>
                            <td class="text-end fw-semibold">{{ $result['notice_pay'] }}</td>
                        </tr>
                        <tr>
                            <td>Desconto por aviso não cumprido</td>
                            <td>Até 30 dias no pedido de demissão</td>
                            <td class="text-end fw-semibold text-danger">- {{ $result['notice_discount'] }}</td>
                        </tr>
                        <tr><td>Indenização do art. 479</td><td>{{ $result['remaining_contract_days'] }} dia(s) restantes, pela metade</td><td class="text-end fw-semibold">{{ $result['article_479_indemnity'] }}</td></tr>
                        <tr><td>Indenizações adicionais</td><td>Valor informado manualmente</td><td class="text-end fw-semibold">{{ $result['extraordinary_indemnities'] }}</td></tr>
                        <tr><td>Desconto informado do art. 480</td><td>Limitado ao equivalente do art. 479</td><td class="text-end fw-semibold text-danger">- {{ $result['article_480_discount'] }}</td></tr>

                        <tr><td>INSS sobre saldo de salário</td><td>Tabela progressiva 2026</td><td class="text-end fw-semibold text-danger">- {{ $result['inss_salary'] }}</td></tr>
                        <tr><td>INSS sobre 13º</td><td>Cálculo separado</td><td class="text-end fw-semibold text-danger">- {{ $result['inss_thirteenth'] }}</td></tr>
                        <tr><td>IRRF sobre saldo de salário</td><td>{{ $result['dependents'] }} dependente(s)</td><td class="text-end fw-semibold text-danger">- {{ $result['irrf_salary'] }}</td></tr>
                        <tr><td>IRRF sobre 13º</td><td>Tributação exclusiva</td><td class="text-end fw-semibold text-danger">- {{ $result['irrf_thirteenth'] }}</td></tr>
                        <tr><td>Outros descontos informados</td><td>Valor declarado</td><td class="text-end fw-semibold text-danger">- {{ $result['other_discounts'] }}</td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" scope="row">Valor líquido estimado</th>
                            <th class="text-end fs-5">{{ $result['net_total'] }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>


            <div class="row g-3 mt-1">
                <div class="col-12 col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-body-secondary d-block">FGTS rescisório estimado</small><strong class="fs-5">{{ $result['fgts_termination_deposit'] }}</strong></div></div>
                <div class="col-12 col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-body-secondary d-block">{{ $result['is_domestic'] ? 'Indenização compensatória doméstica' : 'Multa rescisória' }}</small><strong class="fs-5">{{ $result['fgts_penalty'] }}</strong></div></div>
                <div class="col-12 col-md-4"><div class="border rounded-3 p-3 h-100"><small class="text-body-secondary d-block">Saldo estimado liberado ({{ $result['fgts_withdrawal_percentage'] }}%)</small><strong class="fs-5">{{ $result['estimated_fgts_available'] }}</strong></div></div>
            </div>
            @if ($result['is_domestic'])
                <div class="alert alert-info mt-3 mb-0" role="note">
                    <strong>Regime doméstico:</strong> depósito rescisório normal de 8%: {{ $result['fgts_termination_deposit'] }}; depósito compensatório de 3,2% sobre as verbas rescisórias: {{ $result['domestic_compensatory_deposit'] }}. A reserva informada foi {{ $result['domestic_indemnity_reserve_balance'] }}.
                </div>
            @endif
            @if (! empty($result['warnings']))
                <div class="alert alert-warning mt-4" role="alert">
                    <strong>Atenção aos casos especiais:</strong>
                    <ul class="mb-0 mt-2">@foreach ($result['warnings'] as $warning)<li>{{ $warning }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="alert alert-secondary mt-4 mb-0" role="note">
                <i class="bi bi-calculator me-1" aria-hidden="true"></i>
                Regra {{ $result['rule_version'] }} e tabela tributária {{ $result['tax_table_version'] }}. O aviso indenizado projeta o término do contrato para a contagem de férias e 13º; na justa causa, essas verbas proporcionais não são incluídas.
            </div>
        </section>
    @endif

    <section class="mt-4" aria-labelledby="included-items-title">
        <h2 id="included-items-title" class="prazzu-section-title">O que a ferramenta calculará</h2>
        <div class="row g-3">
            @foreach ([
                ['bi-calendar-check', 'Saldo de salário', 'Dias trabalhados no mês do desligamento.'],
                ['bi-sun', 'Férias', 'Férias vencidas, proporcionais e adicional de 1/3.'],
                ['bi-gift', '13º salário', 'Décimo terceiro proporcional ao período trabalhado.'],
                ['bi-megaphone', 'Aviso-prévio', 'Aviso trabalhado, indenizado ou descontado.'],
                ['bi-bank', 'FGTS e indenização', 'Estimativa de depósitos, saque, multa comum ou reserva doméstica de 3,2%.'],
                ['bi-clock-history', 'Histórico', 'Salvamento criptografado, consulta, exclusão e repetição dos cálculos autenticados.'],
                ['bi-file-earmark-pdf', 'Relatório em PDF', 'Relatório otimizado para impressão e salvamento em PDF pelo navegador.'],
            ] as [$icon, $title, $description])
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="prazzu-related-tool h-100">
                        <i class="bi {{ $icon }} fs-4" aria-hidden="true"></i>
                        <span>
                            <strong>{{ $title }}</strong>
                            <small>{{ $description }}</small>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="alert alert-warning mt-4 mb-0" role="note">
        <i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>
        Os resultados serão estimativos e não substituirão a conferência do termo de rescisão por profissional qualificado.
    </div>
</div>
@endsection
