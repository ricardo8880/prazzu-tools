@extends('layouts.app')

@section('title', 'Histórico de custos CLT | Prazzu Tools')
@section('meta_description', 'Consulte seus cálculos salvos de custo de funcionário CLT.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="prazzu-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.custo-funcionario-clt.index') }}">Custo de funcionário CLT</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>

    <header class="prazzu-page-hero">
        <span class="prazzu-page-hero__icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
        <div>
            <span class="prazzu-eyebrow">Prazzu Plus</span>
            <h1>Histórico de custos CLT</h1>
            <p>Reabra, imprima ou exclua simulações vinculadas à sua conta.</p>
        </div>
    </header>

    @if(session('workspace_message'))
        <div class="alert alert-success" role="status">{{ session('workspace_message') }}</div>
    @endif

    <form method="get" class="card border-0 shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label" for="history-from">De</label>
                <input class="form-control" id="history-from" type="date" name="from" value="{{ request('from') }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="history-to">Até</label>
                <input class="form-control" id="history-to" type="date" name="to" value="{{ request('to') }}">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Filtrar</button>
                <a class="btn btn-outline-secondary" href="{{ route('tools.custo-funcionario-clt.history.index') }}">Limpar</a>
            </div>
        </div>
    </form>

    @if($runs->isEmpty())
        <x-tools.empty-state
            icon="clock-history"
            title="Nenhum cálculo salvo"
            description="Faça uma simulação autenticado para encontrá-la aqui."
        />
    @else
        <div class="table-responsive card border-0 shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Referência</th>
                    <th>Tipo</th>
                    <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                @foreach($runs as $run)
                    @php($type = $run->result['calculation_type'] ?? 'single')
                    <tr>
                        <td>{{ $run->createdAt->format('d/m/Y H:i') }}</td>
                        <td>{{ $run->input['scenario_name'] ?? $run->input['employee_name'] ?? 'Simulação sem nome' }}</td>
                        <td>{{ $type === 'batch' ? 'Lote' : 'Individual' }}</td>
                        <td class="text-end">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('tools.custo-funcionario-clt.history.show', $run->id) }}">Abrir</a>
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
