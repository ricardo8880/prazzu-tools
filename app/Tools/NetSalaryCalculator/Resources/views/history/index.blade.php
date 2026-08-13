@extends('layouts.app')
@section('title', 'Histórico — Salário Líquido')
@section('content')
<div class="container py-4" data-plus-feature="history">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div><h1 class="h3 mb-1">Histórico — Salário Líquido</h1><p class="text-body-secondary mb-0">Cálculos salvos na sua conta Prazzu Plus.</p></div>
        <a class="btn btn-outline-primary" href="{{ route('tools.calculadora-salario-liquido.index') }}">Novo cálculo</a>
    </div>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="vstack gap-3">
        @forelse($runs as $run)
            <article class="card"><div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div><strong>{{ $run->referenceDate->format('d/m/Y') }}</strong><div class="small text-body-secondary">Versão da regra: {{ $run->ruleVersion->value }}</div></div>
                <div class="d-flex flex-wrap gap-2">
                    <form method="post" action="{{ route('tools.calculadora-salario-liquido.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-primary">Reutilizar</button></form>
                    <form method="post" action="{{ route('tools.calculadora-salario-liquido.history.destroy', $run->id) }}" data-confirm="Excluir este cálculo?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Excluir</button></form>
                </div>
            </div></article>
        @empty
            <div class="alert alert-light border">Nenhum cálculo salvo ainda.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $runs->links() }}</div>
</div>
@endsection
