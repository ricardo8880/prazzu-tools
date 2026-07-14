@extends('layouts.app')

@section('title', 'Blog de contabilidade — Prazzu Tools')
@section('meta_description', 'Conteúdos práticos sobre contabilidade, fiscal, tributário, departamento pessoal e gestão para profissionais e escritórios contábeis.')
@section('canonical_url', route('blog.index'))
@section('og_title', 'Blog de contabilidade — Prazzu Tools')
@section('og_description', 'Conteúdos práticos sobre contabilidade, fiscal, tributário, departamento pessoal e gestão para profissionais e escritórios contábeis.')
@if ($search !== '' || $category !== '' || $posts->currentPage() > 1)
    @section('meta_robots', 'noindex,follow')
@endif

@section('content')
<div class="container-fluid py-4 px-3 px-lg-4">
    <header class="mb-4">
        <span class="prazzu-eyebrow">Blog Prazzu</span>
        <h1 class="display-6 fw-bold mt-2 mb-2">Conteúdo para uma rotina contábil melhor</h1>
        <p class="text-body-secondary fs-5 mb-0">Guias, atualizações e explicações práticas conectadas às ferramentas da plataforma.</p>
    </header>

    <form method="get" action="{{ route('blog.index') }}" class="card border-secondary-subtle bg-body-tertiary mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-lg-7">
                    <label for="blog-search" class="form-label">Buscar conteúdo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input id="blog-search" class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Ex.: Simples Nacional, DAS, rescisão">
                    </div>
                </div>
                <div class="col-12 col-md-8 col-lg-3">
                    <label for="blog-category" class="form-label">Categoria</label>
                    <select id="blog-category" class="form-select" name="categoria">
                        <option value="">Todas as categorias</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->category }}" @selected($category === $item->category)>{{ $item->category }} ({{ $item->posts_count }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-2 d-grid">
                    <button class="btn btn-primary prazzu-btn-primary" type="submit">Filtrar</button>
                </div>
            </div>
        </div>
    </form>

    @if ($featured && $posts->currentPage() === 1 && $search === '' && $category === '')
        <section class="card border-secondary-subtle overflow-hidden mb-4" aria-labelledby="featured-post-title">
            <div class="row g-0">
                @if ($featured->cover_image_path)
                    <div class="col-lg-5">
                        <img class="w-100 h-100 object-fit-cover" style="min-height: 280px" src="{{ asset('storage/'.$featured->cover_image_path) }}" alt="{{ $featured->cover_image_alt ?: $featured->title }}">
                    </div>
                @endif
                <div class="{{ $featured->cover_image_path ? 'col-lg-7' : 'col-12' }}">
                    <div class="card-body p-4 p-lg-5 h-100 d-flex flex-column justify-content-center">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge text-bg-primary">Destaque</span>
                            <span class="badge text-bg-secondary">{{ $featured->category }}</span>
                        </div>
                        <h2 id="featured-post-title" class="h2">{{ $featured->title }}</h2>
                        <p class="text-body-secondary fs-5">{{ $featured->excerpt }}</p>
                        <div class="d-flex flex-wrap align-items-center gap-3 small text-body-secondary mb-4">
                            <span><i class="bi bi-calendar3 me-1"></i>{{ $featured->published_at->format('d/m/Y') }}</span>
                            <span><i class="bi bi-clock me-1"></i>{{ $featured->readingTimeMinutes() }} min de leitura</span>
                        </div>
                        <a class="btn btn-primary prazzu-btn-primary align-self-start" href="{{ route('blog.show', $featured->slug) }}">Ler artigo</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($posts->isEmpty())
        <div class="card border-secondary-subtle text-center">
            <div class="card-body py-5">
                <i class="bi bi-journal-x display-4 text-body-secondary" aria-hidden="true"></i>
                <h2 class="h4 mt-3">Nenhum conteúdo encontrado</h2>
                <p class="text-body-secondary">Tente outros termos ou remova os filtros aplicados.</p>
                <a class="btn prazzu-btn-outline" href="{{ route('blog.index') }}">Limpar filtros</a>
            </div>
        </div>
    @else
        <section aria-labelledby="posts-title">
            <h2 id="posts-title" class="h4 mb-3">{{ $search !== '' || $category !== '' ? 'Resultados' : 'Artigos recentes' }}</h2>
            <div class="row g-4">
                @foreach ($posts as $post)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card h-100 border-secondary-subtle bg-body-tertiary">
                            @if ($post->cover_image_path)
                                <img class="card-img-top object-fit-cover" style="height: 190px" src="{{ asset('storage/'.$post->cover_image_path) }}" alt="{{ $post->cover_image_alt ?: $post->title }}">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between gap-2 mb-3">
                                    <span class="badge text-bg-secondary">{{ $post->category }}</span>
                                    <small class="text-body-secondary">{{ $post->readingTimeMinutes() }} min</small>
                                </div>
                                <h3 class="h5 card-title"><a class="stretched-link text-decoration-none" href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                                <p class="card-text text-body-secondary flex-grow-1">{{ $post->excerpt }}</p>
                                <small class="text-body-secondary">Publicado em {{ $post->published_at->format('d/m/Y') }}</small>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="mt-4">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
