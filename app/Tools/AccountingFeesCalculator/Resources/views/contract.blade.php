@extends('layouts.app')
@section('title', 'Accounting Services Agreement | Prazzu Tools')
@section('meta_description', 'Editable accounting services agreement template for review and printing.')
@section('content')
<div class="prazzu-page tool-page" data-tool="accounting-fee-calculator-agreement">
    <div class="d-print-none mb-3 d-flex flex-column flex-sm-row justify-content-between gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('tools.accounting-fee-calculator.index') }}"><i class="bi bi-arrow-left me-1"></i>Back to calculator</a>
        <x-tools.print-button label="Print agreement" />
    </div>
    <div class="alert alert-warning d-print-none" role="alert"><i class="bi bi-exclamation-triangle me-1"></i>This is a general template, not legal advice. Review state-specific terms and have qualified counsel review the agreement before signing.</div>
    <article class="card border-0 shadow-sm"><div class="card-body p-4 p-lg-5">
        <header class="text-center border-bottom pb-4 mb-4"><span class="text-uppercase small fw-semibold text-primary">Service agreement template</span><h1 class="h2 mt-2 mb-0">Accounting Services Agreement</h1></header>
        <p><strong>ACCOUNTING FIRM:</strong> {{ $contract->accountingFirm }}@if($contract->accountingFirmDocument), ID/EIN {{ $contract->accountingFirmDocument }}@endif, represented by {{ $contract->accountingRepresentative }}.</p>
        <p><strong>CLIENT:</strong> {{ $contract->clientCompany }}@if($contract->clientDocument), ID/EIN {{ $contract->clientDocument }}@endif, represented by {{ $contract->clientRepresentative }}.</p>
        <section class="mb-4"><h2 class="h5">1. Services</h2><p>The accounting firm will provide the recurring services selected below:</p><ul>@foreach($contract->services as $service)<li>{{ $service }}</li>@endforeach</ul><p class="mb-0">Work outside this scope requires separate approval and may require additional fees.</p></section>
        <section class="mb-4"><h2 class="h5">2. Client responsibilities</h2><p class="mb-0">The client will provide complete and accurate records on time, disclose material changes, review deliverables, and remain responsible for business decisions and filings that require client approval.</p></section>
        <section class="mb-4"><h2 class="h5">3. Fees and payment</h2><p>Monthly fees are <strong>{{ $contract->monthlyFee->formatEnUs() }}</strong>, due on day <strong>{{ $contract->dueDay }}</strong> of each month.</p><p class="mb-0">A late fee of up to <strong>{{ $contract->lateFeePercent }}%</strong> may apply only to the extent permitted by the agreement and applicable law.</p></section>
        <section class="mb-4"><h2 class="h5">4. Annual fee adjustment</h2><p class="mb-0">The parties selected <strong>{{ $contract->adjustmentIndex }}</strong> as the annual fee-adjustment method. Any adjustment remains subject to the signed agreement and applicable law.</p></section>
        <section class="mb-4"><h2 class="h5">5. Term and termination</h2><p>The initial term runs for {{ $contract->durationMonths }} months, from <strong>{{ $contract->startsAt->format('M j, Y') }}</strong> through <strong>{{ $contract->endsAt->format('M j, Y') }}</strong>.</p><p class="mb-0">Either party may terminate according to the signed agreement with at least <strong>{{ $contract->terminationNoticeDays }} days</strong> written notice, subject to payment for services already performed and any applicable state-law requirements.</p></section>
        @if($contract->includesDataPrivacy)<section class="mb-4"><h2 class="h5">6. Data privacy and security</h2><p class="mb-0">Each party will use reasonable safeguards for confidential and personal information and comply with privacy and security obligations applicable to the services and data involved.</p></section>@endif
        @if($contract->includesConfidentiality)<section class="mb-4"><h2 class="h5">Confidentiality</h2><p class="mb-0">Each party will protect nonpublic business, financial, tax, payroll, and personal information received in connection with the engagement, except when disclosure is authorized or legally required.</p></section>@endif
        @if($contract->additionalTerms)<section class="mb-4"><h2 class="h5">Additional terms</h2><p class="mb-0">{{ $contract->additionalTerms }}</p></section>@endif
        <section class="mb-4"><h2 class="h5">Governing law</h2><p class="mb-0">The parties should specify the governing state, venue, dispute process, professional standards, limitation-of-liability terms, and any required engagement-letter provisions after professional review.</p></section>
        <p class="text-center mb-5">Generated {{ $contract->issuedAt->format('M j, Y') }}.</p>
        <div class="row g-5 pt-4"><div class="col-md-6 text-center"><div class="border-top pt-2">{{ $contract->clientRepresentative }}<br><small class="text-body-secondary">Client</small></div></div><div class="col-md-6 text-center"><div class="border-top pt-2">{{ $contract->accountingRepresentative }}<br><small class="text-body-secondary">Accounting firm</small></div></div></div>
    </div></article>
</div>
@endsection
