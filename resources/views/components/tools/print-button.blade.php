@props([
    'label' => 'Imprimir / Salvar como PDF',
    'icon' => 'bi-printer',
])

<button {{ $attributes->class(['btn btn-primary']) }} type="button" onclick="window.print()">
    @if ($icon)
        <i class="bi {{ $icon }} me-1" aria-hidden="true"></i>
    @endif
    {{ $label }}
</button>
