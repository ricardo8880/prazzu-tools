@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <div class="alert alert-info">U.S. sales tax is primarily state and local. Enter the rates that apply to the transaction location.</div>
    <x-tools.form-panel title="Transaction">
        <form method="POST" action="{{ route('tools.sales-tax-calculator.calculate') }}" class="row g-3">@csrf
            <div class="col-md-3"><x-tools.form.money name="price" label="Unit price" :value="old('price')" required /></div>
            <div class="col-md-2"><x-tools.form.input name="quantity" label="Quantity" type="number" min="1" step="1" :value="old('quantity',1)" required /></div>
            <div class="col-md-2"><x-tools.form.input name="state_rate" label="State rate" type="number" step="0.0001" suffix="%" :value="old('state_rate','0')" required /></div>
            <div class="col-md-2"><x-tools.form.input name="local_rate" label="Local rate" type="number" step="0.0001" suffix="%" :value="old('local_rate','0')" /></div>
            <div class="col-md-3"><x-tools.form.input name="district_rate" label="District / special rate" type="number" step="0.0001" suffix="%" :value="old('district_rate','0')" /></div>
            <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="tax_included" value="1" id="tax_included" @checked(old('tax_included'))><label class="form-check-label" for="tax_included">Entered price already includes sales tax</label></div>
            <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="show_diagnostics" value="1" id="show_diagnostics" @checked(old('show_diagnostics'))><label class="form-check-label" for="show_diagnostics">Show combined-rate diagnostics (Plus)</label></div><div class="col-12"><button class="btn btn-primary" type="submit">Calculate sales tax</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
        <div data-testid="tool-result"><x-tools.result-panel title="Sales tax estimate"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div><x-tools.normative-trust :rules="$result->calculationMemory?->normativeRules ?? []" :assumptions="$result->calculationMemory?->assumptions ?? []" :is-estimate="$result->calculationMemory?->isEstimate ?? true" />@if($showDiagnostics ?? false)<div class="alert alert-light border mt-3 mb-0"><strong>Rate diagnostics:</strong> state {{ $result->details['input']['state_rate'] ?? '0' }}% + local {{ $result->details['input']['local_rate'] ?? '0' }}% + district/special {{ $result->details['input']['district_rate'] ?? '0' }}% = {{ $result->details['combined_rate'] ?? '0' }}%.</div>@endif</x-tools.result-panel></div>
    @endisset
</x-tools.page>
@endsection
