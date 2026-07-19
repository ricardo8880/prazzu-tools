@props(['slug'])

@php
    $manifest = app(\App\Core\Tools\ToolRegistry::class)->findManifest($slug);
    $essential = $manifest?->featuresFor(\App\Core\Tools\Enums\ToolFeatureTier::Essential) ?? [];
    $plus = $manifest?->featuresFor(\App\Core\Tools\Enums\ToolFeatureTier::Plus) ?? [];
    $launchFree = config('access.commercial_mode') === \App\Core\Access\Enums\CommercialAccessMode::LaunchFree->value;
@endphp

@if ($manifest !== null)
    <section class="card border-0 shadow-sm mb-4" aria-labelledby="{{ $slug }}-tiers-title">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
                <div>
                    <span class="prazzu-eyebrow">Essencial × Prazzu Plus</span>
                    <h2 class="h4 mb-1" id="{{ $slug }}-tiers-title">Grátis resolve. Plus acelera.</h2>
                    <p class="text-body-secondary mb-0">A qualidade e a correção do resultado são iguais nas duas experiências.</p>
                </div>
                @if ($launchFree)
                    <span class="badge text-bg-success align-self-start">Plus liberado no lançamento</span>
                @endif
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="fw-semibold text-success mb-2"><i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>Essencial — gratuito e completo</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($essential as $feature)
                                <li>{{ $feature->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="border border-primary-subtle rounded-3 p-3 h-100 bg-primary-subtle bg-opacity-10">
                        <div class="fw-semibold text-primary mb-2"><i class="bi bi-gem me-1" aria-hidden="true"></i>Prazzu Plus — recursos avançados</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($plus as $feature)
                                <li>{{ $feature->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
