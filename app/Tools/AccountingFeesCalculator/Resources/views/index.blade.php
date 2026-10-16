@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route('tools.accounting-fee-calculator.index'))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug" badge="Free + Plus">
    <x-slot:actions>@auth<a class="btn btn-outline-secondary" href="{{ route('tools.accounting-fee-calculator.history.index') }}">History</a>@endauth</x-slot:actions>
    <div class="alert alert-info">Pricing model for bookkeeping/accounting services. It is a commercial estimate, not a published CPA fee schedule.</div>
    <x-tools.form-panel title="Client profile"><form method="POST" action="{{ route('tools.accounting-fee-calculator.calculate') }}" class="row g-3">@csrf
        <div class="col-md-4"><x-tools.form.money name="monthly_revenue" label="Monthly revenue" :value="old('monthly_revenue')" required /></div>
        <div class="col-md-2"><x-tools.form.input name="employees" label="Employees" type="number" min="0" :value="old('employees','0')" required /></div>
        <div class="col-md-2"><x-tools.form.input name="partners" label="Owners / partners" type="number" min="1" :value="old('partners','1')" required /></div>
        <div class="col-md-4"><x-tools.form.select name="tax_regime" label="Entity / filing profile" :options="['sole_proprietor'=>'Sole proprietor / single-member LLC','partnership'=>'Partnership / multi-member LLC','s_corporation'=>'S corporation','c_corporation'=>'C corporation','nonprofit'=>'Nonprofit organization']" :value="old('tax_regime','sole_proprietor')" required /></div>
        <div class="col-md-4"><x-tools.form.select name="business_segment" label="Business segment" :options="['services'=>'Professional services','commerce'=>'Retail / commerce','industry'=>'Manufacturing','construction'=>'Construction','healthcare'=>'Healthcare','digital_business'=>'Digital business','other'=>'Other']" :value="old('business_segment','services')" required /></div>
        <div class="col-md-4"><x-tools.form.input name="monthly_invoices" label="Monthly invoices" type="number" min="0" :value="old('monthly_invoices','0')" required /></div>
        <div class="col-md-4"><x-tools.form.input name="monthly_bank_transactions" label="Monthly bank transactions" type="number" min="0" :value="old('monthly_bank_transactions','0')" required /></div>
        <div class="col-md-4"><x-tools.form.select name="complexity" label="Operational complexity" :options="['low'=>'Low','medium'=>'Medium','high'=>'High','very_high'=>'Very high']" :value="old('complexity','medium')" required /></div>
        <div class="col-12"><button class="btn btn-primary" type="submit">Estimate accounting fee</button></div>
    </form></x-tools.form-panel>
    @isset($result)
        <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Fee estimate"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6 col-xl-4"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div></x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
