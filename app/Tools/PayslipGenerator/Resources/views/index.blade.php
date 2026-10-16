@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.form-panel title="Pay stub details"><form method="POST" action="{{ route('tools.pay-stub-generator.calculate') }}" class="row g-3">@csrf
<div class="col-md-4"><x-tools.form.input name="employee_name" label="Employee name" :value="old('employee_name')" required /></div>
<div class="col-md-4"><x-tools.form.input name="employee_id" label="Employee ID" :value="old('employee_id')" required /></div>
<div class="col-md-4"><x-tools.form.input name="employer" label="Employer" :value="old('employer')" required /></div>
<div class="col-md-4"><x-tools.form.input name="pay_period" label="Pay period" :value="old('pay_period')" placeholder="e.g. Oct 1–15, 2026" required /></div>
@foreach(['regular_pay'=>'Regular pay','overtime_pay'=>'Overtime pay','other_earnings'=>'Other earnings','federal_income_tax'=>'Federal income tax','social_security'=>'Social Security','medicare'=>'Medicare','state_local_tax'=>'State/local tax','other_deductions'=>'Other deductions'] as $n=>$l)
<div class="col-md-4"><x-tools.form.money :name="$n" :label="$l" :value="old($n,$n==='regular_pay'?null:'0.00')" required /></div>
@endforeach
<div class="col-12"><button class="btn btn-primary">Generate pay stub</button></div></form></x-tools.form-panel>
@isset($result)
<span data-analytics-result="main" hidden></span>@php($d=$result->details['document'])
<x-tools.result-panel title="Pay stub"><div class="card"><div class="card-body"><div class="d-flex justify-content-between"><strong>{{ $d['employer'] }}</strong><span>{{ $d['pay_period'] }}</span></div><p>{{ $d['employee'] }} — {{ $d['employee_id'] }}</p><div class="table-responsive"><table class="table"><tbody>@foreach($d['items'] as $l=>$v)<tr><th>{{ $l }}</th><td class="text-end">{{ $v }}</td></tr>@endforeach</tbody><tfoot><tr><th>Net pay</th><th class="text-end">{{ $d['net'] }}</th></tr></tfoot></table></div></div></div><x-tools.export-buttons :pdf-route="route('tools.pay-stub-generator.export.pdf')" :excel-route="route('tools.pay-stub-generator.export.excel')" :input="$calculationInput ?? []" /></x-tools.result-panel>
@endisset
</x-tools.page>
@endsection
