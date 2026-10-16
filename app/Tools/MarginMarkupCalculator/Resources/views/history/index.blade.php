@extends('layouts.app')
@section('title', 'Margin & Markup History — Prazzu Tools')
@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('tools.margin-markup-calculator.index') }}">Margin & Markup</a></li><li class="breadcrumb-item active">History</li></ol></nav>
    <x-tools.intro icon="clock-history" title="Calculation history" description="Review single calculations, product batches, and saved pricing scenarios.">
        <x-slot:actions><a class="btn btn-primary" href="{{ route('tools.margin-markup-calculator.index') }}"><i class="bi bi-plus-lg me-1"></i>New calculation</a></x-slot:actions>
    </x-tools.intro>

    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif

    <section class="prazzu-tool-card mb-4">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-12 col-md-4"><label class="form-label" for="from">Start date</label><input class="form-control" id="from" type="date" name="from" value="{{ request('from') }}"></div>
            <div class="col-12 col-md-4"><label class="form-label" for="to">End date</label><input class="form-control" id="to" type="date" name="to" value="{{ request('to') }}"></div>
            <div class="col-12 col-md-4 d-flex gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button><a class="btn btn-outline-secondary" href="{{ route('tools.margin-markup-calculator.history.index') }}">Clear</a></div>
        </form>
    </section>

    <section class="prazzu-tool-card">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>Completed</th><th>Type</th><th>Reference</th><th>Summary</th><th>Rule</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($runs as $run)
                @php
                    $type = $run->result['calculation_type'] ?? $run->input['calculation_type'] ?? 'single';
                @endphp
                @php
                    $labels = ['single'=>'Individual','batch'=>'Product batch','scenarios'=>'Scenarios'];
                @endphp
                @php
                    $summary = $type === 'single' ? ($run->result['sale_price'] ?? '—') : count($run->result['results'] ?? []).' item(ns)';
                @endphp
                <tr>
                    <td>{{ $run->finishedAt?->format('M j, Y g:i A') }}</td><td><span class="badge text-bg-light">{{ $labels[$type] ?? ucfirst($type) }}</span></td><td>{{ $run->referenceDate->format('M j, Y') }}</td><td class="fw-semibold">{{ $summary }}</td><td>{{ $run->ruleVersion }}</td>
                    <td class="text-end"><x-tools.history-actions
                        :show-url="route('tools.margin-markup-calculator.history.show', $run->id)"
                        :repeat-url="route('tools.margin-markup-calculator.history.repeat', $run->id)"
                        :delete-url="route('tools.margin-markup-calculator.history.destroy', $run->id)"
                    /></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5"><x-tools.empty-state icon="clock-history" title="No saved calculations" description="Sign in and run a calculation to start your history." /></td></tr>
            @endforelse
            </tbody>
        </table></div>
        @if($runs->hasPages())<div class="mt-4">{{ $runs->links() }}</div>@endif
    </section>
    <div class="alert alert-secondary mt-4 mb-0"><i class="bi bi-shield-lock me-1"></i>Saved data is encrypted and follows the retention period configured for this tool.</div>
</div>
@endsection
