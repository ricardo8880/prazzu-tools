@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <x-tools.form-panel title="Salary increase inputs" description="Enter the percentage increase and any fixed additional increase.">
        <form method="POST" action="{{ route('tools.salary-increase-calculator.calculate') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6"><x-tools.form.money name="current_salary" label="Salary atual" :value="old('current_salary')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="adjustment_rate" label="Increase percentage" type="number" step="0.000001" min="0" max="100" suffix="%" :value="old('adjustment_rate')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.money name="fixed_addition" label="Additional fixed increase" :value="old('fixed_addition')" required /></div>
            <div class="col-12 col-md-6"><x-tools.form.input name="retroactive_months" label="Retroactive months" type="number" min="0" max="60" :value="old('retroactive_months')" required /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate increase</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
    <span data-analytics-result="main" hidden></span>
        <x-tools.result-panel title="Salary increase result">
            <div class="row g-3">@foreach($result->summary as $item)<div class="col-12 col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div>
            <h3 class="h5 mt-4">Calculation details</h3>
            <div class="table-responsive"><table class="table table-sm mb-0"><tbody>@foreach(($result->calculationMemory?->steps ?? []) as $step)<tr><th>{{ $step->label }}<div class="small fw-normal text-body-secondary">{{ $step->formula }}</div></th><td class="text-end">{{ is_int($step->result) ? '$'.number_format($step->result / 100, 2, '.', ',') : $step->result }}</td></tr>@endforeach</tbody></table></div>
            <div class="alert alert-info mt-3 mb-0">This estimate does not apply employer policies, collective bargaining terms, or state/local requirements.</div>
        <x-tools.export-buttons :pdf-route="route('tools.salary-increase-calculator.export.pdf')" :excel-route="route('tools.salary-increase-calculator.export.excel')" :input="$calculationInput ?? []" />
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
