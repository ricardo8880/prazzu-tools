@extends('layouts.app')

@section('title', $suggestion->name.' | Sugestões | Prazzu Tools')
@section('meta_robots', 'noindex,nofollow')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a class="small text-decoration-none" href="{{ route('admin.feedback.suggestions.index') }}"><i class="bi bi-arrow-left me-1"></i>Precisa de algo novo?</a>
        <h1 class="h2 mt-2 mb-1">{{ $suggestion->name }}</h1>
        <p class="text-body-secondary mb-0">Sugestão enviada em {{ $suggestion->created_at?->format('d/m/Y H:i') }}</p>
    </div>

    @include('admin.feedback._tabs')

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Problema que a ferramenta deve resolver</h2>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $suggestion->problem }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Contato</h2>
                    <dl class="row g-2 mb-0">
                        <dt class="col-4">Usuário</dt>
                        <dd class="col-8 mb-0">{{ $suggestion->user?->name ?? 'Visitante' }}</dd>
                        <dt class="col-4">E-mail</dt>
                        <dd class="col-8 mb-0 text-break"><a href="mailto:{{ $suggestion->email }}">{{ $suggestion->email }}</a></dd>
                        <dt class="col-4">Data</dt>
                        <dd class="col-8 mb-0">{{ $suggestion->created_at?->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
