<aside class="prazzu-left-sidebar d-none d-xl-flex flex-column" aria-label="Navegação de ferramentas">
    <div class="prazzu-sidebar-scroll p-3">
        <section class="prazzu-panel prazzu-navigation-panel p-2 mb-4" aria-labelledby="navigation-title">
            <h2 id="navigation-title" class="prazzu-eyebrow px-2 pt-2 mb-2">Navegação</h2>
            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link prazzu-sidebar-link {{ request()->routeIs('tools.index') ? 'active' : '' }}" href="{{ route('tools.index') }}">
                    <i class="bi bi-grid" aria-hidden="true"></i>
                    <span>Todas as ferramentas</span>
                </a>

                @foreach ($toolCategories as $category)
                    <a class="nav-link prazzu-sidebar-link {{ request()->routeIs('tools.category') && request()->route('category') === $category['slug'] ? 'active' : '' }}" href="{{ $category['url'] }}">
                        <i class="bi {{ $category['icon'] }}" aria-hidden="true"></i>
                        <span>{{ $category['name'] }}</span>
                    </a>
                @endforeach
            </nav>
        </section>

        <section class="prazzu-panel prazzu-premium-card p-3 mb-4" aria-labelledby="premium-title">
            <i class="bi bi-crown prazzu-premium-card__icon" aria-hidden="true"></i>
            <h2 id="premium-title" class="h6 mt-3 mb-2">Ferramentas Premium</h2>
            <p class="small text-body-secondary mb-3">Acesso ilimitado a todas as ferramentas e recursos exclusivos.</p>
            <a class="btn btn-primary prazzu-btn-primary w-100" href="{{ route('plans') }}">Ver planos</a>
        </section>

        <section class="prazzu-panel p-3" aria-labelledby="suggestion-title">
            <h2 id="suggestion-title" class="h6 mb-2">Precisa de algo novo?</h2>
            <p class="small text-body-secondary mb-3">Sugira uma ferramenta que pode te ajudar no dia a dia.</p>
            <a class="btn prazzu-btn-outline w-100" href="{{ route('tools.suggest') }}">Enviar sugestão</a>
        </section>
    </div>
</aside>
