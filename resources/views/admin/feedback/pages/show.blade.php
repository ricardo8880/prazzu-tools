@extends('layouts.app')

@section('title', 'Avaliação da página | Prazzu Tools')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a class="small text-decoration-none" href="{{ route('admin.feedback.pages.index') }}"><i class="bi bi-arrow-left me-1"></i>Avaliações da página</a>
        <h1 class="h2 mt-2 mb-1">{{ $feedback->page_title ?: $feedback->path }}</h1>
        <p class="text-body-secondary mb-0">Avaliação enviada em {{ $feedback->created_at?->format('d/m/Y H:i') }}</p>
    </div>

    @include('admin.feedback._tabs')

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="display-6 fw-semibold">{{ $feedback->rating }}/5</span>
                        <i class="bi bi-star-fill text-warning fs-3" aria-hidden="true"></i>
                    </div>
                    <h2 class="h5 mb-3">Comentário</h2>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $feedback->comment ?: 'Esta avaliação foi enviada sem comentário.' }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Contexto</h2>
                    <dl class="row g-2 mb-0">
                        <dt class="col-4">Usuário</dt>
                        <dd class="col-8 mb-0">{{ $feedback->user?->name ?? 'Visitante' }}</dd>
                        <dt class="col-4">Página</dt>
                        <dd class="col-8 mb-0 text-break">{{ $feedback->path }}</dd>
                        <dt class="col-4">Título</dt>
                        <dd class="col-8 mb-0">{{ $feedback->page_title ?: 'Não informado' }}</dd>
                        <dt class="col-4">URL</dt>
                        <dd class="col-8 mb-0 text-break"><a href="{{ $feedback->url }}" target="_blank" rel="noopener noreferrer">{{ $feedback->url }}</a></dd>
                        <dt class="col-4">Data</dt>
                        <dd class="col-8 mb-0">{{ $feedback->created_at?->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
