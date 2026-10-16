@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<x-tools.form-panel title="Statement data"><form method="POST" action="{{ route('tools.earnings-statement-generator.calculate') }}" class="row g-3" data-analytics-form="statement">@csrf
<div class="col-md-4"><x-tools.form.input name="name" label="Employee / recipient" :value="old('name')" required /></div><div class="col-md-4"><x-tools.form.input name="employee_id" label="Employee / reference ID" :value="old('employee_id')" required /></div><div class="col-md-4"><x-tools.form.input name="payer" label="Employer / payer" :value="old('payer')" required /></div><div class="col-md-3"><x-tools.form.input name="year" label="Calendar year" type="number" :value="old('year',date('Y')-1)" required /></div>
@foreach(['gross'=>'Gross earnings','federal_income_tax'=>'Federal income tax','social_security'=>'Social Security','medicare'=>'Medicare','state_local_tax'=>'State/local tax','other_deductions'=>'Other deductions'] as $n=>$l)<div class="col-md-3"><x-tools.form.money :name="$n" :label="$l" :value="old($n,$n==='gross'?null:'0.00')" required /></div>@endforeach
<div class="col-12"><button class="btn btn-primary">Generate statement</button></div></form></x-tools.form-panel>
@isset($result)@php($d=$result->details['document'])<x-tools.result-panel title="Annual Earnings Statement" data-analytics-result="statement"><div class="card"><div class="card-body"><h3 class="text-center">{{ $d['title'] }}</h3><p>{{ $d['declaration'] }}</p><div class="table-responsive"><table class="table"><tr><th>Gross earnings</th><td>{{ $d['gross'] }}</td></tr><tr><th>Federal income tax</th><td>{{ $d['federal_income_tax'] }}</td></tr><tr><th>Social Security</th><td>{{ $d['social_security'] }}</td></tr><tr><th>Medicare</th><td>{{ $d['medicare'] }}</td></tr><tr><th>State/local tax</th><td>{{ $d['state_local_tax'] }}</td></tr><tr><th>Other deductions</th><td>{{ $d['other'] }}</td></tr><tr><th>Net earnings</th><td>{{ $d['net'] }}</td></tr></table></div></div></div><div class="alert alert-warning mt-4">{{ implode(' ', $d['notice']['limitations']) }}</div></x-tools.result-panel>@endisset
</x-tools.page>
@endsection
