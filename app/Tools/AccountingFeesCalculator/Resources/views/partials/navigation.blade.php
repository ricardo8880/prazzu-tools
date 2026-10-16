<nav class="mb-4" aria-label="Navegação da Calculadora de Honorários Contábeis">
    <div class="nav nav-pills flex-column flex-sm-row gap-2">
        <a class="nav-link {{ request()->routeIs('tools.accounting-fee-calculator.index', 'tools.accounting-fee-calculator.calculate') ? 'active' : '' }}"
           href="{{ route('tools.accounting-fee-calculator.index') }}"
           @if (request()->routeIs('tools.accounting-fee-calculator.index', 'tools.accounting-fee-calculator.calculate')) aria-current="page" @endif>
            <i class="bi bi-calculator me-1" aria-hidden="true"></i>Calculadora
        </a>
        @auth
        <a class="nav-link {{ request()->routeIs('tools.accounting-fee-calculator.history.*') ? 'active' : '' }}"
           href="{{ route('tools.accounting-fee-calculator.history.index') }}"
           @if (request()->routeIs('tools.accounting-fee-calculator.history.*')) aria-current="page" @endif>
            <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Histórico
        </a>
        @endauth
        <a class="nav-link {{ request()->routeIs('tools.accounting-fee-calculator.adjustments.*') ? 'active' : '' }}"
           href="{{ route('tools.accounting-fee-calculator.adjustments.index') }}"
           @if (request()->routeIs('tools.accounting-fee-calculator.adjustments.*')) aria-current="page" @endif>
            <i class="bi bi-arrow-up-right-circle me-1" aria-hidden="true"></i>Reajustes
        </a>
    </div>
</nav>
