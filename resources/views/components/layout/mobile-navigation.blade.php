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
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/ferramentas') }}"><i class="bi bi-grid"></i>Todas as ferramentas</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/ferramentas/geradores') }}"><i class="bi bi-magic"></i>Geradores</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/ferramentas/calculadoras') }}"><i class="bi bi-calculator"></i>Calculadoras</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/ferramentas/conversores') }}"><i class="bi bi-arrow-left-right"></i>Conversores</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/ferramentas/validadores') }}"><i class="bi bi-shield-check"></i>Validadores</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/blog') }}"><i class="bi bi-journal-text"></i>Blog</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/planos') }}"><i class="bi bi-gem"></i>Planos</a>
            <a class="nav-link prazzu-sidebar-link" href="{{ url('/sobre') }}"><i class="bi bi-info-circle"></i>Sobre</a>
        </nav>

        <div class="d-grid gap-2 mt-auto pt-4">
            <a class="btn prazzu-btn-outline" href="{{ url('/entrar') }}">Entrar</a>
            <a class="btn btn-primary prazzu-btn-primary" href="{{ url('/criar-conta') }}">Criar conta grátis</a>
        </div>
    </div>
</div>
