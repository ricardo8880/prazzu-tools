@extends('layouts.app')

@php
    $seoTitle = $post->meta_title ?: $post->title;
    $seoDescription = $post->meta_description ?: $post->excerpt;
    $canonical = $post->canonical_url ?: route('blog.show', $post->slug);
    $socialImage = $post->social_image_path ?: $post->cover_image_path;
    $articleImage = $socialImage ? asset('storage/'.$socialImage) : null;
    $publishedAt = $post->published_at->toAtomString();
    $modifiedAt = ($post->content_updated_at ?: $post->updated_at)->toAtomString();
@endphp

@section('title', $seoTitle.' — Prazzu Tools')
@section('meta_description', $seoDescription)
@section('meta_robots', $post->should_index ? 'index,follow,max-image-preview:large' : 'noindex,follow')
@section('canonical_url', $canonical)
@section('og_type', 'article')
@section('og_title', $seoTitle)
@section('og_description', $seoDescription)
@if ($articleImage)
    @section('og_image', $articleImage)
@endif

@push('head')
    <meta property="article:published_time" content="{{ $publishedAt }}">
    <meta property="article:modified_time" content="{{ $modifiedAt }}">
    <meta property="article:section" content="{{ $post->category }}">
    @if ($post->author)<meta property="article:author" content="{{ $post->author->name }}">@endif
    @foreach ($post->related_keywords ?? [] as $keyword)<meta property="article:tag" content="{{ $keyword }}">@endforeach
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $seoDescription,
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonical],
        'datePublished' => $publishedAt,
        'dateModified' => $modifiedAt,
        'inLanguage' => 'pt-BR',
        'articleSection' => $post->category,
        'author' => ['@type' => 'Person', 'name' => $post->author?->name ?: 'Equipe Prazzu Tools'],
        'publisher' => ['@type' => 'Organization', 'name' => config('app.name', 'Prazzu Tools'), 'url' => route('home')],
        'image' => $articleImage ? [$articleImage] : null,
        'keywords' => collect([$post->primary_keyword, ...($post->related_keywords ?? [])])->filter()->values()->implode(', '),
    ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $canonical],
        ],
    ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
@if (!empty($isPreview))<div class="alert alert-warning rounded-0 mb-0 text-center" role="status"><i class="bi bi-eye me-1"></i> Pré-visualização administrativa — esta versão pode não estar publicada.</div>@endif
<div class="container-fluid py-4 px-3 px-lg-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
        </ol>
    </nav>

    <article>
        <header class="mx-auto mb-4" style="max-width: 860px">
            <a class="badge text-bg-secondary text-decoration-none mb-3" href="{{ route('blog.index', ['categoria' => $post->category]) }}">{{ $post->category }}</a>
            <h1 class="display-5 fw-bold">{{ $post->title }}</h1>
            <p class="lead text-body-secondary">{{ $post->excerpt }}</p>
            <div class="d-flex flex-wrap gap-3 small text-body-secondary">
                @if ($post->author)<span><i class="bi bi-person me-1"></i>{{ $post->author->name }}</span>@endif
                <span><i class="bi bi-calendar3 me-1"></i>{{ $post->published_at->format('d/m/Y') }}</span>
                <span><i class="bi bi-clock me-1"></i>{{ $post->readingTimeMinutes() }} min de leitura</span>
                @if ($post->content_updated_at)<span><i class="bi bi-arrow-repeat me-1"></i>Atualizado em {{ $post->content_updated_at->format('d/m/Y') }}</span>@endif
            </div>
        </header>

        @if ($post->cover_image_path)
            <figure class="mx-auto mb-4" style="max-width: 1000px">
                <img class="img-fluid rounded w-100" src="{{ asset('storage/'.$post->cover_image_path) }}" alt="{{ $post->cover_image_alt ?: $post->title }}">
            </figure>
        @endif

        <div class="card border-secondary-subtle mx-auto mb-4" style="max-width: 860px">
            <div class="card-body p-4 p-lg-5 fs-6 lh-lg">{!! $post->content !!}</div>
        </div>
    </article>

    @if ($relatedTools->isNotEmpty())
        <section class="mx-auto mb-5" style="max-width: 860px" aria-labelledby="related-tools-title">
            <div class="card border-primary-subtle bg-primary-subtle"><div class="card-body p-4">
                <span class="prazzu-eyebrow">Coloque em prática</span>
                <h2 id="related-tools-title" class="h4 mt-2">Ferramentas relacionadas</h2>
                <div class="row g-3 mt-1">
                    @foreach ($relatedTools as $tool)
                        <div class="col-12 col-md-6"><a class="card h-100 text-decoration-none border-secondary-subtle bg-body js-blog-tool-link" data-tool-slug="{{ $tool['slug'] }}" href="{{ route('tools.show', $tool['slug']) }}"><div class="card-body d-flex gap-3"><span class="prazzu-icon-tile prazzu-icon-tile--{{ $tool['tone'] }} flex-shrink-0"><i class="bi {{ $tool['icon'] }}"></i></span><span><strong class="d-block">{{ $tool['name'] }}</strong><small class="text-body-secondary">{{ $tool['description'] }}</small></span></div></a></div>
                    @endforeach
                </div>
            </div></div>
        </section>
    @endif

    @if ($relatedPosts->isNotEmpty())
        <section aria-labelledby="related-posts-title">
            <h2 id="related-posts-title" class="h4 mb-3">Continue lendo</h2>
            <div class="row g-3">
                @foreach ($relatedPosts as $relatedPost)
                    <div class="col-12 col-md-4"><article class="card h-100 border-secondary-subtle bg-body-tertiary"><div class="card-body"><span class="badge text-bg-secondary mb-2">{{ $relatedPost->category }}</span><h3 class="h5"><a class="stretched-link text-decoration-none" href="{{ route('blog.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a></h3><p class="text-body-secondary mb-0">{{ $relatedPost->excerpt }}</p></div></article></div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
@if (empty($isPreview))
<script>
document.querySelectorAll('.js-blog-tool-link').forEach((link) => {
    link.addEventListener('click', () => {
        const payload = JSON.stringify({event: 'blog_tool_click', post_id: {{ $post->getKey() }}, post_slug: @json($post->slug), tool_slug: link.dataset.toolSlug});
        fetch(@json(route('blog.analytics')), {method: 'POST', keepalive: true, headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: payload}).catch(() => {});
    });
});
</script>
@endif
@endpush
