@props([
    'title',
    'description',
    'icon',
    'slug',
    'badge' => 'Grátis',
    'tone' => 'purple',
    'showValidation' => true,
])

<div {{ $attributes->class(['prazzu-page', 'tool-page']) }} data-tool="{{ $slug }}">
    <nav aria-label="Breadcrumb" class="mb-3">
        <ol class="breadcrumb prazzu-breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>

    <x-tools.intro :icon="$icon" :tone="$tone" :title="$title" :description="$description" :badge="$badge">
        @isset($actions)
            <x-slot:actions>{{ $actions }}</x-slot:actions>
        @endisset
    </x-tools.intro>

    <x-tool-feature-tiers :slug="$slug" />

    @if ($showValidation)
        <x-tools.validation-summary class="mb-4" />
    @endif

    {{ $slot }}
</div>
