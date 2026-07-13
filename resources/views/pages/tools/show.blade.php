@extends('layouts.app')

@section('title', $tool['name'].' — Prazzu Tools')
@section('meta_description', $tool['description'])

@section('content')
    <div class="prazzu-page tool-page" data-tool="{{ $tool['slug'] }}">
        <nav aria-label="Breadcrumb" class="mb-3">
            <ol class="breadcrumb prazzu-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Ferramentas</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $tool['name'] }}</li>
            </ol>
        </nav>

        <header class="prazzu-tool-intro">
            <span class="prazzu-icon-tile prazzu-icon-tile--{{ $tool['tone'] }}"><i class="bi {{ $tool['icon'] }}" aria-hidden="true"></i></span>
            <div class="flex-grow-1">
                <span class="prazzu-badge prazzu-badge--{{ $tool['badge_tone'] }}">{{ $tool['badge'] }}</span>
                <h1>{{ $tool['name'] }}</h1>
                <p>{{ $tool['description'] }}</p>
            </div>
        </header>

        <section class="prazzu-tool-workspace" aria-labelledby="tool-workspace-title">
            <div class="prazzu-tool-workspace__icon"><i class="bi bi-tools" aria-hidden="true"></i></div>
            <h2 id="tool-workspace-title">Módulo preparado</h2>
            <p>A rota, a página e o espaço isolado desta ferramenta já estão prontos. A lógica específica será adicionada no módulo próprio, sem interferir nas demais ferramentas.</p>
            <button class="btn btn-primary prazzu-btn-primary" type="button" disabled aria-disabled="true">Ferramenta em construção</button>
        </section>

        <section class="mt-4" aria-labelledby="related-tools-title">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 id="related-tools-title" class="prazzu-section-title mb-0">Outras ferramentas</h2>
                <a class="prazzu-section-link" href="{{ route('tools.index') }}">Ver catálogo <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="row g-3">
                @foreach ($relatedTools as $related)
                    <div class="col-12 col-md-4">
                        <a class="prazzu-related-tool" href="{{ route('tools.show', $related['slug']) }}">
                            <span class="prazzu-icon-tile prazzu-icon-tile--{{ $related['tone'] }}"><i class="bi {{ $related['icon'] }}"></i></span>
                            <span><strong>{{ $related['name'] }}</strong><small>{{ $related['description'] }}</small></span>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
