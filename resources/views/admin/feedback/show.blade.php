@extends('layouts.app')

@section('title', 'Feedback de '.$feedback->tool_name.' | Prazzu Tools')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <a class="small text-decoration-none" href="{{ route('admin.feedback.tools.index') }}"><i class="bi bi-arrow-left me-1"></i>Feedback das ferramentas</a>
            <h1 class="h2 mt-2 mb-1">{{ $feedback->tool_name }}</h1>
            <p class="text-body-secondary mb-0">{{ $feedback->type->label() }} · enviado em {{ $feedback->created_at?->format('d/m/Y H:i') }}</p>
        </div>
        <form method="post" action="{{ route('admin.feedback.tools.status', $feedback) }}" class="d-flex gap-2 align-items-start">
            @csrf
            @method('PATCH')
            <label class="visually-hidden" for="feedback-status">Status</label>
            <select class="form-select" id="feedback-status" name="status">
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($feedback->status === $status)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary text-nowrap" type="submit">Atualizar status</button>
        </form>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    @include('admin.feedback._tabs')

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">Mensagem</div>
                <div class="card-body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $feedback->message }}</p>
                </div>
            </div>

            @if ($feedback->attempted_action)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent fw-semibold">O que o usuário estava tentando fazer</div>
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-wrap;">{{ $feedback->attempted_action }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">Identificação</div>
                <dl class="card-body row mb-0 gy-3">
                    <dt class="col-5">Usuário</dt>
                    <dd class="col-7 mb-0">{{ $feedback->user?->name ?? 'Visitante' }}</dd>
                    <dt class="col-5">Ferramenta</dt>
                    <dd class="col-7 mb-0">{{ $feedback->tool_slug }}</dd>
                    <dt class="col-5">Versão</dt>
                    <dd class="col-7 mb-0">{{ $feedback->tool_version }}</dd>
                    <dt class="col-5">Status</dt>
                    <dd class="col-7 mb-0">{{ $feedback->status->label() }}</dd>
                    <dt class="col-5">Analisado em</dt>
                    <dd class="col-7 mb-0">{{ $feedback->reviewed_at?->format('d/m/Y H:i') ?? 'Ainda não analisado' }}</dd>
                </dl>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold">Contexto técnico</div>
                <dl class="card-body row mb-0 gy-3">
                    <dt class="col-4">Rota</dt>
                    <dd class="col-8 mb-0 text-break">{{ $feedback->path }}</dd>
                    <dt class="col-4">URL</dt>
                    <dd class="col-8 mb-0 text-break">{{ $feedback->url }}</dd>
                    @if ($feedback->context)
                        @foreach ($feedback->context as $key => $value)
                            <dt class="col-4">{{ ucfirst(str_replace('_', ' ', (string) $key)) }}</dt>
                            <dd class="col-8 mb-0 text-break">{{ is_scalar($value) || $value === null ? ($value ?? '—') : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</dd>
                        @endforeach
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
