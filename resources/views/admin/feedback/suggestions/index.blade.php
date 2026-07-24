@extends('layouts.app')

@section('title', 'Sugestões de novas ferramentas | Prazzu Tools')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Precisa de algo novo?</h1>
        <p class="text-body-secondary mb-0">Sugestões de novas ferramentas enviadas pelos visitantes da plataforma.</p>
    </div>

    @include('admin.feedback._tabs')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Ferramenta sugerida</th>
                        <th>Problema</th>
                        <th>Contato</th>
                        <th>Enviado em</th>
                        <th class="text-end">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suggestions as $suggestion)
                        <tr>
                            <td class="fw-semibold">{{ $suggestion->name }}</td>
                            <td style="min-width: 20rem; max-width: 38rem;">
                                <div class="text-truncate">{{ $suggestion->problem }}</div>
                            </td>
                            <td>
                                <div>{{ $suggestion->user?->name ?? 'Visitante' }}</div>
                                <small class="text-body-secondary">{{ $suggestion->email }}</small>
                            </td>
                            <td class="text-nowrap">{{ $suggestion->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.feedback.suggestions.show', $suggestion) }}">Ler</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-body-secondary py-5">Nenhuma sugestão de ferramenta recebida.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($suggestions->hasPages())
            <div class="card-footer bg-transparent">{{ $suggestions->links() }}</div>
        @endif
    </div>
</div>
@endsection
