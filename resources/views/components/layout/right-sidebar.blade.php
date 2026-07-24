<aside class="prazzu-right-sidebar d-none d-xxl-flex flex-column" aria-label="Conteúdo complementar">
    <div class="prazzu-sidebar-scroll p-3">
        <section class="prazzu-panel p-3 mb-3" aria-labelledby="featured-articles-title">
            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                <h2 id="featured-articles-title" class="h6 mb-0">Artigos recentes</h2>
                <a class="prazzu-text-link flex-shrink-0" href="{{ route('blog.index') }}">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>

            @if ($recentBlogPosts->isEmpty())
                <p class="small text-body-secondary mb-0">Os próximos conteúdos aparecerão aqui assim que forem publicados.</p>
            @else
                <div class="d-grid gap-3">
                    @foreach ($recentBlogPosts as $article)
                        <article>
                            <a class="prazzu-article-card text-decoration-none" href="{{ route('blog.show', $article->slug) }}">
                                @if ($article->cover_image_path)
                                    <img src="{{ asset('storage/'.$article->cover_image_path) }}" alt="{{ $article->cover_image_alt ?: $article->title }}" width="72" height="72">
                                @else
                                    <span class="prazzu-icon-tile flex-shrink-0"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
                                @endif
                                <span class="min-w-0">
                                    <strong class="d-block">{{ $article->title }}</strong>
                                    <small><span class="prazzu-accent-text">{{ $article->category }}</span> <span class="text-body-secondary">· {{ $article->readingTimeMinutes() }} min</span></small>
                                </span>
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif
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

        @if ($toolFeedbackManifest !== null)
            <x-feedback.tool-feedback :tool="$toolFeedbackManifest" />
        @endif
    </div>
</aside>
