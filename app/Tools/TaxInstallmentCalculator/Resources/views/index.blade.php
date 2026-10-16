@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.validation-summary />
<form method="post" action="{{ route('tools.irs-payment-plan-calculator.calculate') }}" class="d-grid gap-4" data-analytics-form="main">@csrf
<x-tools.form-panel title="Tax balance and payment assumptions" description="Estimate a payment schedule using a monthly combined interest/penalty rate that you supply.">
<div class="row g-3">
<div class="col-md-3"><x-tools.form.money name="debt_amount" label="Tax balance" :value="old('debt_amount')" required /></div>
<div class="col-md-3"><x-tools.form.money name="entry_amount" label="Upfront payment" :value="old('entry_amount','0.00')" /></div>
<div class="col-md-3"><x-tools.form.input name="installments" label="Monthly payments" type="number" min="1" max="72" :value="old('installments','24')" required /></div>
<div class="col-md-3"><x-tools.form.input name="monthly_charge" label="Estimated monthly interest + penalty rate" type="number" min="0" max="100" step="0.0001" suffix="%" :value="old('monthly_charge','0')" required /></div>
</div>
<div class="alert alert-info mt-3 mb-0">This is an estimator, not an IRS approval or exact penalty/interest calculation.</div>
</x-tools.form-panel>
<div><button class="btn btn-primary">Estimate payment plan</button></div>
</form>
@isset($result)<span data-analytics-result="main" hidden></span><x-tools.result-panel title="Estimated payment plan"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>@foreach($result->warnings as $warning)<div class="alert alert-light border mt-3">{{ $warning->message }}</div>@endforeach</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
