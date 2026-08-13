@extends('layouts.app')

@section('title', 'Histórico de Contratos — Prazzu Tools')

@section('content')
<div class="container py-5">
    <x-tools.page title="Histórico de Contratos" description="Revise versões salvas, marque favoritos e compare dois contratos." icon="clock-history" slug="gerador-de-contratos">
        @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif

        @if(count($runs->items) >= 2)
            <form method="GET" action="{{ route('tools.gerador-de-contratos.history.compare') }}" class="border rounded p-3 mb-4">
                <h2 class="h5">Comparar versões <span class="badge text-bg-primary">Plus</span></h2>
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="left">Versão A</label>
                        <select class="form-select" id="left" name="left" required>
                            @foreach($runs->items as $run)<option value="{{ $run->id }}">{{ $run->createdAt->format('d/m/Y H:i') }} — {{ $run->result['contract_title'] ?? 'Contrato' }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="right">Versão B</label>
                        <select class="form-select" id="right" name="right" required>
                            @foreach($runs->items as $run)<option value="{{ $run->id }}" @selected($loop->index === 1)>{{ $run->createdAt->format('d/m/Y H:i') }} — {{ $run->result['contract_title'] ?? 'Contrato' }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2"><button class="btn btn-primary w-100" type="submit">Comparar</button></div>
                </div>
            </form>
        @endif

        @forelse($runs->items as $run)
            <article class="border rounded p-3 mb-3">
                <div class="d-flex flex-wrap justify-content-between gap-3">
                    <div>
                        <h2 class="h5 mb-1">{{ $run->result['contract_title'] ?? 'Contrato salvo' }}</h2>
                        <div class="text-body-secondary small">{{ $run->createdAt->format('d/m/Y H:i') }} · versão da ferramenta {{ $run->toolVersion }}</div>
                    </div>
                    @if($run->favorite)<span class="badge text-bg-warning align-self-start">Favorito</span>@endif
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <form method="POST" action="{{ route('tools.gerador-de-contratos.history.favorite', $run->id) }}">@csrf<button class="btn btn-sm btn-outline-warning" type="submit">{{ $run->favorite ? 'Remover favorito' : 'Favoritar' }}</button></form>
                    <form method="POST" action="{{ route('tools.gerador-de-contratos.history.destroy', $run->id) }}" data-confirm="Excluir esta versão do histórico?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button></form>
                </div>
            </article>
        @empty
            <div class="alert alert-info">Nenhuma versão de contrato foi salva ainda.</div>
        @endforelse

        @if($runs->lastPage > 1)
            <nav class="d-flex gap-2 mt-4" aria-label="Paginação do histórico">
                @if($runs->page > 1)<a class="btn btn-outline-secondary" href="?page={{ $runs->page - 1 }}">Anterior</a>@endif
                <span class="align-self-center">Página {{ $runs->page }} de {{ $runs->lastPage }}</span>
                @if($runs->hasMorePages())<a class="btn btn-outline-secondary" href="?page={{ $runs->page + 1 }}">Próxima</a>@endif
            </nav>
        @endif
    </x-tools.page>
</div>
@endsection
