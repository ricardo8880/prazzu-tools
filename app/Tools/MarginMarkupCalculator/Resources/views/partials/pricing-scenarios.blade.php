@php
    $defaultScenarios = [
        ['name' => 'Conservative', 'cost_adjustment' => '10', 'desired_margin' => '20', 'discount_percentage' => '0', 'taxes_percentage' => '6', 'commission_percentage' => '0', 'card_fees_percentage' => '3', 'marketplace_fees_percentage' => '0'],
        ['name' => 'Likely', 'cost_adjustment' => '0', 'desired_margin' => '30', 'discount_percentage' => '0', 'taxes_percentage' => '6', 'commission_percentage' => '0', 'card_fees_percentage' => '3', 'marketplace_fees_percentage' => '0'],
        ['name' => 'Otimista', 'cost_adjustment' => '-5', 'desired_margin' => '40', 'discount_percentage' => '5', 'taxes_percentage' => '6', 'commission_percentage' => '0', 'card_fees_percentage' => '3', 'marketplace_fees_percentage' => '0'],
    ];
    $scenarios = old('scenarios', $defaultScenarios);
@endphp

<section class="prazzu-tool-workspace text-start mt-4" aria-labelledby="scenario-title">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <span class="badge text-bg-warning mb-2">Simulations</span>
            <h2 id="scenario-title" class="mb-1">Compare pricing scenarios</h2>
            <p class="text-body-secondary mb-0">Compare cost, margin, discount, and sales-channel variations side by side.</p>
        </div>
        <button class="btn btn-outline-primary align-self-lg-end" type="button" id="scenario-add"><i class="bi bi-plus-lg me-1"></i>Add scenario</button>
    </div>

    <form method="post" action="{{ route('tools.margin-markup-calculator.scenarios.simulate') }}" id="scenario-form">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6 col-xl-3">
                <label class="form-label" for="scenario_product_name">Product or service</label>
                <input class="form-control" id="scenario_product_name" name="product_name" value="{{ old('product_name') }}" required maxlength="120">
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <label class="form-label" for="scenario_reference_date">Reference date</label>
                <input class="form-control" id="scenario_reference_date" type="date" name="reference_date" value="{{ old('reference_date', date('Y-m-d')) }}" required>
            </div>
            @foreach ([
                'base_cost' => 'Base cost',
                'additional_costs' => 'Additional costs',
                'freight_cost' => 'Shipping',
                'packaging_cost' => 'Packaging',
                'fixed_expenses' => 'Allocated expenses',
            ] as $field => $label)
                <div class="col-12 col-md-6 col-xl">
                    <label class="form-label" for="scenario_{{ $field }}">{{ $label }}</label>
                    <input class="form-control" id="scenario_{{ $field }}" name="{{ $field }}" value="{{ old($field, $field === 'base_cost' ? '' : '0.00') }}" placeholder="0.00" inputmode="decimal" {{ $field === 'base_cost' ? 'required' : '' }}>
                </div>
            @endforeach
        </div>

        <div class="table-responsive border rounded">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th>Scenario</th><th>Cost adjustment %</th><th>Desired margin %</th><th>Discount %</th><th>Taxes %</th><th>Commission %</th><th>Card fees %</th><th>Marketplace %</th><th></th>
                    </tr>
                </thead>
                <tbody id="scenario-rows">
                    @foreach ($scenarios as $index => $scenario)
                        <tr>
                            @foreach ([
                                'name' => ['text', 'Name'],
                                'cost_adjustment' => ['number', '0'],
                                'desired_margin' => ['number', '30'],
                                'discount_percentage' => ['number', '0'],
                                'taxes_percentage' => ['number', '0'],
                                'commission_percentage' => ['number', '0'],
                                'card_fees_percentage' => ['number', '0'],
                                'marketplace_fees_percentage' => ['number', '0'],
                            ] as $field => [$type, $placeholder])
                                <td><input class="form-control form-control-sm" type="{{ $type }}" step="0.000001" name="scenarios[{{ $index }}][{{ $field }}]" value="{{ $scenario[$field] ?? '' }}" placeholder="{{ $placeholder }}" {{ in_array($field, ['name','desired_margin'], true) ? 'required' : '' }}></td>
                            @endforeach
                            <td><button class="btn btn-sm btn-outline-danger scenario-remove" type="button" aria-label="Remove scenario"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @error('scenarios')<div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>@enderror
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
            <small class="text-body-secondary">Compare 2 to 6 scenarios. Discounts reduce the calculated price and show the effective margin.</small>
            <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-bar-chart-line me-1"></i>Simulate scenarios</button>
        </div>
    </form>
</section>

@php
    $scenarioResults = $scenarioSimulationResults ?? session('scenario_simulation_results');
@endphp
@if ($scenarioResults)
    <section class="mt-4" aria-labelledby="scenario-results-title">
        <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
            <div><h2 id="scenario-results-title" class="prazzu-section-title mb-1">Scenario comparison</h2><p class="text-body-secondary mb-0">Use effective margin to evaluate the actual impact of costs and discounts.</p></div>
            <span class="badge text-bg-success">{{ count($scenarioResults) }} scenario(s)</span>
        </div>
        <div class="table-responsive border rounded">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light text-nowrap"><tr><th>Scenario</th><th class="text-end">Cost adjustment</th><th class="text-end">Target margin</th><th class="text-end">Discount</th><th class="text-end">Total cost</th><th class="text-end">List price</th><th class="text-end">Final price</th><th class="text-end">Net profit</th><th class="text-end">Effective margin</th><th class="text-end">Multiplier</th></tr></thead>
                <tbody>
                    @foreach ($scenarioResults as $result)
                        <tr>
                            <th scope="row">{{ $result['name'] }}</th><td class="text-end">{{ $result['cost_adjustment'] }}</td><td class="text-end">{{ $result['desired_margin'] }}</td><td class="text-end">{{ $result['discount'] }}</td><td class="text-end text-nowrap">{{ $result['total_cost'] }}</td><td class="text-end text-nowrap">{{ $result['list_price'] }}</td><td class="text-end text-nowrap fw-semibold">{{ $result['final_price'] }}</td><td class="text-end text-nowrap">{{ $result['net_profit'] }}</td><td class="text-end fw-semibold">{{ $result['effective_margin'] }}</td><td class="text-end">{{ $result['markup_multiplier'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif

<template id="scenario-row-template">
<tr>
    <td><input class="form-control form-control-sm" name="scenarios[__INDEX__][name]" required maxlength="60" placeholder="New scenario"></td>
    @foreach (['cost_adjustment'=>'0','desired_margin'=>'30','discount_percentage'=>'0','taxes_percentage'=>'0','commission_percentage'=>'0','card_fees_percentage'=>'0','marketplace_fees_percentage'=>'0'] as $field => $value)
        <td><input class="form-control form-control-sm" type="number" step="0.000001" name="scenarios[__INDEX__][{{ $field }}]" value="{{ $value }}" {{ $field === 'desired_margin' ? 'required' : '' }}></td>
    @endforeach
    <td><button class="btn btn-sm btn-outline-danger scenario-remove" type="button" aria-label="Remove scenario"><i class="bi bi-trash"></i></button></td>
</tr>
</template>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.getElementById('scenario-rows');
    const template = document.getElementById('scenario-row-template');
    const add = document.getElementById('scenario-add');
    if (!rows || !template || !add) return;
    let nextIndex = rows.querySelectorAll('tr').length;
    add.addEventListener('click', () => {
        if (rows.querySelectorAll('tr').length >= 6) return;
        rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', nextIndex++));
    });
    rows.addEventListener('click', event => {
        const button = event.target.closest('.scenario-remove');
        if (!button || rows.querySelectorAll('tr').length <= 2) return;
        button.closest('tr').remove();
        [...rows.querySelectorAll('tr')].forEach((row, index) => row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/scenarios\[[^\]]+\]/, `scenarios[${index}]`)));
    });
});
</script>
@endpush
