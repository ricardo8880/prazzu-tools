@props([
    'label',
    'value',
    'icon' => 'calculator',
    'description' => null,
])

@php($iconClass = str_starts_with($icon, 'bi-') ? $icon : 'bi-'.$icon)

<div {{ $attributes->class(['card', 'h-100', 'border-0', 'shadow-sm', 'prazzu-result-metric']) }}>
    <div class="card-body d-flex align-items-center gap-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-body-tertiary flex-shrink-0 p-3" aria-hidden="true">
            <i class="bi {{ $iconClass }} fs-5"></i>
        </span>
        <span class="d-flex flex-column">
            <small class="text-body-secondary">{{ $label }}</small>
            <strong class="fs-5">{{ $value }}</strong>
            @if($description)
                <small class="text-body-secondary">{{ $description }}</small>
            @endif
        </span>
    </div>
</div>
