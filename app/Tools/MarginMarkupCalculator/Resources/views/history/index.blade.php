@extends('layouts.app')
@section('title', 'Histórico de Margem e Markup — Prazzu Tools')
@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3"><ol class="breadcrumb prazzu-breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li><li class="breadcrumb-item"><a href="{{ route('tools.calculadora-margem-markup.index') }}">Margem e Markup</a></li><li class="breadcrumb-item active">Histórico</li></ol></nav>
    <x-tools.intro icon="clock-history" title="Histórico de cálculos" description="Consulte cálculos individuais, lotes de produtos e simulações de cenários salvos na sua conta.">
        <x-slot:actions><a class="btn btn-primary" href="{{ route('tools.calculadora-margem-markup.index') }}"><i class="bi bi-plus-lg me-1"></i>Novo cálculo</a></x-slot:actions>
    </x-tools.intro>

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
                @php($type = $run->result['calculation_type'] ?? $run->input['calculation_type'] ?? 'single')
                @php($labels = ['single'=>'Individual','batch'=>'Lote de produtos','scenarios'=>'Cenários'])
                @php($summary = $type === 'single' ? ($run->result['sale_price'] ?? '—') : count($run->result['results'] ?? []).' item(ns)')
                <tr>
                    <td>{{ $run->finishedAt?->format('d/m/Y H:i') }}</td><td><span class="badge text-bg-light">{{ $labels[$type] ?? ucfirst($type) }}</span></td><td>{{ $run->referenceDate->format('d/m/Y') }}</td><td class="fw-semibold">{{ $summary }}</td><td>{{ $run->ruleVersion }}</td>
                    <td class="text-end"><x-tools.history-actions
                        :show-url="route('tools.calculadora-margem-markup.history.show', $run->id)"
                        :repeat-url="route('tools.calculadora-margem-markup.history.repeat', $run->id)"
                        :delete-url="route('tools.calculadora-margem-markup.history.destroy', $run->id)"
                    /></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5"><x-tools.empty-state icon="clock-history" title="Nenhum cálculo salvo" description="Entre na conta e faça um cálculo para iniciar o histórico." /></td></tr>
            @endforelse
            </tbody>
        </table></div>
        @if($runs->hasPages())<div class="mt-4">{{ $runs->links() }}</div>@endif
    </section>
    <div class="alert alert-secondary mt-4 mb-0"><i class="bi bi-shield-lock me-1"></i>Os dados são criptografados e seguem o período de retenção configurado para a ferramenta.</div>
</div>
@endsection
