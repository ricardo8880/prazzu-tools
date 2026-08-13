@props(['slug', 'showContent' => true])

@php
    $tool = app(\App\Core\Tools\ToolCatalog::class)->find($slug);
    $routeName = is_array($tool) ? ($tool['route_name'] ?? null) : null;
    $canonicalUrl = is_string($routeName) && $routeName !== '' && \Illuminate\Support\Facades\Route::has($routeName)
        ? route($routeName)
        : url()->current();
    $trust = is_array($tool)
        ? app(\App\Core\Seo\Application\ToolTrustContent::class)->for($tool)
        : null;
    $vertical = app(\App\Core\Navigation\Application\VerticalBreadcrumbContext::class)->active();
    $structuredData = is_array($tool) ? [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebApplication',
                '@id' => $canonicalUrl.'#application',
                'name' => $tool['name'],
                'description' => $tool['description'],
                'url' => $canonicalUrl,
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'inLanguage' => 'pt-BR',
                'isAccessibleForFree' => true,
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => $canonicalUrl.'#breadcrumb',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => $vertical['name'] ? 'Prazzu Tools — '.$vertical['name'] : 'Prazzu Tools', 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Ferramentas', 'item' => route('tools.index')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $tool['name'], 'item' => $canonicalUrl],
                ],
            ],
        ],
    ] : null;
@endphp

@if ($structuredData !== null)
    <script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">{!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}</script>
@endif

@if ($trust !== null && $showContent)
    <section class="mt-5" aria-labelledby="{{ $slug }}-trust-title" data-tool-trust-content>
        <div class="mb-3">
            <span class="prazzu-eyebrow">Use com segurança</span>
            <h2 class="h4 mb-1" id="{{ $slug }}-trust-title">O que conferir antes de usar o resultado</h2>
            <p class="text-body-secondary mb-0">Informações rápidas para interpretar esta ferramenta sem transformar uma simulação em uma certeza que ela não promete.</p>
        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <article class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check2-circle" aria-hidden="true"></i>
                            <h3 class="h6 mb-0">Antes de calcular</h3>
                        </div>
                        <p class="small text-body-secondary mb-0">{{ $trust['precheck'] }}</p>
                    </div>
                </article>
            </div>
            <div class="col-12 col-lg-4">
                <article class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-journal-check" aria-hidden="true"></i>
                            <h3 class="h6 mb-0">Transparência do resultado</h3>
                        </div>
                        <p class="small text-body-secondary mb-0">{{ $trust['transparency'] }}</p>
                    </div>
                </article>
            </div>
            <div class="col-12 col-lg-4">
                <article class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-person-check" aria-hidden="true"></i>
                            <h3 class="h6 mb-0">Conta é opcional</h3>
                        </div>
                        <p class="small text-body-secondary mb-2">{{ $trust['continuity'] }}</p>
                        <span class="small text-body-secondary">Versão da ferramenta: {{ $trust['version'] }}</span>
                    </div>
                </article>
            </div>
        </div>
    </section>
@endif
