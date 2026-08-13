@extends('layouts.app')

@section('title', $home['page_title'] ?? 'Prazzu Tools')
@section('meta_description', $home['meta_description'] ?? 'Ferramentas práticas para o seu dia a dia.')

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

                    <div class="prazzu-smart-search" data-home-smart-search>
                        <form class="prazzu-search" action="{{ route('tools.index') }}" method="get" role="search">
                            <label class="visually-hidden" for="home-tool-search">Buscar ferramentas</label>
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <input type="hidden" name="source" value="home_search">
                            <input
                                id="home-tool-search"
                                class="form-control"
                                type="search"
                                name="q"
                                placeholder="{{ $home['hero']['search_placeholder'] }}"
                                autocomplete="off"
                                spellcheck="false"
                                aria-autocomplete="list"
                                aria-controls="home-smart-search-panel"
                                aria-expanded="false"
                                data-home-smart-search-input
                            >
                            <button class="btn" type="submit" aria-label="Buscar ferramentas">
                                <i class="bi bi-search" aria-hidden="true"></i>
                            </button>
                        </form>

                        <div
                            id="home-smart-search-panel"
                            class="prazzu-smart-search__panel"
                            data-home-smart-search-panel
                            aria-label="Sugestões de ferramentas"
                            hidden
                        >
                            <div class="prazzu-smart-search__content" data-home-smart-search-content role="listbox"></div>
                            <div class="prazzu-smart-search__footer" aria-hidden="true">
                                <span><kbd>↑</kbd><kbd>↓</kbd> navegar</span>
                                <span><kbd>Enter</kbd> abrir</span>
                                <span><kbd>Esc</kbd> fechar</span>
                                <span class="prazzu-smart-search__shortcut"><kbd>Ctrl</kbd><kbd>K</kbd> buscar</span>
                            </div>
                        </div>

                        <script type="application/json" data-home-smart-search-catalog nonce="{{ $cspNonce ?? '' }}">{!! json_encode([
                            'tools' => $searchToolCatalog->values()->all(),
                            'categories' => $categories->map(static fn (array $category): array => [
                                'slug' => $category['slug'],
                                'name' => $category['name'],
                                'icon' => $category['icon'],
                                'count' => $category['count'],
                                'url' => $category['url'],
                            ])->values()->all(),
                            'favoriteSlugs' => $favoriteToolSlugs->values()->all(),
                            'recentSlugs' => $continueTools->pluck('tool_slug')->values()->all(),
                            'featuredSlugs' => $featuredTools->pluck('slug')->values()->all(),
                            'allToolsUrl' => route('tools.index'),
                        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
                    </div>

                    <ul class="prazzu-benefits list-unstyled mb-0 mt-4" aria-label="Vantagens da plataforma">
                        @foreach ($home['hero']['benefits'] as $benefit)
                            <li>
                                <i class="bi {{ $benefit['icon'] }}" aria-hidden="true"></i>
                                <span>{{ $benefit['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-lg-5 d-none d-lg-block">
                    <div class="prazzu-hero-art" aria-hidden="true">
                        <div class="prazzu-hero-art__float">
                            <svg class="prazzu-hero-art__svg" viewBox="0 0 430 250" role="presentation" focusable="false">
                                <g class="prazzu-hero-art__orbit">
                                    <ellipse cx="216" cy="126" rx="176" ry="82" transform="rotate(-8 216 126)" />
                                    <ellipse cx="216" cy="126" rx="145" ry="60" transform="rotate(10 216 126)" />
                                </g>

                                <g class="prazzu-hero-art__sparkles">
                                    <path d="M67 66v18M58 75h18" />
                                    <path d="M353 177v15M345.5 184.5h15" />
                                    <circle cx="365" cy="72" r="4" />
                                    <circle cx="72" cy="180" r="3" />
                                </g>

                                <g class="prazzu-hero-art__main-card">
                                    <rect x="105" y="47" width="222" height="152" rx="26" />
                                    <path d="M133 78h78" class="prazzu-hero-art__soft-line" />
                                    <circle cx="291" cy="78" r="6" />
                                    <circle cx="270" cy="78" r="6" />

                                    <g class="prazzu-hero-art__chart">
                                        <path d="M140 160V119" />
                                        <path d="M171 160V103" />
                                        <path d="M202 160v-25" />
                                        <path d="M233 160V91" />
                                        <path d="M264 160v-43" />
                                        <path d="M132 160h142" class="prazzu-hero-art__soft-line" />
                                    </g>
                                </g>

                                <g class="prazzu-hero-art__mini-card prazzu-hero-art__mini-card--calculator">
                                    <rect x="55" y="112" width="92" height="89" rx="20" />
                                    <rect x="76" y="133" width="50" height="16" rx="5" class="prazzu-hero-art__mini-screen" />
                                    <circle cx="81" cy="169" r="5" />
                                    <circle cx="101" cy="169" r="5" />
                                    <circle cx="121" cy="169" r="5" />
                                    <circle cx="81" cy="187" r="5" />
                                    <circle cx="101" cy="187" r="5" />
                                    <path d="M118 184h7" />
                                </g>

                                <g class="prazzu-hero-art__mini-card prazzu-hero-art__mini-card--document">
                                    <rect x="296" y="72" width="83" height="92" rx="20" />
                                    <path d="M319 99h36M319 115h27M319 131h19" class="prazzu-hero-art__soft-line" />
                                    <circle cx="352" cy="136" r="15" class="prazzu-hero-art__check-circle" />
                                    <path d="m345 136 5 5 10-11" class="prazzu-hero-art__check" />
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
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

        @if (! $acquisitionContext && $continueTools->isNotEmpty())
            <section class="prazzu-continuity-tools" aria-labelledby="continue-tools-title">
                <div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3 mb-3">
                    <div>
                        <span class="prazzu-eyebrow">Seu Prazzu</span>
                        <h2 id="continue-tools-title" class="prazzu-section-title mb-1">Continue de onde parou</h2>
                        <p class="prazzu-section-caption mb-0">Atalhos para ferramentas que você usou recentemente nesta vertical.</p>
                    </div>
                    <a class="prazzu-section-link text-decoration-none" href="{{ route('account.show') }}">
                        Meu Prazzu <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="row g-3">
                    @foreach ($continueTools as $tool)
                        <div class="col-12 col-sm-6 col-xl-3">
                            <article class="prazzu-tool-card prazzu-tool-card--continuity h-100">
                                <a class="prazzu-tool-card__link text-decoration-none" href="{{ url()->query($tool['open_url'], ['source' => 'home_continuity']) }}" aria-label="Continuar em {{ $tool['tool_name'] }}"></a>
                                <span class="prazzu-icon-tile prazzu-icon-tile--purple mb-3">
                                    <i class="bi {{ $tool['tool_icon'] }}" aria-hidden="true"></i>
                                </span>
                                <h3 class="prazzu-tool-card__title">{{ $tool['tool_name'] }}</h3>
                                <p class="prazzu-tool-card__description">{{ \Illuminate\Support\Str::limit($tool['tool_description'], 105) }}</p>
                                <span class="prazzu-badge prazzu-badge--green">Usado recentemente</span>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
        @elseif (! $acquisitionContext && auth()->guest())
            <section class="prazzu-continuity-tools" aria-labelledby="recent-tools-title" data-home-recent-tools hidden>
                <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
                    <div>
                        <span class="prazzu-eyebrow">Continue sua rotina</span>
                        <h2 id="recent-tools-title" class="prazzu-section-title mb-1">Usadas recentemente</h2>
                        <p class="prazzu-section-caption mb-0">Ficam somente nesta sessão. Nenhum valor de cálculo é armazenado aqui.</p>
                    </div>
                </div>
                <div class="row g-3" data-home-recent-tools-list></div>
            </section>

            <script type="application/json" data-home-recent-tools-catalog nonce="{{ $cspNonce ?? '' }}">{!! json_encode($recentToolCandidates, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
        @endif

        <section class="prazzu-featured-tools" aria-labelledby="featured-tools-title">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h2 id="featured-tools-title" class="prazzu-section-title mb-0">{{ $home['tools_section_title'] }}</h2>
                <a class="prazzu-section-link text-decoration-none" href="{{ route('tools.index') }}">
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
            <a class="btn btn-primary prazzu-btn-primary" href="{{ ($home['cta']['url'] === '/ferramentas' ? route('tools.index') : url($home['cta']['url'])) }}" @if($acquisitionContext) data-acquisition-click="cta" @endif>{{ $home['cta']['label'] }}</a>
        </aside>
    </div>
@endsection


@push('scripts')
@if($acquisitionContext)
<script nonce="{{ $cspNonce ?? '' }}">
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
