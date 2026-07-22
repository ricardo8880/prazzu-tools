@extends('layouts.app')

@section('title', 'Prazzu Tools — Ferramentas para contabilidade')
@section('meta_description', 'Ferramentas contábeis gratuitas e profissionais para facilitar a rotina de contadores e empresas.')

@section('content')
    <div class="prazzu-home" @if($acquisitionContext) data-acquisition-endpoint="{{ route('analytics.acquisition.track') }}" @endif>
        <section class="prazzu-hero" aria-labelledby="home-hero-title" @if($acquisitionContext) data-acquisition-impression="hero" @endif>
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
                <h2 id="featured-tools-title" class="prazzu-section-title mb-0">{{ $home['tools_section_title'] }}</h2>
                <a class="prazzu-section-link text-decoration-none" href="{{ url('/ferramentas') }}">
                    Ver todas <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="row g-3">
                @foreach ($featuredTools as $tool)
                    <div class="col-12 col-sm-6 col-xl-3">
                        <article class="prazzu-tool-card h-100"
                            @if($acquisitionContext)
                                data-acquisition-impression="tool"
                                data-tool-slug="{{ $tool['slug'] }}"
                                data-tool-placement="{{ $tool['slug'] === $acquisitionContext->primaryToolSlug ? 'primary' : 'featured' }}"
                                data-tool-position="{{ $loop->iteration }}"
                            @endif
                        >
                            <a class="prazzu-tool-card__link text-decoration-none" href="{{ route($tool['route_name']) }}" aria-label="Abrir {{ $tool['name'] }}" @if($acquisitionContext) data-acquisition-click="tool" @endif></a>
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

        <aside class="prazzu-home-cta" aria-label="Conheça todas as ferramentas" @if($acquisitionContext) data-acquisition-impression="cta" @endif>
            <div class="d-flex align-items-center gap-3 min-w-0">
                <span class="prazzu-home-cta__icon"><i class="bi bi-rocket-takeoff" aria-hidden="true"></i></span>
                <span class="min-w-0">
                    <strong>{{ $home['cta']['title'] }}</strong>
                    <small>{{ $home['cta']['description'] }}</small>
                </span>
            </div>
            <a class="btn btn-primary prazzu-btn-primary" href="{{ url($home['cta']['url']) }}" @if($acquisitionContext) data-acquisition-click="cta" @endif>{{ $home['cta']['label'] }}</a>
        </aside>
    </div>
@endsection


@push('scripts')
@if($acquisitionContext)
<script>
(() => {
    const endpoint = document.querySelector('.prazzu-home')?.dataset.acquisitionEndpoint;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const sent = new Set();

    const track = (event, properties = {}, uniqueKey = null) => {
        const key = uniqueKey ?? `${event}:${JSON.stringify(properties)}`;
        if (sent.has(key)) return;
        sent.add(key);

        fetch(endpoint, {
            method: 'POST',
            keepalive: true,
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf},
            body: JSON.stringify({...properties, event}),
        }).catch(() => sent.delete(key));
    };

    track('acquisition.context.resolved', {}, 'context');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            const element = entry.target;
            const kind = element.dataset.acquisitionImpression;
            if (kind === 'hero') {
                track('acquisition.hero.viewed', {}, 'hero');
            } else if (kind === 'cta') {
                track('acquisition.cta.viewed', {destination: element.querySelector('a')?.href}, 'cta');
            } else if (kind === 'tool') {
                track('acquisition.tool.impression', {
                    tool_slug: element.dataset.toolSlug,
                    placement: element.dataset.toolPlacement,
                    position: Number(element.dataset.toolPosition || 0) || null,
                }, `tool-impression:${element.dataset.toolSlug}:${element.dataset.toolPlacement}`);
            }

            observer.unobserve(element);
        });
    }, {threshold: 0.35});

    document.querySelectorAll('[data-acquisition-impression]').forEach((element) => observer.observe(element));

    document.querySelectorAll('[data-acquisition-click="tool"]').forEach((link) => {
        link.addEventListener('click', () => {
            const card = link.closest('[data-acquisition-impression="tool"]');
            if (!card) return;
            track('acquisition.tool.clicked', {
                tool_slug: card.dataset.toolSlug,
                placement: card.dataset.toolPlacement,
                position: Number(card.dataset.toolPosition || 0) || null,
                destination: link.href,
            });
        });
    });

    document.querySelectorAll('[data-acquisition-click="cta"]').forEach((link) => {
        link.addEventListener('click', () => track('acquisition.cta.clicked', {destination: link.href}));
    });
})();
</script>
@endif
@endpush
