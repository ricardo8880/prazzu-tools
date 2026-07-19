@extends('layouts.app')

@section('title', 'Histórico de Honorários Contábeis — Prazzu Tools')

@section('content')
<div class="prazzu-page tool-page">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">Honorários Contábeis</a></li>
            <li class="breadcrumb-item active">Histórico</li>
        </ol>
    </nav>

    <x-tools.intro icon="clock-history" title="Histórico de cálculos" description="Recupere precificações e marque referências importantes para reutilizar depois.">
        <x-slot:actions>
            <a class="btn btn-outline-secondary" href="{{ route('tools.calculadora-de-honorarios-contabeis.history.index', ['favorite' => $favorite ? null : 1]) }}"><i class="bi bi-star{{ $favorite ? '-fill' : '' }} me-1"></i>{{ $favorite ? 'Ver todos' : 'Somente favoritos' }}</a>
            <x-tools.export-button :href="route('tools.calculadora-de-honorarios-contabeis.history.export')" label="Exportar CSV" icon="file-earmark-spreadsheet" />
            <a class="btn btn-primary" href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}"><i class="bi bi-plus-lg me-1"></i>Novo cálculo</a>
        </x-slot:actions>
    </x-tools.intro>

    @include('tools-calculadora-de-honorarios-contabeis::partials.navigation')

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    <section class="prazzu-tool-workspace text-start">
        @forelse ($calculations as $calculation)
            <article class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge text-bg-light border">{{ $calculation->created_at?->format('d/m/Y H:i') }}</span>
                                @if ($calculation->is_favorite)<span class="badge text-bg-warning"><i class="bi bi-star-fill me-1"></i>Favorito</span>@endif
                            </div>
                            <h2 class="h5 mb-1">Honorário recomendado: {{ $calculation->recommendedFee() }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ data_get($calculation->input, 'tax_regime') }} · {{ data_get($calculation->input, 'employees', 0) }} funcionário(s) · complexidade {{ data_get($calculation->result, 'complexity_level') }}
                            </p>
                        </div>
                        <div class="text-lg-end">
                            <div class="small text-body-secondary">Faixa sugerida</div>
                            <div class="fw-semibold">{{ data_get($calculation->result, 'minimum_fee') }} a {{ data_get($calculation->result, 'upper_reference_fee') }}</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <form method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.history.duplicate', $calculation) }}">@csrf<button class="btn btn-sm btn-primary"><i class="bi bi-copy me-1"></i>Duplicar</button></form>
                        <form method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.history.favorite', $calculation) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning"><i class="bi bi-star me-1"></i>{{ $calculation->is_favorite ? 'Desfavoritar' : 'Favoritar' }}</button></form>
                        <form method="post" action="{{ route('tools.calculadora-de-honorarios-contabeis.history.delete', $calculation) }}" onsubmit="return confirm('Remover este cálculo do histórico?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Excluir</button></form>
                    </div>
                </div>
            </article>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-clock-history display-5 text-body-secondary"></i>
                <h2 class="h5 mt-3">Nenhum cálculo encontrado</h2>
                <p class="text-body-secondary">Faça uma precificação para iniciar seu histórico.</p>
                <a class="btn btn-primary" href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}">Calcular honorários</a>
            </div>
        @endforelse

        {{ $calculations->links() }}
    </section>
</div>
@endsection
