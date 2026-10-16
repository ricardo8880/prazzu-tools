@extends('layouts.app')

@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))

@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.form-panel title="Hiring costs"><form method="POST" action="{{route('tools.new-hire-cost-calculator.calculate')}}" class="row g-3">@csrf
@foreach(['salary'=>'Salary','benefits'=>'Monthly benefits','exam'=>'Screening / onboarding checks','recruitment'=>'Recruiting','equipment'=>'Equipment','training'=>'Training'] as $n=>$l)<div class="col-md-4"><x-tools.form.money :name="$n" :label="$l" :value="old($n)" required/></div>@endforeach<div class="col-md-4"><x-tools.form.input name="monthly_burden" label="Monthly employer costs" type="number" step="0.000001" suffix="%" :value="old('monthly_burden')" required placeholder="Ex.: 42" /></div><div class="col-12"><button class="btn btn-primary">Estimate hiring cost</button></div></form></x-tools.form-panel>
@isset($result)
    <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Hiring cost"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value"/></div>@endforeach</div><h3 class="h5 mt-4">Checklist</h3><ul class="list-group">@foreach($result->details['checklist'] as $item)<li class="list-group-item"><input class="form-check-input me-2" type="checkbox"> {{$item}}</li>@endforeach</ul><x-tools.export-buttons :pdf-route="route('tools.new-hire-cost-calculator.export.pdf')" :excel-route="route('tools.new-hire-cost-calculator.export.excel')" :input="$calculationInput ?? []" /></x-tools.result-panel>@endisset</x-tools.page>
@endsection
