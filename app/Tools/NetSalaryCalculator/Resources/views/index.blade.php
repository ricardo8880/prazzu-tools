@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <div class="alert alert-info">Uses 2026 federal tax brackets and FICA rates for a planning estimate. It is not a Form W-4 withholding calculator.</div>
    <x-tools.form-panel title="Monthly pay">
        <form method="POST" action="{{ route('tools.take-home-pay-calculator.calculate') }}" class="row g-3">@csrf
            <div class="col-md-3"><x-tools.form.input name="competence" label="Pay month" type="month" :value="old('competence','2026-10')" required /></div>
            <div class="col-md-3"><x-tools.form.money name="base_salary" label="Base gross pay" :value="old('base_salary')" required /></div>
            <div class="col-md-3"><x-tools.form.money name="taxable_additional_earnings" label="Additional taxable earnings" :value="old('taxable_additional_earnings','0.00')" /></div>
            <div class="col-md-3"><x-tools.form.money name="non_taxable_earnings" label="Non-taxable cash earnings" :value="old('non_taxable_earnings','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.select name="filing_status" label="Federal filing status" :options="['single'=>'Single','married_joint'=>'Married filing jointly','married_separate'=>'Married filing separately','head_household'=>'Head of household']" :value="old('filing_status','single')" required /></div>
            <div class="col-md-4"><x-tools.form.money name="pretax_deductions" label="Pre-tax deductions" :value="old('pretax_deductions','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="state_local_withholding" label="State/local withholding" :value="old('state_local_withholding','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="additional_federal_withholding" label="Additional federal withholding" :value="old('additional_federal_withholding','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="benefit_deductions" label="Benefit deductions" :value="old('benefit_deductions','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="other_deductions" label="Other deductions" :value="old('other_deductions','0.00')" /></div>
            <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm_assumptions" @checked(old('confirm_assumptions')) required><label class="form-check-label" for="confirm_assumptions">I understand the federal withholding result is an estimate and state/local rules are entered separately.</label></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Estimate take-home pay</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
        <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Take-home pay estimate"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div><x-tools.normative-trust :rules="$result->calculationMemory?->normativeRules ?? []" :assumptions="$result->calculationMemory?->assumptions ?? []" :is-estimate="$result->calculationMemory?->isEstimate ?? true" /></x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
