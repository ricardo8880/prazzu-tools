@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <div class="alert alert-warning">Final-pay timing, PTO payout, and severance requirements can depend on state law, policy, contract, and the reason for separation. Enter the amounts/rules that apply to your case.</div>
    <x-tools.form-panel title="Final pay assumptions">
        <form method="POST" action="{{ route('tools.final-pay-severance-calculator.calculate') }}" class="row g-3">@csrf
            <div class="col-md-3"><x-tools.form.input name="termination_date" label="Separation date" type="date" :value="old('termination_date', now()->format('Y-m-d'))" required /></div>
            <div class="col-md-3"><x-tools.form.select name="separation_type" label="Separation type" :options="['resignation'=>'Resignation','layoff'=>'Layoff','termination'=>'Termination','contract_end'=>'Contract ended','other'=>'Other']" :value="old('separation_type','resignation')" required /></div>
            <div class="col-md-3"><x-tools.form.money name="hourly_rate" label="Hourly rate" :value="old('hourly_rate')" required /></div>
            <div class="col-md-3"><x-tools.form.input name="regular_hours_due" label="Unpaid regular hours" type="number" min="0" step="0.01" :value="old('regular_hours_due','0')" required /></div>
            <div class="col-md-3"><x-tools.form.input name="unused_pto_hours" label="Unused PTO hours" type="number" min="0" step="0.01" :value="old('unused_pto_hours','0')" /></div>
            <div class="col-md-3"><x-tools.form.input name="pto_payout_percent" label="PTO payout percent" type="number" min="0" max="100" step="0.01" suffix="%" :value="old('pto_payout_percent','100')" required /></div>
            <div class="col-md-3"><x-tools.form.money name="weekly_base_pay" label="Weekly base pay for severance" :value="old('weekly_base_pay','0.00')" /></div>
            <div class="col-md-3"><x-tools.form.input name="severance_weeks" label="Severance weeks" type="number" min="0" step="0.01" :value="old('severance_weeks','0')" /></div>
            <div class="col-md-4"><x-tools.form.money name="bonus_commission_due" label="Bonus / commission due" :value="old('bonus_commission_due','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="other_pay" label="Other pay due" :value="old('other_pay','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="federal_withholding" label="Federal withholding" :value="old('federal_withholding','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="state_local_withholding" label="State/local withholding" :value="old('state_local_withholding','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="other_deductions" label="Other deductions" :value="old('other_deductions','0.00')" /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Estimate final pay</button></div>
        </form>
    </x-tools.form-panel>
    @isset($calculationResult)
        <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Final pay estimate"><div class="row g-3">@foreach(($calculationResult['summary'] ?? []) as $item)<div class="col-md-6"><x-tools.result-metric :label="$item['label'] ?? ''" :value="$item['value'] ?? ''" :description="$item['description'] ?? null" /></div>@endforeach</div></x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
