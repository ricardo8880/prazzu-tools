@extends('layouts.app')

@section('title', 'Calculadora de Custo de Funcionário CLT — Prazzu Tools')
@section('meta_description', 'Calcule salário, FGTS, férias, 13º, encargos, benefícios, custo mensal, anual e por hora de um funcionário CLT.')
@section('canonical_url', route('tools.custo-funcionario-clt.index'))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug" badge="Grátis + Plus">
    <x-slot:actions>
        @auth
            <a class="btn btn-outline-secondary" href="{{ route('tools.custo-funcionario-clt.history.index') }}">
                <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Histórico
            </a>
        @endauth
    </x-slot:actions>

    @if(session('workspace_message'))
        <div class="alert alert-success" role="status">{{ session('workspace_message') }}</div>
    @endif
    @if($historySaved ?? false)
        <div class="alert alert-success" role="status">Cálculo salvo no seu histórico Plus.</div>
    @endif

    <div class="alert alert-warning d-flex gap-2" role="note">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <div><strong>Estimativa gerencial.</strong> As incidências variam por rubrica, convenção coletiva, atividade, FPAS, RAT/FAP e enquadramento. Revise o relatório com o responsável trabalhista.</div>
    </div>

    <x-tools.form-panel title="Simulação individual completa">
        <form method="POST" action="{{ route('tools.custo-funcionario-clt.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6">
                <x-tools.form.input name="employee_name" label="Funcionário (opcional)" :value="old('employee_name')" maxlength="160" />
            </div>
            <div class="col-12 col-md-6">
                <x-tools.form.input name="department" label="Departamento (opcional)" :value="old('department')" maxlength="120" />
            </div>
            @auth
                <div class="col-12 col-md-6">
                    <x-tools.form.select name="company_profile_id" label="Empresa salva (opcional)"
                        :options="$companies->pluck('name', 'id')->all()" :value="old('company_profile_id')" />
                </div>
            @endauth
            <div class="col-12 col-md-6">
                <x-tools.form.input name="scenario_name" label="Nome do cenário (opcional)" :value="old('scenario_name')" maxlength="120" />
            </div>
            @foreach(['salary' => ['Salário', null], 'variable_pay' => ['Média mensal variável', '0,00'], 'benefits' => ['Benefícios mensais', '0,00']] as $name => [$label, $default])
                <div class="col-12 col-md-4">
                    <x-tools.form.money :name="$name" :label="$label" :value="old($name)" required />
                </div>
            @endforeach
            <div class="col-12 col-md-4">
                <x-tools.form.select name="regime" label="Enquadramento patronal"
                    :options="['general' => 'Regime geral', 'simples_annex_iv' => 'Simples — Anexo IV', 'simples_other' => 'Simples — demais anexos']"
                    :value="old('regime', 'general')" required />
            </div>
            <div class="col-12 col-md-4">
                <x-tools.form.input name="rat" label="RAT ajustado" type="number" step="0.000001" suffix="%" :value="old('rat')" required />
            </div>
            <div class="col-12 col-md-4">
                <x-tools.form.input name="third_parties" label="Terceiros" type="number" step="0.000001" suffix="%" :value="old('third_parties')" required />
            </div>
            <div class="col-12 col-md-4">
                <x-tools.form.input name="monthly_hours" label="Jornada mensal" type="number" min="1" max="744" suffix="horas" :value="old('monthly_hours')" required />
            </div>
            <div class="col-12 d-flex flex-wrap gap-2">
                <button class="btn btn-primary" type="submit"><i class="bi bi-calculator me-1"></i>Calcular custo CLT</button>
                <button class="btn btn-outline-secondary" type="reset">Limpar</button>
            </div>
        </form>
    </x-tools.form-panel>

    @isset($result)
    <span data-analytics-result="main" hidden></span>
        @php($resultArray = $result->toArray())
        <x-tools.result-panel title="Custo do funcionário">
            <div class="row g-3">
                @foreach($result->summary as $item)
                    <div class="col-12 col-md-6 col-xl-4">
                        <x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" />
                    </div>
                @endforeach
            </div>
            <h3 class="h5 mt-4">Memória de cálculo</h3>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <tbody>
                    @foreach(($result->calculationMemory?->steps ?? []) as $step)
                        <tr><th scope="row">{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? 'R$ '.number_format($step->result / 100, 2, ',', '.') : $step->result }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach([
                    ['tools.custo-funcionario-clt.download.pdf', 'Baixar PDF', 'btn-primary', 'bi-file-earmark-pdf', 'download-pdf'],
                    ['tools.custo-funcionario-clt.export.csv', 'Exportar CSV · Plus', 'btn-outline-primary', 'bi-filetype-csv', 'download-csv'],
                    ['tools.custo-funcionario-clt.export.xlsx', 'Exportar XLSX · Plus', 'btn-outline-primary', 'bi-file-earmark-excel', 'download-xlsx'],
                ] as [$routeName, $label, $class, $icon, $testId])
                    <form method="post" action="{{ route($routeName) }}">
                        @csrf
                        @foreach($calculationInput as $name => $value)
                            @if(is_scalar($value) || $value === null)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif
                        @endforeach
                        <button class="btn {{ $class }}" type="submit" data-testid="{{ $testId }}"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</button>
                    </form>
                @endforeach
            </div>
        </x-tools.result-panel>
    @endisset

    <section class="mt-4" aria-labelledby="employee-cost-plus-title">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <span class="badge text-bg-primary mb-2">Prazzu Plus</span>
                <h2 id="employee-cost-plus-title" class="h3 mb-1">Produtividade, cenários e relatórios</h2>
                <p class="text-body-secondary mb-0">Os recursos permanecem visíveis e usam o mesmo motor do cálculo individual.</p>
            </div>
        </div>

        <div class="accordion" id="employee-cost-plus">
            <div class="accordion-item">
                <h3 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#employee-cost-batch">
                        Calcular vários funcionários · Plus
                    </button>
                </h3>
                <div id="employee-cost-batch" class="accordion-collapse collapse show" data-bs-parent="#employee-cost-plus">
                    <div class="accordion-body">
                        <form method="post" action="{{ route('tools.custo-funcionario-clt.batch.calculate') }}" class="row g-3">
                            @csrf
                            <div class="col-12 col-md-8"><label class="form-label">Nome do cenário</label><input class="form-control" name="scenario_name" value="{{ old('scenario_name') }}"></div>
                            @auth
                                <div class="col-12 col-md-4"><label class="form-label">Empresa</label><select class="form-select" name="company_profile_id"><option value="">Sem vínculo</option>@foreach($companies as $company)<option value="{{ $company->id }}">{{ $company->name }}</option>@endforeach</select></div>
                            @endauth
                            @for($index = 0; $index < 2; $index++)
                                <div class="col-12"><div class="border rounded-3 p-3"><div class="fw-semibold mb-3">Funcionário {{ $index + 1 }}</div><div class="row g-2">
                                    <div class="col-md-4"><label class="form-label">Nome</label><input class="form-control" name="employees[{{ $index }}][employee_name]" value="{{ old("employees.$index.employee_name") }}" required></div>
                                    <div class="col-md-4"><label class="form-label">Departamento</label><input class="form-control" name="employees[{{ $index }}][department]" value="{{ old("employees.$index.department") }}"></div>
                                    <div class="col-md-4"><label class="form-label">Cargo</label><input class="form-control" name="employees[{{ $index }}][role]" value="{{ old("employees.$index.role") }}"></div>
                                    <div class="col-md-4"><label class="form-label">Salário</label><input class="form-control" name="employees[{{ $index }}][salary]" value="{{ old("employees.$index.salary") }}" placeholder="5.000,00" required></div>
                                    <div class="col-md-4"><label class="form-label">Variável</label><input class="form-control" name="employees[{{ $index }}][variable_pay]" value="{{ old("employees.$index.variable_pay") }}" required></div>
                                    <div class="col-md-4"><label class="form-label">Benefícios</label><input class="form-control" name="employees[{{ $index }}][benefits]" value="{{ old("employees.$index.benefits") }}" required></div>
                                    <div class="col-md-3"><label class="form-label">Regime</label><select class="form-select" name="employees[{{ $index }}][regime]"><option value="general">Geral</option><option value="simples_annex_iv">Simples Anexo IV</option><option value="simples_other">Simples demais</option></select></div>
                                    <div class="col-md-3"><label class="form-label">RAT %</label><input class="form-control" type="number" step="0.000001" name="employees[{{ $index }}][rat]" placeholder="Ex.: 1,20" required></div>
                                    <div class="col-md-3"><label class="form-label">Terceiros %</label><input class="form-control" type="number" step="0.000001" name="employees[{{ $index }}][third_parties]" placeholder="Ex.: 5,80" required></div>
                                    <div class="col-md-3"><label class="form-label">Horas/mês</label><input class="form-control" type="number" name="employees[{{ $index }}][monthly_hours]" placeholder="Ex.: 200" required></div>
                                </div></div></div>
                            @endfor
                            <div class="col-12"><button class="btn btn-primary" type="submit">Calcular lote</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#employee-cost-import">Importar CSV/XLSX · Plus</button></h3>
                <div id="employee-cost-import" class="accordion-collapse collapse" data-bs-parent="#employee-cost-plus">
                    <div class="accordion-body">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('tools.custo-funcionario-clt.import.template.csv') }}">Baixar modelo CSV</a>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('tools.custo-funcionario-clt.import.template.xlsx') }}">Baixar modelo XLSX</a>
                        </div>
                        <form method="post" action="{{ route('tools.custo-funcionario-clt.import.preview') }}" enctype="multipart/form-data" class="row g-3">
                            @csrf
                            <div class="col-12 col-lg-9"><label class="form-label">Arquivo CSV ou XLSX</label><input class="form-control" type="file" name="import_file" accept=".csv,.xlsx" required><div class="form-text">Até 5 MB e 500 linhas. O conteúdo temporário é criptografado.</div></div>
                            <div class="col-12 col-lg-3 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Pré-visualizar</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#employee-cost-scenarios">Comparar cenários de custo · Plus</button></h3>
                <div id="employee-cost-scenarios" class="accordion-collapse collapse" data-bs-parent="#employee-cost-plus">
                    <div class="accordion-body">
                        <p class="text-body-secondary">Compare duas estruturas com as mesmas premissas de encargos. Cada cenário abaixo começa com um funcionário e pode representar uma alternativa de contratação.</p>
                        <form method="post" action="{{ route('tools.custo-funcionario-clt.scenarios.compare') }}" class="row g-3">
                            @csrf
                            @foreach(['Cenário atual', 'Cenário alternativo'] as $scenarioIndex => $scenarioLabel)
                                <div class="col-12 col-xl-6">
                                    <fieldset class="border rounded-3 p-3 h-100">
                                        <legend class="float-none w-auto px-2 h6">{{ $scenarioLabel }}</legend>
                                        <div class="row g-2">
                                            <div class="col-12"><label class="form-label">Nome do cenário</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][scenario_name]" value="{{ old("scenarios.$scenarioIndex.scenario_name", $scenarioLabel) }}" required></div>
                                            <div class="col-12"><label class="form-label">Funcionário</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][employees][0][employee_name]" value="{{ old("scenarios.$scenarioIndex.employees.0.employee_name") }}" required></div>
                                            <div class="col-md-6"><label class="form-label">Salário</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][employees][0][salary]" value="{{ old("scenarios.$scenarioIndex.employees.0.salary", $scenarioIndex ? '5500,00' : '5000,00') }}" required></div>
                                            <div class="col-md-6"><label class="form-label">Benefícios</label><input class="form-control" name="scenarios[{{ $scenarioIndex }}][employees][0][benefits]" value="{{ old("scenarios.$scenarioIndex.employees.0.benefits") }}" required></div>
                                            <input type="hidden" name="scenarios[{{ $scenarioIndex }}][employees][0][department]" value="">
                                            <input type="hidden" name="scenarios[{{ $scenarioIndex }}][employees][0][role]" value="">
                                            <input type="hidden" name="scenarios[{{ $scenarioIndex }}][employees][0][variable_pay]" value="0,00">
                                            <input type="hidden" name="scenarios[{{ $scenarioIndex }}][employees][0][regime]" value="general">
                                            <input type="hidden" name="scenarios[{{ $scenarioIndex }}][employees][0][rat]" value="1">
                                            <input type="hidden" name="scenarios[{{ $scenarioIndex }}][employees][0][third_parties]" value="5.8">
                                            <input type="hidden" name="scenarios[{{ $scenarioIndex }}][employees][0][monthly_hours]" value="220">
                                        </div>
                                    </fieldset>
                                </div>
                            @endforeach
                            <div class="col-12"><button class="btn btn-primary" type="submit">Comparar cenários</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#employee-cost-models">Comparar CLT × PJ × Autônomo · Plus</button></h3>
                <div id="employee-cost-models" class="accordion-collapse collapse" data-bs-parent="#employee-cost-plus">
                    <div class="accordion-body">
                        <form method="post" action="{{ route('tools.custo-funcionario-clt.models.compare') }}" class="row g-3">
                            @csrf
                            @foreach([
                                'salary' => ['Salário CLT', '5000,00'], 'variable_pay' => ['Variável CLT', '0,00'], 'benefits' => ['Benefícios CLT', '800,00'],
                                'clt_employee_discount_rate' => ['Descontos do trabalhador CLT %', '11'], 'pj_monthly_invoice' => ['Nota mensal PJ', '8000,00'],
                                'pj_tax_rate' => ['Tributos PJ %', '10'], 'pj_expenses' => ['Despesas PJ', '500,00'],
                                'autonomous_gross' => ['Bruto autônomo', '8000,00'], 'autonomous_discount_rate' => ['Descontos autônomo %', '20'],
                                'autonomous_employer_rate' => ['Encargo empresa autônomo %', '20'],
                            ] as $field => [$label, $value])
                                <div class="col-12 col-md-6 col-xl-4"><label class="form-label">{{ $label }}</label><input class="form-control" name="{{ $field }}" value="{{ old($field, $value) }}" required></div>
                            @endforeach
                            <input type="hidden" name="regime" value="general"><input type="hidden" name="rat" value="1"><input type="hidden" name="third_parties" value="5.8"><input type="hidden" name="monthly_hours" value="220">
                            <div class="col-12"><button class="btn btn-primary" type="submit">Comparar modalidades</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#employee-cost-profiles">Perfis reutilizáveis · Plus</button></h3>
                <div id="employee-cost-profiles" class="accordion-collapse collapse" data-bs-parent="#employee-cost-plus">
                    <div class="accordion-body">
                        @guest
                            <div class="alert alert-info mb-0">Perfis e histórico dependem de identidade. <a href="{{ route('login') }}">Entre na sua conta</a> para salvar.</div>
                        @else
                            <div class="row g-4">
                                <div class="col-12 col-xl-6">
                                    <h4 class="h6">Salvar empresa e identidade do relatório</h4>
                                    <form method="post" action="{{ route('tools.custo-funcionario-clt.profiles.companies.store') }}" class="row g-2">@csrf
                                        @foreach(['name' => 'Nome curto', 'legal_name' => 'Razão social', 'document' => 'CNPJ/CPF', 'office_name' => 'Nome do escritório', 'accountant_name' => 'Contador responsável', 'accountant_registration' => 'CRC'] as $field => $label)
                                            <div class="col-12 col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control" name="{{ $field }}" @if($field === 'name') required @endif></div>
                                        @endforeach
                                        <div class="col-12"><button class="btn btn-outline-primary" type="submit">Salvar empresa</button></div>
                                    </form>
                                </div>
                                <div class="col-12 col-xl-6">
                                    <h4 class="h6">Salvar funcionário</h4>
                                    <form method="post" action="{{ route('tools.custo-funcionario-clt.profiles.employees.store') }}" class="row g-2">@csrf
                                        <div class="col-12"><label class="form-label">Empresa</label><select class="form-select" name="company_profile_id"><option value="">Sem vínculo</option>@foreach($companies as $company)<option value="{{ $company->id }}">{{ $company->name }}</option>@endforeach</select></div>
                                        @foreach(['name' => ['Nome', 'text', ''], 'document' => ['Documento', 'text', ''], 'department' => ['Departamento', 'text', ''], 'role' => ['Cargo', 'text', ''], 'salary' => ['Salário', 'text', '5000,00'], 'variable_pay' => ['Variável', 'text', '0,00'], 'benefits' => ['Benefícios', 'text', '0,00'], 'rat' => ['RAT %', 'number', '1'], 'third_parties' => ['Terceiros %', 'number', '5.8'], 'monthly_hours' => ['Horas/mês', 'number', '220']] as $field => [$label, $type, $value])
                                            <div class="col-12 col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control" type="{{ $type }}" name="{{ $field }}" value="{{ $value }}" @if(in_array($field, ['name','salary','variable_pay','benefits','rat','third_parties','monthly_hours'])) required @endif></div>
                                        @endforeach
                                        <div class="col-12"><label class="form-label">Regime</label><select class="form-select" name="regime"><option value="general">Geral</option><option value="simples_annex_iv">Simples Anexo IV</option><option value="simples_other">Simples demais</option></select></div>
                                        <div class="col-12"><button class="btn btn-outline-primary" type="submit">Salvar funcionário</button></div>
                                    </form>
                                </div>
                            </div>
                            @if($companies->isNotEmpty() || $employees->isNotEmpty())
                                <div class="table-responsive mt-4"><table class="table table-sm align-middle"><thead><tr><th>Perfil</th><th>Tipo</th><th>Detalhes</th><th></th></tr></thead><tbody>
                                @foreach($companies as $company)<tr><td>{{ $company->name }}</td><td>Empresa</td><td>{{ $company->employees_count }} funcionário(s)</td><td class="text-end"><form method="post" action="{{ route('tools.custo-funcionario-clt.profiles.companies.destroy', $company->id) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Excluir</button></form></td></tr>@endforeach
                                @foreach($employees as $employee)<tr><td>{{ $employee->name }}</td><td>Funcionário</td><td>{{ $employee->department ?: 'Sem departamento' }}</td><td class="text-end"><form method="post" action="{{ route('tools.custo-funcionario-clt.profiles.employees.destroy', $employee->id) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Excluir</button></form></td></tr>@endforeach
                                </tbody></table></div>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    @isset($batchResult)
        <section class="card border-0 shadow-sm mt-4" aria-labelledby="batch-result-title">
            <div class="card-body p-4">
                <h2 id="batch-result-title" class="h4">{{ $batchResult['scenario_name'] }}</h2>
                <div class="row g-3 mb-3">
                    <div class="col-md-4"><x-tools.result-metric label="Funcionários" :value="$batchResult['employee_count']" /></div>
                    <div class="col-md-4"><x-tools.result-metric label="Custo mensal" :value="$batchResult['monthly_total']" /></div>
                    <div class="col-md-4"><x-tools.result-metric label="Custo anual" :value="$batchResult['annual_total']" /></div>
                </div>
                <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Funcionário</th><th>Departamento</th><th class="text-end">Mensal</th><th class="text-end">Anual</th></tr></thead><tbody>
                    @foreach($batchResult['employees'] as $row)<tr><td>{{ $row['name'] }}</td><td>{{ $row['department'] }}</td><td class="text-end">{{ $row['monthly_cost'] }}</td><td class="text-end">{{ $row['annual_cost'] }}</td></tr>@endforeach
                </tbody></table></div>
                <h3 class="h6 mt-4">Consolidado por departamento</h3>
                <div class="table-responsive"><table class="table table-sm"><tbody>@foreach($batchResult['departments'] as $row)<tr><th>{{ $row['department'] }}</th><td class="text-end">{{ $row['monthly_cost'] }} / mês</td><td class="text-end">{{ $row['annual_cost'] }} / ano</td></tr>@endforeach</tbody></table></div>
                <h3 class="h6 mt-4" data-plus-feature="projections">Projeção de 12 meses</h3><p class="small text-body-secondary">{{ $batchResult['projection_assumption'] }}</p>
                <div class="table-responsive"><table class="table table-sm"><thead><tr>@foreach($batchResult['projection'] as $month)<th>{{ $month['competence'] }}</th>@endforeach</tr></thead><tbody><tr>@foreach($batchResult['projection'] as $month)<td>{{ $month['cost'] }}</td>@endforeach</tr></tbody></table></div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach(['batch.export.csv' => 'CSV', 'batch.export.xlsx' => 'XLSX', 'batch.print' => 'Imprimir/PDF'] as $suffix => $label)
                        <form method="post" action="{{ route('tools.custo-funcionario-clt.'.$suffix) }}">@csrf
                            <input type="hidden" name="scenario_name" value="{{ $batchInput['scenario_name'] ?? '' }}">
                            @if(isset($batchInput['company_profile_id']))<input type="hidden" name="company_profile_id" value="{{ $batchInput['company_profile_id'] }}">@endif
                            @foreach($batchInput['employees'] as $i => $employee) @foreach($employee as $field => $value)<input type="hidden" name="employees[{{ $i }}][{{ $field }}]" value="{{ $value }}">@endforeach @endforeach
                            <button class="btn btn-outline-primary" type="submit">{{ $label }} · Plus</button>
                        </form>
                    @endforeach
                </div>
            </div>
        </section>
    @endisset

    @isset($scenarioResult)
        <section class="card border-0 shadow-sm mt-4" aria-labelledby="scenario-comparison-title">
            <div class="card-body p-4">
                <h2 id="scenario-comparison-title" class="h4">Comparação de cenários</h2>
                <p class="text-body-secondary">Menor custo anual: <strong>{{ $scenarioResult['lowest_scenario'] }}</strong> ({{ $scenarioResult['lowest_annual_cost'] }}).</p>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Cenário</th><th class="text-end">Funcionários</th><th class="text-end">Mensal</th><th class="text-end">Anual</th><th class="text-end">Diferença para o menor</th></tr></thead>
                        <tbody>
                        @foreach($scenarioResult['scenarios'] as $scenario)
                            <tr>
                                <th>{{ $scenario['scenario_name'] }}</th>
                                <td class="text-end">{{ $scenario['employee_count'] }}</td>
                                <td class="text-end">{{ $scenario['monthly_total'] }}</td>
                                <td class="text-end">{{ $scenario['annual_total'] }}</td>
                                <td class="text-end">{{ $scenario['difference_from_lowest'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endisset

    @isset($employmentComparison)
        <section class="card border-0 shadow-sm mt-4"><div class="card-body p-4"><h2 class="h4">Comparação numérica das modalidades</h2>
            <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Modalidade</th><th>Bruto</th><th>Descontos/despesas</th><th>Líquido</th><th>Custo empresa</th></tr></thead><tbody>
                @foreach($employmentComparison['models'] as $model)<tr><th>{{ $model['model'] }}</th><td>{{ $model['gross'] }}</td><td>{{ $model['discounts'] }}</td><td>{{ $model['net'] }}</td><td>{{ $model['company_cost'] }}</td></tr>@endforeach
            </tbody></table></div><div class="alert alert-warning mb-0">{{ $employmentComparison['disclaimer'] }}</div>
        </div></section>
    @endisset

    @isset($importPreview)
        <section class="card border-0 shadow-sm mt-4"><div class="card-body p-4"><h2 class="h4">Pré-visualização da importação</h2><p>{{ $importPreview['file_name'] }} · {{ $importPreview['format'] }} · {{ $importPreview['total_rows'] }} linha(s)</p>
            <div class="table-responsive"><table class="table table-sm"><thead><tr>@foreach($importPreview['headers'] as $header)<th>{{ $header }}</th>@endforeach</tr></thead><tbody>@foreach($importPreview['preview_rows'] as $row)<tr>@foreach($importPreview['headers'] as $header)<td>{{ $row[$header] ?? '' }}</td>@endforeach</tr>@endforeach</tbody></table></div>
            <form method="post" action="{{ route('tools.custo-funcionario-clt.import.process') }}" class="row g-3">@csrf<input type="hidden" name="import_token" value="{{ $importPreview['token'] }}">
                @foreach(['name_column' => 'Nome', 'department_column' => 'Departamento', 'role_column' => 'Cargo', 'salary_column' => 'Salário', 'variable_pay_column' => 'Variável', 'benefits_column' => 'Benefícios', 'regime_column' => 'Regime', 'rat_column' => 'RAT', 'third_parties_column' => 'Terceiros', 'monthly_hours_column' => 'Horas mensais'] as $field => $label)
                    <div class="col-12 col-md-6 col-xl-4"><label class="form-label">{{ $label }}</label><select class="form-select" name="{{ $field }}" @if(in_array($field, ['name_column','salary_column'])) required @endif><option value="">Não importar</option>@foreach($importPreview['headers'] as $header)<option value="{{ $header }}" @selected(($importPreview['suggested_mapping'][$field] ?? null) === $header)>{{ $header }}</option>@endforeach</select></div>
                @endforeach
                <div class="col-12"><button class="btn btn-primary" type="submit">Confirmar mapeamento</button></div>
            </form>
        </div></section>
    @endisset

    @isset($importResult)
        <section class="card border-0 shadow-sm mt-4"><div class="card-body p-4"><h2 class="h4">Importação validada</h2><p>{{ $importResult['imported'] }} linha(s) pronta(s) para cálculo.</p>
            @if($importResult['rejected'])<div class="alert alert-warning"><strong>Linhas rejeitadas</strong><ul class="mb-0">@foreach($importResult['rejected'] as $rejected)<li>Linha {{ $rejected['line'] }}: {{ implode('; ', $rejected['errors']) }}</li>@endforeach</ul></div>@endif
            <form method="post" action="{{ route('tools.custo-funcionario-clt.batch.calculate') }}">@csrf<input type="hidden" name="scenario_name" value="Importação {{ now()->format('d/m/Y H:i') }}">@foreach($importResult['employees'] as $i => $employee)@foreach($employee as $field => $value)<input type="hidden" name="employees[{{ $i }}][{{ $field }}]" value="{{ $value }}">@endforeach @endforeach<button class="btn btn-primary" type="submit">Calcular funcionários importados</button></form>
        </div></section>
    @endisset

    <section class="mt-5" aria-labelledby="employee-cost-faq-title">
        <h2 id="employee-cost-faq-title" class="h3">Perguntas frequentes</h2>
        <div class="accordion" id="employee-cost-faq">
            @foreach([
                ['O resultado substitui a folha?', 'Não. É uma estimativa gerencial baseada nas premissas informadas e não substitui eSocial, folha ou análise da convenção coletiva.'],
                ['O PDF é gratuito?', 'Sim. O relatório individual pode ser impresso ou salvo como PDF sem depender do Plus.'],
                ['Por que os encargos mudam por regime?', 'CPP, RAT e terceiros dependem do enquadramento e da atividade. A memória mostra as alíquotas efetivamente usadas.'],
            ] as $i => [$question, $answer])
                <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#employee-cost-faq-{{ $i }}">{{ $question }}</button></h3><div id="employee-cost-faq-{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#employee-cost-faq"><div class="accordion-body">{{ $answer }}</div></div></div>
            @endforeach
        </div>
    </section>
</x-tools.page>
@endsection
