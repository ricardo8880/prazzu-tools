@extends('layouts.app')

@section('title', 'Histórico de recibos — Prazzu Tools')
@section('meta_description', 'Consulte, reutilize, exporte ou exclua recibos salvos na sua conta Prazzu.')

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Histórico de recibos</h1>
                <p class="text-body-secondary mb-0">Recupere dados, gere uma nova via ou exclua recibos salvos.</p>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('tools.emissor-de-recibos.index') }}">Emitir novo recibo</a>
        </div>

        @if(session('history_message'))
            <div class="alert alert-success">{{ session('history_message') }}</div>
        @endif

        <div class="vstack gap-3">
            @forelse($runs as $run)
                @php($receipt = $run->result['details']['receipt'] ?? [])
                <article class="card shadow-sm">
                    <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <strong>Recibo nº {{ $receipt['number'] ?? ($run->input['number'] ?? '—') }}</strong>
                                <span class="badge text-bg-light">{{ $receipt['amount'] ?? 'Valor não disponível' }}</span>
                            </div>
                            <div>{{ $receipt['payer']['name'] ?? ($run->input['payer_name'] ?? 'Pagador não informado') }} → {{ $receipt['payee']['name'] ?? ($run->input['payee_name'] ?? 'Recebedor não informado') }}</div>
                            <div class="small text-body-secondary mt-1">
                                Emitido em {{ $run->referenceDate->format('d/m/Y') }} · salvo em {{ $run->createdAt->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <form method="POST" action="{{ route('tools.emissor-de-recibos.history.repeat', $run->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary" type="submit">Reutilizar</button>
                            </form>
                            <a class="btn btn-sm btn-outline-success" href="{{ route('tools.emissor-de-recibos.history.export.pdf', $run->id) }}" target="_blank">Exportar PDF</a>
                            <form method="POST" action="{{ route('tools.emissor-de-recibos.history.destroy', $run->id) }}" onsubmit="return confirm('Excluir este recibo do histórico?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="alert alert-light border mb-0">Nenhum recibo salvo ainda.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $runs->links() }}</div>
    </div>
@endsection
