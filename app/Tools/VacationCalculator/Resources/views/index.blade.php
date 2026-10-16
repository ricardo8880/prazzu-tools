@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <div class="alert alert-info">U.S. federal law generally does not require paid vacation. PTO accrual and payout depend on employer policy, contract, and state law.</div>
    <x-tools.form-panel title="PTO assumptions">
        <form method="POST" action="{{ route('tools.pto-calculator.calculate') }}" class="row g-3">@csrf
            <div class="col-md-3"><x-tools.form.money name="hourly_rate" label="Hourly rate" :value="old('hourly_rate')" required /></div>
            <div class="col-md-3"><x-tools.form.input name="accrued_pto_hours" label="Current accrued PTO hours" type="number" min="0" step="0.001" :value="old('accrued_pto_hours','0')" required /></div>
            <div class="col-md-3"><x-tools.form.input name="planned_pto_hours" label="Planned PTO hours" type="number" min="0" step="0.001" :value="old('planned_pto_hours','0')" required /></div>
            <div class="col-md-3"><x-tools.form.input name="payout_percent" label="Payout percentage" type="number" min="0" max="100" step="0.01" suffix="%" :value="old('payout_percent','100')" required /></div>
            <div class="col-md-4"><x-tools.form.input name="accrual_hours_per_pay_period" label="PTO accrued per pay period" type="number" min="0" step="0.001" :value="old('accrual_hours_per_pay_period','0')" /></div>
            <div class="col-md-4"><x-tools.form.input name="pay_periods_before_leave" label="Pay periods before leave" type="number" min="0" step="1" :value="old('pay_periods_before_leave','0')" /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate PTO</button></div>
        </form>
    </x-tools.form-panel>
    @isset($calculationResult)
        <span data-analytics-result="main" hidden></span><x-tools.result-panel title="PTO estimate"><div class="row g-3">@foreach(($calculationResult['summary'] ?? []) as $item)<div class="col-md-6"><x-tools.result-metric :label="$item['label'] ?? ''" :value="$item['value'] ?? ''" :description="$item['description'] ?? null" /></div>@endforeach</div></x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
