@props([
    'title',
    'description',
    'icon',
    'slug',
    'badge' => 'Grátis',
    'tone' => 'purple',
    'showValidation' => true,
])

@php
    $toolCatalog = app(\App\Core\Tools\ToolCatalog::class);
    $catalogTool = $toolCatalog->find($slug);
    $analyticsJourney = app(\App\Core\Tools\Analytics\Services\ToolAnalyticsJourneyRegistry::class)->find($slug);
    $analyticsConfig = $analyticsJourney === null ? null : array_merge(
        $analyticsJourney->toFrontendArray(),
        [
            'endpoint' => route('analytics.tools.track'),
            'csrf' => csrf_token(),
            'has_validation_errors' => $errors->any(),
        ],
    );

    $historyUrl = null;
    $toolIsFavorite = auth()->check()
        ? app(\App\Core\Tools\Favorites\Services\UserToolFavorites::class)->isFavorite($slug, (int) auth()->id())
        : false;
    $toolRouteName = is_array($catalogTool) ? ($catalogTool['route_name'] ?? null) : null;
    if (($catalogTool['supports_history'] ?? false) && is_string($toolRouteName) && str_ends_with($toolRouteName, '.index')) {
        $historyRouteName = substr($toolRouteName, 0, -strlen('.index')).'.history.index';
        if (\Illuminate\Support\Facades\Route::has($historyRouteName)) {
            $historyUrl = route($historyRouteName);
        }
    }
@endphp

<div {{ $attributes->class(['prazzu-page', 'tool-page'])->merge(['data-testid' => \App\Core\Quality\E2E\Support\TestId::make('tool-page', $slug)]) }} data-tool="{{ $slug }}">
    <nav aria-label="Breadcrumb" class="mb-3">
        @php($breadcrumbVertical = app(\App\Core\Navigation\Application\VerticalBreadcrumbContext::class)->active())
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            @if ($breadcrumbVertical['name'])
                <li class="breadcrumb-item"><span>{{ $breadcrumbVertical['name'] }}</span></li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>

    <x-tools.intro :icon="$icon" :tone="$tone" :title="$title" :description="$description" :badge="$badge">
        <x-slot:actions>
            <div class="d-flex flex-wrap gap-2">
                @isset($actions)
                    {{ $actions }}
                @endisset
                @auth
                    <form method="POST" action="{{ route('account.tools.favorite', ['tool' => $slug]) }}">
                        @csrf
                        <button class="btn btn-sm {{ $toolIsFavorite ? 'btn-warning' : 'btn-outline-secondary' }}" type="submit" aria-pressed="{{ $toolIsFavorite ? 'true' : 'false' }}">
                            <i class="bi {{ $toolIsFavorite ? 'bi-star-fill' : 'bi-star' }} me-1" aria-hidden="true"></i>
                            {{ $toolIsFavorite ? 'Desfavoritar' : 'Favoritar' }}
                        </button>
                    </form>
                @else
                    <a
                        class="btn btn-sm btn-outline-secondary"
                        href="{{ route('register', ['source' => 'tool_favorite', 'tool' => $slug]) }}"
                        aria-label="Favoritar {{ $title }} com uma conta gratuita"
                    >
                        <i class="bi bi-star me-1" aria-hidden="true"></i>
                        Favoritar
                    </a>
                @endauth
            </div>
        </x-slot:actions>
    </x-tools.intro>

    @if(session('tool_favorite_status'))
        <div class="alert alert-success py-2" role="status">{{ session('tool_favorite_status') }}</div>
    @endif

    @if ($showValidation)
        <x-tools.validation-summary class="mb-4" />
    @endif

    {{ $slot }}

    <x-feedback.tool-resolution :slug="$slug" />

    <x-tool-feature-tiers :slug="$slug" />

    <x-tools.plus-result-cta :slug="$slug" :history-url="$historyUrl" />

    <x-tools.trust-seo :slug="$slug" />

    @php($nextSteps = $toolCatalog->nextSteps($slug))
    @if ($nextSteps->isNotEmpty())
        <section class="mt-5 prazzu-next-steps" aria-labelledby="{{ $slug }}-next-steps-title" data-tool-next-steps hidden>
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-2 gap-sm-3 mb-3">
                <div>
                    <span class="prazzu-eyebrow">Depois deste resultado</span>
                    <h2 class="h4 mb-1" id="{{ $slug }}-next-steps-title">Próximos passos</h2>
                    <p class="small text-body-secondary mb-0">Continue por uma ação que faz sentido a partir desta ferramenta.</p>
                </div>
                <a class="small" href="{{ route('tools.index') }}">Ver catálogo completo</a>
            </div>
            <div class="row g-3">
                @foreach ($nextSteps as $nextStep)
                    <div class="{{ $nextStep['is_primary_next_step'] ? 'col-12 col-xl-6' : 'col-12 col-md-6 col-xl-2' }}">
                        <a class="card border-0 shadow-sm h-100 text-decoration-none text-body prazzu-next-step-card{{ $nextStep['is_primary_next_step'] ? ' prazzu-next-step-card--primary' : '' }}" href="{{ url()->query(route($nextStep['route_name']), ['source' => 'related_tools', 'from_tool' => $slug, 'position' => $nextStep['journey_position']]) }}">
                            <div class="card-body">
                                @if ($nextStep['is_primary_next_step'])
                                    <span class="prazzu-badge prazzu-badge--green mb-3">Próximo passo recomendado</span>
                                @else
                                    <span class="prazzu-eyebrow mb-2 d-block">Também pode ajudar</span>
                                @endif
                                <div class="d-flex align-items-start gap-3">
                                    <span class="prazzu-tool-icon prazzu-tool-icon-sm" aria-hidden="true"><i class="bi {{ $nextStep['icon'] }}"></i></span>
                                    <div>
                                        <h3 class="h6 mb-1">{{ $nextStep['name'] }}</h3>
                                        <p class="small text-body-secondary mb-0">{{ \Illuminate\Support\Str::limit($nextStep['description'], $nextStep['is_primary_next_step'] ? 150 : 90) }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($analyticsConfig !== null)
        <script type="application/json" data-tool-analytics-config nonce="{{ $cspNonce ?? '' }}">{!! json_encode($analyticsConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
    @endif
</div>
