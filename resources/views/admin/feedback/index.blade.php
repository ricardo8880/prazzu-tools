@extends('layouts.app')

@section('title', 'Feedback das ferramentas | Prazzu Tools')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Administração</span>
            <h1 class="h2 mb-1">Feedback das ferramentas</h1>
            <p class="text-body-secondary mb-0">Leia problemas, sugestões e pedidos de melhoria enviados dentro das ferramentas.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    @include('admin.feedback._tabs')

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('admin.feedback.tools.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="feedback-tool">Ferramenta</label>
                    <select class="form-select" id="feedback-tool" name="tool">
                        <option value="">Todas</option>
                        @foreach ($tools as $tool)
                            <option value="{{ $tool->tool_slug }}" @selected($filters['tool'] === $tool->tool_slug)>{{ $tool->tool_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="feedback-type">Tipo</label>
                    <select class="form-select" id="feedback-type" name="type">
                        <option value="">Todos</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected($filters['type'] === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="feedback-status">Status</label>
                    <select class="form-select" id="feedback-status" name="status">
                        <option value="">Todos</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($filters['status'] === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    @if (array_filter($filters))
                        <a class="btn btn-outline-secondary" href="{{ route('admin.feedback.tools.index') }}">Limpar</a>
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
                        <th>Ferramenta</th>
                        <th>Tipo</th>
                        <th>Mensagem</th>
                        <th>Usuário</th>
                        <th>Status</th>
                        <th>Enviado em</th>
                        <th class="text-end">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feedback as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->tool_name }}</div>
                                <small class="text-body-secondary">{{ $item->tool_slug }}</small>
                            </td>
                            <td>{{ $item->type->label() }}</td>
                            <td style="min-width: 18rem; max-width: 32rem;">
                                <div class="text-truncate">{{ $item->message }}</div>
                            </td>
                            <td>{{ $item->user?->name ?? 'Visitante' }}</td>
                            <td>
                                @php
                                    $statusClass = match ($item->status) {
                                        \App\Core\Feedback\Enums\ToolFeedbackStatus::New => 'text-bg-primary',
                                        \App\Core\Feedback\Enums\ToolFeedbackStatus::InReview => 'text-bg-warning',
                                        \App\Core\Feedback\Enums\ToolFeedbackStatus::Planned => 'text-bg-info',
                                        \App\Core\Feedback\Enums\ToolFeedbackStatus::Implemented => 'text-bg-success',
                                        default => 'text-bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $item->status->label() }}</span>
                            </td>
                            <td class="text-nowrap">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.feedback.tools.show', $item) }}">Ler</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-body-secondary py-5">Nenhum feedback encontrado.</td>
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
