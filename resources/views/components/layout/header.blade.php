@php
    $navigation = [
        ['label' => 'Início', 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Ferramentas', 'url' => url('/ferramentas'), 'active' => request()->is('ferramentas*')],
        ['label' => 'Blog', 'url' => url('/blog'), 'active' => request()->is('blog*')],
        ['label' => 'Planos', 'url' => url('/planos'), 'active' => request()->is('planos*')],
        ['label' => 'Recursos', 'url' => url('/recursos'), 'active' => request()->is('recursos*'), 'dropdown' => true],
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
                    <img src="{{ asset('assets/images/prazzu-logo-mark.png') }}" alt="">
                </span>
            </a>

            <nav class="prazzu-primary-nav d-none d-xl-flex align-items-stretch ms-5" aria-label="Navegação principal">
                @foreach ($navigation as $item)
                    @if (!empty($item['dropdown']))
                        <div class="dropdown d-flex">
                            <a
                                class="prazzu-primary-nav__link dropdown-toggle {{ $item['active'] ? 'is-active' : '' }}"
                                href="{{ $item['url'] }}"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                {{ $item['label'] }}
                            </a>
                            <ul class="dropdown-menu prazzu-dropdown-menu">
                                <li><a class="dropdown-item" href="{{ url('/recursos/guias') }}"><i class="bi bi-journal-text me-2"></i>Guias</a></li>
                                <li><a class="dropdown-item" href="{{ url('/recursos/modelos') }}"><i class="bi bi-file-earmark-richtext me-2"></i>Modelos</a></li>
                                <li><a class="dropdown-item" href="{{ url('/recursos/novidades') }}"><i class="bi bi-stars me-2"></i>Novidades</a></li>
                            </ul>
                        </div>
                    @else
                        <a class="prazzu-primary-nav__link {{ $item['active'] ? 'is-active' : '' }}" href="{{ $item['url'] }}">
                            {{ $item['label'] }}
                        </a>
                    @endif
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

                <a class="btn btn-primary prazzu-btn-primary d-none d-sm-inline-flex" href="{{ url('/entrar') }}">Entrar</a>
                <a class="btn btn-primary prazzu-btn-primary d-none d-md-inline-flex" href="{{ url('/criar-conta') }}">Criar conta grátis</a>
            </div>
        </div>
    </div>
</header>
