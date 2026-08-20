@props([
    'title',
    'description' => null,
    'badge' => null,
    'open' => false,
])

<details {{ $attributes->class(['prazzu-form-disclosure']) }} @if($open) open @endif>
    <summary class="prazzu-form-disclosure__summary">
        <div class="min-w-0">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="prazzu-form-disclosure__title">{{ $title }}</span>
                @if($badge)<span class="badge rounded-pill text-bg-light border">{{ $badge }}</span>@endif
            </div>
            @if($description)<span class="prazzu-form-disclosure__description">{{ $description }}</span>@endif
        </div>
        <span class="prazzu-form-disclosure__action" aria-hidden="true">
            <span class="prazzu-form-disclosure__action-label">Mostrar campos</span>
            <i class="bi bi-chevron-down"></i>
        </span>
    </summary>
    <div class="prazzu-form-disclosure__body">
        {{ $slot }}
    </div>
</details>
