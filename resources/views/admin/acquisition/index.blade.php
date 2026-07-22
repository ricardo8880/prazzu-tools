@extends('layouts.app')

@section('title', 'Jornadas de aquisição | Prazzu Tools')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Administração</span>
            <h1 class="h2 mb-1">Jornadas de aquisição</h1>
            <p class="text-body-secondary mb-0">Cadastre os conteúdos que poderão contextualizar a Home por palavra-chave.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.acquisition.contexts.create') }}">
            <i class="bi bi-plus-lg me-1"></i> Novo contexto
        </a>
    </div>

    <div class="alert alert-info" role="note">
        Neste lote, o cadastro fica pronto no admin. A aplicação dos dados na Home será feita no próximo lote.
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="get">
                <div class="col-lg-8">
                    <label class="form-label" for="q">Buscar</label>
                    <input class="form-control" id="q" name="q" value="{{ $search }}" placeholder="Nome, palavra-chave ou identificador da campanha">
                </div>
                <div class="col-lg-2">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>
                                {{ $status->value === 'active' ? 'Ativo' : 'Inativo' }}
                            </option>
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
                        <th class="ps-4">Contexto</th>
                        <th>Campanha</th>
                        <th>Ferramenta principal</th>
                        <th class="text-center">Conteúdos</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contexts as $context)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $context['name'] }}</div>
                                <small class="text-body-secondary">?context={{ $context['keyword'] }}</small>
                            </td>
                            <td>{{ $context['campaign_identifier'] ?: '—' }}</td>
                            <td>{{ $context['primary_tool_slug'] ?: '—' }}</td>
                            <td class="text-center">
                                <span class="badge text-bg-light border">{{ $context['tools_count'] }} ferramentas</span>
                                <span class="badge text-bg-light border">{{ $context['articles_count'] }} artigos</span>
                            </td>
                            <td>
                                <span class="badge {{ $context['status'] === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $context['status'] === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                    <form method="post" action="{{ route('admin.acquisition.contexts.toggle', $context['id']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm {{ $context['status'] === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }}" type="submit">
                                            {{ $context['status'] === 'active' ? 'Desativar' : 'Ativar' }}
                                        </button>
                                    </form>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.acquisition.contexts.edit', $context['id']) }}">Editar</a>
                                    <form method="post" action="{{ route('admin.acquisition.contexts.destroy', $context['id']) }}" onsubmit="return confirm('Excluir este contexto permanentemente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Excluir {{ $context['name'] }}"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-signpost-split display-5 text-body-secondary"></i>
                                <h2 class="h5 mt-3">Nenhum contexto cadastrado</h2>
                                <p class="text-body-secondary">Crie a primeira jornada de aquisição da plataforma.</p>
                                <a class="btn btn-primary" href="{{ route('admin.acquisition.contexts.create') }}">Criar contexto</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contexts->hasPages())
            <div class="card-footer border-0 py-3">{{ $contexts->links() }}</div>
        @endif
    </div>
</div>
@endsection
