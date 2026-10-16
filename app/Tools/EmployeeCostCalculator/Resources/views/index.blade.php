@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route('tools.employee-cost-calculator.index'))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug" badge="Free + Plus">
    <x-slot:actions>@auth<a class="btn btn-outline-secondary" href="{{ route('tools.employee-cost-calculator.history.index') }}"><i class="bi bi-clock-history me-1"></i>History</a>@endauth</x-slot:actions>
    @if($historySaved ?? false)<div class="alert alert-success">Calculation saved to your history.</div>@endif
    <div class="alert alert-info">Uses 2026 federal employer FICA/FUTA assumptions. Enter your effective SUTA and other employer-cost rates because state rules vary.</div>
    <x-tools.form-panel title="Employee cost assumptions">
        <form method="POST" action="{{ route('tools.employee-cost-calculator.calculate') }}" class="row g-3">@csrf
            <div class="col-md-4"><x-tools.form.input name="employee_name" label="Employee name (optional)" :value="old('employee_name')" /></div>
            <div class="col-md-4"><x-tools.form.input name="department" label="Department (optional)" :value="old('department')" /></div>
            <div class="col-md-4"><x-tools.form.input name="scenario_name" label="Scenario name (optional)" :value="old('scenario_name')" /></div>
            <div class="col-md-4"><x-tools.form.money name="salary" label="Monthly base wages" :value="old('salary')" required /></div>
            <div class="col-md-4"><x-tools.form.money name="variable_pay" label="Monthly variable pay" :value="old('variable_pay','0.00')" required /></div>
            <div class="col-md-4"><x-tools.form.money name="benefits" label="Monthly employer-paid benefits" :value="old('benefits','0.00')" required /></div>
            <div class="col-md-4"><x-tools.form.select name="futa_profile" label="FUTA profile" :options="['standard'=>'Standard FUTA estimate','futa_exempt'=>'FUTA-exempt employer']" :value="old('futa_profile','standard')" required /></div>
            <div class="col-md-4"><x-tools.form.input name="suta_rate" label="Effective SUTA rate" type="number" step="0.0001" suffix="%" :value="old('suta_rate','0')" required /></div>
            <div class="col-md-4"><x-tools.form.input name="other_employer_cost_rate" label="Other employer-cost rate" type="number" step="0.0001" suffix="%" :value="old('other_employer_cost_rate','0')" required /></div>
            <div class="col-md-4"><x-tools.form.input name="monthly_hours" label="Monthly paid hours" type="number" min="1" max="744" :value="old('monthly_hours','173')" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate employee cost</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
        <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Employer cost estimate">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6 col-xl-4"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <x-tools.normative-trust :rules="$result->calculationMemory?->normativeRules ?? []" :assumptions="$result->calculationMemory?->assumptions ?? []" :is-estimate="$result->calculationMemory?->isEstimate ?? true" />
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
