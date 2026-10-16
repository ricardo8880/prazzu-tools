@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route('tools.owner-distribution-calculator.index'))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
<form method="post" action="{{ route('tools.owner-distribution-calculator.calculate') }}" class="row g-3">@csrf
<div class="col-md-4"><x-tools.form.input name="partner_label" label="Owner" :value="old('partner_label')" placeholder="e.g. Owner 1" /></div>
<div class="col-md-4"><x-tools.form.input name="ownership_percentage" label="Ownership" type="number" min="0.000001" max="100" step="0.000001" suffix="%" :value="old('ownership_percentage')" required /></div>
<div class="col-md-4"><x-tools.form.money name="accounting_profit" label="Book profit / retained amount available" :value="old('accounting_profit')" required /></div>
<div class="col-md-4"><x-tools.form.money name="accumulated_losses" label="Accumulated losses" :value="old('accumulated_losses','0.00')" /></div>
<div class="col-md-4"><x-tools.form.money name="reserves_and_unavailable_amounts" label="Restricted / unavailable reserves" :value="old('reserves_and_unavailable_amounts','0.00')" /></div>
<div class="col-md-4"><x-tools.form.money name="adjustments" label="Accounting adjustments" :value="old('adjustments','0.00')" /></div>
<div class="col-md-4"><x-tools.form.money name="prior_distributions" label="Prior distributions" :value="old('prior_distributions','0.00')" /></div>
<div class="col-md-4"><x-tools.form.money name="intended_distribution" label="Planned distribution" :value="old('intended_distribution')" required /></div>
@if($plusEnabled ?? true)<div class="col-12"><div class="card border-primary-subtle"><div class="card-body"><h2 class="h6">Additional owners <span class="badge text-bg-primary">Plus</span></h2><p class="small text-body-secondary">Ownership percentages must total 100%.</p>@for($i=0;$i<4;$i++)<div class="row g-2 mb-2"><div class="col-md-8"><input class="form-control" name="partners[{{ $i }}][label]" value="{{ old('partners.'.$i.'.label') }}" placeholder="Owner {{ $i+2 }}"></div><div class="col-md-4"><div class="input-group"><input class="form-control" name="partners[{{ $i }}][ownership_percentage]" value="{{ old('partners.'.$i.'.ownership_percentage') }}"><span class="input-group-text">%</span></div></div></div>@endfor</div></div></div>@endif
<div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="confirm_assumptions" value="1" id="confirm-profit" required><label class="form-check-label" for="confirm-profit">I reviewed the ownership, accounting balances, and distribution assumptions.</label></div>
<div class="col-12"><button class="btn btn-primary">Calculate distribution</button></div>
</form>
@if($errors->any())<div class="alert alert-danger mt-3">{{ $errors->first() }}</div>@endif
@isset($result)<span data-analytics-result="main" hidden></span><x-tools.result-panel title="Distribution estimate"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" /></div>@endforeach</div>@foreach($result->warnings as $warning)<div class="alert alert-light border mt-3">{{ $warning->message }}</div>@endforeach@if(count($result->details['partners']??[])>1)<div class="table-responsive mt-4"><table class="table table-sm"><thead><tr><th>Owner</th><th class="text-end">Ownership</th><th class="text-end">Distribution</th></tr></thead><tbody>@foreach($result->details['partners'] as $partner)<tr><td>{{ $partner['label'] }}</td><td class="text-end">{{ $partner['ownership_percentage'] }}%</td><td class="text-end">{{ \App\Core\Money\Money::fromMinor($partner['distributed_amount_minor'])->formatEnUs() }}</td></tr>@endforeach</tbody></table></div>@endif</x-tools.result-panel>@endisset
</x-tools.page>
@endsection
