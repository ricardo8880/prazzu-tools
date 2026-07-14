@extends('layouts.app')
@section('title', 'Histórico do Validador — Prazzu Tools')
@section('content')
<div class="prazzu-page">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center mb-4">
        <div><h1 class="h3 mb-1">Histórico de validações em lote</h1><p class="text-body-secondary mb-0">Somente resumos são armazenados; documentos e dados cadastrais não entram no histórico.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('tools.validador-de-cnpj.index') }}">Voltar à ferramenta</a>
    </div>
    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif
    <div class="card shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0">
        <thead class="table-light"><tr><th>Data</th><th>Arquivo</th><th>Total</th><th>Inválidos</th><th>Inconsistências</th><th class="text-end">Ações</th></tr></thead>
        <tbody>
        @forelse($runs as $run)
            <tr>
                <td>{{ $run->finished_at?->format('d/m/Y H:i') }}</td>
                <td>{{ data_get($run->input_payload, 'file_name', 'Importação') }}</td>
                <td>{{ data_get($run->result_payload, 'summary.total', 0) }}</td>
                <td>{{ data_get($run->result_payload, 'summary.invalid', 0) }}</td>
                <td>{{ data_get($run->result_payload, 'summary.with_inconsistencies', 0) }}</td>
                <td class="text-end"><form method="post" action="{{ route('tools.validador-de-cnpj.history.destroy', $run) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button></form></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-body-secondary py-5">Nenhuma validação em lote salva.</td></tr>
        @endforelse
        </tbody>
    </table></div></div>
    <div class="mt-3">{{ $runs->links() }}</div>
</div>
@endsection
