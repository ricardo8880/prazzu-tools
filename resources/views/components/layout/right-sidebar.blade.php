@php
    $articles = [
        ['title' => 'Como escolher o melhor regime tributário para seu cliente', 'category' => 'Gestão', 'time' => '5 min de leitura', 'url' => route('blog.show', 'melhor-regime-tributario'), 'image' => asset('assets/images/article-placeholder-1.svg')],
        ['title' => '5 dicas para organizar documentos contábeis de forma eficiente', 'category' => 'Organização', 'time' => '7 min de leitura', 'url' => route('blog.show', 'organizar-documentos-contabeis'), 'image' => asset('assets/images/article-placeholder-2.svg')],
        ['title' => 'Principais obrigações acessórias do mês', 'category' => 'Fiscal', 'time' => '4 min de leitura', 'url' => route('blog.show', 'obrigacoes-acessorias-do-mes'), 'image' => asset('assets/images/article-placeholder-3.svg')],
    ];
@endphp

<aside class="prazzu-right-sidebar d-none d-xxl-flex flex-column" aria-label="Conteúdo complementar">
    <div class="prazzu-sidebar-scroll p-3">
        <section class="prazzu-panel p-3 mb-3" aria-labelledby="popular-tools-title">
            <h2 id="popular-tools-title" class="h6 mb-3">Ferramentas populares</h2>
            <div class="d-grid gap-3">
                @foreach ($popularTools as $tool)
                    <a class="prazzu-popular-tool text-decoration-none" href="{{ route('tools.show', $tool['slug']) }}">
                        <span class="prazzu-icon-tile prazzu-icon-tile--{{ $tool['tone'] }}">
                            <i class="bi {{ $tool['icon'] }}" aria-hidden="true"></i>
                        </span>
                        <span class="min-w-0">
                            <strong class="d-block text-truncate">{{ $tool['name'] }}</strong>
                            <small class="text-body-secondary">{{ $tool['uses_label'] ?? number_format($tool['uses_count'], 0, ',', '.').' usos' }}</small>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="prazzu-panel p-3 mb-3" aria-labelledby="featured-articles-title">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h2 id="featured-articles-title" class="h6 mb-0">Artigos em destaque</h2>
                <a class="prazzu-text-link flex-shrink-0" href="{{ route('blog.index') }}">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="d-grid gap-3">
                @foreach ($articles as $article)
                    <article>
                        <a class="prazzu-article-card text-decoration-none" href="{{ $article['url'] }}">
                            <img src="{{ $article['image'] }}" alt="" width="72" height="72">
                            <span class="min-w-0">
                                <strong class="d-block">{{ $article['title'] }}</strong>
                                <small><span class="prazzu-accent-text">{{ $article['category'] }}</span> <span class="text-body-secondary">· {{ $article['time'] }}</span></small>
                            </span>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="prazzu-panel p-3" aria-labelledby="newsletter-title">
            <h2 id="newsletter-title" class="h6 mb-2">Fique por dentro</h2>
            <p class="small text-body-secondary mb-3">Receba novidades sobre novas ferramentas e conteúdos exclusivos.</p>
            <form action="{{ route('newsletter.store') }}" method="post" class="d-grid gap-2">
                @csrf
                <label for="newsletter-email" class="visually-hidden">Seu melhor e-mail</label>
                <input id="newsletter-email" class="form-control prazzu-form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Seu melhor e-mail" required autocomplete="email">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <button class="btn btn-primary prazzu-btn-primary" type="submit">Inscrever-se</button>
            </form>
        </section>
    </div>
</aside>
