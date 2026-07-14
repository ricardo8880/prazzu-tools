@extends('layouts.app')

@section('title', 'Administrar blog | Prazzu Tools')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Administração</span>
            <h1 class="h2 mb-1">Postagens do blog</h1>
            <p class="text-body-secondary mb-0">Crie, organize, agende e publique conteúdos da plataforma.</p>
        </div>
        <div class="d-flex gap-2"><a class="btn btn-outline-primary" href="{{ route('admin.blog.analytics') }}"><i class="bi bi-graph-up me-1"></i> Analytics</a><a class="btn btn-primary" href="{{ route('admin.blog.posts.create') }}">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Nova postagem
        </a></div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="get">
                <div class="col-lg-7">
                    <label class="form-label" for="q">Buscar</label>
                    <input class="form-control" id="q" name="q" value="{{ $search }}" placeholder="Título, slug ou categoria">
                </div>
                <div class="col-lg-3">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 d-flex align-items-end">
                    <button class="btn btn-outline-primary w-100" type="submit">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Postagem</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Publicação</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $post->title }}</div>
                                <small class="text-body-secondary">/{{ $post->slug }}</small>
                                @if ($post->is_featured)
                                    <span class="badge text-bg-warning ms-2">Destaque</span>
                                @endif
                            </td>
                            <td>{{ $post->category }}</td>
                            <td>
                                <span class="badge {{ $post->status->value === 'published' ? 'text-bg-success' : ($post->status->value === 'scheduled' ? 'text-bg-info' : 'text-bg-secondary') }}">
                                    {{ $post->status->label() }}
                                </span>
                            </td>
                            <td>{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Ações da postagem">
                                    @if ($post->isPubliclyAvailable())
                                        <a class="btn btn-outline-secondary" href="{{ route('blog.show', $post->slug) }}" target="_blank" title="Abrir artigo">
                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    <a class="btn btn-outline-primary" href="{{ route('admin.blog.posts.edit', $post) }}">Editar</a>
                                    <form method="post" action="{{ route('admin.blog.posts.destroy', $post) }}" onsubmit="return confirm('Excluir esta postagem permanentemente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger rounded-start-0" type="submit" aria-label="Excluir {{ $post->title }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-journal-text display-5 text-body-secondary" aria-hidden="true"></i>
                                <h2 class="h5 mt-3">Nenhuma postagem encontrada</h2>
                                <p class="text-body-secondary">Comece criando o primeiro conteúdo do blog.</p>
                                <div class="d-flex gap-2"><a class="btn btn-outline-primary" href="{{ route('admin.blog.analytics') }}"><i class="bi bi-graph-up me-1"></i> Analytics</a><a class="btn btn-primary" href="{{ route('admin.blog.posts.create') }}">Criar postagem</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($posts->hasPages())
            <div class="card-footer border-0 py-3">{{ $posts->links() }}</div>
        @endif
    </div>
</div>
@endsection
