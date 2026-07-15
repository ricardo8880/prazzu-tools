@extends('layouts.app')

@section('title', 'Categorias do blog | Prazzu Tools')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Administração</span>
            <h1 class="h2 mb-1">Categorias do blog</h1>
            <p class="text-body-secondary mb-0">Organize os conteúdos por assuntos editoriais próprios do blog.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.blog.posts.index') }}"><i class="bi bi-journal-text me-1"></i> Postagens</a>
            <a class="btn btn-primary" href="{{ route('admin.blog.categories.create') }}"><i class="bi bi-plus-lg me-1"></i> Nova categoria</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->has('category'))
        <div class="alert alert-danger" role="alert">{{ $errors->first('category') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="get">
                <div class="col-lg-10">
                    <label class="form-label" for="q">Buscar</label>
                    <input class="form-control" id="q" name="q" value="{{ $search }}" placeholder="Nome ou slug">
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
                        <th class="ps-4">Categoria</th>
                        <th>Status</th>
                        <th class="text-end">Postagens</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $category->name }}</div>
                                <small class="text-body-secondary">/{{ $category->slug }}</small>
                            </td>
                            <td><span class="badge {{ $category->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->is_active ? 'Ativa' : 'Inativa' }}</span></td>
                            <td class="text-end">{{ $category->posts_count }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Ações da categoria">
                                    <a class="btn btn-outline-primary" href="{{ route('admin.blog.categories.edit', $category) }}">Editar</a>
                                    <form method="post" action="{{ route('admin.blog.categories.destroy', $category) }}" onsubmit="return confirm('Excluir esta categoria permanentemente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger rounded-start-0" type="submit" aria-label="Excluir {{ $category->name }}" @disabled($category->posts_count > 0)><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-5"><i class="bi bi-tags display-5 text-body-secondary"></i><h2 class="h5 mt-3">Nenhuma categoria cadastrada</h2><p class="text-body-secondary">Cadastre a primeira categoria editorial do blog.</p><a class="btn btn-primary" href="{{ route('admin.blog.categories.create') }}">Criar categoria</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($categories->hasPages())
            <div class="card-footer border-0 py-3">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection
