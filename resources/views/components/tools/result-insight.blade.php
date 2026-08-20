@props([
    'title',
    'description' => null,
    'items' => [],
    'icon' => 'bi-lightbulb',
    'headingId' => null,
])

@php
    $headingId ??= 'result-insight-'.substr(md5((string) $title), 0, 10);
    $items = array_values(array_filter($items, static fn ($item): bool => is_string($item) && trim($item) !== ''));
@endphp

<section {{ $attributes->class(['border rounded-3 bg-body-tertiary p-3 p-md-4 mb-4'])->merge(['data-result-insight' => '']) }} aria-labelledby="{{ $headingId }}">
    <div class="d-flex gap-3 align-items-start">
        <i class="bi {{ $icon }} fs-4" aria-hidden="true"></i>
        <div class="min-w-0">
            <span class="prazzu-eyebrow">Leitura rápida</span>
            <h3 class="h5 mb-2" id="{{ $headingId }}">{{ $title }}</h3>
            @if($description)
                <p class="mb-0 text-body-secondary">{{ $description }}</p>
            @endif

            @if($items !== [])
                <ul class="mb-0 mt-3 ps-3">
                    @foreach($items as $item)
                        <li @class(['mt-2' => ! $loop->first])>{{ $item }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</section>
