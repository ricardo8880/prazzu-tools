@extends('layouts.app')

@section('title', 'Margin, Markup & Pricing Calculator — Prazzu Tools')
@section('meta_description', 'Calculate selling price, margin, markup, taxes, commissions, fees, and profit.')

@section('content')
<div class="prazzu-page tool-page" data-tool="margin-markup-calculator" data-testid="tool-page-margin-markup-calculator">
    <x-tools.trust-seo slug="margin-markup-calculator" :show-content="false" />
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Tools</a></li>
            <li class="breadcrumb-item active">Margin, Markup & Pricing</li>
        </ol>
    </nav>

    <x-tools.intro icon="percent" title="Margin, Markup & Pricing Calculator" description="Build a selling price using costs, expenses, taxes, commissions, and fees." badge="Free">
        <x-slot:actions>
            @auth
                <a class="btn btn-outline-primary" href="{{ route('tools.margin-markup-calculator.history.index') }}"><i class="bi bi-clock-history me-1"></i>History</a>
            @endauth
        </x-slot:actions>
    </x-tools.intro>

    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif

    <x-tools.validation-summary class="mb-4" />

    @if ($taxSnapshotIntegration)
        <div class="alert alert-primary d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" role="status">
            <div>
                <div class="fw-semibold"><i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i> Available sales-tax rate</div>
                <div class="small">Calculated effective rate: {{ $taxSnapshotIntegration->data['effective_rate'] }}. Review it before using it in your pricing.</div>
            </div>
            <button class="btn btn-primary btn-sm flex-shrink-0" type="button" data-apply-effective-rate>Use as tax rate</button>
        </div>
    @endif

    <section class="prazzu-tool-workspace text-start" aria-labelledby="tool-workspace-title" data-testid="tool-form-panel">
        <div class="mb-4">
            <h2 id="tool-workspace-title" class="mb-1">Calculation inputs</h2>
            <p class="text-body-secondary mb-0">Enter only the fields that apply to your business.</p>
        </div>

        <form method="post" action="{{ route('tools.margin-markup-calculator.calculate') }}" class="row g-3">
            @csrf

            <div class="col-12 col-md-4">
                <label class="form-label" for="reference_date">Reference date</label>
                <input class="form-control" id="reference_date" type="date" name="reference_date" value="{{ old('reference_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="base_cost">Base cost</label>
                <input class="form-control" id="base_cost" name="base_cost" value="{{ old('base_cost') }}" placeholder="1,000.00" inputmode="decimal" required>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="additional_costs">Additional costs</label>
                <input class="form-control" id="additional_costs" name="additional_costs" value="{{ old('additional_costs') }}" placeholder="0.00" inputmode="decimal">
            </div>

            <div class="col-12"><hr class="my-1"><h3 class="h5 mb-0">Per-unit or per-sale expenses</h3></div>

            <div class="col-12 col-md-4">
                <label class="form-label" for="freight_cost">Shipping</label>
                <input class="form-control" id="freight_cost" name="freight_cost" value="{{ old('freight_cost') }}" placeholder="0.00" inputmode="decimal">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="packaging_cost">Packaging</label>
                <input class="form-control" id="packaging_cost" name="packaging_cost" value="{{ old('packaging_cost') }}" placeholder="0.00" inputmode="decimal">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="fixed_expenses">Allocated expenses</label>
                <input class="form-control" id="fixed_expenses" name="fixed_expenses" value="{{ old('fixed_expenses') }}" placeholder="0.00" inputmode="decimal">
                <div class="form-text">Example: rent, utilities, and labor allocated per item.</div>
            </div>

            <div class="col-12"><hr class="my-1"><h3 class="h5 mb-0">Percentages of selling price</h3></div>

            @foreach ([
                'desired_margin' => ['Desired net margin (%)', '30'],
                'taxes_percentage' => ['Taxes (%)', '0'],
                'commission_percentage' => ['Commission (%)', '0'],
                'card_fees_percentage' => ['Card fees (%)', '0'],
                'marketplace_fees_percentage' => ['Marketplace fees (%)', '0'],
            ] as $field => [$label, $placeholder])
                <div class="col-12 col-md-6 col-xl">
                    <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                    <input class="form-control" id="{{ $field }}" type="number" step="0.000001" min="0" max="99.999999" name="{{ $field }}" value="{{ old($field, $field === 'desired_margin' ? '' : '0') }}" placeholder="{{ $placeholder }}" {{ $field === 'desired_margin' ? 'required' : '' }}>
                </div>
            @endforeach

            <div class="col-12">
                <div class="alert alert-info mb-0" role="note">
                    <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                    The combined margin, taxes, commission, and fees must be below 100%.
                </div>
            </div>

            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0" role="alert" data-testid="validation-summary" tabindex="-1">
                        <div class="fw-semibold mb-1">Review the information entered:</div>
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif

            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                <button class="btn btn-primary prazzu-btn-primary" type="submit"><i class="bi bi-calculator me-1"></i> Calculate price</button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.margin-markup-calculator.index') }}"><i class="bi bi-eraser me-1"></i> Clear</a>
            </div>
        </form>
    </section>

    @php
        $result = $calculationResult ?? session('calculation_result');
    @endphp
    @if ($result)
        <span data-analytics-result="main" hidden></span>
        <section class="mt-4 prazzu-tool-result-surface" aria-labelledby="calculation-result-title" data-testid="tool-result" data-tool-result-panel>
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-3">
                <div>
                    <h2 id="calculation-result-title" class="prazzu-section-title mb-1">Pricing result</h2>
                    <p class="text-body-secondary mb-0">Estimated amounts based on the information entered.</p>
                </div>
                <span class="badge text-bg-success">Calculation complete</span>
            </div>

            <div class="row g-3 mb-3">
                @foreach ([
                    'sale_price' => 'Suggested selling price',
                    'net_profit' => 'Estimated net profit',
                    'total_cost' => 'Total cost',
                    'gross_profit' => 'Gross profit',
                    'margin' => 'Net margin',
                    'markup' => 'Markup on cost',
                    'markup_multiplier' => 'Markup multiplier',
                ] as $key => $label)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="prazzu-related-tool h-100"><span><small>{{ $label }}</small><strong>{{ $result[$key] }}</strong></span></div>
                    </div>
                @endforeach
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h5 card-title">Amounts deducted from sales</h3>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <tbody>
                                <tr><th scope="row">Taxes</th><td class="text-end">{{ $result['taxes_amount'] }}</td></tr>
                                <tr><th scope="row">Commission</th><td class="text-end">{{ $result['commission_amount'] }}</td></tr>
                                <tr><th scope="row">Card fees</th><td class="text-end">{{ $result['card_fees_amount'] }}</td></tr>
                                <tr><th scope="row">Marketplace fees</th><td class="text-end">{{ $result['marketplace_fees_amount'] }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if (!empty($result['calculation_memory']['steps']))
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h3 class="h5 card-title">Calculation details</h3>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                    @foreach ($result['calculation_memory']['steps'] as $step)
                                        <tr>
                                            <th scope="row">{{ $step['label'] }}<div class="small fw-normal text-body-secondary">{{ $step['formula'] }}</div></th>
                                            <td class="text-end">{{ is_int($step['result']) ? '$'.number_format($step['result'] / 100, 2, '.', ',') : $step['result'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3 mb-0">Net margin and markup are different metrics: net margin is measured against selling price after deductions, while markup measures the gross increase over cost.</div>
                    </div>
                </div>
            @endif

            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                <form method="post" action="{{ route('tools.margin-markup-calculator.export') }}">
                    @csrf
                    @foreach (['reference_date','base_cost','additional_costs','freight_cost','packaging_cost','fixed_expenses','desired_margin','taxes_percentage','commission_percentage','card_fees_percentage','marketplace_fees_percentage'] as $field)
                        <input type="hidden" name="{{ $field }}" value="{{ old($field) }}">
                    @endforeach
                    <button class="btn btn-outline-primary" type="submit" data-testid="download-csv"><i class="bi bi-filetype-csv me-1"></i> Download CSV</button>
                    <button class="btn btn-outline-danger" type="submit" data-testid="download-pdf" formaction="{{ route('tools.margin-markup-calculator.export.pdf') }}" formtarget="_blank"><i class="bi bi-file-earmark-pdf me-1"></i> Download PDF</button>
                </form>
                <p class="text-body-secondary mb-0"><small>Rule {{ $result['rule_version'] }}. Management estimate.</small></p>
            </div>
        </section>
    @endif
    @include('tools-margin-markup-calculator::partials.pricing-scenarios')
    @include('tools-margin-markup-calculator::partials.product-import')
    @include('tools-margin-markup-calculator::partials.batch-calculator')
    <x-tool-feature-tiers slug="margin-markup-calculator" />
    <x-tools.plus-result-cta slug="margin-markup-calculator" />

</div>
@if ($taxSnapshotIntegration)
    <script nonce="{{ $cspNonce ?? '' }}">
        document.querySelector('[data-apply-effective-rate]')?.addEventListener('click', () => {
            const rate = String(@json($taxSnapshotIntegration->data['effective_rate']))
                .replace('%', '').trim();
            document.getElementById('taxes_percentage').value = rate;
            document.getElementById('taxes_percentage').focus();
        });
    </script>
@endif

@endsection
