@props(['title', 'description' => null, 'headingId' => null, 'tone' => null, 'testId' => 'tool-result', 'eyebrow' => 'Seu resultado'])

<section {{ $attributes->class(['prazzu-tool-card', 'prazzu-tool-result-panel'])->merge(['data-testid' => $testId, 'data-tool-result-panel' => '']) }} @if($headingId) aria-labelledby="{{ $headingId }}" @endif>
    <div class="prazzu-tool-result-panel__header mb-4">
        @if ($eyebrow)<span class="prazzu-eyebrow">{{ $eyebrow }}</span>@endif
        <h2 @if($headingId) id="{{ $headingId }}" @endif class="prazzu-section-title mb-1">{{ $title }}</h2>
        @if ($description)<p class="text-body-secondary mb-0">{{ $description }}</p>@endif
    </div>
    {{ $slot }}
</section>
