@props(['title', 'description' => null, 'headingId' => null, 'tone' => null, 'testId' => 'tool-result'])

<section {{ $attributes->class(['prazzu-tool-card'])->merge(['data-testid' => $testId]) }} @if($headingId) aria-labelledby="{{ $headingId }}" @endif>
    <div class="mb-4">
        <h2 @if($headingId) id="{{ $headingId }}" @endif class="prazzu-section-title mb-1">{{ $title }}</h2>
        @if ($description)<p class="text-body-secondary mb-0">{{ $description }}</p>@endif
    </div>
    {{ $slot }}
</section>
