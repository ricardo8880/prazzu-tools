@extends('layouts.app')

@section('title', 'Plans — Prazzu Tools')
@section('meta_description', 'Compare Prazzu Free, individual Prazzu Plus, and the business plan for assigning Plus access to team members.')

@section('content')
    <div class="prazzu-page prazzu-plans">
        <section class="text-center mb-5" aria-labelledby="plans-title">
            <span class="badge rounded-pill prazzu-plans__badge mb-3">
                <i class="bi bi-gem me-1" aria-hidden="true"></i>Prazzu Plus
            </span>
            <h1 id="plans-title" class="display-6 fw-bold mb-3">All tools. One subscription.</h1>
            <p class="lead text-body-secondary mx-auto mb-4" style="max-width: 780px;">
                During launch, Essential and Prazzu Plus features are available for free.
                When subscriptions are activated, Essential will remain complete while Plus adds history,
                higher volume, simulations, projections, alerts, and automation.
            </p>

            <div class="d-inline-flex flex-wrap justify-content-center gap-2" aria-label="Prazzu Plus highlights">
                <span class="badge rounded-pill prazzu-plans__highlight px-3 py-2">
                    <i class="bi bi-infinity me-1 prazzu-plans__accent" aria-hidden="true"></i>Unlimited use
                </span>
                <span class="badge rounded-pill prazzu-plans__highlight px-3 py-2">
                    <i class="bi bi-grid-3x3-gap me-1 prazzu-plans__accent" aria-hidden="true"></i>All tools
                </span>
                <span class="badge rounded-pill prazzu-plans__highlight px-3 py-2">
                    <i class="bi bi-stars me-1 prazzu-plans__accent" aria-hidden="true"></i>New features included
                </span>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="billing-heading">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Choose your billing period</span>
                <h2 id="billing-heading" class="h2 mb-2">The same Prazzu Plus with every option</h2>
                <p class="text-body-secondary mb-0">Only the billing period and savings change.</p>
            </div>

            <div class="d-flex justify-content-center mb-4">
                <div class="nav nav-pills rounded-pill p-1 prazzu-plans__billing" id="billing-options" role="tablist" aria-label="Subscription billing period">
                    <button class="nav-link rounded-pill active" id="billing-monthly-tab" data-bs-toggle="pill" data-bs-target="#billing-monthly" type="button" role="tab" aria-controls="billing-monthly" aria-selected="true">
                        Monthly
                    </button>
                    <button class="nav-link rounded-pill" id="billing-annual-tab" data-bs-toggle="pill" data-bs-target="#billing-annual" type="button" role="tab" aria-controls="billing-annual" aria-selected="false">
                        Annual
                        <span class="badge prazzu-plans__saving ms-1">Best value</span>
                    </button>
                </div>
            </div>

            <div class="row g-4 justify-content-center align-items-stretch">
                <div class="col-12 col-xl-5">
                    <article class="card h-100 shadow-sm prazzu-plans__card prazzu-plans__card--free">
                        <div class="card-body p-4 p-lg-5 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <span class="badge prazzu-plans__badge prazzu-plans__badge--muted mb-3">Free</span>
                                    <h2 class="h3 fw-bold mb-2">Prazzu Free</h2>
                                    <p class="text-body-secondary mb-0">Everything you need to solve the calculation with confidence.</p>
                                </div>
                                <i class="bi bi-shield-check fs-2 prazzu-plans__check" aria-hidden="true"></i>
                            </div>

                            <div class="mb-4">
                                <span class="display-5 fw-bold">$0</span>
                                <span class="text-body-secondary"> forever</span>
                            </div>

                            <ul class="list-unstyled d-grid gap-3 mb-4">
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Complete free versions with no artificially limited calculations</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Unlimited use of all free tools</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Transparent results and calculation details when available</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-stars prazzu-plans__spark flex-shrink-0" aria-hidden="true"></i><span>Plus features unlocked during launch</span></li>
                            </ul>

                            <a class="btn prazzu-btn-outline w-100 mt-auto" href="{{ route('tools.index') }}">
                                Explore free tools
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-xl-7">
                    <article class="card h-100 shadow position-relative overflow-hidden prazzu-plans__card prazzu-plans__card--plus">
                        <div class="card-header border-0 p-3 text-center prazzu-plans__plus-header">
                            <strong><i class="bi bi-stars me-1" aria-hidden="true"></i>One subscription unlocks the entire Plus platform</strong>
                        </div>

                        <div class="card-body p-4 p-lg-5 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <span class="badge prazzu-plans__badge mb-3">Prazzu Plus</span>
                                    <h2 class="h3 fw-bold mb-2">More productivity across every tool</h2>
                                    <p class="text-body-secondary mb-0">Subscribe once and use Plus features in current and future tools.</p>
                                </div>
                                <i class="bi bi-gem fs-2 prazzu-plans__accent" aria-hidden="true"></i>
                            </div>

                            <div class="tab-content mb-4" id="billing-content">
                                <div class="tab-pane fade show active" id="billing-monthly" role="tabpanel" aria-labelledby="billing-monthly-tab" tabindex="0">
                                    <div class="d-flex flex-wrap align-items-end gap-2">
                                        <span class="display-5 fw-bold">$9.99</span>
                                        <span class="text-body-secondary fs-5 mb-2">/month</span>
                                    </div>
                                    <p class="text-body-secondary mb-0">Monthly billing. The most flexible option to get started.</p>
                                </div>

                                <div class="tab-pane fade" id="billing-quarterly" role="tabpanel" aria-labelledby="billing-quarterly-tab" tabindex="0">
                                    <div class="d-flex flex-wrap align-items-end gap-2">
                                        <span class="display-5 fw-bold">$27.99</span>
                                        <span class="text-body-secondary fs-5 mb-2">every 3 months</span>
                                    </div>
                                    <p class="mb-1"><strong>$9.33 per month</strong></p>
                                    <p class="prazzu-plans__check mb-0"><i class="bi bi-piggy-bank me-1" aria-hidden="true"></i>Save $1.98 per quarter.</p>
                                </div>

                                <div class="tab-pane fade" id="billing-annual" role="tabpanel" aria-labelledby="billing-annual-tab" tabindex="0">
                                    <div class="d-flex flex-wrap align-items-end gap-2">
                                        <span class="display-5 fw-bold">$99.99</span>
                                        <span class="text-body-secondary fs-5 mb-2">/year</span>
                                    </div>
                                    <p class="mb-1"><strong>$8.33 per month</strong></p>
                                    <p class="prazzu-plans__check mb-0"><i class="bi bi-piggy-bank me-1" aria-hidden="true"></i>Save $19.89 per year — nearly 2 months free.</p>
                                </div>
                            </div>

                            <ul class="list-unstyled row g-3 mb-4">
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Unlimited Plus features</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>History and saved results</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Simulations and comparisons</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Projections and alerts</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>Automation and productivity</span></li>
                                <li class="col-12 col-md-6 d-flex gap-2"><i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i><span>New tools included</span></li>
                            </ul>

                            <button class="btn prazzu-btn-primary w-100 mt-auto" type="button" disabled>
                                <i class="bi bi-lock me-1" aria-hidden="true"></i>Subscriptions coming soon
                            </button>
                            <p class="small text-body-secondary text-center mt-2 mb-0">No payment will be processed at this stage.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="business-plan-heading">
            <article class="card border-info-subtle shadow-sm overflow-hidden">
                <div class="row g-0 align-items-stretch">
                    <div class="col-12 col-lg-7">
                        <div class="card-body p-4 p-lg-5 h-100">
                            <span class="badge text-bg-info mb-3">
                                <i class="bi bi-buildings me-1" aria-hidden="true"></i>Business plan
                            </span>
                            <h2 id="business-plan-heading" class="h2 fw-bold mb-3">Prazzu Plus for your team</h2>
                            <p class="lead text-body-secondary mb-4">
                                The company purchases a number of Plus seats and assigns them to team members.
                                Each person signs in with their own account and continues working independently.
                            </p>

                            <ul class="list-unstyled d-grid gap-3 mb-4">
                                <li class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i>
                                    <span>Purchase based on the number of Prazzu Plus seats</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i>
                                    <span>Individual invitations and management of purchased seats</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-check-circle-fill prazzu-plans__check flex-shrink-0" aria-hidden="true"></i>
                                    <span>Separate sign-in, history, favorites, results, and preferences for each team member</span>
                                </li>
                                <li class="d-flex gap-2">
                                    <i class="bi bi-shield-lock-fill prazzu-plans__accent flex-shrink-0" aria-hidden="true"></i>
                                    <span>The company manages licenses without automatic access to members’ usage data</span>
                                </li>
                            </ul>

                            <div class="row g-3 mb-4" aria-label="Business plan pricing tiers">
                                <div class="col-12 col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-body">
                                        <div class="fw-semibold mb-1">Up to 5 users</div>
                                        <div class="h4 mb-1">$7.99 <small class="fs-6 fw-normal text-body-secondary">/user/month</small></div>
                                        <div class="small text-body-secondary">Ideal for small teams.</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-body">
                                        <div class="fw-semibold mb-1">6 to 20 users</div>
                                        <div class="h4 mb-1">$6.99 <small class="fs-6 fw-normal text-body-secondary">/user/month</small></div>
                                        <div class="small text-body-secondary">More savings as your team grows.</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="border rounded-3 p-3 h-100 bg-body">
                                        <div class="fw-semibold mb-1">21 or more users</div>
                                        <div class="h4 mb-1">$5.99 <small class="fs-6 fw-normal text-body-secondary">/user/month</small></div>
                                        <div class="small text-body-secondary">Best per-seat value for larger teams.</div>
                                    </div>
                                </div>
                            </div>

                            <p class="small text-body-secondary mb-4">
                                Business pricing is charged per licensed user. For reference, individual Prazzu Plus is $9.99 per month.
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                @auth
                                    <a class="btn prazzu-btn-outline" href="{{ route('organizations.create') }}">
                                        <i class="bi bi-building-add me-1" aria-hidden="true"></i>Create business account
                                    </a>
                                @else
                                    <a class="btn prazzu-btn-outline" href="{{ route('login') }}">
                                        <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Sign in to create a business
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5 prazzu-plans__business-aside border-start-lg">
                        <div class="p-4 p-lg-5 h-100 d-flex flex-column justify-content-center">
                            <h3 class="h4 mb-3">What the business plan does not do</h3>
                            <p class="text-body-secondary">
                                Prazzu Tools remains a tools platform, not a collaboration environment or ERP.
                            </p>
                            <ul class="list-unstyled d-grid gap-3 mb-0">
                                <li class="d-flex gap-2"><i class="bi bi-x-circle text-body-secondary flex-shrink-0" aria-hidden="true"></i><span>Does not share history or calculations</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-x-circle text-body-secondary flex-shrink-0" aria-hidden="true"></i><span>Does not transfer ownership of personal accounts</span></li>
                                <li class="d-flex gap-2"><i class="bi bi-x-circle text-body-secondary flex-shrink-0" aria-hidden="true"></i><span>Does not create departments, workflows, or customer management</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="mb-5" aria-labelledby="comparison-heading">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Compare experiences</span>
                <h2 id="comparison-heading" class="h2">Free gets it done. Plus gets it done faster.</h2>
                <p class="text-body-secondary mb-0">Calculation quality is the same. Plus adds productivity and continuity.</p>
            </div>

            <div class="table-responsive rounded-3 shadow-sm prazzu-plans__comparison">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th scope="col" class="py-3">Feature</th>
                        <th scope="col" class="text-center py-3">Free</th>
                        <th scope="col" class="text-center py-3"><i class="bi bi-gem me-1 prazzu-plans__accent" aria-hidden="true"></i>Prazzu Plus</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><th scope="row">Complete calculations and results</th><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Included"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Included"></i></td></tr>
                    <tr><th scope="row">Tool usage</th><td class="text-center">Unlimited</td><td class="text-center">Unlimited</td></tr>
                    <tr><th scope="row">Plus features</th><td class="text-center">Unlocked during launch</td><td class="text-center">Included with subscription</td></tr>
                    <tr><th scope="row">History and saved results</th><td class="text-center text-body-secondary"><i class="bi bi-dash-lg" aria-label="Not included"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Included"></i></td></tr>
                    <tr><th scope="row">Simulations and comparisons</th><td class="text-center text-body-secondary"><i class="bi bi-dash-lg" aria-label="Not included"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Included"></i></td></tr>
                    <tr><th scope="row">Projections, alerts, and automation</th><td class="text-center text-body-secondary"><i class="bi bi-dash-lg" aria-label="Not included"></i></td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Included"></i></td></tr>
                    <tr><th scope="row">Current and future Plus features</th><td class="text-center">During launch</td><td class="text-center"><i class="bi bi-check-circle-fill prazzu-plans__check" aria-label="Included"></i></td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-5" aria-labelledby="faq-heading">
            <div class="text-center mb-4">
                <span class="prazzu-eyebrow">Frequently asked questions</span>
                <h2 id="faq-heading" class="h2">Before choosing your plan</h2>
            </div>

            <div class="accordion mx-auto prazzu-plans__faq" id="plans-faq" style="max-width: 900px;">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq-free" aria-expanded="true" aria-controls="faq-free">
                            Will the free version remain complete?
                        </button>
                    </h3>
                    <div id="faq-free" class="accordion-collapse collapse show" data-bs-parent="#plans-faq">
                        <div class="accordion-body">Yes. Free tools will continue solving the core problem with complete, reliable results. Plus adds productivity, history, automation, and advanced features.</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-all-tools" aria-expanded="false" aria-controls="faq-all-tools">
                            Do I need a separate subscription for each tool?
                        </button>
                    </h3>
                    <div id="faq-all-tools" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">No. One Prazzu Plus subscription unlocks Plus features across all platform tools, including new tools added to the plan.</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-launch" aria-expanded="false" aria-controls="faq-launch">
                            How does the free launch period work?
                        </button>
                    </h3>
                    <div id="faq-launch" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">During this phase, all public Essential and Plus features are available for free without commercial limits. Sign-in is required only for identity-linked features such as saving and recovering history. Future monetization will be enabled through a central policy without reducing the quality of Essential.</div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-business" aria-expanded="false" aria-controls="faq-business">
                            How does the business plan work?
                        </button>
                    </h3>
                    <div id="faq-business" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">
                            The company purchases a number of Plus seats and assigns each seat to a team member with their own account. The company manages only membership and licenses; history, calculations, favorites, results, and preferences remain individual and private.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-payment" aria-expanded="false" aria-controls="faq-payment">
                            Can I subscribe now?
                        </button>
                    </h3>
                    <div id="faq-payment" class="accordion-collapse collapse" data-bs-parent="#plans-faq">
                        <div class="accordion-body">Not yet. This page presents plans and pricing, but checkout and recurring billing will be connected in a future stage of the project.</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card shadow-sm overflow-hidden prazzu-plans__cta" aria-labelledby="plans-cta-heading">
            <div class="card-body p-4 p-lg-5 text-center">
                <span class="badge prazzu-plans__badge mb-3">Try it first</span>
                <h2 id="plans-cta-heading" class="h2 mb-3">See the difference before subscribing</h2>
                <p class="text-body-secondary mx-auto mb-4" style="max-width: 700px;">
                    Use the launch period to try the complete Essential solutions and all available Plus features for free.
                </p>
                <a class="btn prazzu-btn-primary" href="{{ route('tools.index') }}">
                    <i class="bi bi-grid-3x3-gap me-1" aria-hidden="true"></i>Explore the tools
                </a>
            </div>
        </section>
    </div>
@endsection
