@extends('layouts.app')

@section('title', 'Calculadora de Depreciação de Ativos — Prazzu Tools')

@section('content')
<x-tools.page title="Calculadora de Depreciação de Ativos" description="Informe o bem, o valor e a vida útil para calcular a depreciação e acompanhar a evolução do valor contábil." icon="bi-building-down" slug="calculadora-depreciacao-ativos">
    <form method="POST" action="{{ route('tools.calculadora-depreciacao-ativos.calculate') }}" class="vstack gap-4">
        @csrf

        <x-tools.form-panel title="Bem e vida útil" description="O método linear resolve o caso essencial com depreciação uniforme ao longo da vida útil.">
            <div class="row g-3">
                <div class="col-12 col-lg-5">
                    <label class="form-label" for="asset_name">Bem</label>
                    <input class="form-control @error('asset_name') is-invalid @enderror" id="asset_name" name="asset_name" value="{{ old('asset_name') }}" maxlength="120" placeholder="Ex.: Notebook da empresa" required>
                    @error('asset_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="asset_value">Valor do bem</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('asset_value') is-invalid @enderror" id="asset_value" name="asset_value" value="{{ old('asset_value') }}" inputmode="decimal" placeholder="0,00" required></div>
                    @error('asset_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label" for="residual_value">Valor residual</label>
                    <div class="input-group"><span class="input-group-text">R$</span><input class="form-control @error('residual_value') is-invalid @enderror" id="residual_value" name="residual_value" value="{{ old('residual_value', '0,00') }}" inputmode="decimal" placeholder="0,00"></div>
                    @error('residual_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="form-text">Estimativa do valor do bem ao fim da vida útil. Use zero quando não houver valor residual.</div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label" for="useful_life_years">Vida útil</label>
                    <div class="input-group"><input class="form-control @error('useful_life_years') is-invalid @enderror" id="useful_life_years" name="useful_life_years" type="number" min="1" max="100" value="{{ old('useful_life_years') }}" required><span class="input-group-text">anos</span></div>
                    @error('useful_life_years')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-panel title="Métodos e vários ativos" description="Escolha outro método para o bem principal e inclua ativos adicionais no mesmo cálculo." badge="Prazzu Plus">
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6">
                    <label class="form-label" for="method_plus">Método do bem principal</label>
                    <select class="form-select" id="method_plus" name="method">
                        @foreach($methods as $value => $label)
                            <option value="{{ $value }}" @selected(old('method', 'linear') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Linear é o método padrão do Essencial; os métodos alternativos são recursos Plus.</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Bem adicional</th><th>Valor</th><th>Residual</th><th>Vida útil</th><th>Método</th></tr></thead>
                    <tbody>
                        @for($i = 0; $i < 5; $i++)
                            <tr>
                                <td><input class="form-control" name="assets[{{ $i }}][name]" value="{{ old("assets.$i.name") }}" placeholder="Ex.: Veículo {{ $i + 1 }}"></td>
                                <td><input class="form-control" name="assets[{{ $i }}][value]" value="{{ old("assets.$i.value") }}" inputmode="decimal"></td>
                                <td><input class="form-control" name="assets[{{ $i }}][residual_value]" value="{{ old("assets.$i.residual_value", "0,00") }}" inputmode="decimal" aria-label="Valor residual do ativo adicional"></td>
                                <td><div class="input-group"><input class="form-control" name="assets[{{ $i }}][useful_life_years]" type="number" min="1" max="100" value="{{ old("assets.$i.useful_life_years") }}"><span class="input-group-text">anos</span></div></td>
                                <td><select class="form-select" name="assets[{{ $i }}][method]">@foreach($methods as $value => $label)<option value="{{ $value }}" @selected(old("assets.$i.method", 'linear') === $value)>{{ $label }}</option>@endforeach</select></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            @auth
                @if(($registeredAssets ?? collect())->isNotEmpty())
                    <hr class="my-4">
                    <h3 class="h6">Ativos cadastrados</h3>
                    <p class="small text-body-secondary">Selecione os ativos salvos que também devem entrar nesta simulação.</p>
                    <div class="row g-2">
                        @foreach($registeredAssets as $registeredAsset)
                            <div class="col-12 col-md-6">
                                <label class="border rounded p-3 d-flex gap-3 align-items-start w-100">
                                    <input class="form-check-input mt-1" type="checkbox" name="registered_asset_ids[]" value="{{ $registeredAsset->id }}" @checked(in_array($registeredAsset->id, old('registered_asset_ids', []), true))>
                                    <span>
                                        <strong>{{ $registeredAsset->name }}</strong><br>
                                        <small class="text-body-secondary">{{ \App\Core\Money\Money::fromMinor($registeredAsset->value_minor)->formatPtBr() }} · {{ $registeredAsset->useful_life_years }} anos · {{ $methods[$registeredAsset->method] ?? $registeredAsset->method }}</small>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="form-text">Faça login para salvar ativos e reutilizá-los em novas simulações Plus.</div>
            @endauth
        </x-tools.form-panel>
        @endif

        <div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calcular depreciação</button></div>
    </form>

    @auth
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div><h2 class="h5 mb-1">Cadastro de ativos <span class="badge text-bg-primary">Plus</span></h2><p class="text-body-secondary mb-0">Salve bens para reutilizar em futuras projeções patrimoniais.</p></div>
                </div>
                <form method="POST" action="{{ route('tools.calculadora-depreciacao-ativos.registry.store') }}" class="row g-3 align-items-end mb-4">
                    @csrf
                    <div class="col-12 col-lg-4"><label class="form-label" for="registry_name">Bem</label><input class="form-control" id="registry_name" name="name" maxlength="120" required></div>
                    <div class="col-12 col-md-4 col-lg-3"><label class="form-label" for="registry_value">Valor</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="registry_value" name="value" inputmode="decimal" placeholder="0,00" required></div></div>
                    <div class="col-12 col-md-4 col-lg-2"><label class="form-label" for="registry_life">Vida útil</label><div class="input-group"><input class="form-control" id="registry_life" name="useful_life_years" type="number" min="1" max="100" placeholder="Ex.: 6" required><span class="input-group-text">anos</span></div></div>
                    <div class="col-12 col-md-4 col-lg-2"><label class="form-label" for="registry_method">Método</label><select class="form-select" id="registry_method" name="method">@foreach($methods as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                    <div class="col-12 col-lg-1"><button class="btn btn-outline-primary w-100" type="submit" aria-label="Salvar ativo"><i class="bi bi-plus-lg"></i></button></div>
                </form>
                @if(($registeredAssets ?? collect())->isEmpty())
                    <div class="text-body-secondary small">Nenhum ativo cadastrado.</div>
                @else
                    <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Bem</th><th>Valor</th><th>Vida útil</th><th>Método</th><th></th></tr></thead><tbody>
                    @foreach($registeredAssets as $registeredAsset)
                        <tr><td class="fw-semibold">{{ $registeredAsset->name }}</td><td>{{ \App\Core\Money\Money::fromMinor($registeredAsset->value_minor)->formatPtBr() }}</td><td>{{ $registeredAsset->useful_life_years }} anos</td><td>{{ $methods[$registeredAsset->method] ?? $registeredAsset->method }}</td><td class="text-end"><form method="POST" action="{{ route('tools.calculadora-depreciacao-ativos.registry.destroy', $registeredAsset) }}" onsubmit="return confirm('Remover este ativo do cadastro?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Remover</button></form></td></tr>
                    @endforeach
                    </tbody></table></div>
                @endif
            </div>
        </div>
    @endauth

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        @php($money = static fn(int $minor) => \App\Core\Money\Money::fromMinor($minor)->formatPtBr())
        <div class="mt-5">
            <x-tools.result-panel title="Resultado da depreciação" description="Resumo do bem principal e projeção do valor contábil ao longo da vida útil.">
                <div class="row g-3 mb-4">
                    @foreach($result->summary as $item)
                        <div class="col-12 col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="calculator" /></div>
                    @endforeach
                </div>

                @foreach($result->warnings as $warning)<div class="alert alert-info">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Projeção do bem principal</h3>
                @php($main = $result->details['assets'][0])
                <div class="d-flex flex-wrap gap-2 mb-3"><span class="badge text-bg-secondary">{{ $main['method_label'] }}</span><span class="badge text-bg-light border text-body">Vida útil: {{ $main['useful_life_years'] }} anos</span><span class="badge text-bg-light border text-body">Residual: {{ $money($main['residual_value_minor']) }}</span></div>
                <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Ano</th><th class="text-end">Valor inicial</th><th class="text-end">Depreciação</th><th class="text-end">Depreciação acumulada</th><th class="text-end">Valor contábil</th></tr></thead><tbody>
                    @foreach($main['schedule'] as $row)<tr><td>{{ $row['year'] }}</td><td class="text-end">{{ $money($row['opening_book_value_minor']) }}</td><td class="text-end">{{ $money($row['depreciation_minor']) }}</td><td class="text-end">{{ $money($row['accumulated_depreciation_minor']) }}</td><td class="text-end fw-semibold">{{ $money($row['book_value_minor']) }}</td></tr>@endforeach
                </tbody></table></div>

                @if(count($result->details['assets']) > 1)
                    <h3 class="h5 mt-4">Ativos considerados <span class="badge text-bg-primary">Plus</span></h3>
                    <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Bem</th><th>Método</th><th>Vida útil</th><th class="text-end">Valor</th><th class="text-end">Depreciação no 1º ano</th><th class="text-end">Valor contábil após 1 ano</th></tr></thead><tbody>
                        @foreach($result->details['assets'] as $asset)<tr><td class="fw-semibold">{{ $asset['name'] }}</td><td>{{ $asset['method_label'] }}</td><td>{{ $asset['useful_life_years'] }} anos</td><td class="text-end">{{ $money($asset['cost_minor']) }}</td><td class="text-end">{{ $money($asset['first_year_depreciation_minor']) }}</td><td class="text-end">{{ $money($asset['first_year_book_value_minor']) }}</td></tr>@endforeach
                    </tbody></table></div>

                    @if($portfolioProjectionAllowed ?? false)
                    <section data-plus-feature="portfolio_projection">
                        <h3 class="h5 mt-4">Projeção patrimonial consolidada <span class="badge text-bg-primary">Plus</span></h3>
                        <div class="row g-3 mb-3"><div class="col-12 col-md-4"><x-tools.result-metric label="Valor total dos ativos" :value="$money($result->details['portfolio_cost_minor'])" icon="buildings" /></div><div class="col-12 col-md-4"><x-tools.result-metric label="Depreciação total no 1º ano" :value="$money($result->details['portfolio_first_year_depreciation_minor'])" icon="graph-down" /></div><div class="col-12 col-md-4"><x-tools.result-metric label="Valor contábil após 1 ano" :value="$money($result->details['portfolio_first_year_book_value_minor'])" icon="wallet2" /></div></div>
                        <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Ano</th><th class="text-end">Depreciação do ano</th><th class="text-end">Depreciação acumulada</th><th class="text-end">Valor contábil consolidado</th></tr></thead><tbody>@foreach($result->details['portfolio'] as $row)<tr><td>{{ $row['year'] }}</td><td class="text-end">{{ $money($row['depreciation_minor']) }}</td><td class="text-end">{{ $money($row['accumulated_depreciation_minor']) }}</td><td class="text-end fw-semibold">{{ $money($row['book_value_minor']) }}</td></tr>@endforeach</tbody></table></div>
                    </section>
                    @endif
                @endif

                <h3 class="h5 mt-4">Memória de cálculo</h3>
                <div class="accordion mb-4" id="depreciacao-memory">@foreach($result->calculationMemory->steps as $step)<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#depreciacao-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="depreciacao-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Fórmula:</strong> {{ $step->formula }}@if($step->roundingPolicy)<div class="small text-body-secondary mt-2">{{ $step->roundingPolicy }}</div>@endif</div></div></div>@endforeach</div>

                @php($exportInput = $calculationInput ?? [])
                <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions">
                    <a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.calculadora-depreciacao-ativos.export', array_merge(['format' => 'pdf'], $exportInput)) }}">Exportar PDF</a>
                    <a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.calculadora-depreciacao-ativos.export', array_merge(['format' => 'xlsx'], $exportInput)) }}">Baixar XLSX</a>
                </div>
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>

@endsection
