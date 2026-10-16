@extends('layouts.app')

@section('title', 'Asset Depreciation Calculator — Prazzu Tools')

@section('content')
<x-tools.page title="Asset Depreciation Calculator" description="Enter the asset cost and useful life to estimate depreciation and book value over time." icon="bi-building-down" slug="asset-depreciation-calculator">
    <form method="POST" action="{{ route('tools.asset-depreciation-calculator.calculate') }}" class="vstack gap-4">
        @csrf

        <x-tools.form-panel title="Asset and useful life" description="Straight-line depreciation handles the essential case with uniform depreciation over the asset’s useful life.">
            <div class="row g-3">
                <div class="col-12 col-lg-5">
                    <label class="form-label" for="asset_name">Asset</label>
                    <input class="form-control @error('asset_name') is-invalid @enderror" id="asset_name" name="asset_name" value="{{ old('asset_name') }}" maxlength="120" placeholder="e.g., Company laptop" required>
                    @error('asset_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label" for="asset_value">Asset cost</label>
                    <div class="input-group"><span class="input-group-text">$</span><input class="form-control @error('asset_value') is-invalid @enderror" id="asset_value" name="asset_value" value="{{ old('asset_value') }}" inputmode="decimal" placeholder="0.00" required></div>
                    @error('asset_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label" for="residual_value">Salvage value</label>
                    <div class="input-group"><span class="input-group-text">$</span><input class="form-control @error('residual_value') is-invalid @enderror" id="residual_value" name="residual_value" value="{{ old('residual_value', '0.00') }}" inputmode="decimal" placeholder="0.00"></div>
                    @error('residual_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <div class="form-text">Estimated asset value at the end of its useful life. Use zero when there is no salvage value.</div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <label class="form-label" for="useful_life_years">Useful life</label>
                    <div class="input-group"><input class="form-control @error('useful_life_years') is-invalid @enderror" id="useful_life_years" name="useful_life_years" type="number" min="1" max="100" value="{{ old('useful_life_years') }}" required><span class="input-group-text">years</span></div>
                    @error('useful_life_years')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </x-tools.form-panel>

        @if($plusEnabled ?? true)
        <x-tools.form-panel title="Methods and multiple assets" description="Choose another method for the primary asset and include additional assets in the same calculation." badge="Prazzu Plus">
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6">
                    <label class="form-label" for="method_plus">Primary asset method</label>
                    <select class="form-select" id="method_plus" name="method">
                        @foreach($methods as $value => $label)
                            <option value="{{ $value }}" @selected(old('method', 'linear') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Straight-line is the default Essential method; alternative methods are Plus features.</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Additional asset</th><th>Amount</th><th>Residual</th><th>Useful life</th><th>Method</th></tr></thead>
                    <tbody>
                        @for($i = 0; $i < 5; $i++)
                            <tr>
                                <td><input class="form-control" name="assets[{{ $i }}][name]" value="{{ old("assets.$i.name") }}" placeholder="e.g., Vehicle {{ $i + 1 }}"></td>
                                <td><input class="form-control" name="assets[{{ $i }}][value]" value="{{ old("assets.$i.value") }}" inputmode="decimal"></td>
                                <td><input class="form-control" name="assets[{{ $i }}][residual_value]" value="{{ old("assets.$i.residual_value", "0.00") }}" inputmode="decimal" aria-label="Additional asset salvage value"></td>
                                <td><div class="input-group"><input class="form-control" name="assets[{{ $i }}][useful_life_years]" type="number" min="1" max="100" value="{{ old("assets.$i.useful_life_years") }}"><span class="input-group-text">years</span></div></td>
                                <td><select class="form-select" name="assets[{{ $i }}][method]">@foreach($methods as $value => $label)<option value="{{ $value }}" @selected(old("assets.$i.method", 'linear') === $value)>{{ $label }}</option>@endforeach</select></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            @auth
                @if(($registeredAssets ?? collect())->isNotEmpty())
                    <hr class="my-4">
                    <h3 class="h6">Saved assets</h3>
                    <p class="small text-body-secondary">Select saved assets to include in this calculation.</p>
                    <div class="row g-2">
                        @foreach($registeredAssets as $registeredAsset)
                            <div class="col-12 col-md-6">
                                <label class="border rounded p-3 d-flex gap-3 align-items-start w-100">
                                    <input class="form-check-input mt-1" type="checkbox" name="registered_asset_ids[]" value="{{ $registeredAsset->id }}" @checked(in_array($registeredAsset->id, old('registered_asset_ids', []), true))>
                                    <span>
                                        <strong>{{ $registeredAsset->name }}</strong><br>
                                        <small class="text-body-secondary">{{ \App\Core\Money\Money::fromMinor($registeredAsset->value_minor)->formatEnUs() }} · {{ $registeredAsset->useful_life_years }} years · {{ $methods[$registeredAsset->method] ?? $registeredAsset->method }}</small>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="form-text">Sign in to save assets and reuse them in future Plus calculations.</div>
            @endauth
        </x-tools.form-panel>
        @endif

        <div><button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-calculator me-2"></i>Calculate depreciation</button></div>
    </form>

    @auth
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div><h2 class="h5 mb-1">Asset registry <span class="badge text-bg-primary">Plus</span></h2><p class="text-body-secondary mb-0">Save assets for reuse in future projections.</p></div>
                </div>
                <form method="POST" action="{{ route('tools.asset-depreciation-calculator.registry.store') }}" class="row g-3 align-items-end mb-4">
                    @csrf
                    <div class="col-12 col-lg-4"><label class="form-label" for="registry_name">Asset</label><input class="form-control" id="registry_name" name="name" maxlength="120" required></div>
                    <div class="col-12 col-md-4 col-lg-3"><label class="form-label" for="registry_value">Amount</label><div class="input-group"><span class="input-group-text">$</span><input class="form-control" id="registry_value" name="value" inputmode="decimal" placeholder="0.00" required></div></div>
                    <div class="col-12 col-md-4 col-lg-2"><label class="form-label" for="registry_life">Useful life</label><div class="input-group"><input class="form-control" id="registry_life" name="useful_life_years" type="number" min="1" max="100" placeholder="e.g., 6" required><span class="input-group-text">years</span></div></div>
                    <div class="col-12 col-md-4 col-lg-2"><label class="form-label" for="registry_method">Method</label><select class="form-select" id="registry_method" name="method">@foreach($methods as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                    <div class="col-12 col-lg-1"><button class="btn btn-outline-primary w-100" type="submit" aria-label="Save asset"><i class="bi bi-plus-lg"></i></button></div>
                </form>
                @if(($registeredAssets ?? collect())->isEmpty())
                    <div class="text-body-secondary small">No saved assets.</div>
                @else
                    <div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>Asset</th><th>Amount</th><th>Useful life</th><th>Method</th><th></th></tr></thead><tbody>
                    @foreach($registeredAssets as $registeredAsset)
                        <tr><td class="fw-semibold">{{ $registeredAsset->name }}</td><td>{{ \App\Core\Money\Money::fromMinor($registeredAsset->value_minor)->formatEnUs() }}</td><td>{{ $registeredAsset->useful_life_years }} years</td><td>{{ $methods[$registeredAsset->method] ?? $registeredAsset->method }}</td><td class="text-end"><form method="POST" action="{{ route('tools.asset-depreciation-calculator.registry.destroy', $registeredAsset) }}" onsubmit="return confirm('Remove this asset from the registry?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Remove</button></form></td></tr>
                    @endforeach
                    </tbody></table></div>
                @endif
            </div>
        </div>
    @endauth

    @isset($result)
        <span data-analytics-result="main" hidden></span>
        @php($money = static fn(int $minor) => \App\Core\Money\Money::fromMinor($minor)->formatEnUs())
        <div class="mt-5">
            <x-tools.result-panel title="Depreciation result" description="Summary of the primary asset and projected book value over its useful life.">
                <div class="row g-3 mb-4">
                    @foreach($result->summary as $item)
                        <div class="col-12 col-md-6 col-xl-3"><x-tools.result-metric :label="$item->label" :value="$item->value" icon="calculator" /></div>
                    @endforeach
                </div>

                @foreach($result->warnings as $warning)<div class="alert alert-info">{{ $warning->message }}</div>@endforeach

                <h3 class="h5 mt-4">Primary asset projection</h3>
                @php($main = $result->details['assets'][0])
                <div class="d-flex flex-wrap gap-2 mb-3"><span class="badge text-bg-secondary">{{ $main['method_label'] }}</span><span class="badge text-bg-light border text-body">Useful life: {{ $main['useful_life_years'] }} years</span><span class="badge text-bg-light border text-body">Residual: {{ $money($main['residual_value_minor']) }}</span></div>
                <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Year</th><th class="text-end">Opening amount</th><th class="text-end">Depreciation</th><th class="text-end">Accumulated depreciation</th><th class="text-end">Book value</th></tr></thead><tbody>
                    @foreach($main['schedule'] as $row)<tr><td>{{ $row['year'] }}</td><td class="text-end">{{ $money($row['opening_book_value_minor']) }}</td><td class="text-end">{{ $money($row['depreciation_minor']) }}</td><td class="text-end">{{ $money($row['accumulated_depreciation_minor']) }}</td><td class="text-end fw-semibold">{{ $money($row['book_value_minor']) }}</td></tr>@endforeach
                </tbody></table></div>

                @if(count($result->details['assets']) > 1)
                    <h3 class="h5 mt-4">Assets included <span class="badge text-bg-primary">Plus</span></h3>
                    <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Asset</th><th>Method</th><th>Useful life</th><th class="text-end">Amount</th><th class="text-end">Depreciation in year 1</th><th class="text-end">Book value after 1 year</th></tr></thead><tbody>
                        @foreach($result->details['assets'] as $asset)<tr><td class="fw-semibold">{{ $asset['name'] }}</td><td>{{ $asset['method_label'] }}</td><td>{{ $asset['useful_life_years'] }} years</td><td class="text-end">{{ $money($asset['cost_minor']) }}</td><td class="text-end">{{ $money($asset['first_year_depreciation_minor']) }}</td><td class="text-end">{{ $money($asset['first_year_book_value_minor']) }}</td></tr>@endforeach
                    </tbody></table></div>

                    @if($portfolioProjectionAllowed ?? false)
                    <section data-plus-feature="portfolio_projection">
                        <h3 class="h5 mt-4">Consolidated asset projection <span class="badge text-bg-primary">Plus</span></h3>
                        <div class="row g-3 mb-3"><div class="col-12 col-md-4"><x-tools.result-metric label="Total asset cost" :value="$money($result->details['portfolio_cost_minor'])" icon="buildings" /></div><div class="col-12 col-md-4"><x-tools.result-metric label="Total depreciation in year 1" :value="$money($result->details['portfolio_first_year_depreciation_minor'])" icon="graph-down" /></div><div class="col-12 col-md-4"><x-tools.result-metric label="Book value after 1 year" :value="$money($result->details['portfolio_first_year_book_value_minor'])" icon="wallet2" /></div></div>
                        <div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Year</th><th class="text-end">Depreciation for the year</th><th class="text-end">Accumulated depreciation</th><th class="text-end">Consolidated book value</th></tr></thead><tbody>@foreach($result->details['portfolio'] as $row)<tr><td>{{ $row['year'] }}</td><td class="text-end">{{ $money($row['depreciation_minor']) }}</td><td class="text-end">{{ $money($row['accumulated_depreciation_minor']) }}</td><td class="text-end fw-semibold">{{ $money($row['book_value_minor']) }}</td></tr>@endforeach</tbody></table></div>
                    </section>
                    @endif
                @endif

                <h3 class="h5 mt-4">Calculation details</h3>
                <div class="accordion mb-4" id="depreciation-memory">@foreach($result->calculationMemory->steps as $step)<div class="accordion-item"><h4 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#depreciation-memory-{{ $loop->index }}">{{ $step->label }}</button></h4><div id="depreciation-memory-{{ $loop->index }}" class="accordion-collapse collapse"><div class="accordion-body"><strong>Formula:</strong> {{ $step->formula }}@if($step->roundingPolicy)<div class="small text-body-secondary mt-2">{{ $step->roundingPolicy }}</div>@endif</div></div></div>@endforeach</div>

                @php($exportInput = $calculationInput ?? [])
                <div class="d-flex flex-wrap gap-2" data-result-export-actions data-testid="download-actions">
                    <a class="btn btn-outline-danger" data-testid="download-pdf" href="{{ route('tools.asset-depreciation-calculator.export', array_merge(['format' => 'pdf'], $exportInput)) }}">Export PDF</a>
                    <a class="btn btn-outline-success" data-testid="download-xlsx" href="{{ route('tools.asset-depreciation-calculator.export', array_merge(['format' => 'xlsx'], $exportInput)) }}">Download XLSX</a>
                </div>
            </x-tools.result-panel>
        </div>
    @endisset
</x-tools.page>

@endsection
