@props([
    'slug',
    'plusFeatures' => [],
])

@php
    $featureNames = collect($plusFeatures)
        ->pluck('name')
        ->filter()
        ->take(2)
        ->values();
    $headingId = $slug.'-plus-result-cta-title';
@endphp

<aside
    class="prazzu-plus-result-cta mt-4"
    data-plus-result-cta
    data-plus-result-cta-tool="{{ $slug }}"
    aria-labelledby="{{ $headingId }}"
    hidden
>
    <div class="prazzu-plus-result-cta__icon" aria-hidden="true">
        <i class="bi bi-stars"></i>
    </div>

    <div class="prazzu-plus-result-cta__content min-w-0">
        <span class="prazzu-plus-result-cta__eyebrow">Prazzu Plus</span>
        <h2 class="h5 mb-1" id="{{ $headingId }}">Gostou do resultado? Vá além com o Plus.</h2>
        <p class="text-body-secondary mb-0">
            @if ($featureNames->isNotEmpty())
                Recursos como <strong>{{ $featureNames->join(' e ') }}</strong> fazem parte da experiência Plus.
            @else
                Tenha mais produtividade, continuidade e recursos avançados para aproveitar ainda mais as ferramentas do Prazzu.
            @endif
            Durante a fase de lançamento, os recursos Plus continuam liberados gratuitamente.
        </p>
    </div>

    <a class="btn prazzu-plus-result-cta__action flex-shrink-0" href="{{ route('plans') }}">
        Conhecer o Plus
        <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
    </a>
</aside>
