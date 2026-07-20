@extends('layouts.app')

@section('title', 'Histórico — Comparador Tributário')

@section('content')
<x-tools.page title="Histórico do Comparador Tributário" description="Comparações salvas na sua conta." icon="clock-history" slug="comparador-tributario">
    @if (session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a class="btn btn-outline-secondary" href="{{ route('tools.comparador-tributario.index') }}">Nova comparação</a>
        <span class="text-body-secondary small">Retenção: 365 dias</span>
    </div>

    <form class="card card-body border-0 shadow-sm mb-4" method="get">
        <div class="row g-3 align-items-end">
            <div class="col-md-4"><label class="form-label" for="from">De</label><input class="form-control" type="date" id="from" name="from" value="{{ request('from') }}"></div>
            <div class="col-md-4"><label class="form-label" for="to">Até</label><input class="form-control" type="date" id="to" name="to" value="{{ request('to') }}"></div>
            <div class="col-md-4"><button class="btn btn-primary" type="submit">Filtrar</button></div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Data</th><th>Referência</th><th>Resultado</th><th class="text-end">Ações</th></tr></thead>
                <tbody>
                @forelse($runs as $run)
                    <tr>
                        <td>{{ $run->finishedAt->format('d/m/Y H:i') }}</td>
                        <td>{{ $run->referenceDate->format('d/m/Y') }}</td>
                        <td>{{ $run->result['winner'] ?? 'Sem vencedor' }}</td>
                        <td class="text-end"><a class="btn btn-outline-primary btn-sm" href="{{ route('tools.comparador-tributario.history.show', $run->id) }}">Abrir</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-body-secondary py-5">Nenhuma comparação salva.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $runs->links() }}</div>
</x-tools.page>
@endsection
