@props(['title', 'description' => null, 'headingId' => null, 'badge' => null])

<section {{ $attributes->class(['prazzu-form-panel']) }} @if($headingId) aria-labelledby="{{ $headingId }}" @endif>
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
        <div>
            <h2 @if($headingId) id="{{ $headingId }}" @endif class="prazzu-section-title mb-1">{{ $title }}</h2>
            @if ($description)<p class="text-body-secondary mb-0">{{ $description }}</p>@endif
        </div>
        @if ($badge)<span class="badge rounded-pill text-bg-light border align-self-start">{{ $badge }}</span>@endif
    </div>
    {{ $slot }}
</section>
