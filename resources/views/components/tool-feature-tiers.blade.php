@props(['slug'])

@php
    $manifest = app(\App\Core\Tools\ToolRegistry::class)->findManifest($slug);
    $essential = $manifest?->featuresFor(\App\Core\Tools\Enums\ToolFeatureTier::Essential) ?? [];
    $plus = $manifest?->featuresFor(\App\Core\Tools\Enums\ToolFeatureTier::Plus) ?? [];
    $launchFree = config('access.commercial_mode') === \App\Core\Access\Enums\CommercialAccessMode::LaunchFree->value;
@endphp

@if ($manifest !== null)
    <section class="prazzu-tool-tiers mt-4" data-tool-feature-tiers aria-labelledby="{{ $slug }}-tiers-title">
        <details class="prazzu-tool-tiers__details">
            <summary class="prazzu-tool-tiers__summary">
                <span>
                    <span class="prazzu-eyebrow">Essencial × Prazzu Plus</span>
                    <strong id="{{ $slug }}-tiers-title">Conheça os recursos desta ferramenta</strong>
                </span>
                <span class="d-inline-flex align-items-center gap-2 flex-shrink-0">
                    @if ($launchFree)
                        <span class="badge text-bg-success">Plus liberado</span>
                    @endif
                    <i class="bi bi-chevron-down" aria-hidden="true"></i>
                </span>
            </summary>

            <div class="prazzu-tool-tiers__content">
                <p class="text-body-secondary mb-3">O Essencial resolve o problema principal por completo. O Plus acrescenta produtividade e conveniência, sem alterar a correção do resultado.</p>
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
        </details>
    </section>
@endif
