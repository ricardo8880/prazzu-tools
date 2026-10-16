@extends('layouts.app')

@section('title', 'My Prazzu — Prazzu Tools')
@section('meta_description', 'Return to tools, saved results, and favorites linked to your Prazzu Tools account.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">
                <section class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-4">
                            <div>
                                <span class="badge text-bg-success mb-3">Active account</span>
                                <h1 class="h2 mb-2">My Prazzu</h1>
                                <p class="text-body-secondary mb-2">
                                    Hello, {{ auth()->user()->name }}. This is your home base for continuing the work you have saved in the tools.
                                </p>
                                <p class="small text-body-secondary mb-0">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Sign out
                                </button>
                            </form>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-12 col-sm-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-body-secondary mb-1">Saved results</div>
                                    <div class="fs-3 fw-semibold">{{ number_format($historyCount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-body-secondary mb-1">Favorite tools</div>
                                    <div class="fs-3 fw-semibold">{{ number_format($toolFavoriteCount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <div class="small text-body-secondary mb-1">Tools with history</div>
                                    <div class="fs-3 fw-semibold">{{ number_format($usedToolCount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                @if($historyCount > 0 && $favoriteCount === 0)
                    <div class="alert alert-light border small mb-4" role="note">You have not marked any results as favorites yet.</div>
                @endif

                @if($continueRuns->isNotEmpty())
                    <section class="mb-4" aria-labelledby="continue-title">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-2 mb-3">
                            <div>
                                <h2 class="h4 mb-1" id="continue-title">Continue where you left off</h2>
                                <p class="text-body-secondary mb-0">Your most recent tools with results linked to your account.</p>
                            </div>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.index') }}">View all tools</a>
                        </div>

                        <div class="row g-3">
                            @foreach($continueRuns as $run)
                                <div class="col-12 col-md-6">
                                    <article class="card border h-100">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="fs-4" aria-hidden="true"><i class="bi {{ $run['tool_icon'] }}"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <h3 class="h6 mb-0">{{ $run['tool_name'] }}</h3>
                                                        @if($run['favorite'])
                                                            <span class="badge text-bg-warning"><i class="bi bi-star-fill me-1" aria-hidden="true"></i>Favorite</span>
                                                        @endif
                                                    </div>
                                                    <div class="small text-body-secondary mt-1">
                                                        Last result on {{ $run['finished_at']->format('m/d/Y h:i A') }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if($run['context_label'])
                                                <div class="small text-body-secondary"><strong>Context:</strong> {{ $run['context_label'] }}</div>
                                            @elseif($run['reference_date'])
                                                <div class="small text-body-secondary">Reference: {{ $run['reference_date']->format('m/d/Y') }}</div>
                                            @endif

                                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                                <a class="btn btn-primary btn-sm" href="{{ url()->query($run['open_url'], ['source' => 'account_continuity']) }}">
                                                    <i class="bi {{ $run['has_result_detail'] ? 'bi-box-arrow-up-right' : ($run['history_url'] ? 'bi-clock-history' : 'bi-arrow-right-circle') }} me-1" aria-hidden="true"></i>{{ $run['open_label'] }}
                                                </a>

                                                @if($run['repeat_url'])
                                                    <form method="POST" action="{{ url()->query($run['repeat_url'], ['source' => 'account_continuity']) }}">
                                                        @csrf
                                                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                                                            <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Recalculate
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($run['history_url'] && $run['history_url'] !== $run['open_url'])
                                                    <a class="btn btn-outline-secondary btn-sm" href="{{ url()->query($run['history_url'], ['source' => 'account_continuity']) }}">
                                                        <i class="bi bi-clock-history me-1" aria-hidden="true"></i>History
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @else
                    <section class="card border mb-4" aria-labelledby="continue-title">
                        <div class="card-body p-4">
                            <h2 class="h4 mb-2" id="continue-title">Continue where you left off</h2>
                            <p class="text-body-secondary mb-3">
                                There are no saved results in this account yet. When you save work in a tool, your results will appear here so you can return quickly.
                            </p>
                            <a class="btn btn-primary" href="{{ route('tools.index') }}">Explore tools</a>
                        </div>
                    </section>
                @endif

                <section class="mb-4" aria-labelledby="favorite-tools-title">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-2 mb-3">
                        <div>
                            <h2 class="h4 mb-1" id="favorite-tools-title">Favorite tools</h2>
                            <p class="text-body-secondary mb-0">Favorite the tools you use most so you can return without searching the catalog.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('tools.index') }}">Explore tools</a>
                    </div>

                    @if($favoriteTools->isNotEmpty())
                        <div class="row g-3">
                            @foreach($favoriteTools as $tool)
                                <div class="col-12 col-md-6 col-xl-4">
                                    <article class="card border h-100">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <div class="d-flex align-items-start gap-3">
                                                <i class="bi {{ $tool['icon'] }} fs-5" aria-hidden="true"></i>
                                                <div>
                                                    <h3 class="h6 mb-1">{{ $tool['name'] }}</h3>
                                                    <p class="small text-body-secondary mb-0">{{ \Illuminate\Support\Str::limit($tool['description'], 110) }}</p>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                                <a class="btn btn-sm btn-primary" href="{{ url()->query($tool['url'], ['source' => 'account_tool_favorite']) }}">Open tool</a>
                                                <form method="POST" action="{{ route('account.tools.favorite', ['tool' => $tool['slug']]) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit">
                                                        <i class="bi bi-star-fill me-1" aria-hidden="true"></i>Remove favorite
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card border">
                            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div>
                                    <h3 class="h5 mb-1">No favorite tools yet</h3>
                                    <p class="text-body-secondary mb-0">When you open a tool, use the <strong>Favorite</strong> button at the top. It will appear here.</p>
                                </div>
                                <i class="bi bi-star fs-3 text-body-secondary flex-shrink-0" aria-hidden="true"></i>
                            </div>
                        </div>
                    @endif
                </section>

                @if($historyTools->isNotEmpty())
                    <section class="card border mb-4" aria-labelledby="history-title">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
                                <div>
                                    <h2 class="h4 mb-1" id="history-title">Your history by tool</h2>
                                    <p class="text-body-secondary mb-0">Quickly access the history already saved in your account.</p>
                                </div>
                            </div>

                            <div class="list-group list-group-flush">
                                @foreach($historyTools as $tool)
                                    <div class="list-group-item px-0 py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div class="d-flex align-items-start gap-3">
                                            <i class="bi {{ $tool['tool_icon'] }} fs-5" aria-hidden="true"></i>
                                            <div>
                                                <h3 class="h6 mb-1">{{ $tool['tool_name'] }}</h3>
                                                <div class="small text-body-secondary">
                                                    {{ $tool['runs_count'] }} {{ $tool['runs_count'] === 1 ? 'saved result' : 'saved results' }}
                                                    @if($tool['last_used_at'])
                                                        · last used {{ \Illuminate\Support\Carbon::parse($tool['last_used_at'])->format('m/d/Y') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($tool['history_url'])
                                                <a class="btn btn-sm btn-outline-primary" href="{{ url()->query($tool['history_url'], ['source' => 'account_history']) }}">View history</a>
                                            @endif
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ url()->query($tool['tool_url'], ['source' => 'account_history']) }}">Open tool</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

                <section class="card border mb-4" aria-labelledby="account-settings-title">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3" id="account-settings-title">Account and security</h2>

                        @if (auth()->user()->hasVerifiedEmail())
                            <div class="alert alert-success d-flex align-items-center gap-2" role="status">
                                <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                                <span>Your email is verified.</span>
                            </div>
                        @else
                            <div class="alert alert-warning d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" role="alert">
                                <span>Verify your email to protect recovery of saved data.</span>
                                <a class="btn btn-sm btn-warning" href="{{ route('verification.notice') }}">Verify email</a>
                            </div>
                        @endif

                        <div class="row g-4">
                            <div class="col-12 col-lg-7">
                                <div class="card h-100 border">
                                    <div class="card-body">
                                        <h3 class="h5">Change password</h3>
                                        <form method="POST" action="{{ route('password.update') }}" novalidate>
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label" for="current_password">Current password</label>
                                                <input class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                                                @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password">New password</label>
                                                <input class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                                @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="password_confirmation">Confirm new password</label>
                                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                                            </div>
                                            <button class="btn btn-primary" type="submit">Update password</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card border mb-4" aria-labelledby="business-access-title">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h2 class="h4 mb-1" id="business-access-title">Business access</h2>
                                <p class="text-body-secondary mb-0">Create a business or access businesses your account belongs to.</p>
                            </div>
                            <a class="btn btn-outline-primary" href="{{ route('organizations.create') }}">Create business</a>
                        </div>

                        @if($memberships->isNotEmpty())
                            <div class="list-group list-group-flush mt-3">
                                @foreach($memberships as $membership)
                                    <a class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center" href="{{ route('organizations.show', $membership->organization) }}">
                                        <span>{{ $membership->organization->name }}</span>
                                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <div class="alert alert-info mb-0" role="note">
                    <strong>Unified Prazzu account:</strong>
                    your account is currently local. The structure is already prepared to receive the unified Prazzu account identifier in the future without using your email as an integration key.
                </div>
            </div>
        </div>
    </div>
@endsection
