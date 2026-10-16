@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <div class="alert alert-info">For covered, nonexempt employees, federal FLSA overtime is generally at least 1.5× the regular rate after 40 hours in a workweek. State rules can be more protective.</div>
    <x-tools.form-panel title="Weekly pay assumptions">
        <form method="POST" action="{{ route('tools.overtime-pay-calculator.calculate') }}" class="row g-3">@csrf
            <div class="col-md-3"><x-tools.form.input name="competence" label="Pay month" type="month" :value="old('competence','2026-10')" required /></div>
            <div class="col-md-3"><x-tools.form.money name="hourly_rate" label="Hourly rate" :value="old('hourly_rate')" required /></div>
            <div class="col-md-2"><x-tools.form.input name="regular_hours" label="Regular hours" type="number" min="0" max="40" step="0.001" :value="old('regular_hours','40')" required /></div>
            <div class="col-md-2"><x-tools.form.input name="overtime_hours" label="Overtime hours" type="number" min="0" step="0.001" :value="old('overtime_hours','0')" required /></div>
            <div class="col-md-2"><x-tools.form.input name="overtime_multiplier" label="Overtime multiplier" type="number" min="1.5" max="5" step="0.01" :value="old('overtime_multiplier','1.5')" required /></div>
            <div class="col-md-4"><x-tools.form.money name="nondiscretionary_bonus" label="Nondiscretionary bonus for the week" :value="old('nondiscretionary_bonus','0.00')" /></div>
            <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm_assumptions" @checked(old('confirm_assumptions')) required><label class="form-check-label" for="confirm_assumptions">I understand this is a general federal estimate and state rules may differ.</label></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate overtime pay</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
        <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Estimated weekly pay"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div><x-tools.normative-trust :rules="$result->calculationMemory?->normativeRules ?? []" :assumptions="$result->calculationMemory?->assumptions ?? []" :is-estimate="$result->calculationMemory?->isEstimate ?? true" /></x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
