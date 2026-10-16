@extends('layouts.app')
@section('title', 'Take-Home Pay History')
@section('content')
<div class="container py-4" data-plus-feature="history">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div><h1 class="h3 mb-1">Take-Home Pay History</h1><p class="text-body-secondary mb-0">Calculations saved to your Prazzu Plus account.</p></div>
        <a class="btn btn-outline-primary" href="{{ route('tools.take-home-pay-calculator.index') }}">New calculation</a>
    </div>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="vstack gap-3">
        @forelse($runs as $run)
            <article class="card"><div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div><strong>{{ $run->referenceDate->format('M j, Y') }}</strong><div class="small text-body-secondary">Rule version: {{ $run->ruleVersion->value }}</div></div>
                <div class="d-flex flex-wrap gap-2">
                    <form method="post" action="{{ route('tools.take-home-pay-calculator.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-primary">Reuse</button></form>
                    <form method="post" action="{{ route('tools.take-home-pay-calculator.history.destroy', $run->id) }}" data-confirm="Delete this calculation?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                </div>
            </div></article>
        @empty
            <div class="alert alert-light border">No saved calculations ainda.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $runs->links() }}</div>
</div>
@endsection
