@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <h2 class="h6">Revise os campos abaixo:</h2>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Conteúdo</h2>
                <div class="mb-3">
                    <label class="form-label" for="title">Título</label>
                    <input class="form-control form-control-lg" id="title" name="title" required maxlength="255" value="{{ old('title', $post->title) }}">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label" for="slug">Slug</label>
                        <div class="input-group">
                            <span class="input-group-text">/blog/</span>
                            <input class="form-control" id="slug" name="slug" value="{{ old('slug', $post->slug) }}" placeholder="gerado-automaticamente">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="category">Categoria</label>
                        <input class="form-control" id="category" name="category" required maxlength="100" value="{{ old('category', $post->category) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="excerpt">Resumo</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3" required maxlength="1000">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>
                <div>
                    <label class="form-label" for="content">Conteúdo do artigo</label>
                    <textarea class="form-control font-monospace" id="content" name="content" rows="22" required>{{ old('content', $post->content) }}</textarea>
                    <div class="form-text">Aceita HTML para títulos, parágrafos, listas, tabelas e links.</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Imagens</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="cover_image">Imagem de capa</label>
                        <input class="form-control" id="cover_image" name="cover_image" type="file" accept="image/*">
                        @if ($post->cover_image_path)<div class="form-text">Imagem atual: {{ $post->cover_image_path }}</div>@endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cover_image_alt">Texto alternativo</label>
                        <input class="form-control" id="cover_image_alt" name="cover_image_alt" maxlength="255" value="{{ old('cover_image_alt', $post->cover_image_alt) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="social_image">Imagem social</label>
                        <input class="form-control" id="social_image" name="social_image" type="file" accept="image/*">
                        @if ($post->social_image_path)<div class="form-text">Imagem atual: {{ $post->social_image_path }}</div>@endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">SEO e descoberta</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="primary_keyword">Palavra-chave principal</label>
                        <input class="form-control" id="primary_keyword" name="primary_keyword" value="{{ old('primary_keyword', $post->primary_keyword) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="related_keywords">Palavras-chave relacionadas</label>
                        <input class="form-control" id="related_keywords" name="related_keywords" value="{{ old('related_keywords', implode(', ', $post->related_keywords ?? [])) }}">
                        <div class="form-text">Separe por vírgulas.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="meta_title">Título SEO</label>
                        <input class="form-control" id="meta_title" name="meta_title" maxlength="255" value="{{ old('meta_title', $post->meta_title) }}">
                        <div class="form-text"><span id="meta-title-count">0</span>/60 caracteres recomendados.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="meta_description">Meta description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="320">{{ old('meta_description', $post->meta_description) }}</textarea>
                        <div class="form-text"><span id="meta-description-count">0</span>/160 caracteres recomendados.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="canonical_url">URL canônica</label>
                        <input class="form-control" id="canonical_url" name="canonical_url" type="url" value="{{ old('canonical_url', $post->canonical_url) }}">
                    </div>
                </div>

                <hr class="my-4">
                <h3 class="h6 mb-3">Prévia aproximada no Google</h3>
                <div class="border rounded p-3 bg-body-tertiary" aria-live="polite">
                    <div id="seo-preview-title" class="fs-5 text-primary text-truncate">Título da postagem</div>
                    <div id="seo-preview-url" class="small text-success text-truncate">{{ url('/blog') }}/slug-da-postagem</div>
                    <div id="seo-preview-description" class="small text-body-secondary mt-1">A descrição da postagem aparecerá aqui.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card border-0 shadow-sm mb-4 sticky-xl-top" style="top: 1rem;">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Publicação</h2>
                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $post->status?->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="published_at">Data e hora de publicação</label>
                    <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="content_updated_at">Última revisão do conteúdo</label>
                    <input class="form-control" id="content_updated_at" name="content_updated_at" type="datetime-local" value="{{ old('content_updated_at', $post->content_updated_at?->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" id="is_featured" name="is_featured" type="checkbox" value="1" @checked(old('is_featured', $post->is_featured))>
                    <label class="form-check-label" for="is_featured">Destacar no blog</label>
                </div>
                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="should_index" value="0">
                    <input class="form-check-input" id="should_index" name="should_index" type="checkbox" value="1" @checked(old('should_index', $post->exists ? $post->should_index : true))>
                    <label class="form-check-label" for="should_index">Permitir indexação</label>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i> Salvar postagem</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.blog.posts.index') }}">Voltar</a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Análise técnica de SEO</h2>
                <p class="small text-body-secondary">Os avisos ajudam na revisão, mas não impedem a publicação.</p>
                <div class="vstack gap-2">
                    @foreach ($seoIssues as $issue)
                        <div class="alert alert-{{ $issue['level'] }} py-2 px-3 mb-0 small" role="status">
                            <i class="bi {{ $issue['level'] === 'success' ? 'bi-check-circle' : ($issue['level'] === 'danger' ? 'bi-x-circle' : 'bi-info-circle') }} me-1"></i>
                            {{ $issue['message'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Ferramentas relacionadas</h2>
                @forelse ($tools as $tool)
                    <div class="form-check mb-2">
                        <input class="form-check-input" id="tool-{{ $tool->slug }}" name="related_tools[]" type="checkbox" value="{{ $tool->slug }}" @checked(in_array($tool->slug, old('related_tools', $selectedTools), true))>
                        <label class="form-check-label" for="tool-{{ $tool->slug }}">{{ $tool->name }}</label>
                    </div>
                @empty
                    <p class="text-body-secondary mb-0">Nenhuma ferramenta disponível no catálogo.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
(() => {
    const title = document.getElementById('title');
    const slug = document.getElementById('slug');
    const excerpt = document.getElementById('excerpt');
    const metaTitle = document.getElementById('meta_title');
    const metaDescription = document.getElementById('meta_description');
    const titleCount = document.getElementById('meta-title-count');
    const descriptionCount = document.getElementById('meta-description-count');
    const previewTitle = document.getElementById('seo-preview-title');
    const previewUrl = document.getElementById('seo-preview-url');
    const previewDescription = document.getElementById('seo-preview-description');
    const baseUrl = @json(url('/blog'));

    const slugify = value => value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    const refresh = () => {
        const resolvedTitle = metaTitle.value.trim() || title.value.trim() || 'Título da postagem';
        const resolvedDescription = metaDescription.value.trim() || excerpt.value.trim() || 'A descrição da postagem aparecerá aqui.';
        const resolvedSlug = slug.value.trim() || slugify(title.value) || 'slug-da-postagem';

        titleCount.textContent = metaTitle.value.length;
        descriptionCount.textContent = metaDescription.value.length;
        previewTitle.textContent = resolvedTitle;
        previewUrl.textContent = `${baseUrl}/${resolvedSlug}`;
        previewDescription.textContent = resolvedDescription;
    };

    [title, slug, excerpt, metaTitle, metaDescription].forEach(element => element.addEventListener('input', refresh));
    refresh();
})();
</script>
@endpush
