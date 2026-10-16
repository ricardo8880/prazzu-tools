@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <div class="alert alert-info">Uses 2026 federal employer Social Security, Medicare and FUTA assumptions. Enter your effective SUTA and other employer-cost rates.</div>
    <x-tools.form-panel title="Employer payroll assumptions"><form method="POST" action="{{ route('tools.employer-payroll-tax-calculator.calculate') }}" class="row g-3">@csrf
        <div class="col-md-4"><x-tools.form.money name="salary" label="Monthly wages" :value="old('salary')" required /></div>
        <div class="col-md-4"><x-tools.form.money name="benefits" label="Monthly employer-paid benefits" :value="old('benefits','0.00')" required /></div>
        <div class="col-md-4"><x-tools.form.select name="futa_profile" label="FUTA profile" :options="['standard'=>'Standard FUTA estimate','futa_exempt'=>'FUTA-exempt employer']" :value="old('futa_profile','standard')" required /></div>
        <div class="col-md-4"><x-tools.form.input name="suta_rate" label="Effective SUTA rate" type="number" step="0.0001" suffix="%" :value="old('suta_rate','0')" required /></div>
        <div class="col-md-4"><x-tools.form.input name="other_employer_cost_rate" label="Other employer payroll-cost rate" type="number" step="0.0001" suffix="%" :value="old('other_employer_cost_rate','0')" required /></div>
        <div class="col-12"><button class="btn btn-primary">Calculate employer payroll taxes</button></div>
    </form></x-tools.form-panel>
    @isset($result)
        <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Employer payroll tax estimate"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div><x-tools.normative-trust :rules="$result->calculationMemory?->normativeRules ?? []" :assumptions="$result->calculationMemory?->assumptions ?? []" :is-estimate="$result->calculationMemory?->isEstimate ?? true" /></x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
