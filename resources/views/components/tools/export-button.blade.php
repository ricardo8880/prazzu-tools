@props(['href', 'label' => 'Exportar', 'icon' => 'download', 'variant' => 'outline-success'])
<a {{ $attributes->class(['btn', 'btn-'.$variant]) }} href="{{ $href }}">
    <i class="bi bi-{{ $icon }} me-1" aria-hidden="true"></i>{{ $label }}
</a>
