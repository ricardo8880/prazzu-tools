@extends('layouts.app')

@section('title', 'About Prazzu Tools — Professional tools platform')
@section('meta_description', 'Discover Prazzu Tools, a professional tools platform organized by business categories and built to evolve without duplicating infrastructure.')

@section('content')
    <div class="prazzu-page prazzu-about">
        <section class="text-center py-4 py-lg-5 mb-5" aria-labelledby="about-title">
            <span class="badge rounded-pill prazzu-about__badge mb-3">
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i>About Prazzu Tools
            </span>

            <h1 id="about-title" class="display-5 fw-bold mb-3">
                Professional tools that get the job done
            </h1>

            <p class="lead text-body-secondary mx-auto mb-4" style="max-width: 840px;">
                Prazzu Tools brings reliable solutions together in one platform for professionals across different
                business areas who need to solve specific tasks and make decisions with greater confidence and speed.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a class="btn prazzu-btn-primary" href="{{ route('tools.index') }}">
                    <i class="bi bi-grid-3x3-gap me-2" aria-hidden="true"></i>Explore tools
                </a>
                <a class="btn prazzu-btn-outline" href="{{ route('plans') }}">
                    Learn about Prazzu Plus
                </a>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="problem-title">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-6">
                    <span class="prazzu-eyebrow">The problem we want to solve</span>
                    <h2 id="problem-title" class="h1 fw-bold mb-3">Professional work should not depend on scattered solutions</h2>
                    <p class="text-body-secondary fs-5">
                        Calculations, checks, simulations, and decisions are part of daily work across many business areas.
                        Too often, these tasks depend on outdated spreadsheets, expensive systems, or
                        incomplete tools scattered across different places.
                    </p>
                    <p class="text-body-secondary mb-0">
                        Prazzu Tools was created to bring these solutions into a simple, organized environment
                        designed to grow without sacrificing quality. The platform solves specific tasks; customer management,
                        processes, and ongoing operations belong in Prazzu Core or another appropriate product.
                    </p>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm prazzu-about__feature-panel">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-4">
                                <div class="col-6">
                                    <i class="bi bi-calculator fs-2 prazzu-about__icon" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Calculations</h3>
                                    <p class="small text-body-secondary mb-0">Clear results and calculation details when applicable.</p>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-sliders fs-2 prazzu-about__icon" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Simulations</h3>
                                    <p class="small text-body-secondary mb-0">Scenarios that help professionals make better decisions.</p>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-journal-check fs-2 prazzu-about__icon" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Content</h3>
                                    <p class="small text-body-secondary mb-0">Useful information from each category connected to the tools.</p>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-graph-up-arrow fs-2 prazzu-about__icon" aria-hidden="true"></i>
                                    <h3 class="h5 mt-3">Productivity</h3>
                                    <p class="small text-body-secondary mb-0">Advanced features for people who use the platform frequently.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5 py-4 py-lg-5 border-top border-bottom" aria-labelledby="platform-title">
            <div class="text-center mb-5">
                <span class="prazzu-eyebrow">One platform, not just calculators</span>
                <h2 id="platform-title" class="h1 fw-bold mb-3">Each tool evolves independently. The platform evolves for all of them.</h2>
                <p class="text-body-secondary mx-auto mb-0" style="max-width: 760px;">
                    Tools are independent so each one can grow without breaking another. At the same time,
                    universal features such as plans, users, analytics, and history belong to the platform and can
                    benefit the entire ecosystem.
                </p>
            </div>

            <div class="row g-3 justify-content-center text-center">
                <div class="col-12 col-md-5 col-xl-3">
                    <div class="card h-100 shadow-sm prazzu-about__card">
                        <div class="card-body p-4">
                            <i class="bi bi-boxes fs-1 prazzu-about__icon" aria-hidden="true"></i>
                            <h3 class="h5 mt-3">Shared platform</h3>
                            <p class="text-body-secondary mb-0">Users, plans, analytics, and shared services in a single Core.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto d-none d-md-flex align-items-center">
                    <i class="bi bi-arrow-right fs-3 text-body-secondary" aria-hidden="true"></i>
                </div>

                <div class="col-12 col-md-5 col-xl-3">
                    <div class="card h-100 shadow-sm prazzu-about__card">
                        <div class="card-body p-4">
                            <i class="bi bi-grid-3x3-gap fs-1 prazzu-about__icon" aria-hidden="true"></i>
                            <h3 class="h5 mt-3">Independent tools</h3>
                            <p class="text-body-secondary mb-0">Each module contains only the rules specific to its domain.</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto d-none d-xl-flex align-items-center">
                    <i class="bi bi-arrow-right fs-3 text-body-secondary" aria-hidden="true"></i>
                </div>

                <div class="col-12 col-md-5 col-xl-3">
                    <div class="card h-100 shadow-sm border-primary prazzu-about__card prazzu-about__card--featured">
                        <div class="card-body p-4">
                            <i class="bi bi-gem fs-1 prazzu-about__icon" aria-hidden="true"></i>
                            <h3 class="h5 mt-3">One plan</h3>
                            <p class="text-body-secondary mb-0">One subscription unlocks Plus features across the entire platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="philosophy-title">
            <div class="row g-4 align-items-stretch">
                <div class="col-12 col-lg-5">
                    <div class="card h-100 shadow-sm prazzu-about__card prazzu-about__card--essential">
                        <div class="card-body p-4 p-lg-5">
                            <span class="badge prazzu-about__badge prazzu-about__badge--soft mb-3">Good · Free</span>
                            <h2 id="philosophy-title" class="h2 fw-bold">The free version solves the problem</h2>
                            <p class="text-body-secondary">
                                A free Prazzu tool should not provide an incomplete calculation or create
                                artificial limitations to force a subscription.
                            </p>
                            <ul class="list-unstyled d-grid gap-3 mb-0">
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-about__check" aria-hidden="true"></i><span>Complete and reliable result</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-about__check" aria-hidden="true"></i><span>Unlimited use of the free experience</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-about__check" aria-hidden="true"></i><span>Transparency to understand the calculation</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="card h-100 shadow-sm prazzu-about__card prazzu-about__card--plus">
                        <div class="card-body p-4 p-lg-5">
                            <span class="badge prazzu-about__badge mb-3">Excellent · Prazzu Plus</span>
                            <h2 class="h2 fw-bold">The Plus version transforms how you work</h2>
                            <p class="text-body-secondary">
                                Plus adds productivity, history, comparisons, projections, alerts, and
                                automation. Users do not pay to get a correct calculation; they pay to work more efficiently.
                            </p>
                            <div class="alert prazzu-about__note mb-0" role="note">
                                <i class="bi bi-stars me-2" aria-hidden="true"></i>
                                During launch, public Essential and Plus features are available for free. A future subscription will keep Essential complete while funding the productivity gains provided by Plus.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="commitments-title">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Our commitment</span>
                <h2 id="commitments-title" class="h1 fw-bold">Trust comes first</h2>
            </div>

            <div class="row g-3">
                @foreach ([
                    ['icon' => 'bi-bullseye', 'title' => 'Accuracy', 'text' => 'Consistent results and calculation rules handled responsibly.'],
                    ['icon' => 'bi-eye', 'title' => 'Transparency', 'text' => 'Users should understand where the displayed result comes from.'],
                    ['icon' => 'bi-lightning-charge', 'title' => 'Ease of use', 'text' => 'Direct, responsive interfaces focused on the task that needs to be solved.'],
                    ['icon' => 'bi-arrow-repeat', 'title' => 'Evolution', 'text' => 'Tools designed to receive improvements without affecting the others.'],
                    ['icon' => 'bi-shield-check', 'title' => 'Reliability', 'text' => 'Professional trust is the platform’s most important asset.'],
                    ['icon' => 'bi-people', 'title' => 'Category focus', 'text' => 'Each area receives content and tools aligned with its own business context.'],
                ] as $commitment)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card h-100 shadow-sm prazzu-about__card">
                            <div class="card-body p-4">
                                <i class="bi {{ $commitment['icon'] }} fs-2 prazzu-about__icon" aria-hidden="true"></i>
                                <h3 class="h5 mt-3">{{ $commitment['title'] }}</h3>
                                <p class="text-body-secondary mb-0">{{ $commitment['text'] }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mb-5" aria-labelledby="audience-title">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-5">
                    <span class="prazzu-eyebrow">Who it is for</span>
                    <h2 id="audience-title" class="h1 fw-bold mb-3">Focused solutions for different professional workflows</h2>
                    <p class="text-body-secondary mb-0">
                        Prazzu Tools serves different professional profiles while maintaining the same focus on clarity, quality, and practicality.
                    </p>
                </div>

                <div class="col-12 col-lg-7">
                    <div class="row g-3">
                        @foreach ([
                            ['icon' => 'bi-calculator', 'label' => 'Accounting professionals'],
                            ['icon' => 'bi-people', 'label' => 'Human Resources professionals'],
                            ['icon' => 'bi-building', 'label' => 'Business departments'],
                            ['icon' => 'bi-person-workspace', 'label' => 'Consultants and specialists'],
                            ['icon' => 'bi-mortarboard', 'label' => 'Students and researchers'],
                            ['icon' => 'bi-shop', 'label' => 'Business owners and managers'],
                        ] as $audience)
                            <div class="col-12 col-sm-6">
                                <div class="d-flex align-items-center gap-3 border rounded-3 p-3 h-100 prazzu-about__audience-item">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle prazzu-about__audience-icon flex-shrink-0" style="width: 44px; height: 44px;">
                                        <i class="bi {{ $audience['icon'] }}" aria-hidden="true"></i>
                                    </span>
                                    <strong>{{ $audience['label'] }}</strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="card border-0 text-white shadow prazzu-about__cta" aria-labelledby="future-title">
            <div class="card-body p-4 p-lg-5 text-center">
                <i class="bi bi-rocket-takeoff fs-1" aria-hidden="true"></i>
                <span class="d-block text-uppercase small fw-semibold mt-3 mb-2">The future of the platform</span>
                <h2 id="future-title" class="display-6 fw-bold mb-3">Built to evolve with new categories</h2>
                <p class="lead mx-auto mb-4" style="max-width: 800px;">
                    New tools, content, and features can be added continuously across different categories.
                    The goal is to offer specialized experiences while remaining a single platform underneath.
                </p>
                <a class="btn btn-light" href="{{ route('tools.index') }}">
                    Explore the tools
                </a>
            </div>
        </section>
    </div>
@endsection
