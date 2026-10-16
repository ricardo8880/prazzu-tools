@extends('layouts.app')
@section('title', 'EIN Format Validator | Prazzu Tools')
@section('meta_description', 'Check whether a U.S. Employer Identification Number matches the basic nine-digit EIN format.')
@section('canonical_url', route('tools.us-tax-id-validator.index'))
@section('content')
<x-tools.page title="EIN Format Validator" description="Check the basic format of a U.S. Employer Identification Number (EIN)." icon="bi-shield-check" slug="us-tax-id-validator">
    <x-tools.form-panel title="EIN">
        <form method="POST" action="{{ route('tools.us-tax-id-validator.validate') }}" class="row g-3">@csrf
            <div class="col-md-6"><x-tools.form.input name="document_number" label="Employer Identification Number" :value="old('document_number')" required placeholder="12-3456789" /></div>
            <div class="col-12"><button class="btn btn-primary" type="submit">Validate format</button></div>
        </form>
    </x-tools.form-panel>
    @isset($validationResult)
        <x-tools.result-panel title="Validation result">
            <div class="alert {{ ($validationResult['valid'] ?? false) ? 'alert-success' : 'alert-warning' }} mb-3">
                <strong>{{ ($validationResult['valid'] ?? false) ? 'Valid EIN format' : 'Invalid EIN format' }}</strong>
                @if(!empty($validationResult['formatted']))<div class="mt-1">{{ $validationResult['formatted'] }}</div>@endif
            </div>
            @if(!empty($validationResult['messages']))<ul class="mb-0">@foreach($validationResult['messages'] as $message)<li>{{ $message }}</li>@endforeach</ul>@endif
            <div class="small text-body-secondary mt-3">Format validation does not confirm that the IRS issued the EIN or that it belongs to a specific business.</div>
        </x-tools.result-panel>
    @endisset
</x-tools.page>
@endsection
