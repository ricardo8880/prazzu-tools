@extends('layouts.app')
@section('title', 'Histórico — Pró-Labore e Lucros')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3 mb-1">Histórico de simulações</h1><p class="text-body-secondary mb-0">Cálculos salvos por 180 dias.</p></div><a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-pro-labore-distribuicao-lucros.index') }}">Voltar</a></div>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Competência</th><th>Sócio</th><th>Executado em</th><th></th></tr></thead><tbody>
    @forelse($runs as $run)<tr><td>{{ $run->input['competence'] ?? '—' }}</td><td>{{ $run->input['partner_label'] ?? 'Sócio' }}</td><td>{{ $run->finishedAt->format('d/m/Y H:i') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('tools.calculadora-pro-labore-distribuicao-lucros.history.show', $run->id) }}">Detalhes</a></td></tr>@empty<tr><td colspan="4" class="text-center text-body-secondary py-5">Nenhuma simulação salva.</td></tr>@endforelse
    </tbody></table></div></div><div class="mt-3">{{ $runs->links() }}</div>
</div>
@endsection
