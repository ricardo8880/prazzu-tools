@extends('layouts.app')

@section('title', 'Employee cost history | Prazzu Tools')
@section('meta_description', 'Review your saved employee cost calculations.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="prazzu-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.employee-cost-calculator.index') }}">Employee Cost Calculator</a></li>
            <li class="breadcrumb-item active" aria-current="page">History</li>
        </ol>
    </nav>

    <header class="prazzu-page-hero">
        <span class="prazzu-page-hero__icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
        <div>
            <span class="prazzu-eyebrow">Prazzu Plus</span>
            <h1>Employee cost history</h1>
            <p>Reopen, print, or delete calculations saved to your account.</p>
        </div>
    </header>

    @if(session('workspace_message'))
        <div class="alert alert-success" role="status">{{ session('workspace_message') }}</div>
    @endif

    <form method="get" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label" for="history-from">From</label>
                <input class="form-control" id="history-from" type="date" name="from" value="{{ request('from') }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="history-to">To</label>
                <input class="form-control" id="history-to" type="date" name="to" value="{{ request('to') }}">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Filtrar</button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.employee-cost-calculator.history.index') }}">Clear</a>
            </div>
        </div>
    </form>

    @if($runs->isEmpty())
        <x-tools.empty-state
            icon="clock-history"
            title="No saved calculations"
            description="Run a calculation while signed in to find it here."
        />
    @else
        <div class="table-responsive card border-0 shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Reference</th>
                    <th>Tipo</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($runs as $run)
                    @php($type = $run->result['calculation_type'] ?? 'single')
                    <tr>
                        <td>{{ $run->createdAt->format('M j, Y g:i A') }}</td>
                        <td>{{ $run->input['scenario_name'] ?? $run->input['employee_name'] ?? 'Unnamed calculation' }}</td>
                        <td>{{ $type === 'batch' ? 'Batch' : 'Individual' }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('tools.employee-cost-calculator.history.show', $run->id) }}">Open</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $runs->links() }}</div>
    @endif
</div>
@endsection
