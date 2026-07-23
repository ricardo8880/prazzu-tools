@extends('layouts.app')

@section('title', $section['title'].' — Prazzu Tools')
@section('meta_description', $section['description'])

@section('content')
    <div class="prazzu-page prazzu-resources">
        <nav class="prazzu-breadcrumb mb-3" aria-label="Navegação estrutural">
            <a href="{{ route('resources.index') }}">Recursos</a>
            <span class="mx-2 text-body-secondary">/</span>
            <span aria-current="page">{{ $section['title'] }}</span>
        </nav>

        <nav class="nav nav-pills prazzu-resource-navigation mb-4" aria-label="Categorias de recursos">
            <a class="nav-link" href="{{ route('resources.index') }}">
                <i class="bi bi-grid me-2" aria-hidden="true"></i>Todos
            </a>
            @foreach (config('resources.sections', []) as $slug => $navigationSection)
                <a
                    class="nav-link {{ $resource === $slug ? 'active' : '' }}"
                    href="{{ route('resources.show', $slug) }}"
                    @if ($resource === $slug) aria-current="page" @endif
                >
                    <i class="bi {{ $navigationSection['icon'] }} me-2" aria-hidden="true"></i>{{ $navigationSection['title'] }}
                </a>
            @endforeach
        </nav>

        <header class="prazzu-page-hero">
            <span class="prazzu-page-hero__icon"><i class="bi {{ $section['icon'] }}" aria-hidden="true"></i></span>
            <div>
                <span class="prazzu-eyebrow">{{ $section['eyebrow'] }}</span>
                <h1>{{ $section['title'] }}</h1>
                <p>{{ $section['description'] }}</p>
            </div>
        </header>

        @if ($items->isEmpty())
            <section class="prazzu-empty-state">
                <i class="bi {{ $section['icon'] }}" aria-hidden="true"></i>
                <h2>Conteúdo em preparação</h2>
                <p>{{ $section['empty_message'] }}</p>
            </section>
        @else
            <div class="row g-3">
                @foreach ($items as $item)
                    <div class="col-12 col-lg-6">
                        <x-resources.card :item="$item" :section="$section" />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
