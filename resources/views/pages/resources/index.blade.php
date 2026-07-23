@extends('layouts.app')

@section('title', 'Recursos profissionais — Prazzu Tools')
@section('meta_description', 'Guias e modelos práticos que complementam as ferramentas do Prazzu Tools sem conteúdo superficial.')

@section('content')
    <div class="prazzu-page prazzu-resources">
        <header class="prazzu-page-hero">
            <span class="prazzu-page-hero__icon"><i class="bi bi-collection" aria-hidden="true"></i></span>
            <div>
                <span class="prazzu-eyebrow">Central de recursos</span>
                <h1>Recursos para aplicar, conferir e decidir melhor</h1>
                <p>Uma biblioteca enxuta de materiais práticos, criada para complementar ferramentas reais da rotina contábil.</p>
            </div>
        </header>

        <nav class="nav nav-pills prazzu-resource-navigation mb-4" aria-label="Categorias de recursos">
            <a class="nav-link active" href="{{ route('resources.index') }}" aria-current="page">
                <i class="bi bi-grid me-2" aria-hidden="true"></i>Todos
            </a>
            @foreach ($sections as $slug => $section)
                <a class="nav-link" href="{{ route('resources.show', $slug) }}">
                    <i class="bi {{ $section['icon'] }} me-2" aria-hidden="true"></i>{{ $section['title'] }}
                </a>
            @endforeach
        </nav>

        <section class="prazzu-resource-principles" aria-labelledby="resource-principles-title">
            <div>
                <span class="prazzu-eyebrow">Critério editorial</span>
                <h2 id="resource-principles-title">Menos volume. Mais utilidade.</h2>
                <p>Cada material precisa resolver uma necessidade concreta, deixar claro como deve ser usado e conduzir para uma ferramenta relacionada quando houver cálculo, validação ou simulação.</p>
            </div>
            <i class="bi bi-shield-check" aria-hidden="true"></i>
        </section>

        <div class="row g-3 mb-4">
            @foreach ($sections as $slug => $section)
                <div class="col-12 col-lg-6">
                    <a class="prazzu-resource-section h-100" href="{{ route('resources.show', $slug) }}">
                        <span class="prazzu-icon-tile prazzu-icon-tile--violet"><i class="bi {{ $section['icon'] }}" aria-hidden="true"></i></span>
                        <span>
                            <small>{{ $section['eyebrow'] }}</small>
                            <strong>{{ $section['title'] }}</strong>
                            <span>{{ $section['description'] }}</span>
                        </span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            @endforeach
        </div>

        <section aria-labelledby="resources-in-preparation-title">
            <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
                <div>
                    <span class="prazzu-eyebrow">Próximos recursos</span>
                    <h2 id="resources-in-preparation-title" class="h4 mb-0">Em preparação editorial</h2>
                </div>
            </div>
            <div class="row g-3">
                @foreach ($items as $item)
                    <div class="col-12 col-lg-6">
                        <x-resources.card :item="$item" :section="$sections[$item['type']]" />
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
