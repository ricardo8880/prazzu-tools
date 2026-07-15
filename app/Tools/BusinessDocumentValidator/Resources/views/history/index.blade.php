@extends('layouts.app')

@section('title', 'Histórico do Validador — Prazzu Tools')
@section('meta_description', 'Consulte os resumos das validações em lote realizadas no Validador de CNPJ, CPF e IE.')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.validador-de-cnpj.index') }}">Validador de documentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>

    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--green"><i class="bi bi-clock-history"></i></span>
        <div class="flex-grow-1">
            <h1>Histórico de validações em lote</h1>
            <p>Consulte os resumos armazenados sem expor documentos ou dados cadastrais importados.</p>
        </div>
        <a class="btn btn-primary align-self-start" href="{{ route('tools.validador-de-cnpj.index') }}">
            <i class="bi bi-plus-lg me-1"></i>Nova validação
        </a>
    </header>

    @if (session('history_message'))
        <div class="alert alert-success alert-dismissible fade show" role="status">
            <i class="bi bi-check-circle me-1"></i>{{ session('history_message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <section class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Data</th><th>Arquivo</th><th>Total</th><th>Inválidos</th><th>Inconsistências</th><th class="text-end">Ações</th></tr>
                </thead>
                <tbody>
                @forelse ($runs as $run)
                    <tr>
                        <td class="text-nowrap">{{ $run->finished_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ data_get($run->input_payload, 'file_name', 'Importação') }}</td>
                        <td>{{ data_get($run->result_payload, 'summary.total', 0) }}</td>
                        <td>{{ data_get($run->result_payload, 'summary.invalid', 0) }}</td>
                        <td>{{ data_get($run->result_payload, 'summary.with_inconsistencies', 0) }}</td>
                        <td class="text-end">
                            <form method="post" action="{{ route('tools.validador-de-cnpj.history.destroy', $run) }}" class="d-inline" onsubmit="return confirm('Excluir esta validação do histórico?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir validação">
                                    <i class="bi bi-trash me-1"></i>Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-clock-history display-5 text-body-tertiary"></i>
                            <h2 class="h5 mt-3">Nenhuma validação salva</h2>
                            <p class="text-body-secondary mb-3">Execute uma validação em lote para iniciar o histórico.</p>
                            <a class="btn btn-primary" href="{{ route('tools.validador-de-cnpj.index') }}">Validar documentos</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($runs->hasPages())
            <div class="card-body border-top">{{ $runs->links() }}</div>
        @endif
    </section>

    <div class="alert alert-secondary mt-4 mb-0" role="note">
        <i class="bi bi-shield-lock me-1"></i>Somente os resumos das execuções são armazenados. Documentos e dados cadastrais não entram no histórico.
    </div>
</div>
@endsection
