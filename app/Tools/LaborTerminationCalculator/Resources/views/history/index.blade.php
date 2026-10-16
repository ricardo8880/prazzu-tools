@extends('layouts.app')
@section('title', 'Final Pay & Severance History | Prazzu Tools')
@section('meta_description', 'Review saved final-pay and severance estimates.')
@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('tools.final-pay-severance-calculator.index') }}">Final Pay & Severance Calculator</a></li><li class="breadcrumb-item active">History</li></ol></nav>
    <x-tools.intro icon="clock-history" tone="pink" title="Final pay history" description="Review, reuse, export, or delete saved estimates." />
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Saved</th><th>Separation type</th><th>Separation date</th><th class="text-end">Net final pay</th><th></th></tr></thead><tbody>
    @forelse($runs as $run)
        <tr><td>{{ $run->finishedAt?->format('M j, Y g:i A') }}</td><td>{{ ucfirst(str_replace('_', ' ', $run->result['separation_type'] ?? 'Other')) }}</td><td>{{ $run->referenceDate->format('M j, Y') }}</td><td class="text-end">{{ $run->result['net_final_pay'] ?? ($run->result['net_total'] ?? '—') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('tools.final-pay-severance-calculator.history.show', $run->id) }}">Details</a></td></tr>
    @empty
        <tr><td colspan="5"><div class="text-center py-4"><p class="text-body-secondary mb-3">No saved calculations yet.</p><a class="btn btn-primary" href="{{ route('tools.final-pay-severance-calculator.index') }}">Estimate final pay</a></div></td></tr>
    @endforelse
    </tbody></table></div>
    <div class="mt-3">{{ $runs->links() }}</div>
</div>
@endsection
