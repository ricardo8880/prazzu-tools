@props([
    'title',
    'description',
    'buttonLabel' => 'Usar dados',
    'icon' => 'arrow-left-right',
    'tone' => 'primary',
])

<div {{ $attributes->class(['alert', 'alert-'.$tone, 'd-flex', 'flex-column', 'flex-md-row', 'align-items-md-center', 'justify-content-between', 'gap-3']) }} role="status">
    <div>
        <div class="fw-semibold">
            <i class="bi bi-{{ $icon }} me-1" aria-hidden="true"></i>{{ $title }}
        </div>
        <div class="small">{{ $description }}</div>
    </div>
    @isset($button)
        <button {{ $button->attributes->class(['btn', 'btn-'.$tone, 'btn-sm', 'flex-shrink-0']) }} type="button">
            {{ $button->isEmpty() ? $buttonLabel : $button }}
        </button>
    @endisset
</div>
