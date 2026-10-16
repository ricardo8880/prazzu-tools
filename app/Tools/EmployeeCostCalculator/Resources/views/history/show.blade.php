@extends('layouts.app')

@section('title', 'Saved employee cost calculation | Prazzu Tools')
@section('meta_description', 'Details of a saved employee cost calculation.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
@php
    $type = $run->result['calculation_type'] ?? 'single';
    $singleResult = $run->result['result'] ?? null;
    $batch = $run->result['batch'] ?? null;
@endphp
<div class="prazzu-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('tools.employee-cost-calculator.index') }}">Employee Cost Calculator</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.employee-cost-calculator.history.index') }}">History</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>

    <header class="prazzu-page-hero">
        <span class="prazzu-page-hero__icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
        <div>
            <span class="prazzu-eyebrow">{{ $type === 'batch' ? 'Batch calculation' : 'Individual calculation' }}</span>
            <h1>{{ $run->input['scenario_name'] ?? $run->input['employee_name'] ?? 'Saved calculation' }}</h1>
            <p>Generated on {{ $run->createdAt->format('M j, Y g:i A') }} with version {{ $run->toolVersion }}.</p>
        </div>
    </header>

    @if($singleResult)
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h4">Resumo</h2>
                <div class="row g-3">
                    @foreach($singleResult['summary'] ?? [] as $item)
                        <div class="col-12 col-md-6 col-xl-4">
                            <x-tools.result-metric :label="$item['label']" :value="$item['value']" :description="$item['description'] ?? null" />
                        </div>
                    @endforeach
                </div>
                <h3 class="h5 mt-4">Calculation details</h3>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                        @foreach($singleResult['calculation_memory']['steps'] ?? [] as $step)
                            <tr><th>{{ $step['label'] }}<div class="small fw-normal text-body-secondary">{{ $step['formula'] }}</div></th><td class="text-end">{{ is_int($step['result']) ? '$ '.number_format($step['result'] / 100, 2, '.', ',') : $step['result'] }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @elseif($batch)
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h4">Summary</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><x-tools.result-metric label="Employees" :value="$batch['employee_count']" /></div>
                    <div class="col-md-4"><x-tools.result-metric label="Monthly cost" :value="$batch['monthly_total']" /></div>
                    <div class="col-md-4"><x-tools.result-metric label="Annual cost" :value="$batch['annual_total']" /></div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Employee</th><th>Department</th><th class="text-end">Monthly</th><th class="text-end">Annual</th></tr></thead>
                        <tbody>
                        @foreach($batch['employees'] as $employee)
                            <tr><td>{{ $employee['name'] }}</td><td>{{ $employee['department'] }}</td><td class="text-end">{{ $employee['monthly_cost'] }}</td><td class="text-end">{{ $employee['annual_cost'] }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    <div class="d-flex flex-wrap gap-2">
        <form method="post" action="{{ route('tools.employee-cost-calculator.history.repeat', $run->id) }}">
            @csrf
            <button class="btn btn-primary" type="submit">Reuse data</button>
        </form>
        @if($singleResult)
            <a class="btn btn-outline-secondary" href="{{ route('tools.employee-cost-calculator.history.print', $run->id) }}">Print / PDF</a>
        @endif
        <form method="post" action="{{ route('tools.employee-cost-calculator.history.destroy', $run->id) }}" data-confirm="Delete this calculation?">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger" type="submit">Delete</button>
        </form>
    </div>
</div>
@endsection
