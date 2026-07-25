@props([
    'title',
    'description',
    'icon',
    'slug',
    'badge' => 'Grátis',
    'tone' => 'purple',
    'showValidation' => true,
])

<div {{ $attributes->class(['prazzu-page', 'tool-page']) }} data-tool="{{ $slug }}">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
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

    @php($relatedTools = app(\App\Core\Tools\ToolCatalog::class)->related($slug))
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
</div>
