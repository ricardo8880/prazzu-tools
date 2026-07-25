@props([
    'title' => 'Recursos Prazzu Plus',
    'description' => 'Produtividade, continuidade e relatórios sem alterar a correção do cálculo gratuito.',
    'actions' => [],
])

<section {{ $attributes->class(['card border-primary-subtle shadow-sm mb-4']) }} aria-labelledby="{{ $attributes->get('id', 'plus-actions') }}-title">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
            <div>
                <span class="badge text-bg-primary mb-2">Prazzu Plus</span>
                <h2 class="h4 mb-1" id="{{ $attributes->get('id', 'plus-actions') }}-title">{{ $title }}</h2>
                <p class="text-body-secondary mb-0">{{ $description }}</p>
            </div>
        </div>

        <div class="row g-3">
            @foreach ($actions as $action)
                <div class="col-12 col-md-6 col-xl-4">
                    @if (!empty($action['url']))
                        <a class="border rounded-3 p-3 h-100 d-flex gap-3 text-decoration-none text-body" href="{{ $action['url'] }}">
                            <i class="bi {{ $action['icon'] ?? 'bi-gem' }} fs-4 text-primary" aria-hidden="true"></i>
                            <span>
                                <span class="d-flex align-items-center gap-2 fw-semibold">
                                    {{ $action['label'] }}
                                    <span class="badge text-bg-primary">Plus</span>
                                </span>
                                @if (!empty($action['description']))
                                    <span class="small text-body-secondary d-block mt-1">{{ $action['description'] }}</span>
                                @endif
                            </span>
                        </a>
                    @else
                        <div class="border rounded-3 p-3 h-100 d-flex gap-3">
                            <i class="bi {{ $action['icon'] ?? 'bi-gem' }} fs-4 text-primary" aria-hidden="true"></i>
                            <span>
                                <span class="d-flex align-items-center gap-2 fw-semibold">
                                    {{ $action['label'] }}
                                    <span class="badge text-bg-primary">Plus</span>
                                </span>
                                @if (!empty($action['description']))
                                    <span class="small text-body-secondary d-block mt-1">{{ $action['description'] }}</span>
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
