<nav class="mb-4" aria-label="Navegação da Calculadora de Honorários Contábeis">
    <div class="nav nav-pills flex-column flex-sm-row gap-2">
        <a class="nav-link {{ request()->routeIs('tools.calculadora-de-honorarios-contabeis.index', 'tools.calculadora-de-honorarios-contabeis.calculate') ? 'active' : '' }}"
           href="{{ route('tools.calculadora-de-honorarios-contabeis.index') }}"
           @if (request()->routeIs('tools.calculadora-de-honorarios-contabeis.index', 'tools.calculadora-de-honorarios-contabeis.calculate')) aria-current="page" @endif>
            <i class="bi bi-calculator me-1" aria-hidden="true"></i>Calculadora
        </a>
        @auth
        <a class="nav-link {{ request()->routeIs('tools.calculadora-de-honorarios-contabeis.history.*') ? 'active' : '' }}"
           href="{{ route('tools.calculadora-de-honorarios-contabeis.history.index') }}"
           @if (request()->routeIs('tools.calculadora-de-honorarios-contabeis.history.*')) aria-current="page" @endif>
            <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Histórico
        </a>
        @endauth
        <a class="nav-link {{ request()->routeIs('tools.calculadora-de-honorarios-contabeis.adjustments.*') ? 'active' : '' }}"
           href="{{ route('tools.calculadora-de-honorarios-contabeis.adjustments.index') }}"
           @if (request()->routeIs('tools.calculadora-de-honorarios-contabeis.adjustments.*')) aria-current="page" @endif>
            <i class="bi bi-arrow-up-right-circle me-1" aria-hidden="true"></i>Reajustes
        </a>
    </div>
</nav>
