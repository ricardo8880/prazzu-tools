@extends('layouts.app')
@section('title', $tool->name.' | Prazzu Tools')
@section('meta_description', $tool->description)
@section('canonical_url', route($tool->routeName))
@section('content')
<x-tools.page :title="$tool->name" :description="$tool->description" :icon="$tool->icon" :slug="$tool->slug">
    <div class="alert alert-info">IRS backup withholding is generally 24% when it applies. This calculator does not decide whether a payment is subject to backup withholding.</div>
    <x-tools.form-panel title="Payment">
        <form method="POST" action="{{ route('tools.backup-withholding-calculator.calculate') }}" class="row g-3">@csrf
            <div class="col-md-3"><x-tools.form.input name="competence" label="Payment month" type="month" :value="old('competence','2026-10')" required /></div>
            <div class="col-md-3"><x-tools.form.input name="payment_reference" label="Reference" :value="old('payment_reference')" /></div>
            <div class="col-md-6"><x-tools.form.input name="payment_description" label="Payment description" :value="old('payment_description')" required /></div>
            <div class="col-md-4"><x-tools.form.money name="gross_value" label="Gross payment" :value="old('gross_value')" required /></div>
            <div class="col-md-4"><x-tools.form.input name="backup_withholding_rate" label="Backup withholding rate" type="number" step="0.01" suffix="%" :value="old('backup_withholding_rate','24')" required /></div>
            <div class="col-md-4 form-check mt-4 ms-2"><input class="form-check-input" type="checkbox" name="apply_backup_withholding" value="1" id="apply_backup_withholding" @checked(old('apply_backup_withholding'))><label class="form-check-label" for="apply_backup_withholding">Apply backup withholding</label></div>
            <div class="col-md-4"><x-tools.form.money name="state_withholding" label="State withholding, if any" :value="old('state_withholding','0.00')" /></div>
            <div class="col-md-4"><x-tools.form.money name="other_withholding" label="Other withholding" :value="old('other_withholding','0.00')" /></div>
            <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="confirm_scope" value="1" id="confirm_scope" @checked(old('confirm_scope')) required><label class="form-check-label" for="confirm_scope">I reviewed whether backup withholding actually applies to this payment.</label></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Calculate withholding</button></div>
        </form>
    </x-tools.form-panel>
    @isset($result)
        <span data-analytics-result="main" hidden></span><x-tools.result-panel title="Withholding estimate"><div class="row g-3">@foreach($result->summary as $item)<div class="col-md-6"><x-tools.result-metric :label="$item->label" :value="$item->value" :description="$item->description" /></div>@endforeach</div><x-tools.normative-trust :rules="$result->calculationMemory?->normativeRules ?? []" :assumptions="$result->calculationMemory?->assumptions ?? []" :is-estimate="$result->calculationMemory?->isEstimate ?? true" /></x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
