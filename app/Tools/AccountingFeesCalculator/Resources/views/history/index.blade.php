@extends('layouts.app')

@section('title', 'Accounting Fee History — Prazzu Tools')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.accounting-fee-calculator.index') }}">Accounting Fees</a></li>
            <li class="breadcrumb-item active">History</li>
        </ol>
    </nav>

    <x-tools.intro icon="clock-history" title="Calculation history" description="Review past pricing estimates and mark important references for later reuse.">
        <x-slot:actions>
            <a class="btn btn-outline-secondary" href="{{ route('tools.accounting-fee-calculator.history.index', ['favorite' => $favorite ? null : 1]) }}"><i class="bi bi-star{{ $favorite ? '-fill' : '' }} me-1"></i>{{ $favorite ? 'View all' : 'Favorites only' }}</a>
            <x-tools.export-button :href="route('tools.accounting-fee-calculator.history.export')" label="Export CSV" icon="file-earmark-spreadsheet" />
            <a class="btn btn-primary" href="{{ route('tools.accounting-fee-calculator.index') }}"><i class="bi bi-plus-lg me-1"></i>New calculation</a>
        </x-slot:actions>
    </x-tools.intro>

    @include('tools-accounting-fee-calculator::partials.navigation')

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    <section class="prazzu-tool-workspace text-start">
        @forelse ($calculations as $calculation)
            <article class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge text-bg-light border">{{ $calculation->finishedAt->format('M j, Y g:i A') }}</span>
                                @if ($calculation->favorite)<span class="badge text-bg-warning"><i class="bi bi-star-fill me-1"></i>Favorite</span>@endif
                            </div>
                            <h2 class="h5 mb-1">Recommended fee: {{ data_get($calculation->result, 'recommended_fee') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ data_get($calculation->input, 'tax_regime') }} · {{ data_get($calculation->input, 'employees', 0) }} employee(s) · complexity {{ data_get($calculation->result, 'complexity_level') }}
                            </p>
                        </div>
                        <div class="text-lg-end">
                            <div class="small text-body-secondary">Suggested range</div>
                            <div class="fw-semibold">{{ data_get($calculation->result, 'minimum_fee') }} a {{ data_get($calculation->result, 'upper_reference_fee') }}</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <form method="post" action="{{ route('tools.accounting-fee-calculator.history.duplicate', $calculation->id) }}">@csrf<button class="btn btn-sm btn-primary"><i class="bi bi-copy me-1"></i>Duplicate</button></form>
                        <form method="post" action="{{ route('tools.accounting-fee-calculator.history.favorite', $calculation->id) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning"><i class="bi bi-star me-1"></i>{{ $calculation->favorite ? 'Unfavorite' : 'Favorite' }}</button></form>
                        <form method="post" action="{{ route('tools.accounting-fee-calculator.history.delete', $calculation->id) }}" data-confirm="Remove this calculation from history?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button></form>
                    </div>
                </div>
            </article>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-clock-history display-5 text-body-secondary"></i>
                <h2 class="h5 mt-3">No calculations found</h2>
                <p class="text-body-secondary">Run a pricing estimate to start your history.</p>
                <a class="btn btn-primary" href="{{ route('tools.accounting-fee-calculator.index') }}">Calculate fees</a>
            </div>
        @endforelse

        {{ $calculations->links() }}
    </section>
</div>
@endsection
