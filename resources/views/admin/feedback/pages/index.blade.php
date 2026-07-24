@extends('layouts.app')

@section('title', 'Avaliações da página | Prazzu Tools')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <span class="badge text-bg-primary mb-2">Administração</span>
        <h1 class="h2 mb-1">Avaliações da página</h1>
        <p class="text-body-secondary mb-0">Notas e comentários enviados pelo controle “Avalie a página”.</p>
    </div>

    @include('admin.feedback._tabs')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('admin.feedback.pages.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="page-feedback-rating">Nota</label>
                    <select class="form-select" id="page-feedback-rating" name="rating">
                        <option value="">Todas</option>
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}" @selected($filters['rating'] === (string) $rating)>{{ $rating }} estrela{{ $rating === 1 ? '' : 's' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-7">
                    <label class="form-label" for="page-feedback-path">Página</label>
                    <input class="form-control" id="page-feedback-path" name="path" value="{{ $filters['path'] }}" placeholder="Ex.: /ferramentas/gerador-de-contratos">
                </div>
                <div class="col-md-2 d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    @if (array_filter($filters))
                        <a class="btn btn-outline-secondary" href="{{ route('admin.feedback.pages.index') }}">Limpar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nota</th>
                        <th>Página</th>
                        <th>Comentário</th>
                        <th>Usuário</th>
                        <th>Enviado em</th>
                        <th class="text-end">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feedback as $item)
                        <tr>
                            <td class="text-nowrap" aria-label="{{ $item->rating }} de 5 estrelas">
                                <span class="fw-semibold">{{ $item->rating }}/5</span>
                                <i class="bi bi-star-fill text-warning ms-1" aria-hidden="true"></i>
                            </td>
                            <td style="min-width: 15rem; max-width: 26rem;">
                                <div class="fw-semibold text-truncate">{{ $item->page_title ?: $item->path }}</div>
                                <small class="text-body-secondary text-break">{{ $item->path }}</small>
                            </td>
                            <td style="min-width: 18rem; max-width: 34rem;">
                                <div class="text-truncate">{{ $item->comment ?: 'Sem comentário' }}</div>
                            </td>
                            <td>{{ $item->user?->name ?? 'Visitante' }}</td>
                            <td class="text-nowrap">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.feedback.pages.show', $item) }}">Ler</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-body-secondary py-5">Nenhuma avaliação de página encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($feedback->hasPages())
            <div class="card-footer bg-transparent">{{ $feedback->links() }}</div>
        @endif
    </div>
</div>
@endsection
