@extends('layouts.app')

@section('title', 'Cálculo de custo CLT salvo | Prazzu Tools')
@section('meta_description', 'Detalhes de um cálculo salvo de custo de funcionário CLT.')
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
            <li class="breadcrumb-item"><a href="{{ route('tools.custo-funcionario-clt.index') }}">Custo de funcionário CLT</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.custo-funcionario-clt.history.index') }}">Histórico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
        </ol>
    </nav>

    <header class="prazzu-page-hero">
        <span class="prazzu-page-hero__icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
        <div>
            <span class="prazzu-eyebrow">{{ $type === 'batch' ? 'Cálculo em lote' : 'Cálculo individual' }}</span>
            <h1>{{ $run->input['scenario_name'] ?? $run->input['employee_name'] ?? 'Cálculo salvo' }}</h1>
            <p>Gerado em {{ $run->createdAt->format('d/m/Y H:i') }} com a versão {{ $run->toolVersion }}.</p>
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
                <h3 class="h5 mt-4">Memória de cálculo</h3>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                        @foreach($singleResult['details']['memory'] ?? [] as $label => $value)
                            <tr><th>{{ $label }}</th><td class="text-end">{{ $value }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @elseif($batch)
        <section class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h4">Consolidado</h2>
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><x-tools.result-metric label="Funcionários" :value="$batch['employee_count']" /></div>
                    <div class="col-md-4"><x-tools.result-metric label="Custo mensal" :value="$batch['monthly_total']" /></div>
                    <div class="col-md-4"><x-tools.result-metric label="Custo anual" :value="$batch['annual_total']" /></div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Funcionário</th><th>Departamento</th><th class="text-end">Mensal</th><th class="text-end">Anual</th></tr></thead>
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
        <form method="post" action="{{ route('tools.custo-funcionario-clt.history.repeat', $run->id) }}">
            @csrf
            <button class="btn btn-primary" type="submit">Reutilizar dados</button>
        </form>
        @if($singleResult)
            <a class="btn btn-outline-secondary" href="{{ route('tools.custo-funcionario-clt.history.print', $run->id) }}">Imprimir / PDF</a>
        @endif
        <form method="post" action="{{ route('tools.custo-funcionario-clt.history.destroy', $run->id) }}" onsubmit="return confirm('Excluir este cálculo?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger" type="submit">Excluir</button>
        </form>
    </div>
</div>
@endsection
