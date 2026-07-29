@props([
    'label' => 'Imprimir / Salvar como PDF',
    'icon' => 'bi-printer',
])

@php($buttonLabel = trim((string) $slot) !== '' ? $slot : $label)

<button {{ $attributes->class(['btn btn-primary'])->merge(['data-browser-action' => 'print', 'data-analytics-client-only' => 'true']) }} type="button">
    @if ($icon)
        <i class="bi {{ $icon }} me-1" aria-hidden="true"></i>
    @endif
    {{ $buttonLabel }}
</button>
