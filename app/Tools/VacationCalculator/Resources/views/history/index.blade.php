@extends('layouts.app')
@section('title', 'PTO Calculation History')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">PTO calculation history</h1><p class="text-body-secondary mb-0">Reuse, export, or delete saved PTO estimates.</p></div>
        <a class="btn btn-outline-primary" href="{{ route('tools.pto-calculator.index') }}">New calculation</a>
    </div>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="vstack gap-3">
    @forelse($runs as $run)
        <article class="card"><div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <strong>{{ $run->input['planned_pto_hours'] ?? '0' }} planned PTO hours</strong>
                <div class="small text-body-secondary">Hourly rate: {{ isset($run->input['hourly_rate']) ? '$'.number_format((float) $run->input['hourly_rate'], 2, '.', ',') : '—' }} · saved {{ $run->createdAt->format('M j, Y g:i A') }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <form method="post" action="{{ route('tools.pto-calculator.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-primary">Reuse</button></form>
                @foreach(['csv'=>'CSV','json'=>'JSON','pdf'=>'PDF','xlsx'=>'XLSX'] as $format=>$label)<a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.pto-calculator.history.export', [$run->id,$format]) }}">{{ $label }}</a>@endforeach
                <form method="post" action="{{ route('tools.pto-calculator.history.destroy', $run->id) }}" data-confirm="Delete this calculation?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
            </div>
        </div></article>
    @empty<div class="alert alert-light border">No saved PTO calculations yet.</div>@endforelse
    </div><div class="mt-4">{{ $runs->links() }}</div>
</div>
@endsection
