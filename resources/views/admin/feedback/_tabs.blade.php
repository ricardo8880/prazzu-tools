<div class="nav nav-tabs mb-4" role="tablist" aria-label="Tipos de feedback">
    <a
        class="nav-link {{ request()->routeIs('admin.feedback.tools.*') ? 'active' : '' }}"
        href="{{ route('admin.feedback.tools.index') }}"
        @if (request()->routeIs('admin.feedback.tools.*')) aria-current="page" @endif
    >
        <i class="bi bi-tools me-1" aria-hidden="true"></i>Feedback das ferramentas
    </a>
    <a
        class="nav-link {{ request()->routeIs('admin.feedback.suggestions.*') ? 'active' : '' }}"
        href="{{ route('admin.feedback.suggestions.index') }}"
        @if (request()->routeIs('admin.feedback.suggestions.*')) aria-current="page" @endif
    >
        <i class="bi bi-lightbulb me-1" aria-hidden="true"></i>Precisa de algo novo?
    </a>
    <a
        class="nav-link {{ request()->routeIs('admin.feedback.pages.*') ? 'active' : '' }}"
        href="{{ route('admin.feedback.pages.index') }}"
        @if (request()->routeIs('admin.feedback.pages.*')) aria-current="page" @endif
    >
        <i class="bi bi-star me-1" aria-hidden="true"></i>Avaliações da página
    </a>
</div>
