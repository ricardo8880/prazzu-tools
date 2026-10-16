@extends('layouts.app')

@section('title', 'Reset password — Prazzu Tools')
@section('meta_description', 'Request a secure link to reset your Prazzu Tools account password.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Secure recovery</span>
                        <h1 class="h3 mb-2">Forgot your password?</h1>
                        <p class="text-body-secondary mb-4">Enter your email and we’ll send you a temporary link to create a new password.</p>

                        @if (session('status'))
                            <div class="alert alert-success" role="status">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" novalidate>
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Send reset link</button>
                        </form>

                        <p class="text-center mt-4 mb-0"><a href="{{ route('login') }}">Back to sign in</a></p>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
