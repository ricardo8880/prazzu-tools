@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.form-panel title="Distribution planning inputs">
<form method="POST" action="{{ route('tools.owner-distribution-planner.calculate') }}" class="row g-3">@csrf
<div class="col-md-4"><x-tools.form.money name="retained_earnings" label="Retained earnings" :value="old('retained_earnings')" required /></div>
<div class="col-md-4"><x-tools.form.money name="current_year_net_income" label="Current-year net income" :value="old('current_year_net_income')" required /></div>
<div class="col-md-4"><x-tools.form.money name="prior_distributions" label="Prior distributions" :value="old('prior_distributions','0')" /></div>
<div class="col-md-4"><x-tools.form.money name="cash_available" label="Cash available" :value="old('cash_available')" required /></div>
<div class="col-md-4"><x-tools.form.money name="minimum_cash_reserve" label="Minimum cash reserve" :value="old('minimum_cash_reserve')" required /></div>
<div class="col-md-4"><x-tools.form.money name="planned_distribution" label="Planned distribution" :value="old('planned_distribution','0')" /></div>
@if($plusEnabled ?? false)
<div class="col-md-4"><x-tools.form.money name="monthly_net_income" label="Projected monthly net income" :value="old('monthly_net_income','0')" /></div>
<div class="col-md-4"><x-tools.form.input name="monthly_growth_rate" label="Monthly growth" type="number" step="0.01" suffix="%" :value="old('monthly_growth_rate','0')" /></div>
<div class="col-md-4"><x-tools.form.input name="planning_months" label="Planning months" type="number" min="1" max="24" :value="old('planning_months','12')" /></div>
@endif
<div class="col-12"><button class="btn btn-primary" type="submit">Calculate distribution capacity</button></div>
</form></x-tools.form-panel>
@isset($result)
<span data-analytics-result="main" hidden></span><x-tools.result-panel title="Planning result">
<div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
@foreach($result->warnings as $warning)<div class="alert alert-light border mt-3">{{ $warning->message }}</div>@endforeach
<x-tools.export-buttons :pdf-route="route('tools.owner-distribution-planner.export',['format'=>'pdf'])" :excel-route="route('tools.owner-distribution-planner.export',['format'=>'xlsx'])" :input="$calculationInput ?? []" />
</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
