@extends('layouts.app')

@section('title', 'Histórico de Rescisões — Prazzu Tools')
@section('meta_description', 'Consulte seus cálculos anteriores de rescisão trabalhista.')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.calculadora-de-rescisao.index') }}">Calculadora de Rescisão</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>

    <x-tools.intro icon="clock-history" tone="pink" title="Histórico de cálculos" description="Consulte, repita ou exclua os cálculos salvos na sua conta.">
        <x-slot:actions><a class="btn btn-primary" href="{{ route('tools.calculadora-de-rescisao.index') }}"><i class="bi bi-plus-lg me-1"></i>Novo cálculo</a></x-slot:actions>
    </x-tools.intro>

    <nav class="mb-4" aria-label="Navegação da Calculadora de Rescisão Trabalhista">
        <div class="nav nav-pills flex-column flex-sm-row gap-2">
            <a class="nav-link" href="{{ route('tools.calculadora-de-rescisao.index') }}"><i class="bi bi-calculator me-1"></i>Calculadora</a>
            <a class="nav-link active" href="{{ route('tools.calculadora-de-rescisao.history.index') }}" aria-current="page"><i class="bi bi-clock-history me-1"></i>Histórico</a>
        </div>
    </nav>

    @if (session('history_message'))
        <div class="alert alert-success" role="status">{{ session('history_message') }}</div>
    @endif

    <section class="prazzu-tool-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Realizado em</th><th>Desligamento</th><th>Referência</th><th>Líquido</th><th>Regra</th><th class="text-end">Ações</th></tr></thead>
                <tbody>
                @forelse ($runs as $run)
                    <tr>
                        <td>{{ $run->finishedAt?->format('d/m/Y H:i') }}</td>
                        <td>{{ $run->result['termination_type_label'] ?? 'Rescisão' }}</td>
                        <td>{{ $run->referenceDate->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $run->result['net_total'] ?? '—' }}</td>
                        <td>{{ $run->ruleVersion }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.calculadora-de-rescisao.history.show', $run->id) }}" aria-label="Ver detalhes"><i class="bi bi-eye"></i></a>
                                <form method="post" action="{{ route('tools.calculadora-de-rescisao.history.repeat', $run->id) }}">@csrf<button class="btn btn-sm btn-outline-primary" type="submit" aria-label="Repetir cálculo"><i class="bi bi-arrow-repeat"></i></button></form>
                                <form method="post" action="{{ route('tools.calculadora-de-rescisao.history.destroy', $run->id) }}" onsubmit="return confirm('Excluir este cálculo do histórico?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir cálculo"><i class="bi bi-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-clock-history display-5 text-body-tertiary"></i>
                            <h2 class="h5 mt-3">Nenhum cálculo salvo</h2>
                            <p class="text-body-secondary mb-3">Faça uma rescisão para iniciar o histórico.</p>
                            <a class="btn btn-primary" href="{{ route('tools.calculadora-de-rescisao.index') }}">Calcular rescisão</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($runs->hasPages())<div class="mt-4">{{ $runs->links() }}</div>@endif
    </section>

    <div class="alert alert-secondary mt-4 mb-0" role="note"><i class="bi bi-shield-lock me-1"></i>Os dados do histórico são criptografados e eliminados após o período de retenção de 180 dias.</div>
</div>
@endsection
