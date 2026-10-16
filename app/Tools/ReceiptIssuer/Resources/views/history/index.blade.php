@extends('layouts.app')

@section('title', 'Receipt History — Prazzu Tools')
@section('meta_description', 'Review, reuse, export, or delete receipts saved to your Prazzu account.')

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Receipt history</h1>
                <p class="text-body-secondary mb-0">Reuse data, issue another copy, or delete saved receipts.</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('tools.receipt-generator.index') }}">Issue new receipt</a>
        </div>

        @if(session('history_message'))
            <div class="alert alert-success">{{ session('history_message') }}</div>
        @endif

        <div class="vstack gap-3">
            @forelse($runs as $run)
                @php($receipt = $run->result['details']['receipt'] ?? [])
                <article class="card shadow-sm">
                    <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <strong>Receipt No. {{ $receipt['number'] ?? ($run->input['number'] ?? '—') }}</strong>
                                <span class="badge text-bg-light">{{ $receipt['amount'] ?? 'Amount unavailable' }}</span>
                            </div>
                            <div>{{ $receipt['payer']['name'] ?? ($run->input['payer_name'] ?? 'Payer not provided') }} → {{ $receipt['payee']['name'] ?? ($run->input['payee_name'] ?? 'Payee not provided') }}</div>
                            <div class="small text-body-secondary mt-1">
                                Issued {{ $run->referenceDate->format('M j, Y') }} · saved {{ $run->createdAt->format('M j, Y g:i A') }}
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <form method="POST" action="{{ route('tools.receipt-generator.history.repeat', $run->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary" type="submit">Reuse</button>
                            </form>
                            <a class="btn btn-sm btn-outline-success" href="{{ route('tools.receipt-generator.history.export.pdf', $run->id) }}" target="_blank">Export PDF</a>
                            <form method="POST" action="{{ route('tools.receipt-generator.history.destroy', $run->id) }}" data-confirm="Delete this receipt from history?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="alert alert-light border mb-0">No saved receipts yet.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $runs->links() }}</div>
    </div>
@endsection
