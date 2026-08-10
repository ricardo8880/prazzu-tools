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
        @isset($actions)
            <x-slot:actions>{{ $actions }}</x-slot:actions>
        @endisset
    </x-tools.intro>

    <x-tool-feature-tiers :slug="$slug" />

    @if ($showValidation)
        <x-tools.validation-summary class="mb-4" />
    @endif

    {{ $slot }}

    <x-tools.plus-result-cta :slug="$slug" :plus-features="$catalogTool['plus_features'] ?? []" />

    @php($relatedTools = $toolCatalog->related($slug))
    @if ($relatedTools->isNotEmpty())
        <section class="mt-5" aria-labelledby="{{ $slug }}-related-title">
            <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
                <div>
                    <span class="prazzu-eyebrow">Continue sua análise</span>
                    <h2 class="h4 mb-0" id="{{ $slug }}-related-title">Ferramentas relacionadas</h2>
                </div>
                <a class="small" href="{{ route('tools.index') }}">Ver catálogo completo</a>
            </div>
            <div class="row g-3">
                @foreach ($relatedTools as $related)
                    <div class="col-12 col-md-6 col-xl-3">
                        <a class="card border-0 shadow-sm h-100 text-decoration-none text-body" href="{{ route($related['route_name']) }}">
                            <div class="card-body">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="prazzu-tool-icon prazzu-tool-icon-sm" aria-hidden="true"><i class="bi {{ $related['icon'] }}"></i></span>
                                    <div>
                                        <h3 class="h6 mb-1">{{ $related['name'] }}</h3>
                                        <p class="small text-body-secondary mb-0">{{ \Illuminate\Support\Str::limit($related['description'], 105) }}</p>
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
