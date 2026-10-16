@extends('layouts.app')

@section('title', 'Create account — Prazzu Tools')
@section('meta_description', 'Create a free account to save and recover your results in Prazzu Tools.')
@section('meta_robots', 'noindex,follow')

@section('content')
    @php
        $registrationSource = request()->query('source');
        $registrationAttribution = in_array($registrationSource, ['result_continuity', 'tool_favorite'], true)
            ? array_filter([
                'source' => $registrationSource,
                'tool' => is_string(request()->query('tool')) ? request()->query('tool') : null,
            ])
            : [];
        $isFavoriteIntent = $registrationSource === 'tool_favorite';
    @endphp
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Free</span>
                        <h1 class="h3 mb-2">{{ $isFavoriteIntent ? 'Save this tool so you can find it later' : 'Create your account to continue your work' }}</h1>
                        <p class="text-body-secondary mb-3">
                            @if($isFavoriteIntent)
                                Your free account keeps your favorites available whenever you need them, including on another device.
                            @else
                                Use your account to recover results, favorite tools, repeat calculations, and continue later, including on another device.
                            @endif
                        </p>
                        <p class="small text-body-secondary mb-4">
                            Registration is optional and does not unlock calculations: the tools remain complete for everyone.
                        </p>

                        <form method="POST" action="{{ route('register.store', $registrationAttribution) }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="name">Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" maxlength="120" autocomplete="name" required autofocus>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                <div class="form-text">Use at least 8 characters, including letters and numbers.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Confirm password</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Create free account</button>
                        </form>

                        <p class="text-center text-body-secondary mt-4 mb-0">
                            Already have an account? <a href="{{ route('login', $registrationAttribution) }}">Sign in</a>.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
