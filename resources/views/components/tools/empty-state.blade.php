@props(['icon' => 'inbox', 'title', 'description' => null])
<div {{ $attributes->class(['text-center py-5']) }}>
    <i class="bi bi-{{ $icon }} display-5 text-body-tertiary" aria-hidden="true"></i>
    <h2 class="h5 mt-3">{{ $title }}</h2>
    @if ($description)<p class="text-body-secondary mb-0">{{ $description }}</p>@endif
    @isset($actions)<div class="d-flex justify-content-center gap-2 mt-3">{{ $actions }}</div>@endisset
</div>
