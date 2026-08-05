@props(['href', 'label' => 'Exportar', 'icon' => 'download', 'variant' => 'outline-success', 'format' => null])
<a {{ $attributes->class(['btn', 'btn-'.$variant])->merge(['data-testid' => \App\Core\Quality\E2E\Support\TestId::make('download', $format ?? $label)]) }} href="{{ $href }}">
    <i class="bi bi-{{ $icon }} me-1" aria-hidden="true"></i>{{ $label }}
</a>
