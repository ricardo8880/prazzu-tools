@extends('layouts.app')

@section('title', 'Cálculo salvo — '.$tool->name.' | Prazzu Tools')
@section('meta_description', 'Detalhes de um cálculo salvo em '.$tool->name.'.')
@section('canonical_url', route($routePrefix.'.history.show', ['run' => $entry->id]))

@section('content')
<div class="prazzu-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route($tool->routeName) }}">{{ $tool->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route($routePrefix.'.history.index') }}">Histórico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
        </ol>
    </nav>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Prazzu Plus</span>
            <h1 class="h2 mb-1">{{ $contextLabel ? $tool->name.' — '.$contextLabel : 'Cálculo salvo' }}</h1>
            <p class="text-body-secondary mb-0">Referência {{ $entry->referenceDate->format('d/m/Y') }} · regra {{ $entry->ruleVersion }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="post" action="{{ route($routePrefix.'.history.repeat', ['run' => $entry->id]) }}">
                @csrf
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-arrow-repeat me-1"></i>Repetir</button>
            </form>
            <form method="post" action="{{ route($routePrefix.'.history.duplicate', ['run' => $entry->id]) }}">
                @csrf
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-copy me-1"></i>Duplicar</button>
            </form>
            <a class="btn btn-outline-secondary" href="{{ route($routePrefix.'.history.export', ['run' => $entry->id, 'format' => 'csv']) }}">CSV</a>
            <a class="btn btn-outline-secondary" href="{{ route($routePrefix.'.history.export', ['run' => $entry->id, 'format' => 'xlsx']) }}">XLSX</a>
            <a class="btn btn-primary" target="_blank" rel="noopener" href="{{ route($routePrefix.'.history.export', ['run' => $entry->id, 'format' => 'pdf']) }}">Imprimir / PDF</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Entradas</h2>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach ($entry->input as $key => $value)
                                    <tr>
                                        <th>{{ \Illuminate\Support\Str::headline((string) $key) }}</th>
                                        <td class="text-end">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Resultados</h2>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach ($entry->result as $key => $value)
                                    @continue(in_array($key, ['summary', 'details', 'warnings', 'next_actions'], true))
                                    <tr>
                                        <th>{{ \Illuminate\Support\Str::headline((string) $key) }}</th>
                                        <td class="text-end">{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
