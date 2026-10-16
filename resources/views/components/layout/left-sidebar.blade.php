<aside class="prazzu-left-sidebar d-none d-xl-flex flex-column" aria-label="Tool navigation">
    <div class="prazzu-sidebar-scroll p-3">
        <section class="prazzu-panel prazzu-navigation-panel p-2 mb-4" aria-labelledby="navigation-title">
            <h2 id="navigation-title" class="prazzu-eyebrow px-2 pt-2 mb-2">Navigation</h2>
            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link prazzu-sidebar-link {{ request()->routeIs('tools.index') ? 'active' : '' }}" href="{{ route('tools.index') }}">
                    <i class="bi bi-grid" aria-hidden="true"></i>
                    <span>All tools</span>
                </a>

                @foreach ($toolCategories as $category)
                    <a class="nav-link prazzu-sidebar-link {{ request()->routeIs('tools.category') && request()->route('category') === $category['slug'] ? 'active' : '' }}" href="{{ $category['url'] }}">
                        <i class="bi {{ $category['icon'] }}" aria-hidden="true"></i>
                        <span>{{ $category['name'] }}</span>
                    </a>
                @endforeach
            </nav>
        </section>

        <section class="prazzu-panel prazzu-premium-card p-3 mb-4" aria-labelledby="prazzu-plus-title">
            <i class="bi bi-crown prazzu-premium-card__icon" aria-hidden="true"></i>
            <h2 id="prazzu-plus-title" class="h6 mt-3 mb-2">Prazzu Plus</h2>
            <p class="small text-body-secondary mb-3">Advanced productivity, volume, and history features. Each tool’s Essential experience remains free.</p>
            <a class="btn btn-primary prazzu-btn-primary w-100" href="{{ route('plans') }}">View plans</a>
        </section>

        <section class="prazzu-panel p-3" aria-labelledby="suggestion-title">
            <h2 id="suggestion-title" class="h6 mb-2">Need something new?</h2>
            <p class="small text-body-secondary mb-3">Suggest a tool that could help with your day-to-day work.</p>
            <a class="btn prazzu-btn-outline w-100" href="{{ route('tools.suggest') }}">Send suggestion</a>
        </section>
    </div>
</aside>
