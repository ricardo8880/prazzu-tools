@php
    $items = [
        ['label' => 'Visão geral', 'icon' => 'bi-grid', 'route' => 'admin.index', 'active' => 'admin.index'],
        ['label' => 'Analytics', 'icon' => 'bi-graph-up-arrow', 'route' => 'admin.analytics.index', 'active' => 'admin.analytics.*'],
        ['label' => 'Aquisição', 'icon' => 'bi-signpost-split', 'route' => 'admin.acquisition.contexts.index', 'active' => 'admin.acquisition.*'],
        ['label' => 'Blog', 'icon' => 'bi-journal-text', 'route' => 'admin.blog.posts.index', 'active' => 'admin.blog.*'],
    ];
@endphp

<nav class="container-fluid pt-3" aria-label="Navegação administrativa">
    <div class="card border-0 shadow-sm">
        <div class="card-body py-2 d-flex flex-column flex-lg-row align-items-lg-center gap-2">
            <a class="navbar-brand fw-semibold me-lg-3" href="{{ route('admin.index') }}">
                <i class="bi bi-shield-check me-1" aria-hidden="true"></i> Administração
            </a>
            <div class="nav nav-pills flex-column flex-sm-row gap-1">
                @foreach ($items as $item)
                    <a
                        class="nav-link {{ request()->routeIs($item['active']) ? 'active' : '' }}"
                        href="{{ route($item['route']) }}"
                        @if (request()->routeIs($item['active'])) aria-current="page" @endif
                    >
                        <i class="bi {{ $item['icon'] }} me-1" aria-hidden="true"></i>{{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>
