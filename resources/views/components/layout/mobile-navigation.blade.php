<div class="offcanvas offcanvas-start prazzu-mobile-navigation" tabindex="-1" id="prazzuMobileNavigation" aria-labelledby="prazzuMobileNavigationLabel">
    <div class="offcanvas-header border-bottom">
        <a class="prazzu-brand text-decoration-none" href="{{ route('home') }}" id="prazzuMobileNavigationLabel">
            <span class="prazzu-brand__mark" aria-hidden="true"><span></span><span></span></span>
            <span class="prazzu-brand__name">Prazzu</span>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <nav class="nav nav-pills flex-column gap-1" aria-label="Navegação móvel">
            <a class="nav-link prazzu-sidebar-link" href="{{ route('home') }}"><i class="bi bi-house"></i>Início</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ route('tools.index') }}"><i class="bi bi-grid"></i>Todas as ferramentas</a>

            @foreach ($toolCategories as $category)
                <a class="nav-link prazzu-sidebar-link" href="{{ $category['url'] }}">
                    <i class="bi {{ $category['icon'] }}"></i>{{ $category['name'] }}
                </a>
            @endforeach

            <a class="nav-link prazzu-sidebar-link" href="{{ route('blog.index') }}"><i class="bi bi-journal-text"></i>Blog</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ route('plans') }}"><i class="bi bi-gem"></i>Planos</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ route('about') }}"><i class="bi bi-info-circle"></i>Sobre</a>
        </nav>

        <div class="d-grid gap-2 mt-auto pt-4">
            <a class="btn prazzu-btn-outline" href="{{ route('login.placeholder') }}">Entrar</a>
            <a class="btn btn-primary prazzu-btn-primary" href="{{ route('register.placeholder') }}">Criar conta grátis</a>
        </div>
    </div>
</div>
