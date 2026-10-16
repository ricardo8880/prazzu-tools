@extends('layouts.app')

@section('title', 'Create new password — Prazzu Tools')
@section('meta_description', 'Create a new password for your Prazzu Tools account.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">New password</span>
                        <h1 class="h3 mb-4">Reset your password</h1>

                        <form method="POST" action="{{ route('password.store') }}" novalidate>
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">New password</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                <div class="form-text">Use at least 8 characters, including letters and numbers.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Confirm new password</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Save new password</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
