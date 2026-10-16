@extends('layouts.app')

@section('title', 'Sign in — Prazzu Tools')
@section('meta_description', 'Sign in to your free account to save and recover results in Prazzu Tools.')
@section('meta_robots', 'noindex,follow')

@section('content')
    @php
        $loginSource = request()->query('source');
        $loginAttribution = in_array($loginSource, ['result_continuity', 'tool_favorite'], true)
            ? array_filter([
                'source' => $loginSource,
                'tool' => is_string(request()->query('tool')) ? request()->query('tool') : null,
            ])
            : [];
        $isFavoriteIntent = $loginSource === 'tool_favorite';
    @endphp
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Free account</span>
                        <h1 class="h3 mb-2">{{ $isFavoriteIntent ? 'Sign in to save this tool' : 'Sign in to continue your work' }}</h1>
                        <p class="text-body-secondary mb-4">
                            {{ $isFavoriteIntent
                                ? 'When you sign in, this tool will be added to your favorites so you can find it later.'
                                : 'Recover results, favorites, and history, and continue where you left off—even on another device.' }}
                            <strong>The tools remain fully functional even without signing in.</strong>
                        </p>

                        @if (session('auth_notice'))
                            <div class="alert alert-info" role="status">{{ session('auth_notice') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login.store', $loginAttribution) }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="current-password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check mb-0">
                                <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                                    <label class="form-check-label" for="remember">Keep me signed in</label>
                                </div>
                                <a href="{{ route('password.request') }}">Forgot my password</a>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Sign in</button>
                        </form>

                        <p class="text-center text-body-secondary mt-4 mb-0">
                            Don’t have an account yet?
                            <a href="{{ route('register', $loginAttribution) }}">Create one for free to save your work and continue later</a>.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
