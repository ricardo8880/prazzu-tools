@extends('layouts.app')

@section('title', 'Administração | Prazzu Tools')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Painel administrativo</h1>
        <p class="text-body-secondary mb-0">Acesse as áreas compartilhadas de conteúdo, aquisição e métricas da plataforma.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <a class="card border-0 shadow-sm h-100 text-decoration-none" href="{{ route('admin.analytics.index') }}">
                <div class="card-body p-4">
                    <i class="bi bi-graph-up-arrow fs-2" aria-hidden="true"></i>
                    <h2 class="h4 mt-3">Analytics</h2>
                    <p class="text-body-secondary mb-0">Acompanhe audiência, aquisição, ferramentas, funis, SEO e relatórios.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a class="card border-0 shadow-sm h-100 text-decoration-none" href="{{ route('admin.acquisition.contexts.index') }}">
                <div class="card-body p-4">
                    <i class="bi bi-signpost-split fs-2" aria-hidden="true"></i>
                    <h2 class="h4 mt-3">Aquisição</h2>
                    <p class="text-body-secondary mb-0">Cadastre e ative jornadas que contextualizam a Home por campanha.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a class="card border-0 shadow-sm h-100 text-decoration-none" href="{{ route('admin.blog.posts.index') }}">
                <div class="card-body p-4">
                    <i class="bi bi-journal-text fs-2" aria-hidden="true"></i>
                    <h2 class="h4 mt-3">Blog</h2>
                    <p class="text-body-secondary mb-0">Crie postagens, organize categorias e consulte métricas de conteúdo.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-4">
            <a class="card border-0 shadow-sm h-100 text-decoration-none" href="{{ route('admin.feedback.tools.index') }}">
                <div class="card-body p-4">
                    <i class="bi bi-chat-left-text fs-2" aria-hidden="true"></i>
                    <h2 class="h4 mt-3">Feedback das ferramentas</h2>
                    <p class="text-body-secondary mb-0">Leia problemas, sugestões e pedidos enviados pelos usuários das ferramentas.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
