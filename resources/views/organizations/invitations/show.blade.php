@extends('layouts.app')

@section('title', 'Business invitation — Prazzu Tools')
@section('meta_description', 'Accept a Prazzu Tools business invitation.')
@section('meta_robots', 'noindex,nofollow')

@section('content')
    <div class="prazzu-page">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <section class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-primary mb-3">Business invitation</span>
                        <h1 class="h3 mb-2">{{ $invitation->organization->name }} invited you</h1>
                        <p class="text-body-secondary">Membership grants business Prazzu Plus access without sharing your history, favorites, results, or preferences.</p>

                        @error('invitation')<div class="alert alert-danger">{{ $message }}</div>@enderror

                        <dl class="row border rounded p-3 mx-0 mb-4">
                            <dt class="col-sm-4">Business</dt><dd class="col-sm-8">{{ $invitation->organization->name }}</dd>
                            <dt class="col-sm-4">Expires</dt><dd class="col-sm-8 mb-0">{{ $invitation->expires_at->format('m/d/Y h:i A') }}</dd>
                        </dl>

                        @if(!$invitation->canBeAccepted())
                            <div class="alert alert-warning mb-0">This invitation is no longer available.</div>
                        @elseif(auth()->guest())
                            <div class="d-grid gap-2 d-sm-flex">
                                <a class="btn btn-primary" href="{{ route('login') }}">Sign in to accept</a>
                                <a class="btn btn-outline-primary" href="{{ route('register') }}">Create personal account</a>
                            </div>
                        @else
                            <p class="small text-body-secondary">You are signed in as {{ auth()->user()->email }}. Confirm to link this account to the business.</p>
                            <form method="POST" action="{{ route('organizations.invitations.accept', $invitation->token) }}">
                                @csrf
                                <button class="btn btn-primary" type="submit">Accept invitation</button>
                            </form>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
