@php
    $navigation = [
        ['label' => 'Início', 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Ferramentas', 'url' => url('/ferramentas'), 'active' => request()->is('ferramentas*')],
        ['label' => 'Blog', 'url' => url('/blog'), 'active' => request()->is('blog*')],
        ['label' => 'Planos', 'url' => url('/planos'), 'active' => request()->is('planos*')],
        ['label' => 'Recursos', 'url' => url('/recursos'), 'active' => request()->is('recursos*')],
        ['label' => 'Sobre', 'url' => url('/sobre'), 'active' => request()->is('sobre')],
    ];
@endphp

<header class="prazzu-header border-bottom">
    <div class="container-fluid h-100 px-3 px-lg-4">
        <div class="d-flex align-items-center h-100 gap-3">
            <button
                class="btn prazzu-icon-button d-xl-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#prazzuMobileNavigation"
                aria-controls="prazzuMobileNavigation"
                aria-label="Abrir navegação"
            >
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <a class="prazzu-brand text-decoration-none flex-shrink-0" href="{{ route('home') }}" aria-label="Prazzu - Página inicial">
                <span class="prazzu-brand__mark" aria-hidden="true">
                    <img class="prazzu-brand__logo prazzu-brand__logo--light" src="{{ asset('assets/images/logo-tema-claro.png') }}" alt="">
                    <img class="prazzu-brand__logo prazzu-brand__logo--dark" src="{{ asset('assets/images/logo-tema-escuro.png') }}" alt="">
                </span>
            </a>

            <nav class="prazzu-primary-nav d-none d-xl-flex align-items-stretch ms-5" aria-label="Navegação principal">
                @foreach ($navigation as $item)
                    <a
                        class="prazzu-primary-nav__link {{ $item['active'] ? 'is-active' : '' }}"
                        href="{{ $item['url'] }}"
                        @if ($item['active']) aria-current="page" @endif
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <div class="btn-group prazzu-theme-switcher" role="group" aria-label="Alternar tema">
                    <button class="btn prazzu-icon-button" type="button" data-theme-value="light" aria-label="Usar tema claro">
                        <i class="bi bi-sun" aria-hidden="true"></i>
                    </button>
                    <button class="btn prazzu-icon-button" type="button" data-theme-value="dark" aria-label="Usar tema escuro">
                        <i class="bi bi-moon" aria-hidden="true"></i>
                    </button>
                </div>

                @auth
                    <a class="btn btn-primary prazzu-btn-primary d-none d-sm-inline-flex" href="{{ route('account.show') }}">
                        <i class="bi bi-person-circle me-2" aria-hidden="true"></i>Minha conta
                    </a>
                @else
                    <a class="btn prazzu-btn-outline d-none d-sm-inline-flex" href="{{ route('login') }}">Entrar</a>
                    <a class="btn btn-primary prazzu-btn-primary d-none d-md-inline-flex" href="{{ route('register') }}">Criar conta grátis</a>
                @endauth
            </div>
        </div>
    </div>
</header>
