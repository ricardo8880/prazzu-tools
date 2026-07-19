@props([
    'icon',
    'tone' => 'purple',
    'badge' => null,
    'badgeClass' => 'prazzu-badge prazzu-badge--green',
    'title',
    'description',
])

<header {{ $attributes->class(['prazzu-tool-intro']) }}>
    <span class="prazzu-icon-tile prazzu-icon-tile--{{ $tone }}" aria-hidden="true">
        <i class="bi bi-{{ $icon }}"></i>
    </span>
    <div class="flex-grow-1">
        @if ($badge)
            <span class="{{ $badgeClass }}">{{ $badge }}</span>
        @endif
        <h1>{{ $title }}</h1>
        <p>{{ $description }}</p>
    </div>
    @isset($actions)
        <div class="d-flex flex-wrap gap-2 align-self-start">{{ $actions }}</div>
    @endisset
</header>
