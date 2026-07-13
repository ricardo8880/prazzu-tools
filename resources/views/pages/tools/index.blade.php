@extends('layouts.app')

@section('title', ($activeCategory['name'] ?? 'Todas as ferramentas').' — Prazzu Tools')
@section('meta_description', 'Encontre ferramentas contábeis gratuitas por categoria ou busca.')

@section('content')
    <div class="prazzu-page prazzu-tools-catalog">
        <header class="prazzu-page-hero">
            <span class="prazzu-page-hero__icon"><i class="bi bi-grid" aria-hidden="true"></i></span>
            <div>
                <span class="prazzu-eyebrow">Catálogo Prazzu</span>
                <h1>{{ $activeCategory['name'] ?? 'Todas as ferramentas' }}</h1>
                <p>Busque e acesse soluções criadas para simplificar a rotina contábil.</p>
            </div>
        </header>

        <form class="prazzu-catalog-toolbar" action="{{ $activeCategory ? route('tools.category', $activeCategory['slug']) : route('tools.index') }}" method="get" role="search">
            <div class="prazzu-search flex-grow-1">
                <label class="visually-hidden" for="catalog-search">Buscar no catálogo</label>
                <i class="bi bi-search" aria-hidden="true"></i>
                <input id="catalog-search" class="form-control" type="search" name="q" value="{{ $query }}" placeholder="Buscar por nome, categoria ou problema...">
                <button class="btn" type="submit" aria-label="Buscar"><i class="bi bi-search" aria-hidden="true"></i></button>
            </div>
            @if ($query !== '')
                <a class="btn prazzu-btn-outline" href="{{ $activeCategory ? route('tools.category', $activeCategory['slug']) : route('tools.index') }}">Limpar</a>
            @endif
        </form>

        <nav class="prazzu-filter-pills" aria-label="Filtrar por categoria">
            <a class="btn {{ $activeCategory === null ? 'is-active' : '' }}" href="{{ route('tools.index', ['q' => $query ?: null]) }}">Todas</a>
            @foreach ($categories as $category)
                <a class="btn {{ ($activeCategory['slug'] ?? null) === $category['slug'] ? 'is-active' : '' }}" href="{{ route('tools.category', ['category' => $category['slug'], 'q' => $query ?: null]) }}">
                    <i class="bi {{ $category['icon'] }}" aria-hidden="true"></i>{{ $category['name'] }}
                </a>
            @endforeach
        </nav>

        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
            <p class="mb-0 text-body-secondary"><strong class="text-body">{{ $tools->count() }}</strong> de {{ $totalTools }} ferramentas encontradas</p>
        </div>

        @if ($tools->isEmpty())
            <section class="prazzu-empty-state">
                <i class="bi bi-search" aria-hidden="true"></i>
                <h2>Nenhuma ferramenta encontrada</h2>
                <p>Tente outra palavra ou envie uma sugestão para a equipe.</p>
                <a class="btn btn-primary prazzu-btn-primary" href="{{ route('tools.suggest') }}">Sugerir ferramenta</a>
            </section>
        @else
            <div class="row g-3">
                @foreach ($tools as $tool)
                    <div class="col-12 col-sm-6 col-xl-4">
                        <article class="prazzu-tool-card h-100">
                            <a class="prazzu-tool-card__link text-decoration-none" href="{{ route('tools.show', $tool['slug']) }}" aria-label="Abrir {{ $tool['name'] }}"></a>
                            <span class="prazzu-icon-tile prazzu-icon-tile--{{ $tool['tone'] }} mb-3"><i class="bi {{ $tool['icon'] }}" aria-hidden="true"></i></span>
                            <span class="prazzu-tool-card__category">{{ ucfirst($tool['category']) }}</span>
                            <h2 class="prazzu-tool-card__title">{{ $tool['name'] }}</h2>
                            <p class="prazzu-tool-card__description">{{ $tool['description'] }}</p>
                            <span class="prazzu-badge prazzu-badge--{{ $tool['badge_tone'] }}">{{ $tool['badge'] }}</span>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
