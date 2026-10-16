@extends('layouts.app')

@section('title', 'Verify email — Prazzu Tools')
@section('meta_description', 'Verify the email address for your Prazzu Tools account.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 col-xl-5">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5 text-center">
                        <i class="bi bi-envelope-check display-5 text-primary" aria-hidden="true"></i>
                        <h1 class="h3 mt-3 mb-2">Verify your email</h1>
                        <p class="text-body-secondary mb-4">We sent a link to <strong>{{ auth()->user()->email }}</strong>. Verification protects recovery of your saved data.</p>

                        @if (session('status'))
                            <div class="alert alert-success" role="status">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">Resend verification email</button>
                        </form>
                        <a class="btn btn-link mt-2" href="{{ route('account.show') }}">Back to My Prazzu</a>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
