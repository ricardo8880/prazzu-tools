@extends('layouts.app')
@section('title', 'Histórico de Margem e Markup — Prazzu Tools')
@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li><li class="breadcrumb-item"><a href="{{ route('tools.calculadora-margem-markup.index') }}">Margem e Markup</a></li><li class="breadcrumb-item active">Histórico</li></ol></nav>
    <header class="prazzu-tool-intro">
        <span class="prazzu-icon-tile prazzu-icon-tile--purple"><i class="bi bi-clock-history"></i></span>
        <div class="flex-grow-1"><h1>Histórico de cálculos</h1><p>Consulte cálculos individuais, lotes de produtos e simulações de cenários salvos na sua conta.</p></div>
        <a class="btn btn-primary align-self-start" href="{{ route('tools.calculadora-margem-markup.index') }}"><i class="bi bi-plus-lg me-1"></i>Novo cálculo</a>
    </header>

    @if(session('history_message'))<div class="alert alert-success">{{ session('history_message') }}</div>@endif

    <section class="prazzu-tool-card mb-4">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-12 col-md-4"><label class="form-label" for="from">Referência inicial</label><input class="form-control" id="from" type="date" name="from" value="{{ request('from') }}"></div>
            <div class="col-12 col-md-4"><label class="form-label" for="to">Referência final</label><input class="form-control" id="to" type="date" name="to" value="{{ request('to') }}"></div>
            <div class="col-12 col-md-4 d-flex gap-2"><button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filtrar</button><a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-margem-markup.history.index') }}">Limpar</a></div>
        </form>
    </section>

    <section class="prazzu-tool-card">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>Realizado em</th><th>Tipo</th><th>Referência</th><th>Resumo</th><th>Regra</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
            @forelse($runs as $run)
                @php($type = $run->result_payload['calculation_type'] ?? $run->input_payload['calculation_type'] ?? 'single')
                @php($labels = ['single'=>'Individual','batch'=>'Lote de produtos','scenarios'=>'Cenários'])
                @php($summary = $type === 'single' ? ($run->result_payload['sale_price'] ?? '—') : count($run->result_payload['results'] ?? []).' item(ns)')
                <tr>
                    <td>{{ $run->finished_at?->format('d/m/Y H:i') }}</td><td><span class="badge text-bg-light">{{ $labels[$type] ?? ucfirst($type) }}</span></td><td>{{ $run->reference_date->format('d/m/Y') }}</td><td class="fw-semibold">{{ $summary }}</td><td>{{ $run->rule_version }}</td>
                    <td class="text-end"><div class="d-inline-flex flex-wrap justify-content-end gap-1">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-margem-markup.history.show', $run) }}" aria-label="Ver detalhes"><i class="bi bi-eye"></i></a>
                        <form method="post" action="{{ route('tools.calculadora-margem-markup.history.repeat', $run) }}">@csrf<button class="btn btn-sm btn-outline-primary" aria-label="Repetir"><i class="bi bi-arrow-repeat"></i></button></form>
                        <form method="post" action="{{ route('tools.calculadora-margem-markup.history.destroy', $run) }}" onsubmit="return confirm('Excluir este registro?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" aria-label="Excluir"><i class="bi bi-trash"></i></button></form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5"><i class="bi bi-clock-history display-5 text-body-tertiary"></i><h2 class="h5 mt-3">Nenhum cálculo salvo</h2><p class="text-body-secondary">Entre na conta e faça um cálculo para iniciar o histórico.</p></td></tr>
            @endforelse
            </tbody>
        </table></div>
        @if($runs->hasPages())<div class="mt-4">{{ $runs->links() }}</div>@endif
    </section>
    <div class="alert alert-secondary mt-4 mb-0"><i class="bi bi-shield-lock me-1"></i>Os dados são criptografados e seguem o período de retenção configurado para a ferramenta.</div>
</div>
@endsection
