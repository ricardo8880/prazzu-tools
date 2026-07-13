@extends('layouts.app')

@section('title', 'Prazzu Tools — Ferramentas para contabilidade')
@section('meta_description', 'Ferramentas contábeis gratuitas e profissionais para facilitar a rotina de contadores e empresas.')

@section('content')
    <div class="prazzu-home">
        <section class="prazzu-hero" aria-labelledby="home-hero-title">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-7">
                    <h1 id="home-hero-title" class="prazzu-hero__title mb-3">
                        <span>{{ $home['hero']['title_before'] }}</span>
                        <span>{{ $home['hero']['title_line'] }} <strong>{{ $home['hero']['title_highlight'] }}</strong></span>
                    </h1>

                    <p class="prazzu-hero__description mb-4">{{ $home['hero']['description'] }}</p>

                    <form class="prazzu-search" action="{{ url('/ferramentas') }}" method="get" role="search">
                        <label class="visually-hidden" for="home-tool-search">Buscar ferramentas</label>
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input
                            id="home-tool-search"
                            class="form-control"
                            type="search"
                            name="q"
                            placeholder="{{ $home['hero']['search_placeholder'] }}"
                            autocomplete="off"
                        >
                        <button class="btn" type="submit" aria-label="Buscar ferramentas">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </button>
                    </form>

                    <ul class="prazzu-benefits list-unstyled mb-0 mt-4" aria-label="Vantagens da plataforma">
                        @foreach ($home['hero']['benefits'] as $benefit)
                            <li>
                                <i class="bi {{ $benefit['icon'] }}" aria-hidden="true"></i>
                                <span>{{ $benefit['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

{{--                <div class="col-lg-5 d-none d-lg-block">--}}
{{--                    <div class="prazzu-hero-art" aria-hidden="true">--}}
{{--                        <img src="{{ asset('assets/images/accounting-tools-hero.png') }}" alt="">--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </section>

        <nav class="prazzu-category-strip" aria-label="Categorias de ferramentas">
            @foreach ($categories as $category)
                <a class="prazzu-category-item text-decoration-none" href="{{ url($category['url']) }}">
                    <span class="prazzu-category-item__icon">
                        <i class="bi {{ $category['icon'] }}" aria-hidden="true"></i>
                        <span class="prazzu-category-item__count">{{ $category['count'] }}</span>
                    </span>
                    <span>{{ $category['name'] }}</span>
                </a>
            @endforeach
        </nav>

        <section class="prazzu-featured-tools" aria-labelledby="featured-tools-title">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h2 id="featured-tools-title" class="prazzu-section-title mb-0">Ferramentas em destaque</h2>
                <a class="prazzu-section-link text-decoration-none" href="{{ url('/ferramentas') }}">
                    Ver todas <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="row g-3">
                @foreach ($featuredTools as $tool)
                    <div class="col-12 col-sm-6 col-xl-3">
                        <article class="prazzu-tool-card h-100">
                            <a class="prazzu-tool-card__link text-decoration-none" href="{{ url('/ferramentas/'.$tool['slug']) }}" aria-label="Abrir {{ $tool['name'] }}"></a>
                            <span class="prazzu-icon-tile prazzu-icon-tile--{{ $tool['tone'] }} mb-3">
                                <i class="bi {{ $tool['icon'] }}" aria-hidden="true"></i>
                            </span>
                            <h3 class="prazzu-tool-card__title">{{ $tool['name'] }}</h3>
                            <p class="prazzu-tool-card__description">{{ $tool['description'] }}</p>
                            <span class="prazzu-badge prazzu-badge--{{ $tool['badge_tone'] }}">{{ $tool['badge'] }}</span>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="prazzu-home-cta" aria-label="Conheça todas as ferramentas">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <span class="prazzu-home-cta__icon"><i class="bi bi-rocket-takeoff" aria-hidden="true"></i></span>
                <span class="min-w-0">
                    <strong>{{ $home['cta']['title'] }}</strong>
                    <small>{{ $home['cta']['description'] }}</small>
                </span>
            </div>
            <a class="btn btn-primary prazzu-btn-primary" href="{{ url($home['cta']['url']) }}">{{ $home['cta']['label'] }}</a>
        </aside>
    </div>
@endsection
